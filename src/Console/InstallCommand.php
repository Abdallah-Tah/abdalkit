<?php

namespace Abdalkit\Abdalkit\Console;

use RuntimeException;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
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
                        {--external_database : Whether to create external database connections}';

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
            ['Laravel Breeze (Tailwind)'],
            0
        );

        $external_database = $this->option('external_database');

        if ($external_database) {
            $database_count = $this->ask('How many database settings do you want to create?');

            $envFilePath = base_path('.env');
            $envContent = file_get_contents($envFilePath);

            $newConnections = [];

            for ($i = 0; $i < $database_count; $i++) {

                $dbName = "DB_EXT_{$i}";
                $driver = $this->choice("Please select the driver for database " . ($i + 1) . ":", ['mysql', 'pgsql', 'sqlsrv', 'sqlite'], 0);
                $host = $this->ask("Please enter the host for database " . ($i + 1) . ":");
                $username = $this->ask("Please enter the username for database " . ($i + 1) . ":");
                $password = $this->secret("Please enter the password for database " . ($i + 1) . ":");


                // Update the .env file
                $envContent .= "\n{$dbName}_DRIVER={$driver}\n{$dbName}_HOST={$host}\n{$dbName}_USERNAME={$username}\n{$dbName}_PASSWORD={$password}\n";
                file_put_contents($envFilePath, $envContent);

                // Prepare new connections to be added to the config/database.php file
                $newConnections[$dbName] = [
                    'driver' => env("{$dbName}_DRIVER"),
                    'host' => env("{$dbName}_HOST"),
                    'port' => env('DB_PORT', '3306'),
                    'database' => env("{$dbName}_DATABASE"),
                    'username' => env("{$dbName}_USERNAME"),
                    'password' => env("{$dbName}_PASSWORD"),
                    'unix_socket' => env('DB_SOCKET', ''),
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                    'prefix' => '',
                    'strict' => true,
                    'engine' => null,
                ];
            }

            // Update the config/database.php file
            $databaseConfigPath = config_path('database.php');
            $databaseConfigContent = file_get_contents($databaseConfigPath);
            $newConnectionsPlaceholder = '// Place New Connections Here';

            $newConnectionsString = var_export($newConnections, true);
            $newConnectionsString = preg_replace('/\'env\((.*?)\)\'/', 'env($1)', $newConnectionsString);
            $newConnectionsString = preg_replace('/  /', '    ', $newConnectionsString);

            $databaseConfigContent = str_replace($newConnectionsPlaceholder, $newConnectionsString, $databaseConfigContent);
            file_put_contents($databaseConfigPath, $databaseConfigContent);
        }

        if ($kit === "Laravel Breeze (Tailwind)") {
            $theme = $this->choice(
                'Insert 0 to install the theme below',
                ['tailwindcomponents'],
                0
            );

            // Install breeze
            $this->requireComposerPackages('laravel/breeze:^1.4');
            shell_exec("{$this->php_version} artisan breeze:install blade");

            copy(__DIR__ . '/../../resources/stubs/routes.php', base_path('routes/web.php'));

            copy(__DIR__ . '/../../resources/stubs/controllers/UserController.php', app_path('Http/Controllers/UserController.php'));

            (new Filesystem)->ensureDirectoryExists(app_path('Http/Requests'));
            (new Filesystem)->copyDirectory(__DIR__ . '/../../resources/stubs/requests', app_path('Http/Requests/'));

            if ($theme === 'tailwindcomponents') {
                return $this->replaceWithTailwindComponents();
            }
        }
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
}
