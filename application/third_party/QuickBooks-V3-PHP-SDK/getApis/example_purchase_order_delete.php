<?php
use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\PlatformService\PlatformService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\Facades\PurchaseOrder;
$purchaseOrderId		=	$_POST['purchaseOrderId'];
//echoprintcommand($_POST); die();
// $the_invoice_to_delete = '{-'.$invoiceid.'}';
// $retr = $dataService->delete($the_invoice_to_delete);
$purchaseOrder = PurchaseOrder::create([
    "Id" => $purchaseOrderId,
    "SyncToken" => "0"
]);

$currentResultObj = $dataService->Delete($purchaseOrder);
if ($currentResultObj)
{
	$response['success'] 	 = true;
	$response['success_msg'] = ' Purchase Order Deleted Successfully';
}
else
{
	$response['success']   = false;
  $error                 = $dataService->getLastError();
	$response['error_msg'] = $error->getResponseBody();
}
return $response;
exit;
?>
