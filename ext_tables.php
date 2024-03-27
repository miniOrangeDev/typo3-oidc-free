<?php

defined('TYPO3') or die();

use TYPO3\CMS\Core\Information\Typo3Version;

call_user_func(
    function () {
        $version = new Typo3Version();
        if (version_compare($version, '10.0.0', '>=')) {
            $extensionName = 'oauth';
            $cache_actions_beoidc = [Miniorange\Oauth\Controller\BeoidcController::class => 'request'];
        } else {
            $extensionName = 'miniorange.oauth';
            $cache_actions_beoidc = ['Beoidc' => 'request'];
        }

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
            $extensionName,
            'tools', // Make module a submodule of 'tools'
            'beoidckey', // Submodule key
            '4', // Position
            $cache_actions_beoidc,
            [
                'access' => 'user,group',
                'icon'   => 'EXT:oauth/Resources/Public/Icons/miniorange.png',
                'labels' => 'LLL:EXT:oauth/Resources/Private/Language/locallang_bekey.xlf'
            ]
        );

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            $extensionName,
            'Feoidc',
            'feoidc',
            'EXT:oauth/Resources/Public/Icons/miniorange.svg'
        );

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            $extensionName,
            'Response',
            'response',
            'EXT:oauth/Resources/Public/Icons/miniorange.svg'
        );

    }
);