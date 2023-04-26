<?php
declare(strict_types=1);

namespace VolodymyrOliinyk\TodosStructurizer\Providers;

use Illuminate\Support\ServiceProvider;
use VolodymyrOliinyk\TodosStructurizer\Commands\TodosStructurizedCommand;
use VolodymyrOliinyk\TodosStructurizer\Exceptions\TodosStructurizerException;

final class PackageServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $configPath =  __DIR__.'/../publishable/config/todos-structurized.php';

        if(!file_exists($configPath)){
            throw new TodosStructurizerException(sprintf('Incorrect config path: %s', $configPath));
        }

        $this->mergeConfigFrom($configPath, 'todos-structurized');

        $this->publishes([
            $configPath => config_path('todos-structurized.php'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                TodosStructurizedCommand::class,
            ]);
        }
    }
}