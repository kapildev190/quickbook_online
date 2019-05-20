<?php
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\Facades\Invoice;

$itemsCount = $dataService->query("SELECT count(*) FROM Invoice");
$invoices 	= $dataService->query("SELECT * FROM Invoice ORDER BY Metadata.CreateTime DESC MAXRESULTS $itemsCount");
return $invoices;exit;
?>
