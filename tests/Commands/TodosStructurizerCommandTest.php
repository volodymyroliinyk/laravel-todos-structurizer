<?php

namespace VolodymyrOliinyk\TodosStructurizer\Tests\Commands;

use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\TestCase;

//use Illuminate\Foundation\Testing\TestCase;
//use Tests\TestCase;
//use Tests\CreatesApplication;

use VolodymyrOliinyk\TodosStructurizer\Commands\TodosStructurizerCommand;

/**
 * @group todos-structurizer-command-test
 * @run /usr/bin/php8.0 ./vendor/bin/phpunit tests/Commands/TodosStructurizerCommandTest.php
 * @run /usr/bin/php8.0 ./vendor/bin/phpunit tests/Commands/TodosStructurizerCommandTest.php --verbose --debug
 */
final class TodosStructurizerCommandTest extends TestCase
{
    // use CreatesApplication;
    protected function getPackageProviders($app)
    {
        return \VolodymyrOliinyk\TodosStructurizer\Providers\PackageServiceProvider::class;
    }

    public function setUp(): void
    {
        \Illuminate\Console\Application::starting(function ($artisan) {
            $artisan->resolveCommands([TodosStructurizerCommand::class]);
        });

        parent::setUp();
    }

    /**
     * Test a console command.
     *
     * @test
     */
    public function test_console_command(): void
    {
        echo('Test started.');
        //  var_dump( $this->app['config']);
        /*TODO:[todos-structurizer]:
            fix tests configuration, because some error:
            1) Error: Call to undefined method Illuminate\Container\Container::basePath()
         :ENDTODO*/
//        Artisan::call('todos-structurizer:collect')->assertExitCode(0);

        try {
            echo('## marker 1.');
            // Artisan::call('todos-structurizer:collect')
            Artisan::call('todos-structurizer:collect');

            // Capture the command's output
            $output = Artisan::output();
            var_dump($output);
            // Assert or process the output as needed
            echo('## marker 2.');
        } catch (\Exception $exception) {
            echo('## marker 3.');
            var_dump($exception->getMessage());
            var_dump($exception->getFile());
            var_dump($exception->getLine());
        }


        /*TODO:[todos-structurizer-demo|priority:high]:
            demo 1
         :ENDTODO*/

        /*TODO:[todos-structurizer-demo|priority:high]:
           demo 12
        :ENDTODO*/

        /*TODO:[todos-structurizer-demo|priority:medium]:
            demo 2
           demo21
         :ENDTODO*/

        /*TODO:[todos-structurizer-demo|priority:medium]:
         demo 3
      :ENDTODO*/

        /*TODO:[todos-structurizer-demo|priority:low]:
       demo 4
    :ENDTODO*/

        /*TODO:[todos-structurizer-demo|priority:low]:
     demo 5
  :ENDTODO*/
        echo('Test ended.');
    }
}
