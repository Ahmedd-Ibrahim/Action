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
        $name = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $this->argument('name'));
        $className = Str::studly($name) . 'Action';
        $interfaceName = Str::studly($name);
        $configContractsPath = $this->getContractsPath();
        $configActionPath = $this->getActionsPath();
        $interfaceNamespace = $this->getInterfaceNamespace($interfaceName);
        $classNamespace = $this->getClassNamespace();

        if (!is_null(basename($className))) {
            $classNamespace = $classNamespace . basename($className);
        }

        // Generate the directory paths for interface and class
        $interfacePath = app_path($configContractsPath . DIRECTORY_SEPARATOR . $interfaceName . '.php');
        $classPath = app_path($configActionPath . DIRECTORY_SEPARATOR . $className . '.php');

        $this->createDirectoryIfNotExists(dirname($interfacePath));
        $this->createDirectoryIfNotExists(dirname($classPath));

        if (file_exists($classPath) || file_exists($interfacePath)) {
            $this->error('Action already exists!');
            return;
        }

        $classStub = file_get_contents(__DIR__ . '/stubs/action.stub');
        $classContent = str_replace(
            ['{{className}}', '{{interfaceName}}', '{{interfaceNameSpace}}', '{{classNamespace}}'],
            [basename($className), basename($interfaceName), $interfaceNamespace, $classNamespace],
            $classStub
        );

        $interfaceStub = file_get_contents(__DIR__ . '/stubs/interface.stub');
        $interfaceContent = str_replace(
            ['{{interfaceName}}', '{{interfaceNameSpace}}'],
            [basename($interfaceName), dirname($interfaceNamespace)],
            $interfaceStub
        );

        file_put_contents($classPath, $classContent);
        file_put_contents($interfacePath, $interfaceContent);

        $this->info('Action created successfully: ' . $className);
    }

    private function getContractsPath()
    {
        $contractPath = config('action.contracts_path', 'Actions\\Contracts');

        if (Str::endsWith($contractPath, '\\') || Str::endsWith($contractPath, '/')) {
            $contractPath = substr($contractPath, 0, -1);
        }

        return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $contractPath);
    }

    private function getActionsPath()
    {
        $contractPath = config('action.actions_path', 'Actions');

        if (Str::endsWith($contractPath, '\\') || Str::endsWith($contractPath, '/')) {
            $contractPath = substr($contractPath, 0, -1);
        }

        return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $contractPath);
    }

    private function getInterfaceNamespace($interfaceName)
    {
        return app()->getInstance()->getNamespace() . $this->getContractsPath() . $interfaceName;
    }

    private function getClassNamespace()
    {
        return app()->getInstance()->getNamespace() . $this->getActionsPath();
    }

    private function createDirectoryIfNotExists($path)
    {
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }
    }
}
