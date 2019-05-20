<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

include(APPPATH.'/third_party/QuickBooks-V3-PHP-SDK/src/config.php');
use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\PlatformService\PlatformService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;   

    
	/*
		Get Tokens From Database
	*/

	function getIntegrationDetails($userId = NULL)
    {
        $ci = & get_instance();
        $ci->db->select('*');
        $ci->db->from('qb_tokens');
		if($userId != NULL){
			$ci->db->where('user_id',$userId);
		}
        $query    	= $ci->db->get();
        $result     = array();
        if($query->num_rows() > 0)
        {
            $result	= $query->row();
        }
        return $result;
    }

    
	function updateAccessToken($type = NULL,$userId = NULL)
    {		
		$integrationDetails = getIntegrationDetails();

		//echo "<pre>"; print_r($integrationDetails); die;

		if( !empty($integrationDetails) && $integrationDetails->refresh_token != '' )
		{
			$responseData = include(APPPATH.'/third_party/QuickBooks-V3-PHP-SDK/getApis/refreshtoken.php');

			echo "<pre>"; print_r($responseData); die;


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
			//
		}
		
	}
	    
	
?>
