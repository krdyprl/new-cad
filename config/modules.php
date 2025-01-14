<?php

// Simplified modules config without nwidart package dependencies
return [
    'namespace' => 'Modules',
    'stubs' => [
        'enabled' => false,
        'path' => base_path() . '/stubs',
        'files' => [],
        'replacements' => [],
        'gitkeep' => false,
    ],
    'paths' => [
        'modules' => base_path('Modules'),
        'assets' => public_path('modules'),
        'migration' => base_path('database/migrations'),
        'generator' => [
            'config' => ['path' => 'Config', 'generate' => false],
            'command' => ['path' => 'Console', 'generate' => false],
            'migration' => ['path' => 'Database/Migrations', 'generate' => false],
            'seeder' => ['path' => 'Database/Seeders', 'generate' => false],
            'factory' => ['path' => 'Database/Factories', 'generate' => false],
            'model' => ['path' => 'Entities', 'generate' => false],
            'routes' => ['path' => 'Routes', 'generate' => false],
            'controller' => ['path' => 'Http/Controllers', 'generate' => false],
            'filter' => ['path' => 'Http/Middleware', 'generate' => false],
            'request' => ['path' => 'Http/Requests', 'generate' => false],
            'provider' => ['path' => 'Providers', 'generate' => false],
            'assets' => ['path' => 'Resources/assets', 'generate' => false],
            'lang' => ['path' => 'Resources/lang', 'generate' => false],
            'views' => ['path' => 'Resources/views', 'generate' => false],
            'test' => ['path' => 'Tests/Unit', 'generate' => false],
            'test-feature' => ['path' => 'Tests/Feature', 'generate' => false],
        ],
    ],
    'scan' => [
        'enabled' => false,
        'paths' => [],
    ],
    'composer' => [
        'vendor' => 'local',
        'author' => [
            'name' => 'Developer',
            'email' => 'dev@example.com',
        ],
    ],
    'cache' => [
        'enabled' => false,
        'key' => 'laravel-modules',
        'lifetime' => 60,
    ],
    'register' => [
        'translations' => true,
        'files' => 'register',
    ],
    'activators' => [
        'file' => [
            'class' => \App\Support\Modules\FileActivator::class,
            'statuses-file' => base_path('modules_statuses.json'),
            'cache-key' => 'activator.installed',
            'cache-lifetime' => 604800,
        ],
    ],
    'activator' => 'file',
];
