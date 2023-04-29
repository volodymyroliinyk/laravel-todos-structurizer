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
        /*TODO:[todos-structurizer]:
            fix tests configuration, because some error:
            1) Error: Call to undefined method Illuminate\Container\Container::basePath()
         :ENDTODO*/
        Artisan::call('todos-structurizer:collect')->assertExitCode(0);
//        $this->artisan('todos-structurizer:collect')->assertExitCode(0);

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

    }
}
