<?php
error_reporting(0);
use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\PlatformService\PlatformService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\WebhooksService;
use QuickBooksOnline\API\Facades\Employee;
$employees = $dataService->query("SELECT * FROM Employee ORDER BY Metadata.CreateTime DESC MAXRESULTS 25");
return $employees;
