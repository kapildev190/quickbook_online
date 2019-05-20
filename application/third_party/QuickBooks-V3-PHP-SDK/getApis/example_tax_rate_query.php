<?php
use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\PlatformService\PlatformService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\Facades\TaxService;
use QuickBooksOnline\API\Facades\TaxRate;
$taxRateRefId				=   $_POST['taxRateRef'];
$rates = array();
if(!empty($taxRateRefId))
{
  foreach($taxRateRefId as $val)
  {
    $entities = $dataService->query("SELECT * FROM TaxRate WHERE Id = '$val'");
    if(!empty($entities))
    {
      $effct  = $entities[0]->EffectiveTaxRate;
      if(count($effct) > 1)
      {
        $rateVal  = $effct[0]->RateValue;
      }
      else
      {
        $rateVal  = $effct->RateValue;
      }
      $rates[$val]  =  $rateVal;
    }
  }
}
return $rates;
?>
