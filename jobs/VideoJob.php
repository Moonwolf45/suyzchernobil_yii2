<?php

namespace app\jobs;

use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;
use app\models\NewsVideo;

/**
 * Устаревший джоб получения информации о видео.
 * Для обратной совместимости автоматически определяет тип видео
 * и ставит в очередь соответствующий джоб.
 *
 * @deprecated Используйте напрямую YoutubeVideoJob и VkVideoJob
 */
class VideoJob extends BaseObject implements JobInterface
{
    public $video_id;

    /**
     * @inheritdoc
     */
    public function execute($queue): void
    {
        try {
            // Для обратной совместимости определяем тип видео и выполняем соответствующий джоб
            $newsVideo = NewsVideo::find()->where(['id' => $this->video_id])->one();

            if (!$newsVideo) {
                Yii::warning("Видео с ID {$this->video_id} не найдено", 'jobs-video');
                return;
            }

            // Определяем тип видео по URL
            $videoJob = $this->determineVideoJob($newsVideo->video);

            if ($videoJob) {
                // Выполняем соответствующий джоб
                $videoJob->execute($queue);
            } else {
                // Если тип не определён, сохраняем плейсхолдер
                $newsVideo->preview_image = '/images/placeHolder.png';
                $newsVideo->save(false);
                Yii::warning("Не удалось определить тип видео: {$newsVideo->video}", 'jobs-video');
            }

        } catch (\Throwable $e) {
            Yii::error('VideoJob error: ' . $e->getMessage(), 'jobs-video');
            Yii::error($e->getTraceAsString(), 'jobs-video');
            throw $e;
        }
    }

    /**
     * Определяет тип видео по URL и возвращает соответствующий джоб
     *
     * @param string $videoUrl
     * @return VideoInfoJob|null
     */
    private function determineVideoJob(string $videoUrl): ?VideoInfoJob
    {
        // YouTube
        if (preg_match('#(?:www\.)?(?:youtube\.com|youtu\.be)/#i', $videoUrl)) {
            return new YoutubeVideoJob(['video_id' => $this->video_id]);
        }

        // ВКонтакте
        if (preg_match('#(?:www\.)?vk\.com/(?:video|clip|video_ext\.php)#i', $videoUrl)) {
            return new VkVideoJob(['video_id' => $this->video_id]);
        }

        return null;
    }
}
