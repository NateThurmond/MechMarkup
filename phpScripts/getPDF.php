<?php

require_once('./class.MongoOps.php');

if (isset($_GET['mechID'])) {
    
    $mechID = (string)$_GET['mechID'];
    
    $mongoOps = new mongoOps();
    $cursor = $mongoOps->find('{"refID":"' . $mechID . '"}', '{"pdf":1}', 'MONGO_COLL_PDFS');
    $finalReturns = $mongoOps->objFromCursor($cursor);
    
    //echo json_encode($finalReturns);
    
    foreach((array)$finalReturns as $ind => $val) {
        
        $pdf = $val['pdf'];
        $pdf_decoded = base64_decode ($pdf);
        
        header('Content-type: application/pdf');
        header('Content-Disposition: inline; filename="' . $mechID . '"');
        header('Content-Transfer-Encoding: binary');
        header('Accept-Ranges: bytes');
        echo $pdf_decoded;
    }
}

?>
