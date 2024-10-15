<?php

namespace SVR\Raw\Tests;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Laravel\BrowserKitTesting\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected $baseUrl = 'http://localhost:8000';

    /**
     * Boots the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../vendor/laravel/laravel/bootstrap/app.php';

        $app->booting(function () {
            $loader = \Illuminate\Foundation\AliasLoader::getInstance();
            $loader->alias('Admin', \OpenAdminCore\Admin\Facades\Admin::class);
        });

        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

        $app->register('OpenAdminCore\Admin\AdminServiceProvider');

        return $app;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $adminConfig = require __DIR__.'/config/admin.php';

//        $this->app['config']->set('database.default', env('DB_CONNECTION', 'pgsql'));
//        $this->app['config']->set('database.connections.mysql.host', env('MYSQL_HOST', '127.0.0.1'));
//        $this->app['config']->set('database.connections.mysql.database', env('MYSQL_DATABASE', 'open_test'));
//        $this->app['config']->set('database.connections.mysql.username', env('MYSQL_USER', 'postgres'));
//        $this->app['config']->set('database.connections.mysql.password', env('MYSQL_PASSWORD', 'Pbh@p#&3w5!e'));
//        $this->app['config']->set('app.key', 'AckfSECXIvnK5r28GVIWUAxmbBSjTsmF');
//        $this->app['config']->set('filesystems', require __DIR__.'/config/filesystems.php');
//        $this->app['config']->set('admin', $adminConfig);

        $this->app['config']->set('database.default', env('DB_CONNECTION', 'pgsql'));
        $this->app['config']->set('database.connections.pgsql.host', env('DB_HOST', '127.0.0.1'));
        $this->app['config']->set('database.connections.pgsql.port', env('DB_PORT', '5432'));
        $this->app['config']->set('database.connections.pgsql.database', env('DB_DATABASE', 'laravel'));
        $this->app['config']->set('database.connections.pgsql.username', env('DB_USERNAME', 'root'));
        $this->app['config']->set('database.connections.pgsql.password', env('DB_PASSWORD', ''));
        $this->app['config']->set('app.key', env('APP_KEY', ''));
        $this->app['config']->set('filesystems', require __DIR__.'/config/filesystems.php');
        $this->app['config']->set('admin', $adminConfig);

        foreach (Arr::dot(Arr::get($adminConfig, 'auth'), 'auth.') as $key => $value) {
            $this->app['config']->set($key, $value);
        }

        $this->artisan('vendor:publish', ['--provider' => 'OpenAdminCore\Admin\AdminServiceProvider']);

        Schema::defaultStringLength(191);

        $this->artisan('admin:install');

        $this->migrateTestTables();

        if (file_exists($routes = admin_path('routes.php'))) {
            require $routes;
        }

        require __DIR__.'/routes.php';

        require __DIR__.'/seeds/factory.php';

//        \OpenAdminCore\Admin\Admin::$css = [];
//        \OpenAdminCore\Admin\Admin::$js = [];
//        \OpenAdminCore\Admin\Admin::$script = [];
    }

    protected function tearDown(): void
    {
        (new CreateAdminTables())->down();

        (new CreateTestTables())->down();

        DB::select("delete from `migrations` where `migration` = '2016_01_04_173148_create_admin_tables'");

        parent::tearDown();
    }

    /**
     * run package database migrations.
     *
     * @return void
     */
    public function migrateTestTables()
    {
        $fileSystem = new Filesystem();

        $fileSystem->requireOnce(__DIR__.'/migrations/2016_11_22_093148_create_test_tables.php');

        (new CreateTestTables())->up();
    }
}