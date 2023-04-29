<?php

namespace VolodymyrOliinyk\TodosStructurizer\Tests\Commands;

use Illuminate\Support\Facades\Artisan;
use VolodymyrOliinyk\TodosStructurizer\Tests\TestCase;


/**
 * @group todos-structurizer-command-test
 * @run vendor/bin/phpunit tests/Commands/TodosStructurizerCommandTest.php
 */
final class TodosStructurizerCommandTest extends TestCase
{
    /**
     * Test a console command.
     *
     * @return void
     */
    public function test_console_command(): void
    {
        /*TODO:[cat]:
            fix tests configuration, because some error:
            1) VolodymyrOliinyk\TodosStructurizer\Tests\Commands\TodosStructurizerCommandTest::test_console_command
    RuntimeException: A facade root has not been set.
         :ENDTODO*/
        Artisan::call('todos-structurizer:collect')->assertExitCode(0);
//        $this->artisan('todos-structurizer:collect')->assertExitCode(0);
    }
}
