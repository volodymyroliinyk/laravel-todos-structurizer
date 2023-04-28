<?php

namespace VolodymyrOliinyk\TodosStructurizer\Tests;

use Illuminate\Support\Facades\Facade;
use VolodymyrOliinyk\TodosStructurizer\Providers\PackageServiceProvider;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected $withDummy = true;

    public function setUp(): void
    {
        parent::setUp();

        Facade::clearResolvedInstances();

        // Orchestra Testbench does not contain this file and can't create autoload without
        if (!is_dir(base_path('tests/'))) {
            mkdir(base_path('tests/'));

            file_put_contents(
                base_path('tests/TestCase.php'),
                "<?php\n\n"
            );
        }
    }

    protected function getPackageProviders($app)
    {
        return [
            PackageServiceProvider::class,
        ];
    }

    public function tearDown(): void
    {
        parent::tearDown();

        //$this->artisan('migrate:reset');
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
//        // Setup default database to use sqlite :memory:
//        $app['config']->set('database.default', 'testbench');
//        $app['config']->set('database.connections.testbench', [
//            'driver'   => 'sqlite',
//            'database' => ':memory:',
//            'prefix'   => '',
//        ]);
//
//        // Setup Voyager configuration
//        $app['config']->set('voyager.user.namespace', User::class);
//
//        // Setup Authentication configuration
//        $app['config']->set('auth.providers.users.model', User::class);
    }

    protected function install()
    {
//        $this->artisan('voyager:install', ['--with-dummy' => $this->withDummy]);
//
//        app(VoyagerServiceProvider::class, ['app' => $this->app])->loadAuth();
//
//        if (file_exists(base_path('routes/web.php'))) {
//            require base_path('routes/web.php');
//        }
    }
}
