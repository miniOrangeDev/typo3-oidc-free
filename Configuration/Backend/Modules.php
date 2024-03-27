<?php

use Miniorange\Oauth\Controller\BeoidcController;

/**
 * Definitions for modules provided by EXT:examples
 */
return [
    'tools_oauth' => [
        'parent' => 'tools',
        'position' => [],
        'access' => 'user,group',
        'workspaces' => 'live',
        'iconIdentifier' => 'oauth-plugin-bekey',
        'path' => 'module/tools/beoidckey',
        'labels' => 'LLL:EXT:oauth/Resources/Private/Language/locallang_bekey.xlf',
        'extensionName' => 'oauth',
        'controllerActions' => [
            BeoidcController::class => 'request',
        ],
    ]
];