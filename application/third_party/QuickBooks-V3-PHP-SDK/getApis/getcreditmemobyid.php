<?php

require_once dirname(__FILE__) . '/config.php';

//require_once dirname(__FILE__) . '/views/header.tpl.php';

?>



<?php
$cmId			=	$_POST['cmId'];
$CreditMemoService	=	new QuickBooks_IPP_Service_CreditMemo();
$CMS			=	$CreditMemoService->query($Context, $realm, "SELECT * FROM CreditMemo where Id='$cmId'");
$CM			=	$CMS[0];
return $CM;
//print_r($terms);


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
