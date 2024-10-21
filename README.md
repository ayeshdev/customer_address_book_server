
# Customer Address Book Server Application

This is a Laravel-based API application. Follow the instructions below to get started with setting up and running the application.

## Requirements

Make sure you have the following installed:

- PHP >= 8.0
- Composer
- MySQL

## Installation

### 1. Clone the Repository

```bash
git clone https://github.com/your-repo/your-project.git
cd your-project
```

### 2. Install Dependencies

Install all the PHP dependencies using Composer:

```bash
composer install
```

### 3. Set Up Environment Variables

- Copy the `.env.example` file to create your `.env` file:

```bash
cp .env.example .env
```

- Configure your database and other environment variables in the `.env` file. 

Example for MySQL:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```

### 4. Generate Application Key

Generate the Laravel application key to secure your app:

```bash
php artisan key:generate
```

### 5. Run Migrations

Run the database migrations to set up your tables:

```bash
php artisan migrate
```

### 6. Seed the Database

If your application uses seed data, you can seed the database:

```bash
php artisan db:seed
```

### 7. Running the Application

Start the development server:

```bash
php artisan serve
```

This will serve your application at `http://127.0.0.1:8000`.

### 8. Run Tests

You can run the application’s tests using:

```bash
php artisan test
```
### 9. Seed the Database Again

If your application uses seed data, you can seed the database:

```bash
php artisan db:seed
```
----------------------------------------------------------------
# Admin Credintials

## Admin Email -> admin@cab.com
## Admin Password -> admin@1234
