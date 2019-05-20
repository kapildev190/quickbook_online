<?php
use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\PlatformService\PlatformService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\Facades\Invoice;
use QuickBooksOnline\API\Facades\Item;
$invoiceid = $_POST['invoiceid'];
//echoprintcommand($_SESSION); die;
if( $invoiceid != '' )
{
	if (!is_dir('./assets/uploads/invoicepdfs/'))
	{
		mkdir('./assets/uploads/invoicepdfs/', 0777, TRUE);
	}
	$path 		 	= FCPATH.'assets/uploads/invoicepdfs';
	$dataService->throwExceptionOnError(true);
	$invoice = Invoice::create([
	    "Id" => $invoiceid
	]);
	$directoryForThePDF = $dataService->DownloadPDF($invoice,$path);
	$chkArry = explode('/', $directoryForThePDF);
	if(!empty($chkArry))
	{
		$pdfArry 	=  end($chkArry);
		file_put_contents($path.$pdfArry, $response);
		$this->db->set('pdf',$pdfArry);
		$this->db->where('QbInvoiceId',$invoiceid);
		$this->db->update('invoices');
		$output['status'] 	= true;
		$output['file'] 	= $pdfArry;
	}
	else
	{
		$output['status'] 	= false;
		$output['file'] 	= '';
	}

}
else
{
	$output['status'] 	= false;
	$output['file'] 	= '';
}
return $output;
?>
