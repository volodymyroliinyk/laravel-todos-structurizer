<?php
declare(strict_types=1);

namespace VolodymyrOliinyk\TodosStructurizer\Providers;

use Illuminate\Support\ServiceProvider;
use VolodymyrOliinyk\TodosStructurizer\Commands\TodosStructurizerCommand;
use VolodymyrOliinyk\TodosStructurizer\Exceptions\TodosStructurizerException;

final class PackageServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $configPath = __DIR__ . '/../publishable/config/todos-structurizer.php';

        if (!file_exists($configPath)) {
            throw new TodosStructurizerException(sprintf('Incorrect config path: %s', $configPath));
        }

        $this->mergeConfigFrom($configPath, 'todos-structurizer');

        $this->publishes([
            $configPath => config_path('todos-structurizer.php'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                TodosStructurizerCommand::class,
            ]);
        }
    }
}