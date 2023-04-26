<?php
declare(strict_types=1);

namespace VolodymyrOliinyk\TodosStructurizer\Providers;

use Illuminate\Support\ServiceProvider;
use VolodymyrOliinyk\TodosStructurizer\Commands\TodosStructurizedCommand;

final class PackageServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $publishablePath = '../publishable';
        $configPath = $publishablePath . '/config/todo-structurized.php';

        $this->mergeConfigFrom($configPath, 'todo-structurized');

        $this->publishes([
            $configPath => config_path('todo-structurized.php'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                TodosStructurizedCommand::class,
            ]);
        }
    }
}