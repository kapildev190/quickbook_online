<?php

require_once dirname(__FILE__) . '/config.php';

//require_once dirname(__FILE__) . '/views/header.tpl.php';

?>



<?php

$VendorService = new QuickBooks_IPP_Service_Vendor();
$vendor_id	=	$_POST['ven_id'];
$vendors = $VendorService->query($Context, $realm, "SELECT * FROM Vendor WHERE Id = '$vendor_id'");
$vendor = $vendors[0];
return $vendor;
//print_r($terms);

//foreach ($vendors as $Vendor)
//{
	//print_r($Term);

	//print('Vendor Id=' . $Vendor->getId() . ' is named: ' . $Vendor->getDisplayName() . '<br>');
//}

/*
print("\n\n\n\n");
print('Request [' . $IPP->lastRequest() . ']');
print("\n\n\n\n");
print('Response [' . $IPP->lastResponse() . ']');
print("\n\n\n\n");
*/

?>


<?php

require_once dirname(__FILE__) . '/views/footer.tpl.php';

?>
