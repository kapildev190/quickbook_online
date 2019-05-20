<?php
use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\PlatformService\PlatformService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\Facades\Invoice;
use QuickBooksOnline\API\Facades\Item;
$invoiceid	=	$_POST['invoiceid'];
$invoices = $dataService->query("SELECT * FROM Invoice WHERE Id = '$invoiceid' ");
$Invoice = $invoices[0];
//echoprintcommand(count($Invoice->Line)); die;
return $Invoice;

?>
