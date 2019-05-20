<?php
use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\PlatformService\PlatformService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\Facades\CreditMemo;
use QuickBooksOnline\API\Facades\Item;
//echo "SELECT * FROM Item where ParentRef  > '0' and NOT Type = 'Category' order by Name asc"; die;
$items = $dataService->query("SELECT * FROM Item where Type IN ('Service','NonInventory') order by Name asc");
$data2 = array();
if(!empty($items))
{
	foreach ($items as $key => $item)
	{
		$data2[$key]['itemid']  							= $item->Id;
		$data2[$key]['itemname']  						= $item->Name;
		$data2[$key]['Description']  						= $item->Description;
		$data2[$key]['FullyQualifiedName']  	= $item->FullyQualifiedName;
		$data2[$key]['Type']  								= $item->Type;
		$data2[$key]['IncomeAccountRef']  		= $item->IncomeAccountRef;
		$data2[$key]['IncomeAccountRef_name'] = "";
	}
}
return $data2;exit;
?>
