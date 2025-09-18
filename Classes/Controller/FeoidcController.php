<?php

namespace Miniorange\Oauth\Controller;

use Miniorange\Oauth\Helper\Constants;
use Miniorange\Oauth\Helper\MoUtilities;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use TYPO3\CMS\Core\Information\Typo3Version;

/**
 * FeoidcController
 */
class FeoidcController extends ActionController
{

    /**
     * requestAction
     * @return void
     */
    public function requestAction(): ResponseInterface
    {
        error_log("Feoidc Controller, inside printAction: ");

    	// Handle Test Configuration
    	if (isset($_REQUEST['RelayState']) && $_REQUEST['RelayState'] === 'testconfig') {
        	if (session_id() == '' || !isset($_SESSION)) {
            	session_start();
        	}
        	$_SESSION['mo_oauth_test'] = true;
        	return $this->redirectAction();
    	}

	// Handle actual SSO login (with ?app=XYZ)
	if (isset($_REQUEST['app']) && !empty($_REQUEST['app'])) {
	        if (session_id() == '' || !isset($_SESSION)) {
	            session_start();
	        }
	        $_SESSION['mo_oauth_app'] = $_REQUEST['app'];   // store app name if provided
	        return $this->redirectAction();
    	} else {
	        // Handle SSO without app parameter - use default configuration
	        if (session_id() == '' || !isset($_SESSION)) {
	            session_start();
	        }
	        // Check if we have a valid OIDC configuration
	        $json_object = MoUtilities::fetchFromDb(Constants::OIDC_OIDC_OBJECT, Constants::TABLE_OIDC);
	        if (!empty($json_object)) {
	            $app = json_decode($json_object, true);
	            if ($app && isset($app[Constants::OIDC_APP_NAME]) && !empty($app[Constants::OIDC_APP_NAME])) {
	                $_SESSION['mo_oauth_app'] = $app[Constants::OIDC_APP_NAME]; // use configured app name
	                return $this->redirectAction();
	            }
	        }
	        // If no valid configuration found, show error
	        $responseFactory = GeneralUtility::makeInstance(ResponseFactoryInterface::class);
	        $streamFactory = GeneralUtility::makeInstance(StreamFactoryInterface::class);
	        return $responseFactory->createResponse()
	            ->withHeader('Content-Type', 'text/html; charset=utf-8')
	            ->withBody($streamFactory->createStream('SSO configuration not found. Please configure OAuth/OIDC settings first.'));
    	}

    	// Otherwise render /feoidc page normally
    	$responseFactory = GeneralUtility::makeInstance(ResponseFactoryInterface::class);
    	$streamFactory = GeneralUtility::makeInstance(StreamFactoryInterface::class);

    	try {
	        $renderedContent = $this->view->render();
	        return $responseFactory->createResponse()
	            ->withHeader('Content-Type', 'text/html; charset=utf-8')
	            ->withBody($streamFactory->createStream($renderedContent));
    	} catch (\Exception $e) {
	        error_log('Error rendering view in FeoidcController: ' . $e->getMessage());
	        return $responseFactory->createResponse()
	            ->withHeader('Content-Type', 'text/html; charset=utf-8')
	            ->withBody($streamFactory->createStream('Error rendering view'));
    	}
    }

    /**
     * Redirect to Authorization URL
     */
    public function redirectAction(): ResponseInterface
    {
        $json_object = MoUtilities::fetchFromDb(Constants::OIDC_OIDC_OBJECT, Constants::TABLE_OIDC);
        $app = json_decode($json_object, true);
        
        // Check if we have valid configuration
        if (!$app || !isset($app[Constants::OIDC_APP_NAME]) || empty($app[Constants::OIDC_APP_NAME])) {
            $responseFactory = GeneralUtility::makeInstance(ResponseFactoryInterface::class);
            $streamFactory = GeneralUtility::makeInstance(StreamFactoryInterface::class);
            return $responseFactory->createResponse()
                ->withHeader('Content-Type', 'text/html; charset=utf-8')
                ->withBody($streamFactory->createStream('SSO configuration not found. Please configure OAuth/OIDC settings first.'));
        }
        
        $state = base64_encode($app[Constants::OIDC_APP_NAME]);
        $authorizationUrl = $app[Constants::OIDC_AUTH_URL];

        if (strpos($authorizationUrl, "google") !== false) {
            $authorizationUrl = "https://accounts.google.com/o/oauth2/auth";
        }

        $authorizationUrl .= (strpos($authorizationUrl, '?') !== false ? "&" : "?")
            . "client_id=" . $app[Constants::OIDC_CLIENT_ID]
            . "&scope=" . $app[Constants::OIDC_SCOPE]
            . "&redirect_uri=" . $app[Constants::OIDC_REDIRECT_URL]
            . "&response_type=code&state=" . $state;

        if (session_id() == '' || !isset($_SESSION))
            session_start();
        $_SESSION['oauth2state'] = $state;
        $_SESSION['appname'] = $app[Constants::OIDC_APP_NAME];


        $version = new Typo3Version();
        $typo3Version = $version->getVersion();

        if ($typo3Version >= 12) {
            return $this->responseFactory->createResponse()
                ->withAddedHeader('Location', $authorizationUrl)
                ->withStatus(302);
        } else {
            header('Location: ' . $authorizationUrl);
            return $this->responseFactory->createResponse(302, 'Redirecting to authorization URL');
        }

    }

}
