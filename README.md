# Kutalo Links

Сервис коротких ссылок: Laravel 12 (DDD) + Angular 22 (Material).

- Продакшн: `https://l.kutalo.com`
- Локально: `http://l.kutalo.test`
- База: MySQL `kutalo_links`

## Стек

| Компонент | Версия |
|-----------|--------|
| Laravel | 12.x |
| PHP | 8.4 (Docker) |
| Angular | 22.x |
| MySQL | 8.0 (`kutalo-mysql`) |
| Auth | Laravel Sanctum (Bearer token) |

## Быстрый старт (локально)

1. Запустите инфраструктуру kutalo (Traefik, MySQL, Redis):

```bash
cd kutalo/infra
bash docs/switch-to-local.sh
```

2. Добавьте в hosts: `127.0.0.3 l.kutalo.test`

3. Настройте сервис:

```bash
cd kutalo/links
cp backend/.env.example backend/.env
# заполните DB_PASSWORD и REDIS_PASSWORD из mysql/.env и infra/.env
docker compose -f docker-compose.yml -f docker-compose.local.yml up -d --build
docker compose exec links-php php artisan key:generate
docker compose exec links-php php artisan migrate --seed
```

`--build` собирает PHP-образ и nginx-образ. Nginx-образ внутри себя выполняет `npm run build` и кладет SPA в `public/spa/browser`.

4. Откройте `http://l.kutalo.test` и войдите:
   - email: `ADMIN_EMAIL` из `backend/.env` (по умолчанию `admin@kutalo.test`)
   - пароль: `ADMIN_PASSWORD` из `backend/.env`

## API

| Метод | Путь | Описание |
|-------|------|----------|
| GET | `/api/health` | Healthcheck |
| POST | `/api/login` | Авторизация |
| GET/POST | `/api/links` | Список / создание |
| PATCH | `/api/links/{id}/destination` | Смена URL назначения |
| GET | `/api/links/{id}/statistics` | Статистика переходов |
| GET | `/{slug}` | Редирект 302 + запись клика |

## Разработка frontend

```bash
cd links
bash build-frontend.sh
```

Или с hot-reload (нужен запущенный backend):

```bash
cd frontend
npm start
```

Proxy: `proxy.conf.json` → `http://l.kutalo.test/api`

## Продакшн

```bash
cd kutalo/infra
bash docs/switch-to-production.sh
```

Скрипт поднимет links с overlay `docker-compose.production.yml` (TLS через Traefik).
Код и `vendor` берутся из Docker-образа; с хоста монтируются только `.env`, `storage` и `bootstrap/cache`.
Frontend собирается внутри nginx-образа при `docker compose ... up --build`.

Первый запуск на сервере (если ещё не делали):

```bash
cd kutalo/links
cp backend/.env.example backend/.env
# заполните секреты и APP_ENV=production
docker compose -f docker-compose.yml -f docker-compose.production.yml up -d --build
docker compose exec links-php php artisan key:generate
docker compose exec links-php php artisan migrate --seed
```

## Архитектура backend (DDD)

- `app/Domain/Link/` — сущности, интерфейсы репозиториев, валидация URL
- `app/Application/Link/` — use cases (handlers)
- `app/Infrastructure/Link/` — Eloquent, мапперы
- `app/Http/` — controllers, requests, resources

## Безопасность

- Редирект только на `http://` и `https://` URL
- Admin API защищён Sanctum
- В статистике сохраняются IP, User-Agent и Referer
