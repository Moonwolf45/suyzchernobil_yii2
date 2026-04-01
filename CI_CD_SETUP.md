# 🚀 CI/CD Настройка - Пошаговая инструкция

## Что будет делать CI/CD

```
Push в main/master → Проверка PHP → Сборка → Деплой на сервер → Уведомление в Telegram
```

**Время работы:** ~3-5 минут

---

## 📋 Шаг 1: Создайте репозиторий на GitHub

Если ещё не создан:

```bash
# В папке проекта
git init
git add .
git commit -m "Initial commit"
```

Создайте пустой репозиторий на GitHub, затем:

```bash
git remote add origin https://github.com/ВАШ_НИК/ВАШ_РЕПОЗИТОРИЙ.git
git branch -M main
git push -u origin main
```

---

## 📋 Шаг 2: Настройте секреты в GitHub

### Откройте настройки репозитория

1. Зайдите в ваш репозиторий на GitHub
2. Перейдите во вкладку **Settings**
3. В меню слева выберите **Secrets and variables** → **Actions**
4. Нажмите кнопку **New repository secret**

### Добавьте секреты для деплоя

#### Вариант А: SSH (рекомендуется)

| Имя секрета | Значение | Пример |
|-------------|----------|--------|
| `SSH_PRIVATE_KEY` | Приватный SSH ключ | `-----BEGIN OPENSSH PRIVATE KEY-----...` |
| `SERVER_HOST` | Адрес сервера | `soyzchernobilkurgan.local` или IP |
| `SERVER_USER` | Пользователь | `root` или `deploy` |
| `SERVER_PATH` | Путь к сайту | `/var/www/soyzchernobilkurgan.local` |
| `SERVER_PORT` | SSH порт | `22` |

#### Вариант Б: FTP

| Имя секрета | Значение | Пример |
|-------------|----------|--------|
| `FTP_SERVER` | FTP сервер | `soyzchernobilkurgan.local` |
| `FTP_USERNAME` | FTP пользователь | `soyzchernobilkurgan.local` |
| `FTP_PASSWORD` | FTP пароль | `ваш_пароль` |
| `FTP_PORT` | FTP порт | `21` |

#### Уведомления (опционально)

