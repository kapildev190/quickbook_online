<?php
error_reporting(0);
use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\PlatformService\PlatformService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\WebhooksService;
use QuickBooksOnline\API\Facades\Vendor;
$vendors = $dataService->query("SELECT * FROM Vendor ORDER BY displayName ASC MAXRESULTS 400");
//$vendors = $dataService->query("SELECT * FROM Vendor");
$data = array();
foreach ($vendors as $key => $Vendor)
{
	if($Vendor->BillAddr)
		$data[$key]['streetAddress'] = $Vendor->BillAddr->Line1;
	else
		$data[$key]['streetAddress'] = '';

	if($Vendor->BillAddr)
		$data[$key]['City'] = $Vendor->BillAddr->City;
	else
		$data[$key]['City'] = '';

	if($Vendor->BillAddr)
		$data[$key]['PostalCode'] = $Vendor->BillAddr->PostalCode;
	else
		$data[$key]['PostalCode'] = '';

	$country 					=  $state = '';
	if($Vendor->BillAddr)
	{
		$getCountry = $Vendor->BillAddr->Country;
		$countryCode 							= 	getCountryCodeByCountryName($getCountry,'country');
		if(!empty($countryCode))
		{
			$country = $countryCode->id;
			$data[$key]['country'] = $country;
		}
		else
		{
			$data[$key]['country'] = $country;
		}
	}
	else
	{
		$data[$key]['country'] = $country;
	}

	if($Vendor->BillAddr)
	{
		$getsate = $Vendor->BillAddr->CountrySubDivisionCode;
		$sateCode 							= 	getCountryCodeByCountryName($getsate,'state');
		if(!empty($sateCode))
		{
			$state = $sateCode->state_id;
			$data[$key]['state'] = $state;
		}
		else
		{
			$data[$key]['state'] = $state;
		}
	}
	else
	{
		$data[$key]['state'] = $state;
	}

	$data[$key]['vendorId']  		= $Vendor->Id;
	$data[$key]['title']  			= $Vendor->Title;
	$data[$key]['firstName'] 	 	= $Vendor->GivenName;
	$data[$key]['middleName']   = $Vendor->MiddleName;
	$data[$key]['lastName']  	= $Vendor->FamilyName;
	$data[$key]['suffix']  		= $Vendor->Suffix;
	$data[$key]['displayName'] = $Vendor->DisplayName;
	$data[$key]['company'] = $Vendor->CompanyName;
	$data[$key]['URI'] = $Vendor->URI;
	if($Vendor->PrimaryPhone)
		$data[$key]['phoneNumber'] = $Vendor->PrimaryPhone->FreeFormNumber;
	else
		$data[$key]['phoneNumber'] = '';
	if($Vendor->Mobile)
		$data[$key]['mobile'] = $Vendor->Mobile->FreeFormNumber;
	else
		$data[$key]['mobile'] = '';
	if($Vendor->Fax)
		$data[$key]['fax'] = $Vendor->Fax->FreeFormNumber;
	else
		$data[$key]['fax'] = '';
	if($Vendor->AlternatePhone)
		$data[$key]['others'] = $Vendor->AlternatePhone->FreeFormNumber;
	else
		$data[$key]['others'] = '';
	if($Vendor->PrimaryEmailAddr)
		$data[$key]['email'] = $Vendor->PrimaryEmailAddr->Address;
	else
		$data[$key]['email'] = '';

	$data[$key]['termRef'] = $Vendor->TermRef;
	$data[$key]['AcctNum'] = $Vendor->AcctNum;
	$data[$key]['taxId'] 	 = $Vendor->TaxIdentifier;
	if( $Vendor->MetaData )
	{
		$data[$key]['CreateTime']  = $Vendor->MetaData->CreateTime;
		$data[$key]['LastUpdatedTime']  = $Vendor->MetaData->LastUpdatedTime;
	}
	else
	{
		$data[$key]['CreateTime']  = '';
		$data[$key]['LastUpdatedTime']  = '';
	}
}
return $data;exit;
?>
