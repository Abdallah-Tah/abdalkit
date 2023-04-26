<?php

namespace Abdalkit\Abdalkit\Console;

use RuntimeException;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Process\Process;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'abdalkit:install
                        {--composer=global : Absolute path to the Composer binary which should be used to install packages}
                        {--php_version=php : Php version command, like `sail` or `./vendor/bin/sail` or `docker-compose up...`}
                        {--external_database : Whether to create additional external database connections}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the Abdalkit starter kit';

    /**
     * The artisan command to run. Default is php.
     *
     * @var string
     */
    protected string $php_version;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->php_version = $this->option('php_version');

        $kit = $this->choice(
            'Which starter kit you want to use?',
            ['Laravel Breeze (Tailwind)', 'Laravel No-Auth (Tailwind)'],
            0
        );

        if ($kit === "Laravel No-Auth (Tailwind)") {

            //C:\laragon\www\abdalkit\resources\stubs\no-auth
            $this->replaceWithTailwindComponentsForNoAuth();

            return;
        } elseif ($kit === "Laravel Breeze (Tailwind)") {
            $theme = $this->choice(
                'Insert 0 to install the theme below',
                ['tailwindcomponents'],
                0
            );
    
            // Install breeze
            $this->requireComposerPackages('laravel/breeze --dev');
            shell_exec("{$this->php_version} artisan breeze:install blade");    
          
            $this->setupDatabases();
            $this->updateUserModelConnection();   

            copy(__DIR__ . '/../../resources/stubs/routes.php', base_path('routes/web.php'));

            copy(__DIR__ . '/../../resources/stubs/controllers/UserController.php', app_path('Http/Controllers/UserController.php'));
            copy(__DIR__ . '/../../resources/stubs/controllers/Auth/AuthenticatedSessionController.php', app_path('Http/Controllers/Auth/AuthenticatedSessionController.php'));
            copy(__DIR__ . '/../../resources/stubs/controllers/Auth/SetPasswordController.php', app_path('Http/Controllers/Auth/SetPasswordController.php'));

            // Ensure the Models directory exists and copy the model files
            (new Filesystem)->ensureDirectoryExists(app_path('Models'));
            copy(__DIR__ . '/../../resources/stubs/models/Permission.php', app_path('Models/Permission.php'));
            copy(__DIR__ . '/../../resources/stubs/models/Role.php', app_path('Models/Role.php'));
            copy(__DIR__ . '/../../resources/stubs/models/RolePermission.php', app_path('Models/RolePermission.php'));
            copy(__DIR__ . '/../../resources/stubs/models/UserRole.php', app_path('Models/UserRole.php'));

            // (new Filesystem)->ensureDirectoryExists(app_path('Http/Requests'));
            // (new Filesystem)->copyDirectory(__DIR__ . '/../../resources/stubs/requests', app_path('Http/Requests/'));

            // Ensure the directories exist
            (new Filesystem)->ensureDirectoryExists(app_path('Http/Requests/Auth'));
            (new Filesystem)->ensureDirectoryExists(app_path('Http/Requests'));

            // Copy the LoginRequest.php file to the Auth directory
            copy(__DIR__ . '/../../resources/stubs/requests/Auth/LoginRequest.php', app_path('Http/Requests/Auth/LoginRequest.php'));
            copy(__DIR__ . '/../../resources/stubs/requests/ProfileUpdateRequest.php', app_path('Http/Requests/ProfileUpdateRequest.php'));

            // Ensure the migrations directory exists and copy the migration files
            (new Filesystem)->ensureDirectoryExists(database_path('migrations'));
            //2023_03_27_172234_create_roles_table.php, 2023_03_27_172249_create_user_roles_table.php, 2023_03_27_172344_create_permissions_table.php, 2023_03_27_172344_create_role_permissions_table.php
            copy(__DIR__ . '/../../resources/stubs/migrations/2023_03_27_172234_create_roles_table.php', database_path('migrations/2023_03_27_172234_create_roles_table.php'));
            copy(__DIR__ . '/../../resources/stubs/migrations/2023_03_27_172249_create_user_roles_table.php', database_path('migrations/2023_03_27_172249_create_user_roles_table.php'));
            copy(__DIR__ . '/../../resources/stubs/migrations/2023_03_27_172344_create_permissions_table.php', database_path('migrations/2023_03_27_172344_create_permissions_table.php'));
            copy(__DIR__ . '/../../resources/stubs/migrations/2023_03_27_172344_create_role_permissions_table.php', database_path('migrations/2023_03_27_172344_create_role_permissions_table.php'));

            //run migrations
            shell_exec("{$this->php_version} artisan migrate");

            if ($theme === 'tailwindcomponents') {
                return $this->replaceWithTailwindComponents();
            }
        }
    }

    //replaceWithTailwindComponentsForNoAuth
    public function replaceWithTailwindComponentsForNoAuth()
    {
        // NPM Packages...
        $this->updateNodePackages(function ($packages) {
            return [
                'color' => '^4.0.1'
            ] + $packages;
        });

        // Views...
        (new Filesystem)->ensureDirectoryExists(resource_path('views/auth'));
        (new Filesystem)->ensureDirectoryExists(resource_path('views/layouts'));
        (new Filesystem)->ensureDirectoryExists(resource_path('views/components'));
        (new Filesystem)->ensureDirectoryExists(public_path('images'));
        (new Filesystem)->ensureDirectoryExists(public_path('js'));

        // (new Filesystem)->copyDirectory(__DIR__ . '/../../resources/stubs/no-auth/tailwindcomponents/views/auth', resource_path('views/auth'));
        (new Filesystem)->copyDirectory(__DIR__ . '/../../resources/stubs/no-auth/views/layouts', resource_path('views/layouts'));
        (new Filesystem)->copyDirectory(__DIR__ . '/../../resources/stubs/no-auth/views/components', resource_path('views/components'));

        (new Filesystem)->delete(resource_path('views/components/responsive-nav-link.blade.php'));

        copy(__DIR__ . '/../../resources/stubs/no-auth/views/dashboard.blade.php', resource_path('views/dashboard.blade.php'));


        // Assets
        copy(__DIR__ . '/../../resources/stubs/no-auth/tailwind.config.js', base_path('tailwind.config.js'));
        copy(__DIR__ . '/../../resources/stubs/no-auth/css/app.css', resource_path('css/app.css'));
        copy(__DIR__ . '/../../resources/stubs/no-auth/js/init-alpine.js', public_path('js/init-alpine.js'));

        // Images
        (new Filesystem)->copyDirectory(__DIR__ . '/../../resources/stubs/no-auth/images', public_path('images'));


        // Demo table
        // (new Filesystem)->ensureDirectoryExists(resource_path('views/users'));
        // copy(__DIR__ . '/../../resources/stubs/no-auth/views/users/index.blade.php', resource_path('views/users/index.blade.php'));

        // Run command composer require livewire/livewire
        $this->requireComposerPackages('livewire/livewire');

        // Run command php artisan livewire:install
        $this->runCommands(['php artisan livewire:install']);

        $this->runCommands(['npm install', 'npm run build']);

        $this->components->info('Breeze scaffolding replaced successfully.');
    }


    protected function replaceWithTailwindComponents()
    {

        // NPM Packages...
        $this->updateNodePackages(function ($packages) {
            return [
                'color' => '^4.0.1'
            ] + $packages;
        });

        // Views...
        (new Filesystem)->ensureDirectoryExists(resource_path('views/auth'));
        (new Filesystem)->ensureDirectoryExists(resource_path('views/layouts'));
        (new Filesystem)->ensureDirectoryExists(resource_path('views/components'));
        (new Filesystem)->ensureDirectoryExists(public_path('images'));
        (new Filesystem)->ensureDirectoryExists(public_path('js'));


        (new Filesystem)->copyDirectory(__DIR__ . '/../../resources/stubs/breeze/tailwindcomponents/views/auth', resource_path('views/auth'));
        (new Filesystem)->copyDirectory(__DIR__ . '/../../resources/stubs/breeze/tailwindcomponents/views/layouts', resource_path('views/layouts'));
        (new Filesystem)->copyDirectory(__DIR__ . '/../../resources/stubs/breeze/tailwindcomponents/views/components', resource_path('views/components'));

        (new Filesystem)->delete(resource_path('views/components/responsive-nav-link.blade.php'));

        copy(__DIR__ . '/../../resources/stubs/breeze/tailwindcomponents/views/dashboard.blade.php', resource_path('views/dashboard.blade.php'));
        copy(__DIR__ . '/../../resources/stubs/breeze/tailwindcomponents/views/about.blade.php', resource_path('views/about.blade.php'));
        copy(__DIR__ . '/../../resources/stubs/breeze/tailwindcomponents/views/profile/edit.blade.php', resource_path('views/profile/edit.blade.php'));

        // Assets
        copy(__DIR__ . '/../../resources/stubs/breeze/tailwindcomponents/tailwind.config.js', base_path('tailwind.config.js'));
        copy(__DIR__ . '/../../resources/stubs/breeze/tailwindcomponents/css/app.css', resource_path('css/app.css'));
        copy(__DIR__ . '/../../resources/stubs/breeze/tailwindcomponents/js/init-alpine.js', public_path('js/init-alpine.js'));

        // Images
        (new Filesystem)->copyDirectory(__DIR__ . '/../../resources/stubs/breeze/tailwindcomponents/images', public_path('images'));


        // Demo table
        (new Filesystem)->ensureDirectoryExists(resource_path('views/users'));
        copy(__DIR__ . '/../../resources/stubs/breeze/tailwindcomponents/views/users/index.blade.php', resource_path('views/users/index.blade.php'));

        // Run command composer require livewire/livewire
        $this->call('require', ['packages' => 'livewire/livewire']);
        // Run command php artisan livewire:install
        $this->call('livewire:install');

        $this->runCommands(['npm install', 'npm run build']);

        $this->components->info('Breeze scaffolding replaced successfully.');
    }


    /**
     * Installs the given Composer Packages into the application.
     * Taken from https://github.com/laravel/breeze/blob/1.x/src/Console/InstallCommand.php
     *
     * @param mixed $packages
     * @return void
     */
    protected function requireComposerPackages($packages)
    {
        $composer = $this->option('composer');

        if ($composer !== 'global') {
            $command = ['php', $composer, 'require'];
        }

        $command = array_merge(
            $command ?? ['composer', 'require'],
            is_array($packages) ? $packages : func_get_args()
        );

        (new Process($command, base_path(), ['COMPOSER_MEMORY_LIMIT' => '-1']))
            ->setTimeout(null)
            ->run(function ($type, $output) {
                $this->output->write($output);
            });
    }

    /**
     * Update the "package.json" file.
     * Taken from https://github.com/laravel/breeze/blob/1.x/src/Console/InstallCommand.php
     *
     * @param callable $callback
     * @param bool $dev
     * @return void
     */
    protected static function updateNodePackages(callable $callback, $dev = true)
    {
        if (!file_exists(base_path('package.json'))) {
            return;
        }

        $configurationKey = $dev ? 'devDependencies' : 'dependencies';

        $packages = json_decode(file_get_contents(base_path('package.json')), true);

        $packages[$configurationKey] = $callback(
            array_key_exists($configurationKey, $packages) ? $packages[$configurationKey] : [],
            $configurationKey
        );

        ksort($packages[$configurationKey]);

        file_put_contents(
            base_path('package.json'),
            json_encode($packages, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . PHP_EOL
        );
    }

    /**
     * Run the given commands.
     * Taken from https://github.com/laravel/breeze/blob/1.x/src/Console/InstallCommand.php
     *
     * @param  array  $commands
     * @return void
     */
    protected function runCommands($commands)
    {
        $process = Process::fromShellCommandline(implode(' && ', $commands), null, null, null, null);

        if ('\\' !== DIRECTORY_SEPARATOR && file_exists('/dev/tty') && is_readable('/dev/tty')) {
            try {
                $process->setTty(true);
            } catch (RuntimeException $e) {
                $this->output->writeln('  <bg=yellow;fg=black> WARN </> ' . $e->getMessage() . PHP_EOL);
            }
        }

        $process->run(function ($type, $line) {
            $this->output->write('    ' . $line);
        });
    }

    protected function setupDatabases()
    {
        $additionalDatabases = $this->confirm('Would you like to create more databases?');

        if ($additionalDatabases) {
            $databaseCount = $this->ask('How many databases would you like to create?');

            for ($i = 1; $i <= $databaseCount; $i++) {
                $this->info("You are now setting up Database {$i}");

                $driver = $this->choice('Please select the driver for the database', ['mysql', 'pgsql', 'sqlsrv', 'sqlite'], 0);
                $host = $this->ask('Please enter the host for the database');
                $port = $this->ask('Please enter the port for the database');
                $database = $this->ask('Please enter the database name for the database');
                $username = $this->ask('Please enter the username for the database');
                $password = $this->ask('Please enter the password for the database');

                $this->updateDatabaseConfig($i, $driver, $host, $port, $database, $username, $password);
                $this->updateEnvFile($i, $driver, $host, $port, $database, $username, $password);
            }
        }

        //success message with the list of databases
        $this->info('Databases have been setup successfully');
    }


    protected function updateDatabaseConfig($index, $driver, $host, $port, $database, $username, $password)
    {
        $configPath = config_path('database.php');
        $configContent = file_get_contents($configPath);

        $newConnection = <<<EOD
        'db{$index}' => [
            'driver' => env('DB_CONNECTION_{$index}', '{$driver}'),
            'host' => env('DB_HOST_{$index}', '{$host}'),
            'port' => env('DB_PORT_{$index}', '{$port}'),
            'database' => env('DB_DATABASE_{$index}', '{$database}'),
            'username' => env('DB_USERNAME_{$index}', '{$username}'),
            'password' => env('DB_PASSWORD_{$index}', '{$password}'),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
        ],

EOD;

        $configContent = preg_replace(
            "/'connections' => \[\n(\s+)/",
            "'connections' => [\n$1" . $newConnection,
            $configContent
        );

        file_put_contents($configPath, $configContent);
    }


    protected function updateEnvFile($index, $driver, $host, $port, $database, $username, $password)
    {
        $envFilePath = base_path('.env');
        $envContent = file_get_contents($envFilePath);

        $envContent .= "\nDB_CONNECTION_{$index}={$driver}";
        $envContent .= "\nDB_HOST_{$index}={$host}";
        $envContent .= "\nDB_PORT_{$index}={$port}";
        $envContent .= "\nDB_DATABASE_{$index}={$database}";
        $envContent .= "\nDB_USERNAME_{$index}={$username}";
        $envContent .= "\nDB_PASSWORD_{$index}={$password}\n";

        file_put_contents($envFilePath, $envContent);
    }

    public function updateUserModelConnection()
    {
        $availableConnections = array_keys(config('database.connections'));
        $selectedConnection = $this->choice('Please select the database connection for the User model', $availableConnections);

        $userModelPath = app_path('Models/User.php');
        $userModelContent = file_get_contents($userModelPath);

        if (strpos($userModelContent, 'protected $connection =') !== false) {
            $userModelContent = preg_replace('/protected \$connection = [\'"].*?[\'"];/', "protected \$connection = '{$selectedConnection}';", $userModelContent);
        } else {
            $connectionLine = "    protected \$connection = '{$selectedConnection}';\n";
            $userModelContent = preg_replace('/(use HasApiTokens, HasFactory, Notifiable;)/', "$1\n{$connectionLine}", $userModelContent);
        }

        file_put_contents($userModelPath, $userModelContent);
        $this->info("User model connection has been set to '{$selectedConnection}'.");
    }
}
