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
        $this->createAction();
    }

    protected function createAction()
    {
        $name = $this->argument('name');
        $className = Str::studly($name) . 'Action';
        $interfaceName = Str::studly(basename($name));
        $interfaceWithNamespace = contract_prefix() . DIRECTORY_SEPARATOR . Str::studly($name);
        $classNamespace = action_prefix() . DIRECTORY_SEPARATOR . get_namespace_from_class_name($className);

        $interfacePath = app_path(get_contracts_path() . DIRECTORY_SEPARATOR . basename($interfaceWithNamespace) . '.php');
        $classPath = app_path(get_actions_path() . DIRECTORY_SEPARATOR . $className . '.php');

        $this->createDirectoryIfNotExists(dirname($interfacePath));
        $this->createDirectoryIfNotExists(dirname($classPath));

        if (file_exists($classPath) || file_exists($interfacePath)) {
            $this->error('Action already exists!');
            return;
        }

        $classContent = $this->generateClassContent($className, $interfaceName, $interfaceWithNamespace, $classNamespace);
        $interfaceContent = $this->generateInterfaceContent($interfaceName, $interfaceWithNamespace);

        file_put_contents($classPath, $classContent);
        file_put_contents($interfacePath, $interfaceContent);

        $this->info('Action created successfully: ' . $className);
    }

    private function generateClassContent($className, $interfaceName, $interfaceWithNamespace, $classNamespace)
    {
        $classStub = file_get_contents(__DIR__ . '/stubs/action.stub');
        return str_replace(
            ['{{className}}', '{{interfaceName}}', '{{interfaceNameSpace}}', '{{classNamespace}}'],
            [$className, $interfaceName, $interfaceWithNamespace, $classNamespace],
            $classStub
        );
    }

    private function generateInterfaceContent($interfaceName, $interfaceWithNamespace)
    {
        $interfaceStub = file_get_contents(__DIR__ . '/stubs/interface.stub');
        return str_replace(
            ['{{interfaceName}}', '{{interfaceNameSpace}}'],
            [$interfaceName, $interfaceWithNamespace],
            $interfaceStub
        );
    }

    private function createDirectoryIfNotExists($path)
    {
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }
    }
}
