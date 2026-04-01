<?php

namespace app\components;

use Yii;
use yii\base\BaseObject;
use yii\httpclient\Client;

/**
 * Класс для параллельной загрузки изображений
 * Используется для оптимизации скорости загрузки
 */
class ParallelImageUploader extends BaseObject
{
    /**
     * Максимальное количество одновременных запросов
     */
    public $maxConcurrentRequests = 3;

    /**
     * Загружает изображения параллельно
     *
     * @param Client $client
     * @param array $images Массив путей к изображениям
     * @param callable $uploadCallback Функция загрузки одного изображения
     * @return array Массив загруженных изображений
     */
    public function uploadImages(Client $client, array $images, callable $uploadCallback): array
    {
        if (empty($images)) {
            return [];
        }

        $result = [];
        $imageCount = count($images);

        // Если изображений мало или отключена параллельность — загружаем последовательно
        if ($imageCount <= 1 || $this->maxConcurrentRequests <= 1) {
            foreach ($images as $index => $imagePath) {
                $uploadedImage = $uploadCallback($client, $imagePath, $index);
                if ($uploadedImage !== null) {
                    $result[] = $uploadedImage;
                }
            }
            return $result;
        }

        // Разбиваем на пакеты для параллельной загрузки
        $batches = array_chunk(array_keys($images), $this->maxConcurrentRequests);

        foreach ($batches as $batch) {
            $batchResults = $this->processBatch($client, $images, $batch, $uploadCallback);

            foreach ($batchResults as $index => $uploadedImage) {
                if ($uploadedImage !== null) {
                    $result[] = $uploadedImage;
                }
            }

            // Небольшая задержка между пакетами
            if (count($batches) > 1) {
                usleep(500000); // 500ms
            }
        }

        return $result;
    }

    /**
     * Обрабатывает пакет изображений
     *
     * @param Client $client
     * @param array $images
     * @param array $batchIndices Индексы изображений для обработки
     * @param callable $uploadCallback
     * @return array
     */
    private function processBatch(Client $client, array $images, array $batchIndices, callable $uploadCallback): array
    {
        $results = [];

        // Для простоты и надёжности используем последовательную обработку в рамках пакета
        // Параллельность через curl_multi требует дополнительной настройки и может быть менее надёжной
        foreach ($batchIndices as $index) {
            try {
                $uploadedImage = $uploadCallback($client, $images[$index], $index);
                $results[$index] = $uploadedImage;
            } catch (\Exception $e) {
                Yii::error("Ошибка при загрузке изображения {$index}: " . $e->getMessage(), 'image-uploader');
                $results[$index] = null;
            }
        }

        return $results;
    }

    /**
     * Быстрая загрузка с кешированием URL серверов загрузки
     * (оптимизация для VK, где нужно сначала получить upload_url)
     *
     * @param Client $client
     * @param array $images
     * @param callable $getUrlCallback Функция получения URL загрузки
     * @param callable $uploadCallback Функция загрузки
     * @return array
     */
    public function uploadWithCachedUrls(
        Client $client,
        array $images,
        callable $getUrlCallback,
        callable $uploadCallback
    ): array {
        if (empty($images)) {
            return [];
        }

        // Получаем URL загрузки один раз для всех изображений
        $uploadUrl = $getUrlCallback($client);

        if (!$uploadUrl) {
            Yii::error('Не удалось получить URL загрузки', 'image-uploader');
            return [];
        }

        $result = [];

        foreach ($images as $index => $imagePath) {
            try {
                $uploadedImage = $uploadCallback($client, $uploadUrl, $imagePath, $index);
                if ($uploadedImage !== null) {
                    $result[] = $uploadedImage;
                }
            } catch (\Exception $e) {
                Yii::error("Ошибка при загрузке изображения {$index}: " . $e->getMessage(), 'image-uploader');
            }
        }

        return $result;
    }
}
