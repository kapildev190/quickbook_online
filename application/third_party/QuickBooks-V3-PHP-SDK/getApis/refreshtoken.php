<?php
include(APPPATH.'/third_party/QuickBooks-V3-PHP-SDK/src/config.php');
use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\PlatformService\PlatformService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
$config = include(APPPATH.'/third_party/QuickBooks-V3-PHP-SDK/config.php');

$dataService = DataService::Configure(array(
	'auth_mode' 			=> 'oauth2',
	'ClientID' 				=> $config['client_id'],
	'ClientSecret' 			=>  $config['client_secret'],
	'RedirectURI' 			=> $config['oauth_redirect_uri'],
	'baseUrl' 				=> "development",
	'refreshTokenKey' 		=> $integrationDetails->refresh_token,
	'QBORealmID' 			=> $integrationDetails->realmid,
));
/** Update the OAuth2Token of the dataService object **/
$OAuth2LoginHelper 		 = $dataService->getOAuth2LoginHelper();
$refreshedAccessTokenObj = $OAuth2LoginHelper->refreshToken();
$dataService->updateOAuth2Token($refreshedAccessTokenObj);	
$error 		 = $dataService->getLastError();
if ($error) 
{
	$errorMsg = "The Status code is: " . $error->getHttpStatusCode() . "\n";
	$errorMsg.= "The Helper message is: " . $error->getOAuthHelperError() . "\n";
	$errorMsg.= "The Response message is: " . $error->getResponseBody() . "\n";
	$output['success'] 		 = false;
	$output['error_message'] = $errorMsg;
	return $output;
}
else
{
	$access_token 	= $refreshedAccessTokenObj->getAccessToken();
	$refresh_token 	= $refreshedAccessTokenObj->getRefreshToken();
	$realm_id 		= $refreshedAccessTokenObj->getRealmID();
	
	$qbdata['accessToken']   	= $access_token;
	$qbdata['refreshToken']     = $refresh_token;
	$qbdata['realmId']   		= $realm_id;
	
	$output['success'] 		 = true;
	$output['error_message'] = '';
	$output['qbdata'] 		 = $qbdata;
	$output['dataService'] 	 = $dataService;
	return $output;
}
?>
