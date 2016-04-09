<?php

require_once('./class.MongoOps.php');

$pdf = file_get_contents('./panther.pdf');

$tmpJPG = '/tmp/' . uniqid() . ".jpg";
$tmpPDF = '/tmp/' . uniqid() . ".pdf";

file_put_contents($tmpPDF, $pdf);


$command = 'gs -dNOPAUSE -dBATCH -sDEVICE=jpeg -r144 -sOutputFile=' . $tmpJPG . ' ' . $tmpPDF;

shell_exec($command);

$jpgToWrite = file_get_contents($tmpJPG);
$jpgToWrite = base64_encode($jpgToWrite);

unlink($tmpJPG);
unlink($tmpPDF);


$mongoOps = new mongoOps();

$mechData = array(
    'mechName' => (string)'PNT-9R Panther',
    'tags' => (string)'panther,PNT,pnt-9r,9r,PNT-9R,Panther',
    'tonnage' => (string)'35',
    'walk' => (string)'4',
    'run' => (string)'6',
    'jump' => (string)'4',
    'weapons' => (string)'ppc|srm 4',
);

$mongoOps->insert($mechData, 'MONGO_COLL_MECHS');
$createdMechID =  $mechData['_id'];
$createdMechID = strval($createdMechID);


$mechSheet = array(
    'refID' => $createdMechID,
    'image' => $jpgToWrite
);

$mongoOps->insert($mechSheet, 'MONGO_COLL_PDFS');


?>
