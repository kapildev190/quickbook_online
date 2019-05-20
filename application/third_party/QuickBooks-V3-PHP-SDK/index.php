<?php
ini_set("display_errors", "1");
error_reporting(E_ALL);
include('src/config.php');
use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\PlatformService\PlatformService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
$config = include('config.php');
$dataService  = DataService::Configure(array(
                'auth_mode' => 'oauth2',
                'ClientID' => $config['client_id'],
                'ClientSecret' =>  $config['client_secret'],
                'RedirectURI' => $config['oauth_redirect_uri'],
                'scope' => $config['oauth_scope'],
                'baseUrl' => $config['base_url'],
            ));

$OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
$url = $OAuth2LoginHelper->getAuthorizationCodeURL();

if (isset($_GET["code"]))
{

    $code    = $_GET["code"];
    $realmId = $_GET["realmId"];
    $accessToken = $OAuth2LoginHelper->exchangeAuthorizationCodeForToken($code, $realmId);
    $dataService->updateOAuth2Token($accessToken);
    $error = $OAuth2LoginHelper->getLastError();
    if ($error != null)
    {
        echo "The Status code is: " . $error->getHttpStatusCode() . "\n";
        echo "The Helper message is: " . $error->getOAuthHelperError() . "\n";
        echo "The Response message is: " . $error->getResponseBody() . "\n";
        return;
    }
    $_SESSION["realmID"]         = $accessToken->getRealmID();
    $_SESSION["getAccessToken"]  = $accessToken->getAccessToken();
    $_SESSION["getRefreshToken"] = $accessToken->getRefreshToken();
    $_SESSION["qblogin"]         = 'yes';
  
    $qbData = $this->Common_model->addAccessTokenQbtoDb();
    $redirectUrl                 =  site_url('dashboard');
    $quickbooks_is_connected = true;
    header("Location: $redirectUrl");
    exit();
}
// else if (isset($_SESSION["realmID"]))
// {
//     $_SESSION["qblogin"] = 'yes';
//     $qbData = $this->Common_model->addAccessTokenQbtoDb();
//     //$redirectUrl                 =  site_url('invoices');
//    // //$quickbooks_is_connected = true;
//    // header("Location: $redirectUrl");
// }
else
{
  $quickbooks_is_connected = false;
    ?>
    <div class="clsCont" style="text-align: center; padding: 8px;">
      <br>
      <br>
        <a href="<?php echo $url; ?>"><img height="100" width="150" src="<?php echo site_url().'assets/images/QB_IntuitLogo_Vert.png'?>"/></a>
      <br>
      <br>
      You must authenticate to QuickBooks <b>once</b> before you can exchange data with it. <br>
      <br>
      <strong>You only have to do this once!</strong> <br><br>

      After you've authenticated once, you never have to go
      through this connection process again. <br>
      Click the button above to
      authenticate and connect.
    </div>

    <?php
}
die();
 ?>
