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
     * Максимальное количество картинок в одной пачке
     */
    private const OK_BATCH_SIZE = 5;

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

        // Загружаем картинки пачками
        $uploadedImages = $this->uploadImagesInBatches($client, $images, $uploadedImagesCount, $failedImagesCount);

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
            'application_key' => Yii::$app->params['OkAppId'],
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
            $response = $this->executeWithRetry(function () use ($client, $params, $sig_string) {
                $request = $client->createRequest()
                    ->setMethod('POST')
                    ->setUrl('https://api.ok.ru/api/mediatopic/post')
                    ->setData(array_merge($params, [
                        'application_key' => Yii::$app->params['OkAppPublicKey'],
                        'sig' => $this->calculateSignature($sig_string),
                        'access_token' => Yii::$app->params['OkApiKey']
                    ]));

                Yii::info("REQUEST [mediatopic.post]: POST https://api.ok.ru/api/mediatopic/post", 'jobs-ok');

                $resp = $request->send();

                Yii::info("REQUEST [mediatopic.post]: POST {$request->getFullUrl()}", 'jobs-ok');
                Yii::info("RESPONSE [mediatopic.post]: " . json_encode($resp->data), 'jobs-ok');

                return $resp;
            }, 'mediatopic.post');

            Yii::info('mediatopic.post: ' . (string)$response->data, 'jobs-ok');
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
        $uploadedCount = 0;
        $failedCount = 0;
        
        return $this->uploadImagesInBatches($client, $images, $uploadedCount, $failedCount);
    }

    /**
     * Загружает картинки пачками по OK_BATCH_SIZE
     *
     * @param Client $client
     * @param array $images
     * @param int &$uploadedCount Счётчик загруженных изображений (выходной параметр)
     * @param int &$failedCount Счётчик неудачных загрузок (выходной параметр)
     *
     * @return array
     */
    private function uploadImagesInBatches(Client $client, array $images, int &$uploadedCount = 0, int &$failedCount = 0): array
    {
        $uploadedImages = [];
        $totalImages = count($images);
        
        // Разбиваем на пачки
        for ($i = 0; $i < $totalImages; $i += self::OK_BATCH_SIZE) {
            $batch = array_slice($images, $i, self::OK_BATCH_SIZE);
            $batchSize = count($batch);
            $batchIndex = (int)($i / self::OK_BATCH_SIZE) + 1;
            
            Yii::info("Загрузка пачки {$batchIndex}: {$batchSize} изображений (с {$i} по " . ($i + $batchSize - 1) . ")", 'jobs-ok');
            
            // Загружаем пачку
            $batchTokens = $this->uploadBatchOfImages($client, $batch, $batchSize, $batchIndex);
            
            if ($batchTokens !== null) {
                $uploadedImages = array_merge($uploadedImages, $batchTokens);
                $uploadedCount += count($batchTokens);
                Yii::info("Пачка {$batchIndex} загружена успешно: " . count($batchTokens) . " изображений", 'jobs-ok');
            } else {
                Yii::error("Не удалось загрузить пачку {$batchIndex}", 'jobs-ok');
                $failedCount += $batchSize;
            }
        }

        if ($failedCount > 0) {
            Yii::warning(
                "Загружено {$uploadedCount} из {$totalImages} картинок (ошибок: {$failedCount})",
                'jobs-ok'
            );
        }

        return $uploadedImages;
    }

    /**
     * Загружает пачку изображений в Одноклассники
     *
     * @param Client $client
     * @param array $imagePaths Массив путей к изображениям
     * @param int $batchSize Размер пачки (для count параметра)
     * @param int $batchIndex Индекс пачки для логирования
     *
     * @return array|null Массив токенов загруженных изображений или null
     */
    private function uploadBatchOfImages(Client $client, array $imagePaths, int $batchSize, int $batchIndex): ?array
    {
        // Получаем URL для загрузки с правильным count
        $uploadUrl = $this->getOkUploadUrl($client, $batchSize);
        
        if (!$uploadUrl) {
            Yii::error("Не удалось получить URL загрузки для пачки {$batchIndex}", 'jobs-ok');

            return null;
        }

        // Загружаем все изображения пачкой
        $uploadData = $this->uploadPhotosBatchViaCurl($uploadUrl, $imagePaths, $batchIndex);
        
        if (!$uploadData) {
            return null;
        }

        $photos = $uploadData['photos'] ?? [];
        if (empty($photos)) {
            Yii::error("Пустой массив photos в ответе для пачки {$batchIndex}: " . json_encode($uploadData), 'jobs-ok');

            return null;
        }

        // Возвращаем токены всех загруженных картинок
        $tokens = [];
        foreach ($photos as $photo) {
            if (isset($photo['token'])) {
                $tokens[] = ['id' => $photo['token']];
            }
        }

        if (empty($tokens)) {
            Yii::error("Не удалось извлечь токены из пачки {$batchIndex}: " . json_encode($photos), 'jobs-ok');

            return null;
        }

        return $tokens;
    }

    /**
     * Загружает пачку фото на сервер Одноклассников через чистый curl
     *
     * @param string $uploadUrl
     * @param array $imagePaths Массив путей к файлам
     * @param int $batchIndex Индекс пачки для логирования
     *
     * @return array|null Массив с данными или null
     */
    private function uploadPhotosBatchViaCurl(string $uploadUrl, array $imagePaths, int $batchIndex): ?array
    {
        $ch = curl_init();
        
        // Формируем данные для multipart POST
        $postData = [];
        foreach ($imagePaths as $index => $imagePath) {
            $fullPath = Yii::getAlias('@app/web/' . $imagePath);
            
            if (!file_exists($fullPath) || !is_readable($fullPath)) {
                Yii::warning("Файл не найден или не читаем (пачка {$batchIndex}, фото {$index}): {$fullPath}", 'jobs-ok');
                continue;
            }
            
            // Одноклассники принимают pic1, pic2, pic3 и т.д.
            $fieldName = 'pic' . ($index + 1);
            $postData[$fieldName] = new \CURLFile($fullPath, 'image/jpeg', basename($fullPath));
        }

        if (empty($postData)) {
            Yii::error("Нет валидных файлов для загрузки в пачке {$batchIndex}", 'jobs-ok');

            return null;
        }

        curl_setopt_array($ch, [
            CURLOPT_URL => $uploadUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_CONNECTTIMEOUT => 30,
        ]);

        Yii::info("CURL REQUEST [photo batch upload] (пачка {$batchIndex}): POST {$uploadUrl}, файлов: " . count($postData), 'jobs-ok');

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        
        curl_close($ch);

        if ($error) {
            Yii::error("CURL error (пачка {$batchIndex}): {$error}", 'jobs-ok');
            return null;
        }

        if ($httpCode !== 200) {
            Yii::error("HTTP error (пачка {$batchIndex}): code {$httpCode}, response: {$response}", 'jobs-ok');
            return null;
        }

        $data = json_decode($response, true);
        if (!$data) {
            Yii::error("JSON decode error (пачка {$batchIndex}): {$response}", 'jobs-ok');
            return null;
        }

        Yii::info("CURL RESPONSE [photo batch upload] (пачка {$batchIndex}): " . json_encode($data), 'jobs-ok');

        // Проверяем на наличие ошибки
        if (isset($data['error_code']) || isset($data['error'])) {
            $errorInfo = $data['error_code'] ?? $data['error'] ?? 'Unknown error';
            Yii::error("API error from upload server (пачка {$batchIndex}): " . json_encode($errorInfo), 'jobs-ok');

            return null;
        }

        return $data;
    }

    /**
     * Вычисляет подпись для запроса к API Одноклассников
     *
     * @param string $stringParams
     * @return string
     */
    private function calculateSignature(string $stringParams): string
    {
        return md5($stringParams . Yii::$app->params['OkAppSecretKey']);
    }

    /**
     * Получает URL сервера загрузки Одноклассников
     *
     * @param Client $client
     * @param int $count Количество фотографий для загрузки
     *
     * @return string
     */
    private function getOkUploadUrl(Client $client, int $count = 1): string
    {
        $response = $this->executeWithRetry(function () use ($client, $count) {
            $request = $client->createRequest()
                ->setMethod('POST')
                ->setUrl('https://api.ok.ru/api/photosV2/getUploadUrl')
                ->setData([
                    'application_key' => Yii::$app->params['OkAppPublicKey'],
                    'count' => $count,
                    'gid' => Yii::$app->params['OkGroupId'],
                    'access_token' => Yii::$app->params['OkApiKey']
                ]);

            Yii::info("REQUEST [photosV2.getUploadUrl]: POST https://api.ok.ru/api/photosV2/getUploadUrl (count: {$count})", 'jobs-ok');

            $resp = $request->send();

            Yii::info("REQUEST [photosV2.getUploadUrl]: GET {$request->getFullUrl()}", 'jobs-ok');
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
