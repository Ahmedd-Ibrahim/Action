<?php

namespace AhmeddIbrahim\Action\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeActionCommand extends Command
{
    protected $signature = 'action:make {name}';

    protected $description = 'Generate a new action class';

    public function handle()
    {
        $name = $this->argument('name');
        $className = Str::studly($name) . 'Action';
        $interfaceName = Str::studly($name);
        $configContractsPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, config('action.contracts_path', 'Actions\\Contracts\\'));
        $configActionPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, config('action.actions_path', 'Actions\\'));
        $interfaceNamespace = app()->getInstance()->getNamespace() . $configContractsPath . str_replace(['\\', '/'], '\\', $interfaceName);

        $classContent = file_get_contents(__DIR__ . '/stubs/action.stub');
        $classStub = str_replace(
            ['{{className}}', '{{interfaceName}}', '{{interfaceNameSpace}}'],
            [basename($className), basename($interfaceName), $interfaceNamespace],
            $classContent
        );

        $interfaceContent = file_get_contents(__DIR__ . '/stubs/interface.stub');
        $interfaceStub = str_replace(
            ['{{interfaceName}}', '{{interfaceNameSpace}}'],
            [basename($interfaceName), dirname($interfaceNamespace)],
            $interfaceContent
        );

        // Generate the directory paths for interface and class
        $interfacePath = app_path($configContractsPath . str_replace(['\\', '/'], '\\', $name));
        $classPath = app_path($configActionPath . str_replace('\\', '/', $name));
        if (!file_exists(dirname($classPath))) {
            mkdir(dirname($interfacePath), 0755, true);
        }

        if (!file_exists(dirname($classPath))) {
            mkdir(dirname($classPath), 0755, true);
        }

        $classPath = dirname($classPath) . '/' . basename($className) . '.php';
        $interfacePath = dirname($interfacePath) . '/' . basename($interfaceName) . '.php';

        if (file_exists($classPath) || file_exists($interfacePath)) {
            $this->error('Action already exists!');
            return;
        }

        file_put_contents($classPath, $classStub);
        file_put_contents($interfacePath, $interfaceStub);

        $this->info('Action created successfully: ' . $className);
    }
}
