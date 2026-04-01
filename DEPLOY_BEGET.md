# 🏗️ Деплой на BeGet (shared-хостинг)

## Проблема

```
rsync: failed to set times on "***/.": Operation not permitted (1)
rsync error: some files/attrs were not transferred (code 23)
```

**Причина:** На shared-хостинге **BeGet** ограничены права:
- ❌ Нельзя менять права (`chmod`)
- ❌ Нельзя менять владельца (`chown`)
- ❌ Нельзя менять времена файлов
- ❌ Ограниченный доступ к корневой директории

---

## ✅ Решение

### Обновлённые параметры rsync

```yaml
switches: -avzt \
  --no-times      # Не менять времена файлов
  --no-perms      # Не менять права
  --no-owner      # Не менять владельца
  --no-group      # Не менять группу
  --exclude=...   # Исключения
```

### Что делает каждый флаг

| Флаг | Описание |
|------|----------|
| `-a` | Архивный режим (рекурсивно + сжатие) |
| `-v` | Подробный вывод (verbose) |
| `-z` | Сжатие при передаче |
| `-t` | Сохранять времена (но мы отключаем) |
| `--no-times` | **Не трогать** времена файлов |
| `--no-perms` | **Не трогать** права файлов |
| `--no-owner` | **Не трогать** владельца |
| `--no-group` | **Не трогать** группу |

---

## 🔧 Настройка для BeGet

### 1️⃣ Структура директорий на BeGet

```
home/
└── username/          # Ваша домашняя директория
    └── www/
        └── soyzchernobilkurgan.local/   # Корень сайта
            ├── public_html/             # Web-доступная директория
            ├── app/
            ├── config/
            ├── runtime/                 # Создаётся автоматически
            └── ...
```

### 2️⃣ Настройте SERVER_PATH

В GitHub Secrets укажите **правильный путь**:

```
Name:  SERVER_PATH
Value: /home/username/www/soyzchernobilkurgan.local
```

**Где:**
- `username` — ваш логин на BeGet
- Путь начинается с `/home/`, а не с `/var/www/`

### 3️⃣ Создайте директории вручную

Через SSH подключитесь к BeGet:

```bash
ssh username@beget.com
```

Создайте необходимые директории:

```bash
cd /home/username/www/soyzchernobilkurgan.local

# Создайте директории
mkdir -p runtime/cache runtime/logs
mkdir -p web/assets

# Права менять НЕ нужно - BeGet сам установит правильные
```

### 4️⃣ Настройте public_html

На BeGet **public_html** — это корневая директория для веб-доступа:

```bash
# На BeGet
cd /home/username/www/soyzchernobilkurgan.local

# Проверьте, что public_html существует
ls -la public_html/

# Если нет - создайте
mkdir public_html
```

В workflow укажите, что файлы должны копироваться в `public_html`:

```yaml
- name: Deploy via SSH/rsync
  uses: burnett01/rsync-deployments@7.0.1
  with:
    remote_path: ${{ secrets.SERVER_PATH }}/public_html
    # ...остальные параметры
```

---

## 📁 Обновлённый workflow

```yaml
- name: Deploy via SSH/rsync
  uses: burnett01/rsync-deployments@7.0.1
  with:
    switches: -avzt --no-times --no-perms --no-owner --no-group \
      --exclude='.git/' \
      --exclude='tests/' \
      --exclude='docs/' \
      --exclude='vagrant/' \
      --exclude='.env.example' \
      --exclude='.github/' \
      --exclude='runtime/cache/' \
      --exclude='runtime/logs/' \
      --exclude='web/assets/' \
      --exclude='.env' \
      --exclude='composer.lock'
    path: ./
    remote_path: ${{ secrets.SERVER_PATH }}
    remote_host: ${{ secrets.SERVER_HOST }}
    remote_user: ${{ secrets.SERVER_USER }}
    remote_key: ${{ secrets.SSH_PRIVATE_KEY }}
    remote_port: ${{ secrets.SERVER_PORT }}
```

---

## 📋 Секреты для BeGet

| Secret | Значение | Пример |
|--------|----------|--------|
| `SSH_PRIVATE_KEY` | Приватный SSH ключ | `-----BEGIN OPENSSH PRIVATE KEY-----...` |
| `SERVER_HOST` | Хост BeGet | `beget.com` или `hulk.beget.com` |
| `SERVER_USER` | Ваш логин | `username` |
| `SERVER_PATH` | Путь к сайту | `/home/username/www/soyzchernobilkurgan.local` |
| `SERVER_PORT` | SSH порт | `22` |

---

## 🚀 Деплой

### 1. Запушьте изменения

```bash
git add .
git commit -m "Fix: Deploy to BeGet shared hosting"
git push origin main
```

### 2. Проверьте Actions

https://github.com/НИК/РЕПОЗИТОРИЙ/actions

### 3. Проверьте файлы на сервере

```bash
# Подключитесь к BeGet
ssh username@beget.com

# Перейдите в директорию
cd /home/username/www/soyzchernobilkurgan.local

# Проверьте файлы
ls -la
```

---

## 🐛 Решение проблем

### Ошибка: "failed to set times"

**Решение:** Флаг `--no-times` уже добавлен в workflow.

### Ошибка: "Operation not permitted"

**Решение:** Флаги `--no-perms --no-owner --no-group` уже добавлены.

### Ошибка: "No such file or directory"

**Решение:**
```bash
# Создайте директории вручную
ssh username@beget.com
cd /home/username/www/soyzchernobilkurgan.local
mkdir -p runtime/cache runtime/logs web/assets
```

### Файлы не видны в браузере

**Решение:** На BeGet файлы должны быть в `public_html`:

```yaml
# В workflow измените remote_path
remote_path: ${{ secrets.SERVER_PATH }}/public_html
```

Или сделайте симлинк:

```bash
# На BeGet
cd /home/username/www/soyzchernobilkurgan.local
ln -s public_html web
```

---

## 📊 Проверка после деплоя

### 1. Проверьте файлы

```bash
# На BeGet
cd /home/username/www/soyzchernobilkurgan.local

# Проверьте, что файлы на месте
ls -la app/ config/ public_html/
```

### 2. Проверьте права

```bash
# Файлы должны иметь права 644 или 664
ls -la public_html/index.php

# Директории должны иметь права 755 или 775
ls -ld public_html/
```

### 3. Проверьте сайт

Откройте в браузере: `https://soyzchernobilkurgan.local`

---

## ✅ Чек-лист

- [ ] Обновлён workflow с флагами `--no-times --no-perms --no-owner --no-group`
- [ ] Добавлен секрет `SERVER_PATH` с правильным путём `/home/username/...`
- [ ] Созданы директории `runtime/` и `web/assets/` на сервере
- [ ] Workflow запушен
- [ ] Деплой прошёл без ошибок
- [ ] Сайт открывается в браузере

---

## 🆘 Если не работает

### 1. Проверьте лог деплоя

В GitHub Actions откройте последний запуск и посмотрите вывод rsync.

### 2. Проверьте подключение

```bash
# Проверьте SSH подключение
ssh -v username@beget.com
```

### 3. Свяжитесь с поддержкой BeGet

Если проблема не решена, обратитесь в поддержку BeGet:
- Тикет: https://www.beget.com/support/ticket
- Чат: https://www.beget.com

---

## 📖 Полезные ссылки

- Документация BeGet: https://www.beget.com/ru/kb
- SSH доступ: https://www.beget.com/ru/kb/ssh
- Структура директорий: https://www.beget.com/ru/kb/filestructure
