# Рефакторинг PublishJob

## Обзор изменений

Оригинальный `PublishJob` был разделён на несколько классов для повышения отказоустойчивости и поддерживаемости.

## Структура файлов

```
jobs/
├── SocialPublishJob.php      # Базовый абстрактный класс с общей логикой
├── VkPublishJob.php          # Публикация ВКонтакте
├── OkPublishJob.php          # Публикация в Одноклассниках
├── PublishJob.php            # Устаревший класс для обратной совместимости
└── ...

components/
└── ParallelImageUploader.php # Утилита для параллельной загрузки изображений
```

## Ключевые улучшения

### 1. Разделение ответственности
- **VkPublishJob** — отвечает только за публикацию ВКонтакте
- **OkPublishJob** — отвечает только за публикацию в Одноклассниках
- Ошибка в одной соцсети **не влияет** на другую

### 2. Отказоустойчивость при загрузке изображений
- Каждая картинка загружается в отдельном try-catch блоке
- Если загрузка картинки не удалась — продолжается загрузка следующих
- Публикация происходит даже если ни одна картинка не загрузилась (только текст)
- Предварительная проверка существования файла

### 3. Retry-механизм
- Автоматические повторные попытки при временных ошибках API (до 3 попыток)
- Задержка между попытками: 1 секунда
- Логирование каждой неудачной попытки

### 4. Оптимизация производительности
- **Кеширование URL серверов загрузки** — запрос к API за upload_url делается один раз для всех картинок
- Возможность параллельной загрузки (класс `ParallelImageUploader`)
- Максимум 10 картинок на публикацию (настраивается в константе `MAX_IMAGES`)

### 5. Валидация данных
- Проверка существования новости перед публикацией
- Проверка наличия файла изображения перед загрузкой
- Безопасное сохранение времени публикации

### 6. Улучшенное логирование
- Разделение логов по соцсетям: `jobs-vk`, `jobs-ok`, `jobs-social`
- Детальная информация о каждой операции
- Логирование ошибок с trace

## Использование

### Вариант 1: Прямое использование новых джобов (рекомендуется)

```php
use app\jobs\VkPublishJob;
use app\jobs\OkPublishJob;

// Публикация только ВКонтакте
Yii::$app->queue->push(new VkPublishJob([
    'news_id' => $newsId
]));

// Публикация только в Одноклассниках
Yii::$app->queue->push(new OkPublishJob([
    'news_id' => $newsId
]));

// Публикация в обе соцсети
Yii::$app->queue->push(new VkPublishJob(['news_id' => $newsId]));
Yii::$app->queue->push(new OkPublishJob(['news_id' => $newsId]));
```

### Вариант 2: Обратная совместимость (старый способ)

```php
use app\jobs\PublishJob;

// Автоматически поставит в очередь оба джоба
Yii::$app->queue->push(new PublishJob([
    'news_id' => $newsId
]));
```

## Расширение для других соцсетей

Для добавления новой соцсети создайте класс, наследующий `SocialPublishJob`:

```php
namespace app\jobs;

use app\models\News;
use yii\httpclient\Client;

class TelegramPublishJob extends SocialPublishJob
{
    protected function getPublishedAtField(): string
    {
        return 'published_at_telegram';
    }

    protected function getSocialNetworkName(): string
    {
        return 'Telegram';
    }

    protected function publish(Client $client, News $news): bool
    {
        // Ваша логика публикации
        return true;
    }

    protected function uploadImages(Client $client, array $images): array
    {
        // Ваша логика загрузки изображений
        return [];
    }
}
```

## Конфигурация

### Настройки в базовом классе

```php
// Максимальное количество картинок
protected const MAX_IMAGES = 10;

// Количество попыток при ошибке API
protected const MAX_RETRIES = 3;

// Задержка между попытками (мс)
protected const RETRY_DELAY = 1000;
```

### Настройки ParallelImageUploader

```php
$uploader = new ParallelImageUploader([
    'maxConcurrentRequests' => 3, // Количество одновременных запросов
]);
```

## Миграция

### Шаг 1: Обновите места вызова

Найдите все места, где используется `PublishJob`:

```bash
grep -r "PublishJob" app/ --include="*.php"
```

### Шаг 2: Замените на новые джобы

**Было:**
```php
Yii::$app->queue->push(new PublishJob(['news_id' => $id]));
```

**Стало:**
```php
Yii::$app->queue->push(new VkPublishJob(['news_id' => $id]));
Yii::$app->queue->push(new OkPublishJob(['news_id' => $id]));
```

### Шаг 3: Протестируйте

1. Проверьте логи: `@runtime/logs/app.log`
2. Убедитесь, что публикации проходят успешно
3. Проверьте обработку ошибок (например, при отсутствии картинок)

## Логи

### Примеры логов

**Успешная загрузка картинки:**
```
[info][jobs-vk] Картинка 0 загружена: photo-123456_789
```

**Ошибка загрузки картинки (продолжение работы):**
```
[warning][jobs-vk] Не удалось загрузить картинку 2: /uploads/news/image.jpg
[warning][jobs-vk] Загружено 2 из 3 картинок (ошибок: 1)
```

**Временная ошибка API (автоматическая повторная попытка):**
```
[warning][jobs-social] Попытка 1 не удалась (wall.post): Connection timeout
[info][jobs-vk] wall.post: SUCCESS
```

**Все попытки исчерпаны:**
```
[error][jobs-social] Все попытки исчерпаны (wall.post): Connection timeout after 3 retries
```

## Дополнительные рекомендации

### 1. Мониторинг очереди

Настройте мониторинг очереди для отслеживания неудачных джобов:

```php
// В config/console.php
'queue' => [
    'class' => \yii\queue\file\Queue::class,
    'as log' => \yii\queue\LogBehavior::class
],
```

### 2. Обработка "мёртвых" джобов

Настройте обработку джобов, которые не удалось выполнить:

```bash
# Запуск воркера с обработкой ошибок
php yii queue/listen --maxAttempts=3 --maxTime=300
```

### 3. Кеширование

Для кеширования URL серверов загрузки между запусками джобов можно использовать Redis:

```php
// В базовом классе
private function getCachedUploadUrl($key)
{
    return Yii::$app->cache->get("upload_url_{$key}");
}

private function setCachedUploadUrl($key, $url, $duration = 3600)
{
    Yii::$app->cache->set("upload_url_{$key}", $url, $duration);
}
```

### 4. Тестирование

Создайте тесты для проверки различных сценариев:

```php
// tests/unit/jobs/VkPublishJobTest.php
class VkPublishJobTest extends TestCase
{
    public function testExecuteWithMissingImage()
    {
        // Тест: публикация должна пройти даже если картинки нет
    }

    public function testExecuteWithApiError()
    {
        // Тест: проверка retry-механизма
    }
}
```

## Сравнение с оригинальной версией

| Характеристика | Было | Стало |
|----------------|------|-------|
| Обработка ошибок картинок | Падал весь джоб | Продолжение работы |
| Разделение ВК/ОК | Связаны | Независимы |
| Retry при ошибках API | Нет | 3 попытки |
| Кеширование upload_url | Нет | Есть |
| Проверка файла изображения | Нет | Есть |
| Логирование | Базовое | Детальное |
| Расширяемость | Сложно | Легко (наследование) |
