# PHP Registration System

A secure user registration system built with PHP 8.2+ using Clean Architecture and DDD patterns.

**Features:** CSRF protection, bcrypt password hashing, fraud detection middleware, SMTP email notifications, responsive UI.

---

## Requirements

- **Docker Setup:** Docker & Docker Compose
- **Manual Setup:** PHP 8.2+, Composer, MySQL 8.0, `ext-mysqli`, `ext-pdo`

---

## Installation

### 1. Create `.env` File

```env
# Database (use 'db' for Docker, '127.0.0.1' for local)
DB_HOST=db
DB_USER=root
DB_PASSWORD=root
DB_DATABASE=my_db

# Mail (Mailtrap recommended for dev - https://mailtrap.io)
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=587
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@example.com
MAIL_FROM_NAME="Registration System"
MAIL_DEBUG=0
```

### 2. Create Database

Connect to MySQL and run:

```sql
CREATE DATABASE IF NOT EXISTS my_db;
USE my_db;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 3. Start the Application

**With Docker:**
```bash
cd docker
docker-compose up -d
docker exec -it app composer install
```

**Without Docker:**
```bash
composer install
php -S localhost:8000 -t public
```

### 4. Open in Browser

```
http://localhost:8000
```

---

## Routes

| Method | URL | Description |
|--------|-----|-------------|
| GET | `/registration` | Registration form |
| POST | `/register` | Submit registration |
| GET | `/dashboard` | User dashboard (auth required) |

---

## Stopping

```bash
# Docker
cd docker && docker-compose down

# Manual
Ctrl+C
```

---

## Troubleshooting

- **DB Connection Error:** Check `.env` credentials and that MySQL is running
- **Emails Not Sending:** Verify SMTP settings; set `MAIL_DEBUG=2` for logs
- **Docker MySQL:** Connect via `docker exec -it app-db mysql -u root -proot`
