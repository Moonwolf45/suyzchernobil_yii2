# ⚠️ Исправление проблемы с удалением файлов

## Проблема

При деплое **удалялись все файлы** на сервере, которых нет в репозитории.

### Причина

В rsync использовался флаг `--delete`, который **синхронизирует** директорию на сервере с локальной, удаляя всё лишнее.

```yaml
# ❌ БЫЛО (удаляло файлы на сервере)
switches: -avzr --delete --exclude=...
```

---

## ✅ Решение

Убран флаг `--delete` и добавлены исключения для важных директорий:

```yaml
# ✅ СТАЛО (только добавление/обновление файлов)
switches: -avzr \
  --exclude='.git' \
  --exclude='tests' \
  --exclude='docs' \
  --exclude='vagrant' \
  --exclude='.env.example' \
  --exclude='.github' \
  --exclude='runtime/' \
  --exclude='web/assets/' \
  --exclude='.env' \
  --exclude='composer.lock'
```

### Исключения

| Директория | Почему исключаем |
|------------|------------------|
| `runtime/` | Кеш, логи, временные файлы |
| `web/assets/` | Скомпилированные ассеты |
| `.env` | Локальные настройки (пароли, API ключи) |
| `.github/` | Конфигурация GitHub Actions |
| `composer.lock` | Может отличаться для разных окружений |

---

## 🔧 Что делать, если файлы уже удалены

### 1. Восстановите `.env` файл

Создайте на сервере файл `.env` с вашими настройками:

```bash
# На сервере
cd /var/www/soyzchernobilkurgan.local
nano .env
```

Пример содержимого:
```env
DB_DSN=mysql:host=localhost;dbname=soyzchernobilkurgan_local
DB_USERNAME=root
DB_PASSWORD=ваш_пароль
```

### 2. Восстановите права доступа

```bash
chmod -R 0777 /var/www/soyzchernobilkurgan.local/runtime
chmod -R 0777 /var/www/soyzchernobilkurgan.local/web/assets
```

### 3. Проверьте базу данных

Убедитесь, что БД существует и доступна:

```bash
mysql -u root -p -e "SHOW DATABASES LIKE 'soyzchernobilkurgan_local';"
```

### 4. Примените миграции

```bash
cd /var/www/soyzchernobilkurgan.local
php yii migrate --interactive=0
```

---

## 📋 Проверка перед следующим деплоем

### 1. Проверьте workflow

Убедитесь, что в `.github/workflows/deploy.yml` **НЕТ** флага `--delete`:

```yaml
# ✅ Правильно
switches: -avzr --exclude='...'

# ❌ Неправильно
switches: -avzr --delete --exclude='...'
```

### 2. Проверьте исключения

Убедитесь, что исключены:
- `runtime/`
- `web/assets/`
- `.env`
- `.github/`

### 3. Создайте `.env` на сервере

Файл `.env` должен существовать на сервере **до** деплоя.

---

## 🚀 Альтернативный вариант деплоя

Если хотите больше контроля, используйте **ручной скрипт**:

```yaml
- name: Deploy via SSH with script
  uses: easingthemes/ssh-deploy@v4
  with:
    SSH_PRIVATE_KEY: ${{ secrets.SSH_PRIVATE_KEY }}
    REMOTE_HOST: ${{ secrets.SERVER_HOST }}
    REMOTE_USER: ${{ secrets.SERVER_USER }}
    TARGET: ${{ secrets.SERVER_PATH }}
    EXCLUDE: "/.git/, /tests/, /docs/, /vagrant/, /.env.example, /.github/, /runtime/, /web/assets/, /.env"
    ARGS: "-rltgoDzvO"
    SCRIPT_AFTER: |
      chmod -R 0777 ${{ secrets.SERVER_PATH }}/runtime
      chmod -R 0777 ${{ secrets.SERVER_PATH }}/web/assets
      cd ${{ secrets.SERVER_PATH }} && php yii migrate --interactive=0
```

---

## 📊 Сравнение подходов

| Подход | Преимущества | Недостатки |
|--------|--------------|------------|
| **Без --delete** (текущий) | Не удаляет файлы на сервере | Старые файлы могут накапливаться |
| **С --delete** | Полная синхронизация | Удаляет всё, включая `.env`, кеш и т.д. |
| **С --delete-excluded** | Удаляет только то, что в исключениях | Требует точной настройки исключений |

---

## ✅ Чек-лист безопасности

- [ ] Убран флаг `--delete` из workflow
- [ ] Добавлены все необходимые исключения
- [ ] На сервере создан файл `.env`
- [ ] Права на `runtime/` и `web/assets/` установлены в 0777
- [ ] База данных существует и доступна
- [ ] Миграции применены
- [ ] Тестовый деплой прошёл успешно

---

## 🆘 Если что-то не работает

### Проверьте логи деплоя

1. Откройте https://github.com/НИК/РЕПОЗИТОРИЙ/actions
2. Кликните на последний запуск
3. Разверните шаг **Deploy via SSH/rsync**
4. Проверьте, какие файлы передаются

### Проверьте файлы на сервере

```bash
# Зайдите на сервер
ssh user@server

# Перейдите в директорию сайта
cd /var/www/soyzchernobilkurgan.local

# Проверьте наличие файлов
ls -la

# Проверьте права
ls -ld runtime web/assets
```

### Проверьте логи приложения

```bash
# На сервере
tail -f /var/www/soyzchernobilkurgan.local/runtime/logs/app.log
```

---

## 📞 Поддержка

Если проблема не решена:

1. Откройте issue на GitHub
2. Приложите логи деплоя
3. Приложите вывод команды `ls -la` с сервера
