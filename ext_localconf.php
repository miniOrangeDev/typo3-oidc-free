<?php

defined('TYPO3') or die();

use TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use TYPO3\CMS\Core\Information\Typo3Version;

$GLOBALS['TYPO3_CONF_VARS']['SYS']['features']['security.backend.enforceContentSecurityPolicy'] = false;
$GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['enforceValidation'] = false;
$GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['excludedParameters'] = ['idp_name', 'RelayState', 'option', 'SAMLRequest', 'SAMLResponse', 'SigAlg', 'Signature', 'type', 'app', 'code', 'state'];

call_user_func(
    function () {
        $pluginNameBeoidc = "Beoidc";
        $pluginNameFeoidc = 'Feoidc';
        $pluginNameResponse = 'Response';
        $pluginNameLogout = 'Logout';
        $version = new Typo3Version();
        if (version_compare($version, '10.0.0', '>=')) {
            $extensionName = 'oauth';
            $cache_actions_beoidc = [Miniorange\Oauth\Controller\BeoidcController::class => 'request'];
            $cache_actions_feoidc = [Miniorange\Oauth\Controller\FeoidcController::class => 'request'];
            $non_cache_actions_feoidc = [Miniorange\Oauth\Controller\FeoidcController::class => 'control'];
            $cache_actions_response = [Miniorange\Oauth\Controller\ResponseController::class => 'response'];
            $cache_actions_logout = [Miniorange\Oauth\Controller\LogoutController::class => 'logout'];
        } else {
            $extensionName = 'miniorange.oauth';
            $cache_actions_beoidc = ['Beoidc' => 'request'];
            $cache_actions_feoidc = ['Feoidc' => 'request'];
            $non_cache_actions_feoidc = ['Feoidc' => 'control'];
            $cache_actions_response = ['Response' => 'response'];
            $cache_actions_logout = ['Logout' => 'logout'];
        }

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            $extensionName,
            $pluginNameBeoidc,
            [
                'Beoidc' => 'request',
            ]
        );

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            $extensionName,
            $pluginNameFeoidc,
            $cache_actions_feoidc,
            // non-cacheable actions
            $non_cache_actions_feoidc
        );

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            $extensionName,
            $pluginNameResponse,
            $cache_actions_response
        );

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            $extensionName,
            $pluginNameLogout,
            $cache_actions_logout
        );


        // wizards
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
            'mod {
            wizards.newContentElement.wizardItems.plugins {
                elements {
                    Feoidckey {
                        iconIdentifier = oauth-plugin-feoidc
                        title = LLL:EXT:oauth/Resources/Private/Language/locallang_db.xlf:tx_oauth_feoidc.name
                        description = LLL:EXT:oauth/Resources/Private/Language/locallang_db.xlf:tx_oauth_feoidc.description
                        tt_content_defValues {
                            CType = list
                            list_type = oauth_feoidc
                        }
                    }
                    Responsekey {
                        iconIdentifier = oauth-plugin-response
                        title = LLL:EXT:oauth/Resources/Private/Language/locallang_db.xlf:tx_oauth_response.name
                        description = LLL:EXT:oauth/Resources/Private/Language/locallang_db.xlf:tx_oauth_response.description
                        tt_content_defValues {
                            CType = list
                            list_type = oauth_response
                        }
                    }
                }
                show = *
            }
       }'
        );

        $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
        $iconRegistry->registerIcon(
            'oauth-plugin-feoidc',
            \TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
            ['source' => 'EXT:oauth/Resources/Public/Icons/Extension.png']
        );
        $iconRegistry->registerIcon(
            'oauth-plugin-response',
            \TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
            ['source' => 'EXT:oauth/Resources/Public/Icons/Extension.png']
        );
        $iconRegistry->registerIcon(
            'oauth-plugin-logout',
            \TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
            ['source' => 'EXT:oauth/Resources/Public/Icons/Extension.png']
        );
        $iconRegistry->registerIcon(
            'oauth-plugin-bekey',
            \TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
            ['source' => 'EXT:oauth/Resources/Public/Icons/Extension.png']
        );

    }
);