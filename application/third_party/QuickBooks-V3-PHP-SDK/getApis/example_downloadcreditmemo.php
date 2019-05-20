<?php

require_once dirname(__FILE__) . '/config.php';
$creditmemoid = $_POST['creditmemoid'];

if( $creditmemoid != '' )
{
	$CreditMemo = new QuickBooks_IPP_Service_CreditMemo();
	$response   	= $CreditMemo->pdf($Context, $realm, $creditmemoid);
	//echoprintcommand($CreditMemo); die('dead');
	$path 		 	= FCPATH.'/assets/uploads/creditmemos/';
	if (!is_dir('./assets/uploads/creditmemos/'))
	{
		mkdir('./assets/uploads/creditmemos/', 0777, TRUE);
	}
	$file = $creditmemoid.'.pdf';
	file_put_contents($path.$file, $response);

	$this->db->set('pdf',$file);
	$this->db->where('qbCreditMemoId',$creditmemoid);
	$this->db->update('creditmemo');
	$output['status'] 	= true;
	$output['file'] 	= $file;
}
else
{
	$output['status'] 	= false;
	$output['file'] 	= '';
}
return $output;
?>
