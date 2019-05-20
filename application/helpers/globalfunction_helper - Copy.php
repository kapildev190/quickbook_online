<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

include(APPPATH.'/third_party/QuickBooks-V3-PHP-SDK/src/config.php');
use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\PlatformService\PlatformService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;   

    function generateRef()
    {
        $length         = 8;
        $randomString   = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
        $randomString1  = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
        $randomString   = $randomString . $randomString1;
        return $randomString;
    }

    function randomPassword()
    {
        $alphabet       = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass           = array(); //remember to declare $pass as an array
        $alphaLength    = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n      = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

	function getSomeFields( $fields = null, $condition = null,$table = null )
    {
		$ci = & get_instance();
        $ci->db->select($fields);
        $ci->db->where($condition);
        $query  = $ci->db->get($table);
        $result = array();
        if( $query->num_rows() > 0 )
        {
            $result = $query->row();
        }
        return $result;
    }
	
    function getLastCreateDate($type = NULL,$userId = NULL,$table = NULL,$service_organisation_id = NULL)
    {
		if( $table == NULL )
			$table = 'companies';
        $ci = & get_instance();
        $ci->db->select('createDateTime');
        $ci->db->from($table);
        $ci->db->where('user_id',$userId);
        $ci->db->where('type',$type);
		if( $service_organisation_id != null )
			$ci->db->where('service_organisation_id',$service_organisation_id);	
		$ci->db->order_by("createDateTime", "desc");
		$ci->db->limit(1);
        $query    	= $ci->db->get();
        $createDateTime = '';
        if($query->num_rows() > 0)
        {
            $createDateTime	= $query->row()->createDateTime;
        }
        return $createDateTime;
    }
	
	function getIntegrationDetailsForCron($type = NULL)
    {
        $ci = & get_instance();
        $ci->db->select('*');
        $ci->db->from('integrationsdetails');
		$ci->db->where('type',$type);
        $query    	= $ci->db->get();
        $result     = array();
        if($query->num_rows() > 0)
        {
            $result	= $query->result();
        }
        return $result;
    }
	
    function getIntegrationDetails($type = NULL,$userId = NULL)
    {
        $ci = & get_instance();
        $ci->db->select('*');
        $ci->db->from('integrationsdetails');
        $ci->db->where('user_id',$userId);
		if($type != 'both')
			$ci->db->where('type',$type);
        $query    	= $ci->db->get();
        $result     = array();
        if($query->num_rows() > 0)
        {
            $result	= $query->row();
        }
        return $result;
    }
	function getXeroDetails(){
		$ci = & get_instance();
		$ci->db->select('*');
		$ci->db->from('mook_adminSetting');
		$result = 	$ci->db->get();
		//echo $this->db->last_query();
		$result = $result->result();
		return $result;
	 }
	 
	function getIntegrationAuthUrl($type = NULL,$userId = NULL)
    {
		if( $type == 'Quickbooks' )
		{
			$config = include(APPPATH.'/third_party/QuickBooks-V3-PHP-SDK/config.php');
			$dataService = DataService::Configure(array(
				'auth_mode' => 'oauth2',
				'ClientID' => $config['client_id'],
				'ClientSecret' =>  $config['client_secret'],
				'RedirectURI' => $config['oauth_redirect_uri'],
				'scope' => $config['oauth_scope'],
				'baseUrl' => $config['base_url'],
				'state' => base64_encode($userId),
			));
			$OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
			return $authUrl = $OAuth2LoginHelper->getAuthorizationCodeURL();
		}
		else
		{
			require_once(APPPATH . 'third_party/xerophp/public.php');
		}
    }
	
	function updateAccessToken($type = NULL,$userId = NULL)
    {
		if( $type == 'Quickbooks' )
		{
			$integrationDetails = getIntegrationDetails('Quickbooks',$userId);
			if( !empty($integrationDetails) && $integrationDetails->refreshToken != '' )
			{
				$responseData = include(APPPATH.'/third_party/QuickBooks-V3-PHP-SDK/getApis/refreshtoken.php');
				if( $responseData['success'] )
				{					
					$ci 			= & get_instance();
					$ci->db->where('id',$integrationDetails->id);
					$result = $ci->db->update("integrationsdetails",$responseData['qbdata']);
					if($result)
					{
						$output['success'] 		 = true;
						$output['error_message'] = '';
						$output['dataService']   = $dataService;
					}
					else
					{
						$output['success'] 		 = true;
						$output['error_message'] = '';
					}
					return $output;
				}
				else
				{
					return $responseData;
				}
			}
			else
			{
				$output['authUrl']       = getIntegrationAuthUrl('Quickbooks',$userId);
				$output['success'] 		 = false;
				$output['error_message'] = "Please authorize your quickbooks account first. <a href='".$output['authUrl']."'>Click here </a>to authorize QuickBooks.";
				return $output;
			}
		}
		else if( $type == 'Xero' )
		{
			$integrationDetails = getIntegrationDetails('Xero',$userId);
			//echo "<pre>";print_r($integrationDetails);
			if( !empty($integrationDetails) && $integrationDetails->accessToken != ''  && $integrationDetails->sessionHandle != '' )
			{
				include_once(APPPATH . 'third_party/xerophp/public.php');
				$response = $XeroOAuth->refreshToken($integrationDetails->accessToken, $integrationDetails->sessionHandle);
				//echo "<pre>";print_r($response);die('lol');
				if ($XeroOAuth->response['code'] == 200) 
				{
					$session 		= persistSession($response);
					$oauthSession 	= retrieveSession();
								
					$XeroOAuth->config['access_token']  	  = $oauthSession['oauth_token'];
					$XeroOAuth->config['access_token_secret'] = $oauthSession['oauth_token_secret'];
					$XeroOAuth->config['session_handle'] 	  = $oauthSession['oauth_session_handle'];
					
					$xeroData  = array();
					$xeroData['accessToken']   	= $oauthSession['oauth_token'];
					$xeroData['refreshToken']   = $oauthSession['oauth_token_secret'];
					$xeroData['sessionHandle']	= $integrationDetails->sessionHandle;
					
					$ci 			= & get_instance();
					$ci->db->where('id',$integrationDetails->id);
					$result = $ci->db->update("integrationsdetails",$xeroData);
					if($result)
					{
						$output['success'] 		 = true;
						$output['error_message'] = '';
						$output['XeroOAuth']     = $XeroOAuth;
					}
					else
					{
						$output['success'] 		 = true;
						$output['error_message'] = '';
					}
					return $output;
				}
				else if ($XeroOAuth->response['code'] == 401) 
    			{
    				$output['success'] 		 = false;
    				$output['error_message'] = "Token expired. Please authorize again.";
    				return $output;
    			}
    			else
    			{
    				$output['success'] 		 = false;
    				$output['error_message'] = $XeroOAuth->response['response'];
    				return $output;
    			}
			}
		}
	}

    function updateLastSync($integrationId = NULL)
    {
		if($integrationId == null)
			return false;
		$ci 			= & get_instance();
		$ci->db->where('id',$integrationId);
		$ci->db->set('lastSync',date('Y-m-d h:i:s'));		
		$result = $ci->db->update("integrationsdetails");
	}
	
?>
