<?php
ini_set("display_errors", "1");
error_reporting(E_ALL);
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\Facades\Customer;
if(isset($_POST))
{
	$companyname 				= 	isset($_POST['company_name']) ? trim($_POST['company_name'])   : '';
	$displayName				= 	isset($_POST['company_name_as']) ? $_POST['company_name_as']   : '';
	$phoneNo 	 				= 	isset($_POST['company_contact']) ? $_POST['company_contact']   : '';
	$email		 				= 	isset($_POST['company_email'])   ? $_POST['company_email'] 	   : '';

	$theResourceObj = Customer::create([
		"FullyQualifiedName" => $companyname,
		"CompanyName" 		 => $companyname,
		"DisplayName" 		 => $displayName,
		"PrimaryPhone" => [
			"FreeFormNumber" => $phoneNo
		],
		"PrimaryEmailAddr" => [
			"Address" => $email
		]
	]);
	$response 	  = array();
	$resultingObj = $dataService->Add($theResourceObj);
	$error 		  = $dataService->getLastError();
	if ($error) {
		$response['success'] 	 = false;
		$response['error_msg'] 	 =  $error->getResponseBody() . "\n";
	}
	else 
	{
		$response['success'] 	 = true;
		$response['last_cus_id'] = $resultingObj->Id;
	}
	return $response;exit;
}
