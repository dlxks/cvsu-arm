# CVSU-ARM (Cavite State University - Academic Records Management)

A Laravel + Livewire application for managing academic records, built with Laravel 12, Livewire 4, Tailwind CSS, and Flux UI.

## Prerequisites

- PHP 8.2+
- Composer
- Node.js & npm
- SQLite (optional) or MySQL/PostgreSQL

## Installation

Follow these steps to set up the project locally:

### 1. Install PHP Dependencies

```
bash
composer install
```

### 2. Install Node.js Dependencies

```
bash
npm install
```

### 3. Build Frontend Assets

```
bash
npm run build
```

### 4. Generate Application Key

```
bash
php artisan key:generate
```

## Database Setup

### Option A: SQLite (Recommended for local development)

1. Create a SQLite database file in the database folder:

```
bash
touch database/database.sqlite
```

2. Update your `.env` file to use SQLite:

```
env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database/database.sqlite
```

### Option B: MySQL/PostgreSQL

1. Update your `.env` file with your database credentials:

```
env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cvsu_arm
DB_USERNAME=root
DB_PASSWORD=
```

2. Create the database:

```
bash
mysql -u root -p -e "CREATE DATABASE cvsu_arm;"
```

## Run Migrations and Seeders

```
bash
php artisan migrate:fresh --seed
```

## Publish Vendor Files

```
bash
php artisan vendor:publish --all
```

## Running the Application

Start the development server:

```
bash
php artisan serve
```

Then visit `http://localhost:8000` in your browser.

## Features

- User Authentication (Laravel Fortify)
- Role-based Access Control (Spatie Laravel Permission)
- Faculty Profile Management
- Data Tables (Livewire PowerGrid)
- Excel Export (Maatwebsite Excel)
- Modern UI with Flux Components

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
