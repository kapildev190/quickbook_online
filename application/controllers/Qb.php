<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include(APPPATH.'/third_party/QuickBooks-V3-PHP-SDK/src/config.php');
use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\PlatformService\PlatformService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;  

class Qb extends CI_Controller {

	/**
	 * Quickbook online integration controller.
	 *
	 * @see https://github.com/intuit/QuickBooks-V3-PHP-SDK
	 */

	public function index()
	{
		$this->load->view('welcome_message');
	}

	
	/*
		Refresh Tokens
	*/
	public function refreshTokens(){
		$integrationDetails = updateAccessToken();
		echo "<pre>"; print($integrationDetails); die;
	}

}
