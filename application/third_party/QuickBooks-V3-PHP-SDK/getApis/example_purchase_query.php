<?php

require_once dirname(__FILE__) . '/config.php';

//require_once dirname(__FILE__) . '/views/header.tpl.php';

?>

<?php

$PurchaseService = new QuickBooks_IPP_Service_Purchase();

$purchases = $PurchaseService->query($Context, $realm, "SELECT * FROM Purchase STARTPOSITION 1 MAXRESULTS 10");
//print_r($purchases);die;
return $purchases;

/*
print("\n\n\n\n");
print('Request [' . $IPP->lastRequest() . ']');
print("\n\n\n\n");
print('Response [' . $IPP->lastResponse() . ']');
print("\n\n\n\n");
*/

?>



<?php

//require_once dirname(__FILE__) . '/views/footer.tpl.php';

?>
