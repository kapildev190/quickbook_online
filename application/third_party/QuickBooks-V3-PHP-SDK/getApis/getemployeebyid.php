<?php
ini_set("display_errors", "1");
error_reporting(E_ALL);
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\Facades\Employee;
$employee_id		     =	$_POST['cust_id'];
$entities = $dataService->Query("SELECT * FROM Employee WHERE Id = '$employee_id'");
$error = $dataService->getLastError();
if ($error) {
    echo "The Status code is: " . $error->getHttpStatusCode() . "\n";
    echo "The Helper message is: " . $error->getOAuthHelperError() . "\n";
    echo "The Response message is: " . $error->getResponseBody() . "\n";
    exit();
}
else
{
  $entit = $entities[0];
}
return $entit;
?>
