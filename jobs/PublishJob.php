<?php

namespace app\jobs;

use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;
use yii\queue\Queue;

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
            // Пытаемся поставить в очередь оба джоба
            if ($queue instanceof Queue) {
                $queue->push(new VkPublishJob(['news_id' => $this->news_id]));
                $queue->push(new OkPublishJob(['news_id' => $this->news_id]));

                Yii::info("PublishJob: поставлены в очередь VkPublishJob и OkPublishJob для новости {$this->news_id}", 'jobs');
            } else {
                // Если queue не доступен, просто выполняем джобы последовательно
                // Это может произойти при прямом вызове execute()
                (new VkPublishJob(['news_id' => $this->news_id]))->execute($queue);
                (new OkPublishJob(['news_id' => $this->news_id]))->execute($queue);
            }

        } catch (\Throwable $e) {
            Yii::error('PublishJob error: ' . $e->getMessage(), 'jobs');
            throw $e;
        }
    }
}
