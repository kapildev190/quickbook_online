<?php
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\Facades\CreditMemo;

 if($_POST['page'] == 'project-credit-memo')
 {
  // echo "Dyna";
	$ids = $_POST['ids'];
	$newIds = array();
	foreach($ids as $val)
	{
		$newIds[]= $val['qbCreditMemoId'];
	}
	$ids = "'" . implode("','", $newIds) . "'";
	$invoices = $dataService->query("SELECT * FROM CreditMemo WHERE id IN ($ids) ORDER BY Metadata.CreateTime DESC");

	//echoprintcommand($invoices); die;
 }
 else
 {
 	$itemsCount = $dataService->query("SELECT count(*) FROM CreditMemo");
 	$invoices 	= $dataService->query("SELECT * FROM CreditMemo ORDER BY Metadata.CreateTime DESC MAXRESULTS $itemsCount");
}
return $invoices;exit;
?>
