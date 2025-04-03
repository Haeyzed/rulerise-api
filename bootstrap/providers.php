<?php

use Spatie\Permission\PermissionServiceProvider;

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\AuthServiceProvider::class,
    App\Providers\ResponseServiceProvider::class,
    App\Providers\SearchServiceProvider::class,
    App\Providers\StorageServiceProvider::class,
    App\Providers\TelescopeServiceProvider::class,
    PermissionServiceProvider::class
];
