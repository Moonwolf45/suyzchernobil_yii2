<?php

namespace app\jobs;

use app\models\News;
use Yii;
use yii\httpclient\Client;

/**
 * Джоб для публикации новости в Одноклассниках
 */
class OkPublishJob extends SocialPublishJob
{
    /**
     * @var string Кеш URL серверов загрузки
     */
    private static $uploadUrlCache = null;

    /**
     * @inheritdoc
     */
    protected function getPublishedAtField(): string
    {
        return 'published_at_ok';
    }

    /**
     * @inheritdoc
     */
    protected function getSocialNetworkName(): string
    {
        return 'Одноклассники';
    }

    /**
     * @inheritdoc
     */
    protected function publish(Client $client, News $news, int &$uploadedImagesCount = 0, int &$failedImagesCount = 0): bool
    {
        $message = $this->prepareMessage($news);
        $images = $this->getImages($news);

        // Загружаем картинки с отказоустойчивостью
        $uploadedImages = $this->uploadImagesWithFallback($client, $images, $uploadedImagesCount, $failedImagesCount);

        // Формируем attachment
        $attachment = [
            'media' => [
                [
                    'type' => 'photo',
                    'list' => $uploadedImages
                ],
                [
                    'type' => 'text',
                    'text' => $message
                ]
            ]
        ];

        $attachmentJson = json_encode($attachment);

        $params = [
            'application_key' => Yii::$app->params['OkAppPublicKey'],
            'attachment' => $attachmentJson,
            'format' => 'json',
            'gid' => Yii::$app->params['OkGroupId'],
            'method' => 'mediatopic.post',
            'type' => 'GROUP_THEME'
        ];

        ksort($params);
        $sig_string = '';
        foreach ($params as $k => $v) {
            $sig_string .= $k . '=' . $v;
        }

        try {
            $response = $this->executeWithRetry(function () use ($client, $attachmentJson, $sig_string) {
                $request = $client->createRequest()
                    ->setMethod('GET')
                    ->setUrl('https://api.ok.ru/fb.do')
                    ->setData([
                        'application_key' => Yii::$app->params['OkAppPublicKey'],
                        'attachment' => $attachmentJson,
                        'format' => 'json',
                        'gid' => Yii::$app->params['OkGroupId'],
                        'method' => 'mediatopic.post',
                        'type' => 'GROUP_THEME',
                        'sig' => $this->calculateSignature($sig_string),
                        'access_token' => Yii::$app->params['OkApiKey']
                    ]);

                Yii::info("REQUEST [mediatopic.post]: GET {$request->getFullUrl()}", 'jobs-ok');
                Yii::info("REQUEST [mediatopic.post]: GET https://api.ok.ru/fb.do", 'jobs-ok');
                $resp = $request->send();
                Yii::info("RESPONSE [mediatopic.post]: " . json_encode($resp->data), 'jobs-ok');

                return $resp;
            }, 'mediatopic.post');

            if ($response && $response->isOk) {
                $hasError = array_key_exists('error_code', (array) $response->data);

                Yii::info('mediatopic.post: ' . ($hasError ? 'ERROR' : 'SUCCESS'), 'jobs-ok');
                Yii::info($response->data ?? 'No data', 'jobs-ok');

                return !$hasError;
            }

            Yii::error('mediatopic.post: response is not ok', 'jobs-ok');

            return false;

        } catch (\Exception $e) {
            Yii::error('mediatopic.post exception: ' . $e->getMessage(), 'jobs-ok');

            return false;
        }
    }

    /**
     * @inheritdoc
     */
    protected function uploadImages(Client $client, array $images): array
    {
        $result = [];

        foreach ($images as $index => $imagePath) {
            $uploadedImage = $this->uploadSingleImageOk($client, $imagePath, $index);

            if ($uploadedImage !== null) {
                $result[] = $uploadedImage;
            }
        }

        return $result;
    }

    /**
     * Загружает картинки с продолжением работы при ошибках
     *
     * @param Client $client
     * @param array $images
     * @param int &$uploadedCount Счётчик загруженных изображений (выходной параметр)
     * @param int &$failedCount Счётчик неудачных загрузок (выходной параметр)
     *
     * @return array
     */
    private function uploadImagesWithFallback(Client $client, array $images, int &$uploadedCount = 0, int &$failedCount = 0): array
    {
        $uploadedImages = [];

        foreach ($images as $index => $imagePath) {
            // Проверяем существование файла
            if (!$this->imageExists($imagePath)) {
                Yii::warning("Файл изображения не найден: {$imagePath}", 'jobs-ok');
                $failedCount++;

                continue;
            }

            try {
                $uploadedImage = $this->uploadSingleImageOk($client, $imagePath, $index);

                if ($uploadedImage !== null) {
                    $uploadedImages[] = $uploadedImage;
                    $uploadedCount++;
                    Yii::info("Картинка {$index} загружена", 'jobs-ok');
                } else {
                    Yii::warning("Не удалось загрузить картинку {$index}: {$imagePath}", 'jobs-ok');
                    $failedCount++;
                }

            } catch (\Exception $e) {
                Yii::error("Ошибка при загрузке картинки {$index}: " . $e->getMessage(), 'jobs-ok');
                $failedCount++;
                // Продолжаем загрузку следующих картинок
            }
        }

        if ($failedCount > 0) {
            Yii::warning(
                "Загружено {$uploadedCount} из " . count($images) .
                " картинок (ошибок: {$failedCount})",
                'jobs-ok'
            );
        }

        return $uploadedImages;
    }

