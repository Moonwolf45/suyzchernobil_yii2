<?php

namespace app\behaviors;


use Yii;
use yii\base\Behavior;
use yii\base\Event;
use yii\caching\TagDependency;
use yii\db\ActiveRecord;

class CacheBehavior extends Behavior {

    /**
     * The name cache component to use.
     *
     * @var string
     */
    public string $cacheName = 'cache';

    /**
     * @inheritdoc
     */
    public function events(): array {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'clearCacheEvent',
            ActiveRecord::EVENT_AFTER_UPDATE => 'clearCacheEvent',
            ActiveRecord::EVENT_AFTER_DELETE => 'clearCacheEvent',
        ];
    }


    /**
     * Event to clear cache.
     *
     * @param Event $event
     *
     * @return void
     */
    public function clearCacheEvent(Event $event): void {
        TagDependency::invalidate(Yii::$app->cache, $this->cacheName);
    }
}