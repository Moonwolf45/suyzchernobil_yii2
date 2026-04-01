# GitHub Actions CI/CD - Инструкция по настройке

## 📋 Содержание

1. [Быстрый старт](#быстрый-старт)
2. [Настройка секретов GitHub](#настройка-секретов-github)
3. [Настройка базы данных для тестов](#настройка-базы-данных-для-тестов)
4. [Настройка деплоя](#настройка-деплоя)
5. [Настройка уведомлений](#настройка-уведомлений)
6. [Запуск workflow](#запуск-workflow)
7. [Устранение проблем](#устранение-проблем)

---

## 🚀 Быстрый старт

### Шаг 1: Создайте репозиторий на GitHub

```bash
cd D:\OSPanel\home\soyzchernobilkurgan.local
git init
git remote add origin https://github.com/YOUR_USERNAME/YOUR_REPO.git
git add .
git commit -m "Initial commit with CI/CD"
git push -u origin main
```

### Шаг 2: Создайте файл `.env`

```bash
cp .env.example .env
```

Заполните `.env` своими значениями (не коммитьте в репозиторий!).

### Шаг 3: Настройте секреты в GitHub

Перейдите в: `GitHub Repo → Settings → Secrets and variables → Actions`

---

## 🔐 Настройка секретов GitHub

### Обязательные секреты

| Secret | Описание | Пример |
|--------|----------|--------|
| `DB_DSN` | DSN базы данных для тестов | `mysql:host=127.0.0.1;port=3306;dbname=test_db` |
| `DB_USERNAME` | Пользователь БД | `test_user` |
| `DB_PASSWORD` | Пароль БД | `test_password` |

### Опциональные секреты (для деплоя и уведомлений)

| Secret | Описание | Пример |
|--------|----------|--------|
| `TELEGRAM_BOT_TOKEN` | Токен бота Telegram | `123456789:ABCdefGHIjklMNOpqrsTUVwxyz` |
| `TELEGRAM_CHAT_ID` | ID чата для уведомлений | `-1001234567890` |
| `SSH_PRIVATE_KEY` | SSH ключ для деплоя | `-----BEGIN OPENSSH PRIVATE KEY-----...` |
| `SERVER_HOST` | Хост сервера | `example.com` |
| `SERVER_USER` | Пользователь сервера | `deploy` |
| `SERVER_PATH` | Путь на сервере | `/var/www/soyzchernobilkurgan.local` |
| `FTP_SERVER` | FTP сервер | `ftp.example.com` |
| `FTP_USERNAME` | FTP пользователь | `ftp_user` |
| `FTP_PASSWORD` | FTP пароль | `ftp_password` |

### Как создать секреты

1. Откройте ваш репозиторий на GitHub
2. Перейдите в **Settings** → **Secrets and variables** → **Actions**
3. Нажмите **New repository secret**
4. Введите имя и значение
5. Нажмите **Add secret**

---

## 🗄️ Настройка базы данных для тестов

### Вариант 1: GitHub Actions (автоматически)

В CI/CD конфигурации уже настроен MySQL сервис. Ничего делать не нужно.

### Вариант 2: Локальная разработка

Создайте тестовую базу данных:

```sql
CREATE DATABASE test_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'test_user'@'localhost' IDENTIFIED BY 'test_password';
GRANT ALL PRIVILEGES ON test_db.* TO 'test_user'@'localhost';
FLUSH PRIVILEGES;
```

Проверьте файл `config/test_db.php`:

```php
<?php
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=127.0.0.1;port=3306;dbname=test_db',
    'username' => 'test_user',
    'password' => 'test_password',
    'charset' => 'utf8mb4',
];
```

---

## 🚀 Настройка деплоя

### Вариант 1: SSH деплой (рекомендуется)

#### 1. Создайте SSH ключ

```bash
ssh-keygen -t ed25519 -C "github-actions-deploy" -f ~/.ssh/github_actions_deploy
```

#### 2. Добавьте публичный ключ на сервер

```bash
cat ~/.ssh/github_actions_deploy.pub | ssh user@server "mkdir -p ~/.ssh && cat >> ~/.ssh/authorized_keys"
```

#### 3. Добавьте приватный ключ в секреты GitHub

```bash
cat ~/.ssh/github_actions_deploy | gh secret set SSH_PRIVATE_KEY
```

Или вручную через веб-интерфейс GitHub.

#### 4. Раскомментируйте шаг деплоя в `.github/workflows/ci-cd.yml`

Найдите и раскомментируйте:

```yaml
- name: Deploy to server via SSH
  uses: easingthemes/ssh-deploy@v4
  with:
    SSH_PRIVATE_KEY: ${{ secrets.SSH_PRIVATE_KEY }}
    REMOTE_HOST: ${{ secrets.SERVER_HOST }}
    REMOTE_USER: ${{ secrets.SERVER_USER }}
    TARGET: ${{ secrets.SERVER_PATH }}
    EXCLUDE: "/.git/, /tests/, /docs/, /vagrant/"
    ARGS: "-rltgoDzvO"
```

### Вариант 2: FTP деплой

#### 1. Добавьте секреты FTP

- `FTP_SERVER` - адрес FTP сервера
- `FTP_USERNAME` - имя пользователя
- `FTP_PASSWORD` - пароль

#### 2. Раскомментируйте шаг FTP деплоя

Найдите и раскомментируйте в `.github/workflows/ci-cd.yml`:

```yaml
- name: Deploy via FTP
  uses: SamKirkland/FTP-Deploy-Action@v4.3.4
  with:
    server: ${{ secrets.FTP_SERVER }}
    username: ${{ secrets.FTP_USERNAME }}
    password: ${{ secrets.FTP_PASSWORD }}
```

### Вариант 3: Ручная загрузка артефакта

1. После успешного workflow скачайте артефакт `deploy-package`
2. Распакуйте на сервере:

```bash
tar -xzf deploy-<sha>.tar.gz -C /var/www/soyzchernobilkurgan.local
```

---

## 📢 Настройка уведомлений

### Telegram уведомления

#### 1. Создайте бота

1. Откройте [@BotFather](https://t.me/BotFather)
2. Отправьте `/newbot`
3. Следуйте инструкциям
4. Скопируйте токен

#### 2. Узнайте Chat ID

1. Добавьте бота в чат/канал
2. Отправьте сообщение в чат
3. Откройте: `https://api.telegram.org/bot<YOUR_TOKEN>/getUpdates`
4. Найдите `chat.id` в ответе

#### 3. Добавьте секреты

- `TELEGRAM_BOT_TOKEN` - токен бота
- `TELEGRAM_CHAT_ID` - ID чата

---

## ▶️ Запуск workflow

### Автоматический запуск

Workflow запускается автоматически при:

- Push в ветки `main`, `master`, `develop`
- Pull Request в эти ветки
- По расписанию (каждый день в 3:00 UTC)

### Ручной запуск

1. Перейдите в **Actions** → **Yii2 CI/CD**
2. Нажмите **Run workflow**
3. Выберите ветку
4. Нажмите **Run workflow**

### Проверка статуса

- 🟡 Жёлтый - выполняется
- 🟢 Зелёный - успешно
- 🔴 Красный - ошибка

---

## 🐛 Устранение проблем

### Ошибка: "MySQL connection failed"

**Решение:**
1. Проверьте секреты `DB_*`
2. Убедитесь, что MySQL сервис запускается
3. Проверьте логи workflow

### Ошибка: "Composer install failed"

**Решение:**
```yaml
# Добавьте в workflow перед composer install
- name: Update composer
  run: composer self-update
```

### Ошибка: "Permission denied"

**Решение:**
```bash
# На сервере
chmod -R 0777 runtime
chmod -R 0777 web/assets
chown -R www-data:www-data /var/www/soyzchernobilkurgan.local
```

### Ошибка: "PHPStan analysis failed"

**Решение:**
1. Проверьте `phpstan.neon`
2. Исправьте ошибки в коде
3. Или добавьте исключения в `ignoreErrors`

### Ошибка: "Tests failed"

**Решение:**
1. Скачайте артефакт с отчётами
2. Откройте `tests/_output/`
3. Проверьте логи тестов

---

## 📊 Мониторинг и отчёты

### Просмотр логов

1. Откройте вкладку **Actions**
2. Выберите запуск workflow
3. Кликните на задание для просмотра логов

### Тестовые отчёты

Отчёты о тестах сохраняются в артефактах:
- `unit-test-results`
- `functional-test-results`

### Покрытость кода

Для включения покрытости раскомментируйте в `codeception.yml`:

```yaml
coverage:
    enabled: true
    whitelist:
        include:
            - models/*
            - controllers/*
            - commands/*
            - mail/*
            - jobs/*
```

---

## ⚙️ Дополнительная настройка

### Изменение PHP версии

Отредактируйте в `.github/workflows/ci-cd.yml`:

```yaml
env:
  PHP_VERSION: '8.2'  # Или другая версия
```

### Добавление тестовых окружений

Создайте матрицу тестирования:

```yaml
strategy:
  matrix:
    php-version: ['8.0', '8.1', '8.2']
    mysql-version: ['5.7', '8.0']
```

### Кэширование Composer

Добавьте шаг кэширования:

```yaml
- name: Cache Composer packages
  uses: actions/cache@v4
  with:
    path: vendor
    key: ${{ runner.os }}-php-${{ env.PHP_VERSION }}-composer-${{ hashFiles('composer.lock') }}
```

---

## 📞 Поддержка

При возникновении проблем:

1. Проверьте [документацию GitHub Actions](https://docs.github.com/en/actions)
2. Посмотрите логи workflow
3. Проверьте секреты и переменные окружения
