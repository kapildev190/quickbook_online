<?php
use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\PlatformService\PlatformService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\Facades\Invoice;
$invoiceid		=	$_POST['invoiceid'];
// $the_invoice_to_delete = '{-'.$invoiceid.'}';
// $retr = $dataService->delete($the_invoice_to_delete);
$invoice = Invoice::create([
    "Id" => $invoiceid,
    "SyncToken" => "0"
]);
$currentResultObj = $dataService->Delete($invoice);
if ($currentResultObj)
{
	$response['success'] 	 = true;
	$response['success_msg'] = ' Invoice Deleted Successfully';
}
else
{
	$response['success'] 	     = false;
  $error                     = $dataService->getLastError();
	$response['error_msg'] 	   = $error->getResponseBody();
}
return $response;exit;
?>
