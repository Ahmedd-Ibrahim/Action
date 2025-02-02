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

    public function createAction()
    {
        $name = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $this->argument('name'));
        $className = Str::studly($name) . 'Action';
        $interfaceName = Str::studly(basename($name));
        $interfaceNameWithPrefix = Str::studly($name);
        $interfaceWithNamespace = contract_prefix() . DIRECTORY_SEPARATOR . $interfaceNameWithPrefix;
        $classNamespace = action_prefix();

        if (!blank(get_namespace_from_class_name($className))) {
            $classNamespace .= DIRECTORY_SEPARATOR . get_namespace_from_class_name($className);
        }

        $interfacePath = app_path(get_contracts_path() . DIRECTORY_SEPARATOR . $interfaceNameWithPrefix . '.php');
        $classPath = app_path(get_actions_path() . DIRECTORY_SEPARATOR . $className . '.php');

        $this->createDirectoryIfNotExists(dirname($interfacePath));
        $this->createDirectoryIfNotExists(dirname($classPath));

        if (file_exists($classPath) || file_exists($interfacePath)) {
            $this->error('Action already exists!');
            return;
        }

        $classContent = file_get_contents(__DIR__ . '/stubs/action.stub');
        $classContent = str_replace(
            ['{{className}}', '{{interfaceName}}', '{{interfaceNameSpace}}', '{{classNamespace}}'],
            [basename($className), basename($interfaceName), $interfaceWithNamespace, $classNamespace],
            $classContent
        );

        $interfaceContent = file_get_contents(__DIR__ . '/stubs/interface.stub');
        $interfaceContent = str_replace(
            ['{{interfaceName}}', '{{interfaceNameSpace}}'],
            [basename($interfaceName), dirname($interfaceWithNamespace)],
            $interfaceContent
        );

        file_put_contents($classPath, $classContent);
        file_put_contents($interfacePath, $interfaceContent);

        $this->info('Action created successfully: ' . $className);
    }

    private function createDirectoryIfNotExists($path)
    {
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }
    }
}
