
# Laravel News Aggregator (Backend Case Study)

This is a Laravel 12 backend service that aggregates news articles from multiple external news providers, normalizes the data, stores articles locally in a database, and exposes clean REST API endpoints for filtered and searchable access.

This implementation follows **SOLID**, **DRY**, and **KISS** principles and meets the requirements of the backend news aggregation challenge.

---

## This project fetches news articles from three providers:

- The Guardian API
- NewsAPI.org
- New York Times Top Stories API

Articles are normalized, de-duplicated, stored in MySQL, and exposed through a clean REST API.


## 🧰 Tech Stack

| Component     | Used |
|--------------|------|
| PHP Version   | **>= 8.2** (required for Laravel 12) |
| Framework     | **Laravel 12.x** |
| Database      | **MySQL 8+** (or MariaDB equivalent) |
| Scheduler     | Laravel Task Scheduler + Cron (or Windows Task Scheduler) |
| API Clients   | Native Laravel `Http::` Client |

---

## 📦 Installation & Setup 

```bash
git clone <repository-url>
cd news-aggregator

composer install

cp .env.example .env
php artisan key:generate
```

## Update the following value of keys to the created .env:
```bash
DB_DATABASE=news_aggregator
DB_USERNAME=root
DB_PASSWORD=your_password_here

NEWSAPI_KEY=your_newsapi_key
GUARDIAN_KEY=your_guardian_key
NYT_KEY=your_nyt_key
```

## Run Migrations command
```
php artisan migrate
```

## Run Application command
```
php artisan serve
```

## 🔄 Fetching News (Manual Runs)
```
### fetch ALL providers (Guardian + NewsAPI + NYT)
php artisan news:fetch

### To fetch only one provider
php artisan news:fetch guardian
php artisan news:fetch newsapi
php artisan news:fetch nyt
```

## 🎯 API Endpoints

```
GET /api/articles                                             (Articles - List (page-based))
GET /api/articles?source=guardian                             (Articles - Source filter, eg. guardian, newsapi, nyt)
GET /api/articles?category=business                           (Articles - Category filter)
GET /api/articles?search=trump                                (Articles - Search term)
GET /api/articles?date=2025-11-09                             (Articles - Date filter (YYYY-MM-DD))
GET /api/articles?limit=20&offset=0                           (Articles - Limit/Offset)
GET /api/articles?source=nyt&search=trump&limit=50&offset=0   (For: Articles - Combined (source + search + limit/offset))
```

## Scheduler

To enable automatic updates:
```
php artisan schedule:run
```

# Add to system cron:
## for Linux/macOS crontab
```
* * * * * php /path/to/your/project/artisan schedule:run >> /dev/null 2>&11
```
## for Windows Task Scheduler
```
Program: php

Arguments: artisan schedule:run

Start in: E:\xampp\htdocs\news-aggregator

Trigger: every 1 minute
```
## 🧽 Useful Maintenance Commands
```
php artisan optimize:clear
php artisan cache:clear
php artisan config:clear
```

## 👨‍💻 Author

### Amit Kumar
Backend Developer — Laravel | API Architecture | System Design