| Имя секрета | Значение | Как получить |
|-------------|----------|--------------|
| `TELEGRAM_BOT_TOKEN` | Токен бота | [@BotFather](https://t.me/BotFather) → `/newbot` |
| `TELEGRAM_CHAT_ID` | ID чата | Добавить бота в чат → написать сообщение → https://api.telegram.org/bot<TOKEN>/getUpdates |

---

## 📋 Шаг 3: Настройте SSH ключ (если используете SSH)

### Создайте SSH ключ на компьютере

```bash
ssh-keygen -t ed25519 -C "github-deploy" -f ~/.ssh/github_deploy
```

Нажимайте Enter, оставляя пароль пустым.

### Добавьте ключ на сервер

**Для Linux/Mac:**
```bash
ssh-copy-id -i ~/.ssh/github_deploy.pub пользователь@сервер
```

**Для Windows (PowerShell):**
```bash
# Скопируйте содержимое ключа
cat ~/.ssh/github_deploy.pub

# Зайдите на сервер вручную
ssh пользователь@сервер

# Вставьте ключ в файл
mkdir -p ~/.ssh
notepad ~/.ssh/authorized_keys

# Вставьте содержимое github_deploy.pub в конец файла
```

### Проверьте подключение

```bash
ssh -i ~/.ssh/github_deploy пользователь@сервер
```

### Добавьте ключ в секреты GitHub

**Windows:**
```bash
cat ~/.ssh/github_deploy | clip
```

Вставьте содержимое в секрет `SSH_PRIVATE_KEY` на GitHub.

---

## 📋 Шаг 4: Выберите способ деплоя

Откройте файл `.github/workflows/deploy.yml`

### Для SSH (рекомендуется)

Найдите и **раскомментируйте** блок SSH (удалите `#` в начале строк):

```yaml
# ❌ БЫЛО (закомментировано)
# - name: Deploy via SSH
#   uses: easingthemes/ssh-deploy@v4
#   with:
#     SSH_PRIVATE_KEY: ${{ secrets.SSH_PRIVATE_KEY }}
#     ...

# ✅ СТАЛО (раскомментировано)
- name: Deploy via SSH
  uses: easingthemes/ssh-deploy@v4
  with:
    SSH_PRIVATE_KEY: ${{ secrets.SSH_PRIVATE_KEY }}
    ...
```

### Для FTP

Найдите и **раскомментируйте** блок FTP:

```yaml
# Раскомментируйте этот блок для FTP
- name: Deploy via FTP
  uses: SamKirkland/FTP-Deploy-Action@v4.3.5
  with:
    server: ${{ secrets.FTP_SERVER }}
    username: ${{ secrets.FTP_USERNAME }}
    password: ${{ secrets.FTP_PASSWORD }}
    ...
```

---

## 📋 Шаг 5: Запушьте изменения

```bash
git add .
git commit -m "Add GitHub Actions CI/CD"
git push origin main
```

---

## 📋 Шаг 6: Проверьте работу

### Откройте GitHub Actions

1. Зайдите в репозиторий на GitHub
2. Перейдите во вкладку **Actions**
3. Кликните на запущенный workflow **Yii2 Deploy**

### Что должно произойти

```
✅ Checkout code         — скачивание кода
✅ Setup PHP             — установка PHP 8.1
✅ Install dependencies  — composer install
✅ PHP Lint              — проверка синтаксиса
✅ Setup permissions     — права на папки
✅ Deploy                — деплой на сервер
✅ Telegram notification — уведомление
```

### Если всё успешно

Вы получите сообщение в Telegram:
```
✅ Деплой успешен!
📦 Commit: abc1234
👤 Автор: ваш_ник
```

---

## 📋 Шаг 7: Проверьте сайт

Откройте ваш сайт и проверьте:

1. ✅ Сайт открывается
2. ✅ В папке `runtime/` можно писать файлы
3. ✅ В папке `web/assets/` создаются файлы
4. ✅ Миграции применены (если добавили в workflow)

---

## 🐛 Если что-то пошло не так

### Ошибка: "Permission denied"

**Проблема:** Недостаточно прав на сервере

**Решение:**
```bash
# На сервере
chmod -R 0777 /var/www/soyzchernobilkurgan.local/runtime
chmod -R 0777 /var/www/soyzchernobilkurgan.local/web/assets
chown -R www-data:www-data /var/www/soyzchernobilkurgan.local
```

### Ошибка: "SSH connection failed"

**Проблема:** Неверный SSH ключ или доступы

**Решение:**
1. Проверьте, что ключ добавлен в `~/.ssh/authorized_keys` на сервере
2. Проверьте секрет `SSH_PRIVATE_KEY` (должен начинаться с `-----BEGIN`)
3. Проверьте `SERVER_HOST` и `SERVER_USER`

### Ошибка: "FTP login failed"

**Проблема:** Неверный логин/пароль FTP

**Решение:**
1. Проверьте секреты `FTP_USERNAME` и `FTP_PASSWORD`
2. Попробуйте подключиться через FileZilla

### Workflow не запускается

**Проблема:** Actions отключены

**Решение:**
1. Settings → Actions → General
2. Включите **Allow all actions**

---

## 📝 Шпаргалка команд

### Добавить новый секрет

```bash
# Через GitHub CLI
gh secret set SECRET_NAME --body "значение"
```

### Запустить workflow вручную

1. Actions → Yii2 Deploy
2. Кнопка **Run workflow**
3. Выберите ветку
4. **Run workflow**

### Посмотреть логи

1. Actions → выберите запуск
2. Кликните на шаг для просмотра логов

---

## ✅ Чек-лист готовности

- [ ] Создан репозиторий на GitHub
- [ ] Добавлен файл `.github/workflows/deploy.yml`
- [ ] Добавлены секреты в GitHub
- [ ] Настроен SSH ключ (если используется SSH)
- [ ] Раскомментирован нужный блок в deploy.yml
- [ ] Запушены изменения
- [ ] Workflow успешно отработал
- [ ] Сайт работает после деплоя

---

## 📞 Нужна помощь?

1. Проверьте логи в Actions
2. Убедитесь, что все секреты заполнены
3. Проверьте права на сервере
