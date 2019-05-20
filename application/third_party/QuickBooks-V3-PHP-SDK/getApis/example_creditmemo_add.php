<?php
error_reporting(0);
use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\PlatformService\PlatformService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\WebhooksService;
use QuickBooksOnline\API\Facades\CreditMemo;
use QuickBooksOnline\API\Facades\Line;
	$DocNumber				=   $_POST['invoiceNo'];
	$page							=		$_POST['page'];
	$customer_id			=		$_POST['customerid'];
	$subject					=		isset($_POST['subject']) ? substr($_POST['subject'], 0, 30) : ''; //Ravinder
	$project					=		isset($_POST['project']) ? substr($_POST['project'], 0, 30) : ''; //Ravinder
	$details					=		isset($_POST['details']) ? substr($_POST['details'], 0, 30) : ''; //Ravinder
	$invoiceDate			=		$_POST['creditMemoDate'];
	$productService		=		$_POST['productService'];
	$description			=		$_POST['description'];
	$invoice_message	=		$_POST['invoice_message'];
	$invoice_statement=		$_POST['invoice_statement'];
	$send_emailnotify	=		$_POST['send_emailnotify'];
	$billaddress		=	isset( $_POST['billaddress']) ? $_POST['billaddress'] : '';
	$billaddressArray   = array();
	if($billaddress != '')
	{
		$billaddressArray = preg_split('/\r\n|[\r\n]/', $_POST['billaddress']);
	}
	$line1 = $line2 = $line3 = $line4 = $linee5 ='';
	if( isset($billaddressArray[0]) )
		$line1 = $billaddressArray[0];
	if( isset($billaddressArray[1]) )
		$line2 = $billaddressArray[1];
	if( isset($billaddressArray[2]) )
		$line3 = $billaddressArray[2];
	if( isset($billaddressArray[3]) )
		$line4 = $billaddressArray[3];
	for( $ij = 4; $ij < 15; $ij++ )
	{
		if( isset($billaddressArray[$ij]) )
		$linee5 = $linee5.' '.$billaddressArray[$ij];
	}
	$qty					=	isset( $_POST['qty']) ? $_POST['qty'] : '0';
	$rate					=	isset( $_POST['rate'] ) ? $_POST['rate'] : '0';
	$amount				=	isset( $_POST['amount'] ) ? $_POST['amount'] : '0';
	$taxService		=	$_POST['tax'];
	//Add a new Invoice
	 $lineArray = array();
	 $i = 0;
	 for($i=0;$i<count($taxService);$i++)
	 {
		// foreach($taxService as $vals)
		 //{
			 $serviceId 	= $productService[$i];
			 $serviceIdn	=	($serviceId != "") ? $serviceId : "";
			 $servcName = getInvoiceServicesNameByServiceID($serviceId);
			 $servcNamec = ($servcName != "") ? $servcName : "";
			 $LineObj = Line::create([
					 "Id" => $i,
					 "LineNum" => $i,
					 "Description" => trim(strip_tags($description[$i])),
					 "Amount" => trim(strip_tags($rate[$i])) * trim(strip_tags($qty[$i])),
					 "DetailType" => "SalesItemLineDetail",
					 "SalesItemLineDetail" => [
							 "ItemRef" => [
									 "value" => $serviceIdn,
									 "name" => $servcNamec,
							 ],
							 "UnitPrice" => $rate[$i],
							 "Qty" => $qty[$i],
							 "TaxCodeRef" => [
									 "value" => $taxService[$i]
							 ]
					 ]
			 ]);
			 $lineArray[$i] = $LineObj;
		 //}
	 }
	 $theResourceObj = CreditMemo::create([
			 "DocNumber" => $DocNumber,
			  "TxnDate"=> date('Y-m-d',strtotime($invoiceDate)),
			 "Line" =>  $lineArray,
				//  "SalesTermRef"=> [
				// 			 "value"=> $invoicetermid,
				//  ],
				 "CustomerRef"=> [
							 "value"=> $customer_id,
				 ],
				 "CustomerMemo"=> [
            "value" => $invoice_message,
        ],
        "BillAddr"=> [
            "Line1"=> $line1,
            "Line2"=> $line2,
            "Line3"=> $line3,
            "Line4"=> $line4
        ],
				//"DueDate" => date('Y-m-d',strtotime($dueDate)),
				"EmailStatus" => $send_emailnotify,
				"CustomField" => [
					[
            "DefinitionId"=> "1",
            "Name"=> "Subject",
            "Type"=> "StringType",
            "StringValue"=> $subject,
					],
					[
						"DefinitionId"=> "2",
						"Name"=> "Project",
						"Type"=> "StringType",
						"StringValue"=> $project
					],
					[
						"DefinitionId"=> "3",
						"Name"=> "Details",
						"Type"=> "StringType",
						"StringValue"=> $details
					]
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
		 $response['qbCreditMemoId']  = $resultingObj->Id;
		 $response['success_msg'] = 'Credit memo Added Successfully';
	 }
	return $response;exit;
