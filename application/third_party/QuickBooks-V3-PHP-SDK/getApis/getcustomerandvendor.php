<?php

require_once dirname(__FILE__) . '/config.php';
?>

<?php
//require_once dirname(__FILE__) . '/views/header.tpl.php';

$CustomerService = new QuickBooks_IPP_Service_Customer();
$VendorService = new QuickBooks_IPP_Service_Vendor();
$Customers = $CustomerService->query($Context, $realm, "SELECT Id,DisplayName FROM Customer");
$customer=$Customers[0];
$Vendors = $VendorService->query($Context, $realm, "SELECT Id,DisplayName FROM Vendor");
$vendor=$Vendors[0];
$data = array();
$data =array_merge($Customers,$Vendors);
return $data;exit;
?>

//require_once dirname(__FILE__) . '/views/footer.tpl.php';
