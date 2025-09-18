<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'OAuth/OIDC Single Sign-On (SSO) | Backend & Frontend',
    'description' => 'Typo3 OAuth / OpenID Connect Single Sign-On SSO extension by miniOrange allows your users (Frontend & Backend) to login (Single Sign-On) into Typo3 with Azure AD, Azure B2C, AWS Cognito, Office 365, WSO2, Okta, Salesforce, Discord, Keycloak, PingFederate, OneLogin or other custom OAuth 2.0 providers. Typo3 OAuth / OIDC extension can be used for authorization and authentication purposes with any OAuth / OIDC Provider that conforms to the OAuth 2.0 and OpenID Connect (OIDC) standards. Typo3 OAuth / OpenID Connect Single Sign-On SSO provides user authentication with OAuth & OpenID Connect protocol and allows authorized users to login into the Typo3 site. We provide features like Attribute Mapping & Role Mapping which help to map user data returned from your OAuth Provider to Typo3. You can configure and enable SSO Login on both your Typo3 frontend and backend (Admin Panel) login page with our extension. This extension is fully compatible with TYPO3 v13.',
    'author' => 'miniOrange',
    'constraints' => [
        'depends' => [
            'typo3' => '8.7.30-13.4.99',
        ],
    ],
    'version' => '1.1.1',
    'icon' => 'EXT:oauth/Resources/Public/Icons/Extension.svg',
    'state' => 'stable',
    'autoload' => [
        'psr-4' => [
            'Miniorange\\Oauth\\' => 'Classes/',
        ],
    ]
];
