<?php
use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\PlatformService\PlatformService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\Facades\TaxService;
use QuickBooksOnline\API\Facades\TaxRate;
$taxcodes = $dataService->query(" SELECT * FROM TaxCode where active = true ");
$tax 	  = array();
$i 		  = 0;
if(!empty($taxcodes))
{
	foreach ($taxcodes as $key => $TaxCode)
	{
		$SalesTaxRateList  	=	$TaxCode->SalesTaxRateList;
		if(!empty($SalesTaxRateList))
		{
			$dds = $SalesTaxRateList->TaxRateDetail;
			if( count($dds) == 1 )
				$tax[$i]['taxTaxRateRef']  	=	$dds->TaxRateRef;
			else if( count($dds) > 1 )
			{
				$tax[$i]['taxTaxRateRef']  	=	$dds[0]->TaxRateRef;
			}
		}
		else 
		{
			$tax[$i]['taxTaxRateRef']  	=	"";
		}
		$tax[$i]['taxCodeId'] =	$TaxCode->Id;
		$tax[$i]['taxName']   =	$TaxCode->Name;
		$i++;
	}
}
return $tax;
?>