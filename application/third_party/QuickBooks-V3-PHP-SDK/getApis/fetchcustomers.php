<?php
use QuickBooksOnline\API\Facades\Customer;

$totalCustomers = $dataService->query("SELECT count(*) FROM customer");
$customers  	= $dataService->query("SELECT * FROM customer ORDER BY Metadata.CreateTime DESC MAXRESULTS $totalCustomers");
return $customers;exit;
?>
