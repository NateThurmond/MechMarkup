<?php

require_once('./class.MongoOps.php');

if (isset($_GET['mechID'])) {
    
    $mechID = (string)$_GET['mechID'];
    
    $mongoOps = new mongoOps();
    $cursor = $mongoOps->find('{"refID":"' . $mechID . '"}', '{"image":1}', 'MONGO_COLL_PDFS');
    $finalReturns = $mongoOps->objFromCursor($cursor);
    
    //echo json_encode($finalReturns);
    
    foreach((array)$finalReturns as $ind => $val) {
        
        $image = $val['image'];
        $image_decoded = base64_decode ($image);
        
        header('Content-type: image/jpeg');
        header('Content-Disposition: inline; filename="' . $mechID . '"');
        header('Content-Transfer-Encoding: binary');
        header('Accept-Ranges: bytes');
        echo $image_decoded;
    }
}

?>
