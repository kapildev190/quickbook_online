<?php
	// error_reporting(0);
	use QuickBooksOnline\API\Core\ServiceContext;
	use QuickBooksOnline\API\DataService\DataService;
	use QuickBooksOnline\API\PlatformService\PlatformService;
	use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
	use QuickBooksOnline\API\WebhooksService;
	use QuickBooksOnline\API\Facades\PurchaseOrder;
	use QuickBooksOnline\API\Facades\Line;

	if(isset($_POST['changeStatus']))	{
		if($_POST['poStatus'] == 'Open')
			$status = "Closed";
		if($_POST['poStatus'] == 'Closed')
			$status = "Open";
	$purchaseorder = $dataService->FindbyId('purchaseorder', $_POST['poQbId']);
	 $theResourceObj = PurchaseOrder::update($purchaseorder , [
  		"POStatus"=> $status,
		]);
	  $message = "Purchase Order Status Changed Successfully";
	}
	else {
		$purchaseorder = $dataService->FindbyId('purchaseorder', $_POST['purchaseOrderId']);
			$DocNumber			=   $_POST['poOrderNo'];
			$shipvia				=   $_POST['shipvia'];
			$project				=		isset($_POST['project']) ? substr($_POST['project'], 0, 30) : ''; //Ravinder
			$poDate					=		$_POST['poDate'];
			$billaddress		=		isset( $_POST['billaddress']) ? $_POST['billaddress'] : '';
			$billaddressArray   = array();
			if($billaddress != '')
			{
				$billaddressArray = preg_split('/\r\n|[\r\n]/', $_POST['billaddress']);
			}
			$line1 = $line2 = $line3 = $line4 = $linee5 = '';
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

			$shippingaddress		=	isset( $_POST['shippingaddress']) ? $_POST['shippingaddress'] : '';
			$shippingaddressArray   = array();
			if($shippingaddress != '')
			{
				$shippingaddressArray = preg_split('/\r\n|[\r\n]/', $_POST['shippingaddress']);
			}

			$vline1 = $vline2 = $vline3 = $vline4 = $vlinee5 ='';

			if( isset($shippingaddressArray[0]) )
				$vline1 = $shippingaddressArray[0];
			if( isset($shippingaddressArray[1]) )
				$vline2 = $shippingaddressArray[1];
			if( isset($shippingaddressArray[2]) )
				$vline3 = $shippingaddressArray[2];
			if( isset($shippingaddressArray[3]) )
				$vline4 = $shippingaddressArray[3];

			for( $ij = 4; $ij < 15; $ij++ )
			{
				if( isset($shippingaddressArray[$ij]) )
					$vlinee5 = $vlinee5.' '.$shippingaddressArray[$ij];
			}

			$qty					=	isset( $_POST['qty']) ? $_POST['qty'] : '0';
			$rate					=	isset( $_POST['rate'] ) ? $_POST['rate'] : '0';
			$amount				=	isset( $_POST['amount'] ) ? $_POST['amount'] : '0';
			$taxService		=	$_POST['tax'];
			$productService		=	$_POST['productService'];
			$description		=	$_POST['description'];
			$pro_invClass			=	isset($_POST['pro_invoice_class_id']) ? $_POST['pro_invoice_class_id'] : "";
			$pro_cus					=	isset($_POST['pro_customer']) ? $_POST['pro_customer'] : "";

			$acc_acct					=	$_POST['accountTerm'];
			$acc_desc					=	$_POST['acc_desc'];
			$acc_rate					=	isset( $_POST['acc_rate'] ) ? $_POST['acc_rate'] : '0';
			$acc_tax					=	$_POST['acc_tax'];
			$acc_cus					=	isset($_POST['acc_customer']) ? $_POST['acc_customer'] : "";
			$acc_invClass			=	isset($_POST['acc_invoice_class_id']) ? $_POST['acc_invoice_class_id'] : "";

			//echoprintcommand($_POST);die('dead');
			//Add a new Invoice
			 $lineArray = array();
			 if(!empty($taxService[0])){
			 		for($i=0;$i<count($taxService);$i++){
						 $serviceId 		= $productService[$i];
						 $serviceIdn		=	($serviceId != "") ? $serviceId : "";
						 $servcName 		= getInvoiceServicesNameByServiceID($serviceId);
						 $servcNamec 		= ($servcName != "") ? $servcName : "";
						 $taxSer				=	$taxService[$i];
						 $pro_cuss			=	$pro_cus[$i];
						 $pro_invClasss =	$pro_invClass[$i];
						 $LineObj = Line::create([
						 		"DetailType" => "ItemBasedExpenseLineDetail",
						 		"Id" => $i,
						 		"LineNum" => $i,
						 		"Description" => trim(strip_tags($description[$i])),
						 		"Amount" => trim(strip_tags($rate[$i])) * trim(strip_tags($qty[$i])),
								"ItemBasedExpenseLineDetail" => [
										"ItemRef" => [
												"value" => $serviceIdn,
												"name" => $servcNamec,
										],
										"UnitPrice" => $rate[$i],
										"Qty" => $qty[$i],
										"TaxCodeRef" => [
												"value" => $taxService[$i]
										],
										"CustomerRef" => [
										 "value" => $pro_cus[$i]
									 ],
									 "ClassRef" => [
											"value" => $pro_invClass[$i],
									],
								]
						 ]);
						 $lineArray[$i] = $LineObj;
			 	 }
			  }
	 		//$i = 0;

			// if(!empty($acc_tax[0])){
			// 	//foreach ($acc_desc as $i => $value)	{
			// 	for($j=0;$j<count($acc_tax);$j++){
			// 			$acc_taxx = $acc_tax[$j];
			// 			$acc_acctt = $acc_acct[$j];
			// 			$acc_cuss = $acc_cus[$j];
			// 			$acc_invClasss = $acc_invClass[$j];
			// 			$LineObj = Line::create([
			// 					"DetailType" => "AccountBasedExpenseLineDetail",
			// 					"Id" => $j,
			// 					//"LineNum" => count($lineArray),
			// 					"Description" => trim(strip_tags($acc_desc[$j])),
			// 					"Amount" => trim(strip_tags($acc_rate[$j])),
			// 					"AccountBasedExpenseLineDetail" => [
			// 							"TaxCodeRef" => [
			// 									"value" => $acc_taxx
			// 							],
			// 							"AccountRef" => [
			// 								"value" => $acc_acctt
			// 							],
			// 							"CustomerRef" => [
			// 								"value" => $acc_cuss
			// 							],
			// 							"ClassRef" => [
			// 								 "value" => $acc_invClasss,
			// 						 ],
			// 					]
			// 			]);
			// 			$lineArray[count($lineArray)] = $LineObj;
			// 		}
			// }
			//
			// //echoprintcommand($lineArray); die();

		 $theResourceObj = PurchaseOrder::update($purchaseorder,[
			   "TxnDate"=> date('Y-m-d',strtotime($poDate)),
				 "TotalAmt" =>  0,
				 "Line" =>  $lineArray,
				  "TxnTaxDetail"=> [
				   	"TotalTax"=> 0
				 		],
	        "VendorAddr"=> [
	            "Line1"=> $line1,
	            "Line2"=> $line2,
	            "Line3"=> $line3,
	            "Line4"=> $line4
	        ],
					"ShipAddr"=> [
	            "Line1"=> $vline1,
	            "Line2"=> $vline2,
	            "Line3"=> $vline3,
	            "Line4"=> $vline4
	        ],
					"CustomField" => [
						[
							"DefinitionId"	=> "1",
							"Name"=> "Project",
							"Type"=> "StringType",
							"StringValue"=> $project
						]
	        ],
					"VendorRef" => [
							"value" => $_POST['vendor']
					],
					"ShipMethodRef" => [
	      		"value"=> $shipvia
	    		],
					"PrivateNote"=> $_POST['po_memo'],
					"Memo"	=> $_POST['invoice_message'],
	    		"GlobalTaxCalculation"	=> $_POST['taxofamounts']
		 ]);
	 $message = "Purchase Order Updated Successfully";
	}
	 $resultingObj = $dataService->Update($theResourceObj);
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
		 $response['qbPoId']  			= $resultingObj->Id;
		 $response['success_msg'] 	= $message;
	 }

	//  echo "<pre>";
	//  print_r($theResourceObj);
	//  die;
return $response;exit;
