# Warehouse Stock Management System

## Prerequisites

- PHP 8.2+
- Composer
- Node.js 18+
- npm

## Installation

```bash
composer install
cd frontend && npm install && cd ..
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
```

## Running the Application

Start both servers in separate terminals:

```bash
# Terminal 1 - Backend API
php artisan serve

# Terminal 2 - Frontend
cd frontend
npm run dev
```

Access the application at http://localhost:5173

## Running Tests

```bash
php artisan test
```