    /**
     * Загружает одно изображение в Одноклассники
     *
     * @param Client $client
     * @param string $imagePath
     * @param int $index
     *
     * @return array|null Токен загруженного изображения или null
     */
    private function uploadSingleImageOk(Client $client, string $imagePath, int $index): ?array
    {
        // Получаем URL с кешированием
        $uploadUrl = $this->getOkUploadUrl($client);
        
        if (!$uploadUrl) {
            Yii::error("Не удалось получить URL загрузки (image {$index})", 'jobs-ok');
            
            return null;
        }

        // Шаг 2: Загружаем изображение
        $fullPath = Yii::getAlias('@app/web/' . $imagePath);

        if (!file_exists($fullPath) || !is_readable($fullPath)) {
            Yii::error("Файл не существует или не читаем (image {$index}): {$fullPath}", 'jobs-ok');
            return null;
        }

        $uploadResponse = $this->executeWithRetry(function () use ($client, $uploadUrl, $fullPath, $index) {
            $fileContent = file_get_contents($fullPath);

            if ($fileContent === false) {
                Yii::error("Не удалось прочитать содержимое файла (image {$index}): {$fullPath}", 'jobs-ok');
                return null;
            }

            $request = $client->createRequest()
                ->setMethod('POST')
                ->setUrl($uploadUrl)
                ->addFileContent('pic1', $fileContent);

            Yii::info("REQUEST [photo upload] (image {$index}): POST {$uploadUrl}", 'jobs-ok');
            $resp = $request->send();
            Yii::info("RESPONSE [photo upload] (image {$index}): " . json_encode($resp->data), 'jobs-ok');

            return $resp;
        }, "photo upload (image {$index})");

        if (!$uploadResponse || !$uploadResponse->isOk) {
            Yii::error("Не удалось загрузить фото на сервер (image {$index})", 'jobs-ok');

            return null;
        }

        if (array_key_exists('error_code', $uploadResponse->data)) {
            Yii::error(
                "API error upload: " . json_encode($uploadResponse->data['error_code']),
                'jobs-ok'
            );

            return null;
        }

        $photos = $uploadResponse->data['photos'] ?? [];
        if (!empty($photos)) {
            // Возвращаем токен первой картинки
            return ['id' => $photos[0]['token']];
        }

        Yii::error("Пустой массив photos в ответе", 'jobs-ok');

        return null;
    }

    /**
     * Вычисляет подпись для запроса к API Одноклассников
     *
     * @param string $stringParams
     * @return string
     */
    private function calculateSignature(string $stringParams): string
    {
        return md5($stringParams . md5(Yii::$app->params['OkApiKey'] . Yii::$app->params['OkAppSecretKey']));
    }

    /**
     * Получает URL сервера загрузки Одноклассников с кешированием
     *
     * @param Client $client
     * @return string
     */
    private function getOkUploadUrl(Client $client): string
    {

        $method = 'photosV2.getUploadUrl';

        $params = [
            'application_key' => Yii::$app->params['OkAppPublicKey'],
            'count' => 1,
            'format' => 'json',
            'gid' => Yii::$app->params['OkGroupId'],
            'method' => $method,
            'access_token' => Yii::$app->params['OkApiKey']
        ];

        ksort($params);
        $sig_string = '';
        foreach ($params as $key => $value) {
            $sig_string .= $key . '=' . $value;
        }

        $response = $this->executeWithRetry(function () use ($client, $method, $sig_string) {
            $request = $client->createRequest()
                ->setMethod('GET')
                ->setUrl('https://api.ok.ru/fb.do')
                ->setData([
                    'application_key' => Yii::$app->params['OkAppPublicKey'],
                    'count' => 1,
                    'format' => 'json',
                    'gid' => Yii::$app->params['OkGroupId'],
                    'method' => $method,
                    'sig' => $this->calculateSignature($sig_string),
                    'access_token' => Yii::$app->params['OkApiKey']
                ]);

            Yii::info("REQUEST [photosV2.getUploadUrl]: GET {$request->getFullUrl()}", 'jobs-ok');
            Yii::info("REQUEST [photosV2.getUploadUrl]: GET https://api.ok.ru/fb.do", 'jobs-ok');
            $resp = $request->send();
            Yii::info("RESPONSE [photosV2.getUploadUrl]: " . json_encode($resp->data), 'jobs-ok');

            return $resp;
        }, 'photosV2.getUploadUrl');

        if (!$response || !$response->isOk) {
            $errorData = $response ? json_encode($response->data) : 'no response';
            Yii::error("getUploadUrl: response is not ok. Data: {$errorData}", 'jobs-ok');

            return '';
        }

        if (array_key_exists('error_code', $response->data)) {
            Yii::error(
                "API error getUploadUrl: " . json_encode($response->data),
                'jobs-ok'
            );

            return '';
        }

        $uploadUrl = $response->data['upload_url'] ?? null;

        return $uploadUrl ?? '';
    }
}
