<?php
use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\PlatformService\PlatformService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\Facades\PurchaseOrder;
use QuickBooksOnline\API\Facades\Line;
use QuickBooksOnline\API\Facades\Item;
$purchaseorderid	=	$_POST['purchaseorderid'];
$purchaseorder    = $dataService->query("SELECT * FROM PurchaseOrder WHERE Id = '$purchaseorderid' ");
$purchaseorderd   = $purchaseorder[0];
return $purchaseorderd;
?>
