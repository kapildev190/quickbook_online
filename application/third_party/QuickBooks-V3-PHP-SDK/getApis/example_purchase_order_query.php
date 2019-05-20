<?php
error_reporting(0);
use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\PlatformService\PlatformService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\WebhooksService;
use QuickBooksOnline\API\Facades\PurchaseOrder;
use QuickBooksOnline\API\Facades\Line;
$currentDate 									= date('Y-m-d');
$qbpoid 											= $_POST['qbPoId'];
$currentDate 									= $currentDate.'T00:00:00-07:00';
$purchaseOrders 	  					= $dataService->query(" SELECT * FROM PurchaseOrder where Id = '$qbpoid' ");
//echoprintcommand($purchaseOrders); die();
$finalPurchaseOrders  				= array();
$ItemBasedExpenseLineDetail  	= array();
$AccountBasedExpenseLineDetail= array();
$TxnTaxDetail				    			= array();
$qbPoId    										= array();
$itemlineIndex = $accountitemlineIndex = $taxitemlineIndex = 0;
foreach( $purchaseOrders as $key=>$val)
{
	$id 		 			= $val->Id;
	$qbPoId[]    	= $id;
	$finalPurchaseOrders[$key]['qbPoId'] 		  		= $id;
	$finalPurchaseOrders[$key]['CreateTime']      = $val->MetaData->CreateTime;
	$finalPurchaseOrders[$key]['LastUpdatedTime'] = $val->MetaData->LastUpdatedTime;
	$finalPurchaseOrders[$key]['DocNumber']	 			= $val->DocNumber;
	$finalPurchaseOrders[$key]['TxnDate'] 	 			= $val->TxnDate;
	$finalPurchaseOrders[$key]['CurrencyRef'] 	 	= $val->CurrencyRef;
	$finalPurchaseOrders[$key]['ExchangeRate']	 	= $val->ExchangeRate;
	$finalPurchaseOrders[$key]['PrivateNote']	 		= $val->PrivateNote;
	$subject = $project = $details = $finalPurchaseOrders[$key]['projectNo'] = '';
	if(!empty($val->CustomField))
	{
			$finalPurchaseOrders[$key]['projectNo']  = $val->CustomField->StringValue;
	}
	$finalPurchaseOrders[$key]['Memo']		 						= $val->Memo;
	$finalPurchaseOrders[$key]['VendorRef']	 					= $val->VendorRef;
	$finalPurchaseOrders[$key]['ShipMethodRef']	 			= $val->ShipMethodRef;
	$finalPurchaseOrders[$key]['TotalAmt']	 					= $val->TotalAmt;
	$finalPurchaseOrders[$key]['GlobalTaxCalculation']= $val->GlobalTaxCalculation;
	if(!empty($val->VendorAddr))
	{
		$finalPurchaseOrders[$key]['VendorAddr']['Line1']	 	= $val->VendorAddr->Line1;
		$finalPurchaseOrders[$key]['VendorAddr']['Line2']	 	= $val->VendorAddr->Line2;
		$finalPurchaseOrders[$key]['VendorAddr']['Line3']	 	= $val->VendorAddr->Line3;
		$finalPurchaseOrders[$key]['VendorAddr']['Line4']	 	= $val->VendorAddr->Line4;
		$finalPurchaseOrders[$key]['VendorAddr']['City']	 	= $val->VendorAddr->City;
		$finalPurchaseOrders[$key]['VendorAddr']['PostalCode']	= $val->VendorAddr->PostalCode;
		$finalPurchaseOrders[$key]['VendorAddr']['CountrySubDivisionCode']	 	= $val->VendorAddr->CountrySubDivisionCode;
		$finalPurchaseOrders[$key]['VendorAddr'] 				= serialize($finalPurchaseOrders[$key]['VendorAddr']);
	}
	else
		$finalPurchaseOrders[$key]['VendorAddr'] = "";
	if(!empty($val->ShipAddr))
	{
		$finalPurchaseOrders[$key]['ShipAddr']['Line1']	 	= $val->ShipAddr->Line1;
		$finalPurchaseOrders[$key]['ShipAddr']['Line2']	 	= $val->ShipAddr->Line2;
		$finalPurchaseOrders[$key]['ShipAddr']['Line3']	 	= $val->ShipAddr->Line3;
		$finalPurchaseOrders[$key]['ShipAddr']['Line4']	 	= $val->ShipAddr->Line4;
		$finalPurchaseOrders[$key]['ShipAddr'] 						= serialize($finalPurchaseOrders[$key]['ShipAddr']);
	}
	else
		$finalPurchaseOrders[$key]['ShipAddr']						= "";

	$finalPurchaseOrders[$key]['POStatus']	 						= $val->POStatus;
	$liness = count($val->Line);
	//echoprintcommand($val->Line);
	if($liness != "" && $liness == 1)
	{
		$DetailType = $val->Line->DetailType;
		if( trim($DetailType) == 'ItemBasedExpenseLineDetail' )
		{
			$ItemBasedExpenseLineDetail[$itemlineIndex]['poQbId']  		 = $id;
			$ItemBasedExpenseLineDetail[$itemlineIndex]['Description'] = $val->Line->Description;
			$ItemBasedExpenseLineDetail[$itemlineIndex]['Amount'] 		= $val->Line->Amount;
			$ItemBasedExpenseLineDetail[$itemlineIndex]['UnitPrice'] = $val->Line->ItemBasedExpenseLineDetail->UnitPrice;
			$ItemBasedExpenseLineDetail[$itemlineIndex]['Qty'] = $val->Line->ItemBasedExpenseLineDetail->Qty;
			$ItemBasedExpenseLineDetail[$itemlineIndex]['ItemRef'] = $val->Line->ItemBasedExpenseLineDetail->ItemRef;
			$ItemBasedExpenseLineDetail[$itemlineIndex]['CustomerRef'] = $val->Line->ItemBasedExpenseLineDetail->CustomerRef;
			$ItemBasedExpenseLineDetail[$itemlineIndex]['TaxCodeRef']= $val->Line->ItemBasedExpenseLineDetail->TaxCodeRef;
			$ItemBasedExpenseLineDetail[$itemlineIndex]['ClassRef']= $val->Line->ItemBasedExpenseLineDetail->ClassRef;
			$itemlineIndex = $itemlineIndex + 1;
		}
		else if(trim($DetailType) == 'AccountBasedExpenseLineDetail' )
		{
				$AccountBasedExpenseLineDetail[$accountitemlineIndex]['poQbId']  	 	 = $id;
				$AccountBasedExpenseLineDetail[$accountitemlineIndex]['Description'] = $val->Line->Description;
				$AccountBasedExpenseLineDetail[$accountitemlineIndex]['Amount'] 		 = $val->Line->Amount;
				$AccountBasedExpenseLineDetail[$accountitemlineIndex]['CustomerRef'] = $val->Line->AccountBasedExpenseLineDetail->CustomerRef;
				$AccountBasedExpenseLineDetail[$accountitemlineIndex]['ClassRef'] = $val->Line->AccountBasedExpenseLineDetail->ClassRef;
				$AccountBasedExpenseLineDetail[$accountitemlineIndex]['AccountRef'] = $val->Line->AccountBasedExpenseLineDetail->AccountRef;
				$AccountBasedExpenseLineDetail[$accountitemlineIndex]['TaxCodeRef'] = $val->Line->AccountBasedExpenseLineDetail->TaxCodeRef;
				$accountitemlineIndex = $accountitemlineIndex + 1 ;
		}
	}
	else
	{
		$liness = $liness;
		for( $i = 0; $i < $liness ; $i++ )
		{
			$DetailType = $val->Line[$i]->DetailType;
			if( trim($DetailType) == 'ItemBasedExpenseLineDetail' )
			{
				$ItemBasedExpenseLineDetail[$itemlineIndex]['poQbId']  		 = $id;
				$ItemBasedExpenseLineDetail[$itemlineIndex]['Description'] = $val->Line[$i]->Description;
				$ItemBasedExpenseLineDetail[$itemlineIndex]['Amount'] 		= $val->Line[$i]->Amount;
				$ItemBasedExpenseLineDetail[$itemlineIndex]['UnitPrice'] = $val->Line[$i]->ItemBasedExpenseLineDetail->UnitPrice;
				$ItemBasedExpenseLineDetail[$itemlineIndex]['Qty'] = $val->Line[$i]->ItemBasedExpenseLineDetail->Qty;
				$ItemBasedExpenseLineDetail[$itemlineIndex]['ItemRef'] = $val->Line[$i]->ItemBasedExpenseLineDetail->ItemRef;
				$ItemBasedExpenseLineDetail[$itemlineIndex]['CustomerRef'] = $val->Line[$i]->ItemBasedExpenseLineDetail->CustomerRef;
				$ItemBasedExpenseLineDetail[$itemlineIndex]['TaxCodeRef']= $val->Line[$i]->ItemBasedExpenseLineDetail->TaxCodeRef;
				$ItemBasedExpenseLineDetail[$itemlineIndex]['ClassRef']= $val->Line[$i]->ItemBasedExpenseLineDetail->ClassRef;
				$itemlineIndex = $itemlineIndex + 1;
			}
			else if(trim($DetailType) == 'AccountBasedExpenseLineDetail' )
			{
				$AccountBasedExpenseLineDetail[$accountitemlineIndex]['poQbId']  	 	 = $id;
				$AccountBasedExpenseLineDetail[$accountitemlineIndex]['Description'] = $val->Line[$i]->Description;
				$AccountBasedExpenseLineDetail[$accountitemlineIndex]['Amount'] 		 = $val->Line[$i]->Amount;
				$AccountBasedExpenseLineDetail[$accountitemlineIndex]['CustomerRef'] = $val->Line[$i]->AccountBasedExpenseLineDetail->CustomerRef;
				$AccountBasedExpenseLineDetail[$accountitemlineIndex]['ClassRef'] = $val->Line[$i]->AccountBasedExpenseLineDetail->ClassRef;
				$AccountBasedExpenseLineDetail[$accountitemlineIndex]['AccountRef'] = $val->Line[$i]->AccountBasedExpenseLineDetail->AccountRef;
				$AccountBasedExpenseLineDetail[$accountitemlineIndex]['TaxCodeRef'] = $val->Line[$i]->AccountBasedExpenseLineDetail->TaxCodeRef;
				$accountitemlineIndex = $accountitemlineIndex + 1 ;
			}
		}
  }


	$taxliness 	= $val->TxnTaxDetail->TaxLine;
	//echoprintcommand($taxliness);
	$taxliness	=	count($taxliness);
	$txnTaxDe 	= $val->TxnTaxDetail;
	if($taxliness != "" && $taxliness == 1){
		$TxnTaxDetail[$taxitemlineIndex]['poQbId']  							= $id;
		$TxnTaxDetail[$taxitemlineIndex]['Amount']  							= $txnTaxDe->TaxLine->TaxLineDetail->Amount;
		$TxnTaxDetail[$taxitemlineIndex]['TaxPercent']  					= $txnTaxDe->TaxLine->TaxLineDetail->TaxPercent;
		$TxnTaxDetail[$taxitemlineIndex]['NetAmountTaxable']  		= $txnTaxDe->TaxLine->TaxLineDetail->NetAmountTaxable;
		$TxnTaxDetail[$taxitemlineIndex]['TaxRateRef']  					= $txnTaxDe->TaxLine->TaxLineDetail->TaxRateRef;
		$TxnTaxDetail[$taxitemlineIndex]['Amount']  							= $txnTaxDe->TaxLine->Amount;
		$taxitemlineIndex = $taxitemlineIndex + 1;
	} else {
			for( $j=0; $j < $taxliness ; $j++ )
			{
				$TxnTaxDetail[$taxitemlineIndex]['poQbId']  					= $id;
				$TxnTaxDetail[$taxitemlineIndex]['Amount']  					= $txnTaxDe->TaxLine[$j]->TaxLineDetail->Amount;
				$TxnTaxDetail[$taxitemlineIndex]['TaxPercent']  			= $txnTaxDe->TaxLine[$j]->TaxLineDetail->TaxPercent;
				$TxnTaxDetail[$taxitemlineIndex]['NetAmountTaxable']  = $txnTaxDe->TaxLine[$j]->TaxLineDetail->NetAmountTaxable;
				$TxnTaxDetail[$taxitemlineIndex]['TaxRateRef']  			= $txnTaxDe->TaxLine[$j]->TaxLineDetail->TaxRateRef;
				$TxnTaxDetail[$taxitemlineIndex]['Amount']  					= $txnTaxDe->TaxLine[$j]->Amount;
				$taxitemlineIndex = $taxitemlineIndex + 1;
			}
	}
}
$output['finalPurchaseOrders'] 			 			= $finalPurchaseOrders;
$output['ItemBasedExpenseLineDetail'] 	 	= $ItemBasedExpenseLineDetail;
$output['AccountBasedExpenseLineDetail'] 	= $AccountBasedExpenseLineDetail;
$output['TxnTaxDetail'] 				 					= $TxnTaxDetail;
$output['qbPoId'] 				 		 						= $qbPoId;
return $output;
?>
