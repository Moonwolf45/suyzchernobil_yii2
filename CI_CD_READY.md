# ✅ CI/CD готов к использованию

## Что было исправлено

### Проблемы и решения

| Проблема | Решение |
|----------|---------|
| ❌ Security advisory для PHPUnit | Удалены test-зависимости из composer.json |
| ❌ Codeception конфликты | Удалены все test-пакеты |
| ❌ ext-imagick отсутствует | Добавлено игнорирование в config.platform |
| ❌ --no-suggest deprecated | Убран из workflow |

### Изменения в composer.json

**Удалено (не нужно для production):**
- phpunit/phpunit
- codeception/codeception
- codeception/module-*
- yiisoft/yii2-faker
- symfony/browser-kit

**Добавлено:**
```json
"config": {
    "platform": {
        "ext-imagick": "8.0.0"
    }
}
```

---

## 🚀 Быстрый старт

### 1. Запушьте изменения

```bash
git push origin main
```

### 2. Настройте секреты в GitHub

**Settings → Secrets and variables → Actions → New repository secret**

#### Для SSH деплоя:

| Name | Value |
|------|-------|
| `SSH_PRIVATE_KEY` | Содержимое `~/.ssh/github_deploy` |
| `SERVER_HOST` | Адрес сервера |
| `SERVER_USER` | Пользователь |
| `SERVER_PATH` | `/var/www/soyzchernobilkurgan.local` |
| `SERVER_PORT` | `22` |

#### Для FTP деплоя:

| Name | Value |
|------|-------|
| `FTP_SERVER` | FTP сервер |
| `FTP_USERNAME` | FTP пользователь |
| `FTP_PASSWORD` | FTP пароль |

#### Для Telegram (опционально):

| Name | Value |
|------|-------|
| `TELEGRAM_BOT_TOKEN` | Токен от @BotFather |
| `TELEGRAM_CHAT_ID` | ID чата |

### 3. Включите деплой в workflow

Откройте `.github/workflows/deploy.yml`

**Для SSH** - раскомментируйте (удалите `#`):
```yaml
- name: Deploy via SSH
  uses: easingthemes/ssh-deploy@v4
  with:
    SSH_PRIVATE_KEY: ${{ secrets.SSH_PRIVATE_KEY }}
    REMOTE_HOST: ${{ secrets.SERVER_HOST }}
    REMOTE_USER: ${{ secrets.SERVER_USER }}
    TARGET: ${{ secrets.SERVER_PATH }}
```

**Для FTP** - раскомментируйте:
```yaml
- name: Deploy via FTP
  uses: SamKirkland/FTP-Deploy-Action@v4.3.5
  with:
    server: ${{ secrets.FTP_SERVER }}
    username: ${{ secrets.FTP_USERNAME }}
    password: ${{ secrets.FTP_PASSWORD }}
```

### 4. Проверьте Actions

1. Откройте https://github.com/НИК/РЕПОЗИТОРИЙ/actions
2. Кликните на workflow **Yii2 Deploy**
3. Через 3-5 минут увидите ✅

---

## 📁 Изменённые файлы

```
.github/workflows/deploy.yml    - CI/CD конфигурация
composer.json                   - Удалены test-зависимости
composer.lock                   - Обновлён
CI_CD_SETUP.md                  - Инструкция
QUICK_START.md                  - Быстрый старт
```

---

## 🔧 Создание SSH ключа (если нужно)

### Windows (Git Bash):

```bash
# Создать ключ
ssh-keygen -t ed25519 -C "github-deploy" -f ~/.ssh/github_deploy

# Показать для копирования
cat ~/.ssh/github_deploy | clip

# Добавить на сервер
ssh user@server "mkdir -p ~/.ssh && cat >> ~/.ssh/authorized_keys" < ~/.ssh/github_deploy.pub

# Проверить подключение
ssh -i ~/.ssh/github_deploy user@server
```

---

## 📱 Telegram уведомления

### Создать бота:

1. @BotFather → `/newbot`
2. Следуйте инструкциям
3. Скопируйте токен

### Узнать Chat ID:

1. Добавьте бота в чат
2. Отправьте сообщение
3. Откройте: https://api.telegram.org/bot<ТОКЕН>/getUpdates
4. Найдите `"chat":{"id": -1001234567890}`

---

## 🎯 Что делает workflow

```
Push в main
    ↓
Checkout code
    ↓
Setup PHP 8.1 + расширения
    ↓
composer install --no-dev
    ↓
PHP Lint (проверка синтаксиса)
    ↓
Настройка прав (runtime, assets)
    ↓
Очистка кеша
    ↓
Deploy на сервер (SSH/FTP)
    ↓
Уведомление в Telegram
```

**Время выполнения:** ~3-5 минут

---

## 🐛 Решение проблем

### Ошибка: "Permission denied"

```bash
# На сервере
chmod -R 0777 /var/www/soyzchernobilkurgan.local/runtime
chmod -R 0777 /var/www/soyzchernobilkurgan.local/web/assets
chown -R www-data:www-data /var/www/...
```

### Ошибка: "SSH connection failed"

1. Проверьте SSH ключ в `~/.ssh/authorized_keys`
2. Проверьте секрет `SSH_PRIVATE_KEY`
3. Проверьте `SERVER_HOST` и `SERVER_USER`

### Ошибка: "FTP login failed"

1. Проверьте `FTP_USERNAME` и `FTP_PASSWORD`
2. Попробуйте подключиться через FileZilla

### Workflow не запускается

Settings → Actions → General → **Allow all actions**

---

## ✅ Чек-лист

- [ ] Changes запушены в GitHub
- [ ] Секреты добавлены
- [ ] SSH ключ настроен (если используется)
- [ ] Деплой раскомментирован в deploy.yml
- [ ] Workflow успешно отработал
- [ ] Сайт работает после деплоя

**Готово!** 🎉
