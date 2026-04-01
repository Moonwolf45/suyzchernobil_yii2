# Рефакторинг VideoJob

## Обзор изменений

Оригинальный `VideoJob` был разделён на несколько классов для повышения отказоустойчивости, поддерживаемости и расширения функциональности.

## Структура файлов

```
jobs/
├── VideoInfoJob.php          # Базовый абстрактный класс с общей логикой
├── YoutubeVideoJob.php       # Получение данных из YouTube
├── VkVideoJob.php            # Получение данных из ВКонтакте
├── VideoJob.php              # Устаревший класс для обратной совместимости
└── ...
```

## Ключевые улучшения

### 1. Разделение ответственности
- **YoutubeVideoJob** — отвечает только за YouTube
- **VkVideoJob** — отвечает только за ВКонтакте
- Легко добавить новые источники (Rutube, Vimeo и т.д.)

### 2. Отказоустойчивость
- Проверка существования записи перед обработкой
- Автоматическая установка плейсхолдера при ошибке
- Продолжение работы при недоступности API

### 3. Retry-механизм
- До 3 автоматических попыток при временных ошибках API
- Задержка между попытками: 1 секунда
- Детальное логирование каждой попытки

### 4. Улучшенный парсинг URL

**YouTube:**
- `youtube.com/embed/VIDEO_ID`
- `youtube.com/watch?v=VIDEO_ID`
- `youtu.be/VIDEO_ID`
- `youtube.com/v/VIDEO_ID`

**ВКонтакте:**
- `vk.com/video_ext.php?oid=...&id=...`
- `vk.com/video-123456_789012345`
- `vk.com/clip-123456_789012345`

### 5. Оптимизация превью
- YouTube: проверка доступности maxresdefault, fallback на hqdefault
- VK: выбор изображения с максимальным разрешением
- Автоматическая установка плейсхолдера при ошибке

### 6. Улучшенная работа с длительностью
- Корректное форматирование в ISO 8601 (PT1H30M15S)
- Поддержка часов, минут, секунд
- Валидация данных перед сохранением

### 7. Устранение проблем оригинала
- ❌ Статические методы → ✅ Инкапсуляция
- ❌ Нет обработки ошибок → ✅ Try-catch + retry
- ❌ Хардкод парсинга → ✅ Гибкие regex-паттерны
- ❌ Нет проверки данных → ✅ Валидация перед сохранением

## Использование

### Вариант 1: Прямое использование новых джобов (рекомендуется)

```php
use app\jobs\YoutubeVideoJob;
use app\jobs\VkVideoJob;

// Для YouTube
Yii::$app->queue->push(new YoutubeVideoJob([
    'video_id' => $videoId
]));

// Для ВКонтакте
Yii::$app->queue->push(new VkVideoJob([
    'video_id' => $videoId
]));
```

### Вариант 2: Обратная совместимость (старый способ)

```php
use app\jobs\VideoJob;

// Автоматически определит тип видео и выполнит нужный джоб
Yii::$app->queue->push(new VideoJob([
    'video_id' => $videoId
]));
```

## Расширение для других источников

Для добавления нового источника видео создайте класс, наследующий `VideoInfoJob`:

```php
namespace app\jobs;

use Yii;
use yii\httpclient\Client;

class RutubeVideoJob extends VideoInfoJob
{
    protected function getSourceName(): string
    {
        return 'Rutube';
    }

    protected function parseVideoUrl(string $videoUrl): ?array
    {
        // Парсинг URL Rutube
        if (preg_match('#rutube\.ru/video/([a-zA-Z0-9]+)#i', $videoUrl, $matches)) {
            return ['video_id' => $matches[1]];
        }
        return null;
    }

    protected function fetchVideoInfo(Client $client, array $videoData): ?array
    {
        // Получение информации из API Rutube
        $response = $this->executeWithRetry(function () use ($client, $videoData) {
            return $client->createRequest()
                ->setMethod('GET')
                ->setUrl('https://rutube.ru/api/video/' . $videoData['video_id'])
                ->send();
        }, 'Rutube API');

        if ($response && $response->isOk) {
            return [
                'preview_image' => $response->data['thumbnail_url'],
                'duration' => $this->formatDuration($response->data['duration']),
            ];
        }

        return null;
    }
}
```

## Конфигурация

### Настройки в базовом классе

```php
// Максимальное количество попыток
protected const MAX_RETRIES = 3;

// Задержка между попытками (мс)
protected const RETRY_DELAY = 1000;

// Плейсхолдер по умолчанию
protected const DEFAULT_PREVIEW_IMAGE = '/images/placeHolder.png';
```

