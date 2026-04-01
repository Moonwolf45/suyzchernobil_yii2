# 🔧 Решение ошибки rsync (code 23)

## Ошибка

```
widgets/views/videoNewsWidget.php
rsync error: some files/attrs were not transferred (see previous errors) (code 23)
```

## Причина

**rsync (code 23)** означает, что некоторые файлы или атрибуты **не удалось передать** из-за:

1. ❌ Отсутствуют директории на сервере
2. ❌ Недостаточно прав для записи
3. ❌ Владелец файлов не совпадает

---

## ✅ Решение

### 1️⃣ Добавьте секрет SERVER_PORT

В GitHub: **Settings → Secrets and variables → Actions → New repository secret**

| Name | Value |
|------|-------|
| `SERVER_PORT` | `22` (или ваш порт SSH) |

### 2️⃣ Создайте директории на сервере вручную

```bash
# Зайдите на сервер
ssh user@your-server.com

# Перейдите в директорию сайта
cd /var/www/soyzchernobilkurgan.local

# Создайте директории
mkdir -p runtime/logs runtime/cache web/assets

# Установите права
chmod -R 0777 runtime
chmod -R 0777 web/assets

# Проверьте владельца
chown -R www-data:www-data /var/www/soyzchernobilkurgan.local
```

### 3️⃣ Проверьте права на родительские директории

```bash
# Проверка
ls -la /var/www/

# Если нужно, исправьте
chmod 755 /var/www/soyzchernobilkurgan.local
```

---

## 🚀 Обновлённый workflow

Теперь workflow **автоматически создаёт директории** перед деплоем:

```yaml
# Шаг 1: Подготовка директорий
- name: Prepare remote directories
  uses: burnett01/rsync-deployments@7.0.1
  with:
    switches: -avz --include='runtime/' --include='runtime/logs/' --include='runtime/cache/' --include='web/assets/' --exclude='*'
    # ...остальные параметры

# Шаг 2: Основной деплой
- name: Deploy via SSH/rsync
  uses: burnett01/rsync-deployments@7.0.1
  with:
    switches: -avz --exclude='.git/' --exclude='tests/' --exclude='docs/' ...
    remote_port: ${{ secrets.SERVER_PORT }}  # ← Добавлен порт
```

---

## 📋 Проверка после исправления

### 1. Запушьте изменения

```bash
git add .
git commit -m "Fix rsync error code 23"
git push origin main
```

### 2. Проверьте Actions

https://github.com/НИК/РЕПОЗИТОРИЙ/actions

### 3. Проверьте файлы на сервере

```bash
# Зайдите на сервер
ssh user@your-server.com

# Перейдите в директорию
cd /var/www/soyzchernobilkurgan.local

# Проверьте, что файлы на месте
ls -la widgets/views/

# Проверьте права
ls -ld runtime web/assets
```

---

## 🐛 Другие возможные проблемы

### Ошибка: "Permission denied"

**Решение:**
```bash
# На сервере
chmod -R 0777 /var/www/soyzchernobilkurgan.local/runtime
chmod -R 0777 /var/www/soyzchernobilkurgan.local/web/assets
```

### Ошибка: "No such file or directory"

**Решение:**
```bash
# Создайте директории
mkdir -p /var/www/soyzchernobilkurgan.local/runtime/{logs,cache}
mkdir -p /var/www/soyzchernobilkurgan.local/web/assets
```

### Ошибка: "Owner mismatch"

**Решение:**
```bash
# Узнайте, под каким пользователем работает веб-сервер
ps aux | grep -E 'apache|nginx|httpd'

# Измените владельца (замените www-data на вашего)
chown -R www-data:www-data /var/www/soyzchernobilkurgan.local
```

---

## 📊 Диагностика

### Проверка логов rsync

```bash
# Запустите rsync вручную с подробными логами
rsync -avz --progress \
  -e "ssh -i ~/.ssh/your_key" \
  ./path/to/source/ \
  user@server:/var/www/soyzchernobilkurgan.local/
```

### Проверка SSH подключения

```bash
# Проверьте подключение
ssh -v -i ~/.ssh/your_key user@server

# Проверьте команду rsync на сервере
ssh user@server "which rsync"
```

---

## ✅ Чек-лист

- [ ] Добавлен секрет `SERVER_PORT` в GitHub
- [ ] Созданы директории `runtime/` и `web/assets/` на сервере
- [ ] Установлены права `0777` на директории
- [ ] Владелец файлов совпадает с веб-сервером
- [ ] Workflow обновлён и запушен
- [ ] Деплой прошёл без ошибок

---

## 🆘 Если не помогло

### 1. Проверьте версию rsync на сервере

```bash
# На сервере
rsync --version

# Должна быть 3.x
# Если старая - обновите
apt-get update && apt-get install rsync  # Debian/Ubuntu
yum install rsync  # CentOS
```

### 2. Включите подробное логирование

В workflow добавьте:

```yaml
- name: Debug rsync
  run: |
    rsync --version
    ssh -V
```

### 3. Проверьте свободное место

```bash
# На сервере
df -h
```

---

## 📞 Поддержка

Если проблема не решена:

1. Откройте **DEPLOY_FIX.md** для общей информации
2. Проверьте логи в GitHub Actions
3. Выполните команды диагностики выше
