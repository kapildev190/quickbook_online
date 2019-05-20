<?php
	require_once dirname(__FILE__) . '/config.php';
	$deleteQbId			=	$_POST['deleteQbId'];
	$VendorService = new QuickBooks_IPP_Service_Vendor();
	$Vendors = $VendorService->query($Context, $realm, "SELECT * FROM Vendor WHERE Id = '$deleteQbId' ");
	$Vendor = $Vendors[0];
	//echoprintcommand($Vendor); //die('I am dead');
	//die('dead');
	$Vendor->setActive(False);
	$Vendor->setGivenName('tes2tooye');
	$Vendor->setFamilyName('tes2tooye');
	$Vendor->setDisplayName('tes2tooey');
	$response = array();
	if ($resp = $VendorService->update($Context, $realm, $deleteQbId, $Vendor))
	{
		//echoprintcommand($resp); die('dead ravo');
		$response['success'] 	 			= true;
		$response['success_msg'] 		= "Deleted";
	}
	else
	{
		$response['success'] 	 = false;
		$response['error_msg'] 	 = $VendorService->lastError($Context);
		//print($VendorService->lastError($Context));
	}
return $response;exit;
require_once dirname(__FILE__) . '/views/footer.tpl.php';
