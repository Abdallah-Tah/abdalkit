# AbdalKit

AbdalKit is a Laravel starter kit with a few features that are typical for almost any project.

(#available-themes).

The package supports:

- [Laravel Breeze](https://github.com/laravel/breeze) (Tailwind)

` ❗ ` **IMPORTANT:**  This package must be used in a **NEW** Laravel project. Existing project functionalities, such as routes or controllers, may be overridden by Larastarters.

---

## Features

Along with the Design Themes, AbdalKit adds a few features that are typical for almost any project:

- Profile management form to change name/email/password
- A sample table of Users list
- A sample static text page
- Two-level menu on the sidebar
- Roles and Permissions management
- Livewire integration

<br/>

## Get Started

AbdalKit requires PHP 8+ and Laravel 9+.

1. Create a new Laravel project.

2. Require AbdalKit as a dev dependency, run:

    ```shell
    composer require abdallah-tah/abdalkit:dev-main --dev
    ```

3. Configure AbdalKit, run the command below:

    ```shell
    php artisan abdalkit:install
    ```

4. Create a new database for your project using your preferred database management system (e.g., MySQL, PostgreSQL, SQLite, SQL Server).

5. Set up your database connection in the `.env` file with the appropriate credentials and database name.

6. Run the migrations to set up the required tables in the database:

    ```shell
    php artisan migrate
    ```

7. Compile the project assets, run:

    ```shell
    npm install && npm run dev
    ```

8. That's it! You have Laravel Auth starter, just visit the home page and click Log in / Register.

<br/>

## Available Themes

In the current version, there is only one theme available - Tailwind. We are planning to add more themes in the future.

**Tailwind Themes with Laravel Breeze**

- [Tailwind Components](https://github.com/tailwindcomponents/dashboard)
