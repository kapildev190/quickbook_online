<?php
use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\PlatformService\PlatformService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\Facades\CreditMemo;
$creditmemoid				=	$_POST['creditmemoid'];
$CreditMemo = CreditMemo::create([
    "Id" => $creditmemoid,
    "SyncToken" => "0"
]);
$currentResultObj = $dataService->Delete($CreditMemo);
if ($currentResultObj)
{
	$response['success'] 	 		= true;
	$response['success_msg'] 	= 'CreditMemo Deleted Successfully';
}
else
{
	$response['success'] 	 		 = false;
	$error                     = $dataService->getLastError();
	$response['error_msg'] 	   = $error->getResponseBody();
}
return $response;exit;
?>
