<?php
	error_reporting(0);
	use QuickBooksOnline\API\Core\ServiceContext;
	use QuickBooksOnline\API\DataService\DataService;
	use QuickBooksOnline\API\PlatformService\PlatformService;
	use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
	use QuickBooksOnline\API\WebhooksService;
	use QuickBooksOnline\API\Facades\Invoice;
	use QuickBooksOnline\API\Facades\Line;

	//echoprintcommand($_POST);
	$DocNumber				=   $_POST['invoiceNo'];
	$customer_id			=	$_POST['customer_id'];
	$invoiceDate			=	$_POST['invoiceDate'];
	//$productService			=	$_POST['productService'];
	//$description			=	$_POST['description'];
	$qty					=	isset( $_POST['qty']) ? $_POST['qty'] : '0';
	$rate					=	isset( $_POST['rate'] ) ? $_POST['rate'] : '0';
	$amount				=	isset( $_POST['amount'] ) ? $_POST['amount'] : '0';
	//Add a new Invoice
	$lineArray = array();
	$i = 0;
	//for($i=0;$i<1;$i++)
	//{
		$LineObj = Line::create([
			//"Description" => trim(strip_tags($description[$i])),
			"Description" => 'test',
			"Amount" => trim(strip_tags($rate)) * trim(strip_tags($qty)),
			"DetailType" => "SalesItemLineDetail",
			"SalesItemLineDetail" => [
				"ItemRef" => [
					"value" => 6,
					"name"  => 'Services',
				],
				"UnitPrice"  => $rate,
				"Qty" 		 => $qty,
				"TaxCodeRef" => [
					"value" => 1
				]
			]
		]);
		$lineArray[0] = $LineObj;
	//}
	$theResourceObj = Invoice::create([
		"DocNumber" => $DocNumber,
		"TxnDate"=> date('Y-m-d',strtotime($invoiceDate)),
		"Line" =>  $lineArray,
		"CustomerRef"=> [
			"value"=> $customer_id,
		],
	]);
	
	$resultingObj = $dataService->Add($theResourceObj);
	$error = $dataService->getLastError();
	$response = array();
	if ($error)
	{
		$response['success'] 	 = false;
		$response['error_msg'] 	 = $error->getResponseBody();
	}
	else
	{
		$response['success'] 	    = true;
		$response['qbInvoiceId']  = $resultingObj->Id;
		$response['invoiceNo']    = $DocNumber;
		$response['success_msg'] = 'Invoice Added Successfully';
	}
	return $response;exit;