## Миграция

### Шаг 1: Найдите все места использования

```bash
grep -r "VideoJob" app/ --include="*.php"
```

### Шаг 2: Обновите код (опционально)

**Было:**
```php
Yii::$app->queue->push(new VideoJob(['video_id' => $id]));
```

**Стало (рекомендуется):**
```php
// Если известен тип видео
Yii::$app->queue->push(new YoutubeVideoJob(['video_id' => $id]));

// Или оставьте автоопределение
Yii::$app->queue->push(new VideoJob(['video_id' => $id]));
```

### Шаг 3: Протестируйте

1. Проверьте логи: `@runtime/logs/app.log`
2. Убедитесь, что превью загружаются корректно
3. Проверьте обработку ошибок (недоступные видео)

## Логи

### Примеры логов

**Успешное получение информации:**
```
[info][jobs-video] Видео 123 успешно обновлено
```

**Видео не найдено:**
```
[warning][jobs-video] Видео с ID 456 не найдено
```

**URL не распознан:**
```
[warning][jobs-video] Не удалось распознать URL видео: https://example.com/video.mp4
```

**Ошибка API (автоматическая повторная попытка):**
```
[warning][jobs-video] Попытка 1 не удалась (YouTube API): Connection timeout
[info][jobs-video] Видео 123 успешно обновлено
```

**Все попытки исчерпаны:**
```
[error][jobs-video] Все попытки исчерпаны (VK Video API): Connection timeout
[warning][jobs-video] Не удалось получить информацию о видео 789
```

## Сравнение с оригинальной версией

| Характеристика | Было | Стало |
|----------------|------|-------|
| Обработка ошибок | Нет | Есть (try-catch + retry) |
| Разделение YouTube/VK | В одном методе | Разные классы |
| Retry при ошибках API | Нет | 3 попытки |
| Проверка записи | Нет | Есть |
| Форматирование длительности | Частично | Полное (ISO 8601) |
| Выбор превью | Фиксированное | Лучшее качество |
| Парсинг URL | 2 паттерна | 7+ паттернов |
| Расширяемость | Сложно | Легко (наследование) |
| Логирование | Минимальное | Детальное |

## Дополнительные рекомендации

### 1. Кеширование API запросов

Для экономии квот API можно кешировать результаты:

```php
// В VideoInfoJob или наследниках
private function getCachedVideoInfo(string $cacheKey)
{
    return Yii::$app->cache->get("video_info_{$cacheKey}");
}

private function setCachedVideoInfo(string $cacheKey, array $data, int $duration = 86400)
{
    Yii::$app->cache->set("video_info_{$cacheKey}", $data, $duration);
}
```

### 2. Пакетная обработка

Для обработки множества видео используйте пакетные джобы:

```php
class BatchVideoJob extends BaseObject implements JobInterface
{
    public $videoIds = [];

    public function execute($queue): void
    {
        foreach ($this->videoIds as $videoId) {
            $queue->push(new VideoJob(['video_id' => $videoId]));
        }
    }
}
```

### 3. Мониторинг квот API

YouTube API имеет дневные лимиты. Добавьте мониторинг:

```php
// В YoutubeVideoJob::fetchVideoInfo()
if (isset($response->data['quotaExceeded'])) {
    Yii::error('YouTube API quota exceeded', 'jobs-video');
    // Отправить уведомление администратору
}
```

### 4. Тестирование

Создайте тесты для различных сценариев:

```php
// tests/unit/jobs/YoutubeVideoJobTest.php
class YoutubeVideoJobTest extends TestCase
{
    public function testParseVideoUrlEmbed()
    {
        $job = new YoutubeVideoJob();
        $result = $this->invokeMethod($job, 'parseVideoUrl', [
            'https://www.youtube.com/embed/dQw4w9WgXcQ'
        ]);
        $this->assertEquals(['video_id' => 'dQw4w9WgXcQ'], $result);
    }

    public function testParseVideoUrlShort()
    {
        $job = new YoutubeVideoJob();
        $result = $this->invokeMethod($job, 'parseVideoUrl', [
            'https://youtu.be/dQw4w9WgXcQ'
        ]);
        $this->assertEquals(['video_id' => 'dQw4w9WgXcQ'], $result);
    }
}
```

### 5. Обработка недоступных видео

Добавьте повторную обработку для временно недоступных видео:

```php
// В VideoInfoJob::execute()
if (!$info) {
    // Повторная попытка через 1 час
    $queue->delay(3600)->push(new static(['video_id' => $this->video_id]));
}
```
