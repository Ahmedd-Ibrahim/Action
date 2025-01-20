<?php

namespace AhmeddIbrahim\Action\StructureScouts;

use Spatie\StructureDiscoverer\Cache\DiscoverCacheDriver;
use Spatie\StructureDiscoverer\Cache\FileDiscoverCacheDriver;
use Spatie\StructureDiscoverer\Discover;
use Spatie\StructureDiscoverer\StructureScout;

class ActionsStructureScout extends StructureScout
{
    public function identifier(): string
    {
        return 'app-actions';
    }

    protected function definition(): Discover
    {
        $actionDirectory = config('action.actions_suffix', 'Actions');
        is_dir(app_path($actionDirectory)) || mkdir(app_path($actionDirectory));
        return Discover::in(app_path($actionDirectory))
            ->interfaces();
    }

    public function cacheDriver(): DiscoverCacheDriver
    {
        return new FileDiscoverCacheDriver(base_path('bootstrap/cache'));
    }
}
