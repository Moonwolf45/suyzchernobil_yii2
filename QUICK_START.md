# 🚀 БЫСТРЫЙ СТАРТ - 5 минут

## Команды для настройки

### 1️⃣ Инициализация Git (если не сделано)

```bash
cd D:\OSPanel\home\soyzchernobilkurgan.local
git init
git add .
git commit -m "Initial commit"
```

### 2️⃣ Создание репозитория на GitHub

1. Зайдите на https://github.com/new
2. Введите имя репозитория
3. Создайте **ПУСТОЙ** репозиторий (без README)
4. Скопируйте URL репозитория

### 3️⃣ Привязка к GitHub

```bash
git remote add origin https://github.com/ВАШ_НИК/ВАШ_РЕПОЗИТОРИЙ.git
git branch -M main
git push -u origin main
```

---

## 🔐 Настройка секретов (через браузер)

### Откройте:
https://github.com/ВАШ_НИК/ВАШ_РЕПОЗИТОРИЙ/settings/secrets/actions

### Добавьте секреты (кнопка "New repository secret"):

#### Для SSH деплоя:

| Name | Value |
|------|-------|
| `SSH_PRIVATE_KEY` | Содержимое файла `~/.ssh/github_deploy` (см. ниже) |
| `SERVER_HOST` | `soyzchernobilkurgan.local` или IP сервера |
| `SERVER_USER` | `root` или ваш пользователь |
| `SERVER_PATH` | `/var/www/soyzchernobilkurgan.local` |
| `SERVER_PORT` | `22` |

#### Для FTP деплоя:

| Name | Value |
|------|-------|
| `FTP_SERVER` | `soyzchernobilkurgan.local` |
| `FTP_USERNAME` | Ваш FTP пользователь |
| `FTP_PASSWORD` | Ваш FTP пароль |

#### Для Telegram (опционально):

| Name | Value |
|------|-------|
| `TELEGRAM_BOT_TOKEN` | Токен от @BotFather |
| `TELEGRAM_CHAT_ID` | ID вашего чата |

---

## 🔑 Создание SSH ключа (если нужно)

### Windows (Git Bash):

```bash
# Создать ключ
ssh-keygen -t ed25519 -C "github-deploy" -f ~/.ssh/github_deploy

# Показать содержимое (скопировать для GitHub)
cat ~/.ssh/github_deploy | clip

# Копировать на сервер (замените user@server)
ssh user@server "mkdir -p ~/.ssh && cat >> ~/.ssh/authorized_keys" < ~/.ssh/github_deploy.pub
```

### Проверка подключения:

```bash
ssh -i ~/.ssh/github_deploy user@server
```

---

## ⚙️ Настройка deploy.yml

### Откройте файл:
`.github/workflows/deploy.yml`

### Для SSH - раскомментируйте (удалите #):

```yaml
# Было:
# - name: Deploy via SSH
#   uses: easingthemes/ssh-deploy@v4

# Стало:
- name: Deploy via SSH
  uses: easingthemes/ssh-deploy@v4
```

### Для FTP - раскомментируйте:

```yaml
# Было:
# - name: Deploy via FTP
#   uses: SamKirkland/FTP-Deploy-Action@v4.3.5

# Стало:
- name: Deploy via FTP
  uses: SamKirkland/FTP-Deploy-Action@v4.3.5
```

---

## 🚀 Финальный push

```bash
git add .
git commit -m "Add CI/CD deploy workflow"
git push origin main
```

---

## ✅ Проверка

1. Откройте https://github.com/ВАШ_НИК/ВАШ_РЕПОЗИТОРИЙ/actions
2. Должен запуститься workflow **Yii2 Deploy**
3. Через 3-5 минут увидите ✅ или ❌

---

## 📱 Telegram уведомления (опционально)

### Создать бота:

1. Откройте @BotFather в Telegram
2. Отправьте `/newbot`
3. Введите имя бота
4. Скопируйте токен → секрет `TELEGRAM_BOT_TOKEN`

### Узнать Chat ID:

1. Добавьте бота в чат/канал
2. Отправьте любое сообщение
3. Откройте: https://api.telegram.org/bot<ВАШ_ТОКЕН>/getUpdates
4. Найдите `"chat":{"id": -1001234567890}` → секрет `TELEGRAM_CHAT_ID`

---

## 🎯 Готово!

Теперь при каждом push в ветку `main` будет автоматически:

1. ✅ Проверка синтаксиса PHP
2. ✅ Сборка проекта
3. ✅ Деплой на сервер
4. ✅ Уведомление в Telegram
