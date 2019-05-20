<?php
	error_reporting(0);
	use QuickBooksOnline\API\Core\ServiceContext;
	use QuickBooksOnline\API\DataService\DataService;
	use QuickBooksOnline\API\PlatformService\PlatformService;
	use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
	use QuickBooksOnline\API\WebhooksService;
	use QuickBooksOnline\API\Facades\Vendor;
	$title			=	$_POST['title'];
	$firstName	=	$_POST['firstName'];
	$lastName		=	$_POST['lastName'];
	$middleName	=	$_POST['middleName'];
	$suffix  		=	$_POST['suffix'];
	$displayName=	$_POST['displayName'];
	$phoneNo		=	$_POST['phoneNo'];
	$mobileNo		=	$_POST['mobileNo'];
	$fax				=	$_POST['fax'];
	$companyName=	$_POST['companyName'];
	$street			=	$_POST['street'];
	$city				=	$_POST['city'];
	$zip				=	$_POST['zip'];
	$email			=	$_POST['email'];
	$others			=	$_POST['others'];
	$websAdd		=	$_POST['webAddr'];
	$valTerm		=	$_POST['valTerm'];
	$billing_country_code	= 	isset($_POST['country'])    ? $_POST['country']    : '';
	$countryCode= 	getCountryNameByCodeId($billing_country_code,'country');
	$country 		=  $state = '';
	if(!empty($countryCode))
	{
		$country = $countryCode->name;
	}
	$billing_country_subcode 	= 	isset($_POST['state']) ? $_POST['state'] : '';
	$stateCode 								= 	getCountryNameByCodeId($billing_country_subcode,'state');
	if(!empty($stateCode))
	{
		$state = $stateCode->state_name;
	}
	$accountNo	=	$_POST['accountNo'];
	$taxId			=	$_POST['taxId'];
	if( $displayName == '' )
		$DissplayName = $givenName.' '.$middleName;
	else
		$DissplayName = $displayName ;

	//Add a new Vendor
	$theResourceObj = Vendor::create([
    "BillAddr" => [
        "Line1" => $street,
        "City" => $city,
        "Country" => $country,
        "CountrySubDivisionCode" => $state,
        "PostalCode" => $zip
    ],
    "Title" => $title,
    "GivenName" => $firstName,
  	"MiddleName" => $middleName,
    "FamilyName" => $lastName,
    "Suffix" => $suffix,
    "CompanyName" => $companyname,
    "DisplayName" => $DissplayName,
    "PrintOnCheckName" => $companyname,
		"TaxIdentifier"=>$taxId,
    "AcctNum"=> $accountNo,
    "TermRef"=> $valTerm,
		"Fax"=> $fax,
		"AlternatePhone"=> $others,
    "PrimaryPhone" => [
        "FreeFormNumber" => $phoneNo
    ],
    "Mobile" => [
        "FreeFormNumber" => $mobileNo
    ],
    "PrimaryEmailAddr" => [
        "Address" => $email
    ],
    "WebAddr" => [
        "URI" => $WebAddr
    ]
]);
$response = array();
$resultingObj = $dataService->Add($theResourceObj);
$error = $dataService->getLastError();
if ($error) {
		$response['success'] 	 = false;
		$response['error_msg'] 	 =  $error->getResponseBody() . "\n";
}
else {
		$response['success'] 	 = true;
		$response['last_cus_id'] = $resultingObj->Id;
		$response['vendor_name'] = $resultingObj->DisplayName;
}
return $response;exit;
