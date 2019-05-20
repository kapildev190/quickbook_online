<?php
ini_set("display_errors", "1");
error_reporting(E_ALL);
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\Facades\Customer;
if(isset($_POST) && $_POST['isValidForm'] == 'yes')
{
	$countryCode = $stateCode = array();
	$companyname 				= 	isset($_POST['parentCompany']) ? 		trim($_POST['parentCompany'])   : '';
	$displayName				= 	isset($_POST['parentCompany']) ? $_POST['parentCompany'] 			   : '';
	$title 							= 	'';
	$givenName  				= 	'';
	$middleName  				= 	'';
	$familyName 				= 	'';
	$phoneNo 	 					= 	isset($_POST['parentPhoneNo']) 			 	? $_POST['parentPhoneNo'] 				   : '';
	$mobileNo 	 				= 	'';
	$fax 								= 	isset($_POST['parentFaxNo']) 					? $_POST['parentFaxNo'] 					   : '';
	$email		 					= 	isset($_POST['parentMainEmail']) 				   ? $_POST['parentMainEmail'] 				   : '';
	$billing_line1		 	= 	isset($_POST['parentStreetAddress']) 		   ? $_POST['parentStreetAddress'] 		   : '';
	$billing_line2		 	= 	 '';
	$billing_city		 	 	= 	isset($_POST['parentCity']) 		   ? $_POST['parentCity'] 		   : '';
	$billing_country_code		= 	isset($_POST['parentCountry'])    ? $_POST['parentCountry']    : '';
	$countryCode 						= getCountryNameByCodeId($billing_country_code,'country');
	$countryNm 					=  $stateNm = '';
	if(!empty($countryCode))
	{
		$countryNm = $countryCode->name;
	}
	$billing_country_subcode 	= 	isset($_POST['parentState']) ? $_POST['parentState'] : '';
	$stateCode 				= getCountryNameByCodeId($billing_country_subcode,'state');
	if(!empty($stateCode))
	{
		$stateNm = $stateCode->state_name;
	}
	$billing_postal_code  		= 	isset($_POST['parentZipCode'])     ? $_POST['parentZipCode']     : '';

	if( $displayName == '' )
		$DissplayName = $givenName.' '.$middleName;
	else
		$DissplayName = $displayName ;
	//Add a new Vendor
	$theResourceObj = Customer::create([
    "BillAddr" => [
        "Line1" => $billing_line1,
        "City" => $billing_city,
        "Country" => $countryNm,
        "CountrySubDivisionCode" => $stateNm,
        "PostalCode" => $billing_postal_code
    ],

    "Title" => $title,
    "GivenName" => '',
  	"MiddleName" => '',
    "FamilyName" => '',
    "FullyQualifiedName" => $companyname,
    "CompanyName" => $companyname,
    "DisplayName" => $DissplayName,
    "PrimaryPhone" => [
        "FreeFormNumber" => $phoneNo
    ],
    "PrimaryEmailAddr" => [
        "Address" => $email
    ]
]);
$response = array();
$resultingObj = $dataService->Add($theResourceObj);
$error = $dataService->getLastError();
if ($error) {
		$response['success'] 	 = false;
		$response['error_msg'] 	 =  $error->getResponseBody() . "\n";
  //  echo "The Status code is: " . $error->getHttpStatusCode() . "\n";
    //echo "The Helper message is: " . $error->getOAuthHelperError() . "\n";
  	//echo "The Response message is: " . $error->getResponseBody() . "\n";
}
else {
		$response['success'] 	 = true;
		$response['last_cus_id'] = $resultingObj->Id;
		$response['displayName'] = $resultingObj->DisplayName;
		$response['success_msg'] = 'Our new customer ID is: [' . $resultingObj->Id . '] (name "' . $resultingObj->CompanyName . '")';
}
return $response;exit;
}
