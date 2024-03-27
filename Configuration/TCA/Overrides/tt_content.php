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
            $extensionName = 'Miniorange.oauth';
            $cache_actions_beoidc = ['Beoidc' => 'request'];
        }

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            $extensionName,
            'Feoidc',
            'feoidc'
        );

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            $extensionName,
            'Response',
            'response'
        );

    }
);
