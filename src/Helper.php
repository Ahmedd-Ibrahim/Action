<?php

use Illuminate\Support\Str;

function get_actions_path()
{
    $contractPath = config('action.actions_path', 'Actions');

    if (Str::endsWith($contractPath, '\\') || Str::endsWith($contractPath, '/')) {
        $contractPath = substr($contractPath, 0, -1);
    }

    return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $contractPath);
}

function get_contracts_path()
{
    $contractPath = config('action.contracts_path', 'Actions\\Contracts');

    if (Str::endsWith($contractPath, '\\') || Str::endsWith($contractPath, '/')) {
        $contractPath = substr($contractPath, 0, -1);
    }

    return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $contractPath);
}

function contract_prefix()
{
    return app()->getInstance()->getNamespace() . get_contracts_path();
}


function action_prefix()
{
    return app()->getInstance()->getNamespace() . get_actions_path();
}

function action_suffix()
{
    return config('action.actions_suffix', 'Action');
}


if (!function_exists('get_namespace_from_class_name')) {
    function get_namespace_from_class_name($className)
    {
        $lastBackslashPos = strrpos($className, '\\');

        if ($lastBackslashPos !== false) {
            return substr($className, 0, $lastBackslashPos);
        }

        return '';
    }
}
