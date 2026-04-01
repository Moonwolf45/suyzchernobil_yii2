# 🚀 GitHub CI/CD - Быстрый старт

## Что было настроено

Для вашего Yii2 проекта создана полная CI/CD инфраструктура:

### ✅ Возможности

| Функция | Описание |
|---------|----------|
| 🔍 **Линтинг** | PHP Lint + PHPStan + PHP CS Fixer |
| 🧪 **Тесты** | Codeception (unit + functional) |
| 📦 **Сборка** | Оптимизация autoload, очистка |
| 🚀 **Деплой** | SSH или FTP (на выбор) |
| 📢 **Уведомления** | Telegram при завершении |

---

## ⚡ Быстрая настройка (5 минут)

### 1️⃣ Создайте файлы

```bash
# В корне проекта
cp .env.example .env
```

### 2️⃣ Настройте секреты в GitHub

Перейдите: `GitHub → Settings → Secrets and variables → Actions`

Добавьте **обязательные** секреты:

```bash
DB_DSN=mysql:host=127.0.0.1;port=3306;dbname=test_db
DB_USERNAME=test_user
DB_PASSWORD=test_password
```

### 3️⃣ Запушьте в репозиторий

```bash
git add .
git commit -m "Add GitHub Actions CI/CD"
git push origin main
```

### 4️⃣ Проверьте Actions

Перейдите во вкладку **Actions** — workflow запустится автоматически!

---

## 📁 Созданные файлы

```
.github/
├── workflows/
│   └── ci-cd.yml           # Основная конфигурация CI/CD
└── .gitignore              # Доп. игноры для GitHub

config/
└── test_db.php             # Конфигурация тестовой БД

.env.example                # Пример переменных окружения
phpstan.neon                # Настройки PHPStan
.php-cs-fixer.dist.php      # Настройки PHP CS Fixer

docs/
└── GITHUB_ACTIONS_SETUP.md # Полная документация
```

---

## 🎯 Что делает workflow

### При каждом push/PR:

```
┌─────────────────────────────────────────────────────┐
│  1. Lint & Static Analysis (5-7 мин)                │
│     ├─ PHP Lint (синтаксис)                         │
│     ├─ PHPStan (статический анализ)                 │
│     └─ PHP CS Fixer (стиль кода)                    │
└─────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────┐
│  2. Unit Tests (3-5 мин)                            │
│     ├─ MySQL сервис                                 │
│     ├─ Codeception unit тесты                       │
│     └─ Отчёт в артефактах                           │
└─────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────┐
│  3. Functional Tests (3-5 мин)                      │
│     ├─ MySQL сервис                                 │
│     ├─ Codeception functional тесты                 │
│     └─ Отчёт в артефактах                           │
└─────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────┐
│  4. Deploy (только main/master)                     │
│     ├─ composer install --no-dev                    │
│     ├─ Очистка кеша                                 │
│     ├─ Создание архива                              │
│     └─ Деплой на сервер (SSH/FTP)                   │
└─────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────┐
│  5. Notifications                                   │
│     └─ Telegram уведомление                         │
└─────────────────────────────────────────────────────┘
```

---

## 🔧 Настройка деплоя

### SSH (рекомендуется)

1. Создайте SSH ключ:
```bash
ssh-keygen -t ed25519 -C "github-deploy" -f ~/.ssh/github_deploy
```

2. Добавьте публичный ключ на сервер:
```bash
cat ~/.ssh/github_deploy.pub | ssh user@server "cat >> ~/.ssh/authorized_keys"
```

3. Добавьте приватный ключ в секреты GitHub:
```bash
gh secret set SSH_PRIVATE_KEY < ~/.ssh/github_deploy
```

4. Добавьте секреты:
```bash
gh secret set SERVER_HOST --body "your.server.com"
gh secret set SERVER_USER --body "deploy"
gh secret set SERVER_PATH --body "/var/www/soyzchernobilkurgan.local"
```

5. Раскомментируйте SSH шаг в `.github/workflows/ci-cd.yml`

### FTP (альтернатива)

1. Добавьте секреты:
```bash
gh secret set FTP_SERVER --body "ftp.yourserver.com"
gh secret set FTP_USERNAME --body "ftp_user"
gh secret set FTP_PASSWORD --body "ftp_password"
```

2. Раскомментируйте FTP шаг в `.github/workflows/ci-cd.yml`

---

## 📢 Настройка Telegram уведомлений

1. Создайте бота в [@BotFather](https://t.me/BotFather):
```
/newbot
Follow instructions...
```

2. Добавьте бота в чат/канал

3. Узнайте Chat ID:
```bash
curl https://api.telegram.org/bot<YOUR_TOKEN>/getUpdates
```

4. Добавьте секреты:
```bash
gh secret set TELEGRAM_BOT_TOKEN --body "123456789:ABCdef..."
gh secret set TELEGRAM_CHAT_ID --body "-1001234567890"
```

---

## 🧪 Локальное тестирование

Перед пушем протестируйте локально:

```bash
# Установите зависимости
composer install

# Запустите тесты
./vendor/bin/codecept run unit
./vendor/bin/codecept run functional

# Проверьте код
vendor/bin/phpstan analyse
vendor/bin/php-cs-fixer fix --dry-run
```

---

## 🐛 Частые проблемы

| Проблема | Решение |
|----------|---------|
| ❌ Tests failed | Проверьте `config/test_db.php` |
| ❌ Deploy failed | Проверьте SSH ключи и доступы |
| ❌ PHPStan errors | Исправьте ошибки или добавьте в `ignoreErrors` |
| ❌ Composer timeout | Увеличьте `COMPOSER_VERSION` или добавьте кэш |

---

## 📊 Мониторинг

1. **Actions** — статус всех запусков
2. **Artifacts** — отчёты тестов (хранятся 90 дней)
3. **Environments** — история деплоев

---

## 📖 Полная документация

См. [`docs/GITHUB_ACTIONS_SETUP.md`](docs/GITHUB_ACTIONS_SETUP.md)

---

## ✨ Следующие шаги

1. ✅ Настройте секреты в GitHub
2. ✅ Запушьте изменения
3. ✅ Проверьте первый запуск в Actions
4. ✅ Настройте деплой на продакшен
5. ✅ Добавьте Telegram уведомления

**Готово!** 🎉
