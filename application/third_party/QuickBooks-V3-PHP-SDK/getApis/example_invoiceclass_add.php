<?php
error_reporting(0);
use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\PlatformService\PlatformService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\WebhooksService;
use QuickBooksOnline\API\Facades\QuickBookClass;
		$Name				=   $_POST['title'];
	  $theResourceObj = QuickBookClass::create([
			 "Name" => $Name,
	 ]);
	 $resultingObj = $dataService->Add($theResourceObj);
	 $error = $dataService->getLastError();
	 $response = array();
	 if ($error)
	 {
			 $response['success'] 	 		= false;
			 $response['error_msg'] 	 	= $error->getResponseBody();
	 }
	 else
	 {
		 $response['success'] 	    = true;
		 $response['classID']  			= $resultingObj->Id;
		 $response['success_msg'] 	= 'Class Added Successfully';
	 }
	return $response;exit;
