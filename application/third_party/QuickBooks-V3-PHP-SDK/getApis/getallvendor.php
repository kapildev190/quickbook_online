<?php

require_once dirname(__FILE__) . '/config.php';

//require_once dirname(__FILE__) . '/views/header.tpl.php';
?>

<?php
$VendorService = new QuickBooks_IPP_Service_Vendor();

$vendors = $VendorService->query($Context, $realm, "SELECT * FROM Vendor");

//$vendordata = array();

//foreach ($vendors as $key => $Vendor)
//{
//	$vendordata[$key]['vendorId']  = $Vendor->getId();
//	$vendordata[$key]['fullyQname']  = $Vendor->getFullyQualifiedName();
//	$vendordata[$key]['displayName'] = $Vendor->getDisplayName();
	
	
//}
return $vendors;exit;
?>


