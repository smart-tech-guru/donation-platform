# 📦 Full Stack Laravel + Vite App

This project is a full stack web application built with **Laravel 12** as the backend and **Vite + Vue3** as the frontend. It includes database migrations, seeders, unit/static analysis testing, and hot-reloading development setup.

---

## 🛠 Installation Guide

### 🔧 Backend (Laravel)

1. **Navigate to backend directory:**

```bash
cd backend
```

2. **Install PHP dependencies:**
```bash
   composer install
```

3. **Create environment file:**

```bash
  cp .env.example .env
```

4. **Generate application key:**

```bash
php artisan key:generate
```

5. **Run database migrations:**

```bash
php artisan migrate
```

6. **Seed the database:**

```bash
php artisan db:seed
```

Seeder will create an admin user. the credentials for admin user is 
```
Email: admin@test.com
Pass: password
```

Note: Make sure your .env file contains valid database credentials.


### 🔧 Frontend (Vue3 + TypeScript + Vite)

1. **Navigate to frontend directory:**

```bash
cd frontend
```

2. **Copy the frontend environment file:**

```bash
cp .env.example .env
```

3. **Install Node.js dependencies:**

```bash
npm install
```

## 🚀 Running the Application

### Start Laravel backend server:
```bash
php artisan serve
```
Default URL: http://localhost:8000

### Start Vue3 frontend server:
```bash
npm run dev
```
Default URL: http://localhost:5173


## ✅ Running Laravel API Tests

### Reset Database
```bash
cd backend && php artisan test:setup
```

### Run unit and feature tests with Pest:
```bash
composer run pest
```

### Run static analysis with PHPStan:
```bash
composer run phpstan
```

### 📂 Project Structure Overview
```
├── backend/              # Laravel backend code
│   ├── app/              # Laravel application code
│   │   ├── Http/        # Controllers, middleware, etc.
│   │   ├── Models/      # Eloquent models
│   │   └── ...          # Other application code
│   ├── database/        # Database migrations and seeders
│   ├── tests/           # Pest tests
│   └── ...              # Other Laravel files
├── frontend/            # Vue3 frontend code
│   ├── src/             # Vue3 source code
│   ├── public/          # Public assets
│   └── ...              # Other Vue3 files
└── ...                  # Other project files
```
