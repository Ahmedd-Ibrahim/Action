<?php

namespace AhmeddIbrahim\Action\Providers;

use Illuminate\Support\ServiceProvider;
use AhmeddIbrahim\Action\Console\Commands\MakeActionCommand;
use Spatie\StructureDiscoverer\Support\StructureScoutManager;
use AhmeddIbrahim\Action\StructureScouts\ActionsStructureScout;

class ActionServiceProvider extends ServiceProvider
{
    public array $customBindings = [];

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/action.php', 'action');

        require_once(__DIR__ . '/../Helper.php');
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

        $contracts = ActionsStructureScout::create()->get();

        foreach ($contracts as $contract) {
            if (str_starts_with($contract, contract_prefix())) {
                $action = 'App\\Actions' . explode('Contracts', $contract, 2)[1] . action_suffix();

                $this->app->bind($contract, $this->customBindings[$contract] ?? $action);
            }
        }
    }
}
