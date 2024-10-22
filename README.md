
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
git clone https://github.com/ayeshdev/customer_address_book_server.git
cd customer_address_book_server
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

- Uncomment the `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` variables and update them with your database credentials.

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

---

## Admin Credentials (after seeding the database)

- **Admin Email**: admin@cab.com
- **Admin Password**: admin@1234

---

## Testing API

### 1. Admin Registration
- To register an admin, you can either:
  - Use the `db:seed` command to create an admin user.
  - Or use the default **Admin Register** API route (temporarily for testing purposes).
- **Important**: The default register API route and the `register` function in the `AuthController` will be removed after testing.

### 2. Authentication (Bearer Token)
- For testing **Products** and **Customer** sections, you need to pass a **Bearer Token** in the request headers.
- You can generate this token by logging into the system using the **Login API**.
  - Upon successful login, the token will be returned in the response.
  - Include this token in the headers of all further requests to authenticate the user.

#### Example Header:
```bash
Authorization: Bearer {your_token_here}
```
