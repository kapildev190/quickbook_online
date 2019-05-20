<?php
use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\PlatformService\PlatformService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\Facades\TaxService;
use QuickBooksOnline\API\Facades\TaxRate;
$terms = $dataService->query("SELECT * FROM Term");
$dataTerm = array();
foreach ($terms as $key => $Term)
{
	$dataTerm[$key]['termid']  = $Term->Id;
	$dataTerm[$key]['fullname']  = $Term->Name;
}
return $dataTerm;exit;
