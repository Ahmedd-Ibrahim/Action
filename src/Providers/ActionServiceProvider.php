<?php

namespace AhmeddIbrahim\Action\Providers;

use Illuminate\Support\ServiceProvider;
use App\Support\StructureScouts\ActionsStructureScout;
use AhmeddIbrahim\Action\Console\Commands\MakeActionCommand;
use Spatie\StructureDiscoverer\Support\StructureScoutManager;

class ActionServiceProvider extends ServiceProvider
{
    public array $customBindings = [];

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/action.php', 'action');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/action.php' => config_path('action.php'),
        ], 'config');

        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeActionCommand::class,
            ]);
        }


        StructureScoutManager::add(ActionsStructureScout::class);

        $contractPrefix = 'App\\Actions\\Contracts';
        $actionPrefix = 'App\\Actions';
        $actionSuffix = 'Action';

        $contracts = ActionsStructureScout::create()->get();

        foreach ($contracts as $contract) {
            if (str_starts_with($contract, $contractPrefix)) {
                $action = $actionPrefix . explode('Contracts', $contract)[1] . $actionSuffix;

                $this->app->bind($contract, $this->customBindings[$contract] ?? $action);
            }
        }
    }
}
