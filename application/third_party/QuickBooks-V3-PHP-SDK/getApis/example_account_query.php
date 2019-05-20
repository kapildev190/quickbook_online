<?php
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\Facades\Account;
$accounts = $dataService->query("SELECT * FROM Account");
$accountlist= array();
foreach ($accounts as $key => $Account)
{
	$deposit_id													=	$Account->Id;
	$accountlist[$key]['itemid']  			= $deposit_id;
	$accountlist[$key]['accountname']  	= $Account->FullyQualifiedName;
	$accountlist[$key]['Classification']= $Account->Classification;
	$accountlist[$key]['Name']  				= $Account->Name;
	$accountlist[$key]['ParentRef']  		= $Account->ParentRef;
	$accountlist[$key]['AccountSubType']= $Account->AccountSubType;
}
return $accountlist;exit;
?>
