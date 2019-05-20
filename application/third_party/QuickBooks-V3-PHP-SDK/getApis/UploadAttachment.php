<?php
use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\PlatformService\PlatformService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\Data\IPPReferenceType;
use QuickBooksOnline\API\Data\IPPAttachableRef;
use QuickBooksOnline\API\Data\IPPAttachable;

// Prepare entities for attachment upload
//echoprintcommand($_POST);
$imageBase64 = array();
if($_POST['encoded_file'] != "" )
{
  $fileType = $_POST['fileType'];
  $imageBase64[$fileType] = $_POST['encoded_file'];
  $sendMimeType = $_POST['fileType'];
  $entityRef = new IPPReferenceType(array('value'=>$_POST['qbPoId'], 'type'=>$_POST['uploadType']));
  $attachableRef = new IPPAttachableRef(array('EntityRef'=>$entityRef));
  $objAttachable = new IPPAttachable();
  $objAttachable->FileName = $_POST['attachmentFile'];
  $objAttachable->AttachableRef = $attachableRef;
//  echoprintcommand($objAttachable);
  //die();
  $resultObj = $dataService->Upload(base64_decode($imageBase64[$sendMimeType]),
                                    $objAttachable->FileName,
                                    $sendMimeType,
                                    $objAttachable);
//  echoprintcommand($resultObj);
//  die();
}
