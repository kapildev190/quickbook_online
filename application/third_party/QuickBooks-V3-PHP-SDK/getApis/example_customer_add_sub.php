<?php
ini_set("display_errors", "1");
error_reporting(E_ALL);
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\Facades\Customer;
if(isset($_POST) && $_POST['isValidForm'] == 'yes')
{
	$subCompanyName 	= $this->input->post('subCompanyName');
	$subStreetAddress = $this->input->post('subStreetAddress');
	$subCountry       = $this->input->post('subCountry');
	$subState         = $this->input->post('subState');
	$subCity          = $this->input->post('subCity');
	$subZipCode       = $this->input->post('subZipCode');
	$subPhoneNo       = $this->input->post('subPhoneNo');
	$subFaxno         = $this->input->post('subFaxno');
	$subEmail         = $this->input->post('subEmail');
	$subRole          = $this->input->post('subRole');
	$isParent         = false;
	$isSubCompany     = true;
	$data = array();
	if(!empty($subCompanyName)){
	$response = array();
	foreach ($subCompanyName as $key=>$val)
	{
			$companyname 				= 	isset($val) ? trim($val)   : '';
			$displayName				= 	$companyname;
			$title 							= 	'';
			$givenName  				= 	'';
			$middleName  				= 	'';
			$familyName 				= 	'';
			$phoneNo 	 					= 	isset($subPhoneNo[$key]) 			 	? $subPhoneNo[$key]				   : '';
			$mobileNo 	 				= 	'';
			$fax 								= 	isset($subFaxno[$key]) 					? $subFaxno[$key] 					   : '';
			$email		 					= 	isset($subEmail[$key]) 				   ? $subEmail[$key]			   : '';
			$billing_line1		 	= 	isset( $subStreetAddress[$key] ) 		   ? $subStreetAddress[$key] 		   : '';
			$billing_line2		 	= 	 '';
			$billing_city		 	 	= 	isset($subCity[$key]) 		   ? $subCity[$key]	   : '';
			$billing_country_code		= 	isset($subCountry[$key])    ? $subCountry[$key]    : '';
			$countryCode 				= getCountryNameByCodeId($billing_country_code,'country');
			$countryNm 					=  $stateNm = '';
			if(!empty($countryCode))
			{
				$countryNm = $countryCode->name;
			}
			$billing_country_subcode 	= 	isset( $subState[$key] ) ? $subState[$key]: '';
			$stateCode 								= getCountryNameByCodeId($billing_country_subcode,'state');
			if(!empty($stateCode))
			{
				$stateNm = $stateCode->state_name;
			}
			$billing_postal_code  		= 	isset($subZipCode[$key])     ? $subZipCode[$key]     : '';
			$notes  									= 	'';
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
				"GivenName" => $givenName,
				"MiddleName" => $middleName,
				"FamilyName" => $familyName,
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
			$resultingObj = $dataService->Add($theResourceObj);
			$error = $dataService->getLastError();
			if ($error) {
					$response['success_company'] 	 = false;
					$response['error_msg'] 	 =  $error->getResponseBody() . "\n";
			}
			else
			{
				$response['success_company'] 	 = true;
				$response['last_cus_id'][] = $resultingObj->Id;
				$response['CompanydisplayName'][] = $resultingObj->DisplayName;
				$response['success_msg'] = 'Our new customer ID is: [' . $resultingObj->Id . '] (name "' . $resultingObj->DisplayName . '")';
			}
		}
		return $response;exit;
	}
}
