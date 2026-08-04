<?php

namespace app\jobs;

use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;

/**
 * Устаревший джоб публикации.
 * Для обратной совместимости ставит в очередь VkPublishJob и OkPublishJob.
 *
 * @deprecated Используйте напрямую VkPublishJob и OkPublishJob
 */
class PublishJob extends BaseObject implements JobInterface
{
    public $news_id;

    /**
     * @inheritdoc
     */
    public function execute($queue): void
    {
        // Для обратной совместимости ставим в очередь оба джоба
        // Если queue доступен через $queue параметр

        try {
            $queue->push(new VkPublishJob(['news_id' => $this->news_id]));
            $queue->push(new OkPublishJob(['news_id' => $this->news_id]));

            Yii::info("PublishJob: поставлены в очередь VkPublishJob и OkPublishJob для новости {$this->news_id}", 'jobs');
        } catch (\Throwable $e) {
            Yii::error('PublishJob error: ' . $e->getMessage(), 'jobs');

            throw $e;
        }
    }
}
