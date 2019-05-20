<?php
ini_set("display_errors", "1");
error_reporting(E_ALL);
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\Facades\Customer;
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
$subCompanyId  		= $this->input->post('subCompanyId');
//echoprintcommand($subCompanyId); die;
if(!empty($subCompanyId))
{
	$response = array();
	foreach ($subCompanyId as $key=>$companyId)
	{
			if($companyId != '')
			{
								$companyname 							= 	isset($subCompanyName[$key]) ? trim($subCompanyName[$key])   : '';
								$displayName							= 	$companyname;
								$title 										= 	'';
								$givenName  							= 	'';
								$middleName  							= 	'';
								$familyName 							= 	'';
								$phoneNo 	 								= 	isset($subPhoneNo[$key]) 			 	? $subPhoneNo[$key]				   : '';
								$mobileNo 	 							= 	'';
								$fax 											= 	isset($subFaxno[$key]) 					? $subFaxno[$key] 					   : '';
								$email		 								= 	isset($subEmail[$key]) 				   ? $subEmail[$key]			   : '';
								$billing_line1		 				= 	isset( $subStreetAddress[$key] ) 		   ? $subStreetAddress[$key] 		   : '';
								$billing_line2		 				= 	 '';
								$billing_city		 	 				= 	isset($subCity[$key]) 		   ? $subCity[$key]	   : '';
								$billing_country_code			= 	isset($subCountry[$key])    ? $subCountry[$key]    : '';
								$countryCode 							= 	getCountryNameByCodeId($billing_country_code,'country');
								$countryNm 					=  $stateNm = '';
								if(!empty($countryCode))
								{
									$countryNm = $countryCode->name;
								}
								$billing_country_subcode 	= 	isset( $subState[$key] ) ? $subState[$key]: '';
								$stateCode 								= 	getCountryNameByCodeId($billing_country_subcode,'state');
								if(!empty($stateCode))
								{
									$stateNm = $stateCode->state_name;
								}
								$billing_postal_code  		= 	isset($subZipCode[$key])     ? $subZipCode[$key]     : '';
								$customer_id							=		getQbiIdByCustID($companyId);
								if( $displayName == '' )
									$DissplayName = $givenName.' '.$middleName;
								else
									$DissplayName = $displayName ;
								// Get the existing customer first (you need the latest SyncToken value)
								$customer = $dataService->FindbyId('customer', $customer_id);
								// Change something
								$theResourceObj = Customer::update($customer,[
									"BillAddr" => [
											"Line1" => $billing_line1,
											"City" => $billing_city,
											"Country" => $countryNm,
											"CountrySubDivisionCode" => $stateNm,
											"PostalCode" => $billing_postal_code
									],
									//"Notes" => $notes,
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

								$resultingObj = $dataService->Update($theResourceObj);
								$error = $dataService->getLastError();
								if ($error) {
									$response['success_company'] 	 		=  false;
									$response['error_msg'] 	  =  $error->getResponseBody() . "\n";
								}
								else {
									$response['success_company'] 	 = true;
									$response['last_cus_id'] = $resultingObj->Id;
									$response['displayName'] = $resultingObj->DisplayName;
									$response['success_msg'] = 'Our new customer ID is: [' . $resultingObj->Id . '] (name "' . $resultingObj->CompanyName . '")';
								}
			}
			else
			{
								$companyname 				= 	isset($subCompanyName[$key]) ? trim($subCompanyName[$key])   : '';
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
								$stateCode 				= getCountryNameByCodeId($billing_country_subcode,'state');
								if(!empty($stateCode))
								{
									$stateNm = $stateCode->state_name;
								}
								$billing_postal_code  		= 	isset($subZipCode[$key])     ? $subZipCode[$key]     : '';
								if( $displayName == '' )
									$DissplayName = $givenName.' '.$middleName;
								else
									$DissplayName = $displayName ;
								$theResourceObj = Customer::create([
									"BillAddr" => [
											"Line1" => $billing_line1,
											"City" => $billing_city,
											"Country" => $countryNm,
											"CountrySubDivisionCode" => $stateNm,
											"PostalCode" => $billing_postal_code
									],
									//"Notes" => $notes,
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
									$response['newlast_cus_id'][] = $resultingObj->Id;
									$response['newCompanydisplayName'][] = $resultingObj->DisplayName;
									$response['success_msg'] = 'Our new customer ID is: [' . $resultingObj->Id . '] (name "' . $resultingObj->DisplayName . '")';
								}
			}
	}
			return $response;exit;
}
