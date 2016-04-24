<?php

require_once('./class.MongoOps.php');


if (isset($_COOKIE["uuid"]) && isset($_GET['viewNum']) && isset($_GET['mechNum'])) {
    
    $uuid = (string)$_COOKIE["uuid"];
    $viewNum = (string)$_GET['viewNum'];
    $mechNum = (int)$_GET['mechNum'];
    
    $mongoOps = new mongoOps();
    
    $commandRemMarkup = json_decode('{"userUuid":"' . $uuid . '","viewNum":"' . $viewNum . '","mechNum":"' . strval($mechNum) . '"}', true);
    $cursor = $mongoOps->remove($commandRemMarkup, 'MONGO_COLL_MARKUPS');
    
    $mechNum++;
    while ($mechNum < 6) {
        
        $commandFindMarkup = '{"userUuid":"' . $uuid . '","viewNum":"' . $viewNum . '","mechNum":"' . strval($mechNum) . '"}';
        $cursor2 = $mongoOps->find($commandFindMarkup, '{"mechNum":1}', 'MONGO_COLL_MARKUPS');
        $return = $mongoOps->objFromCursor($cursor2);
        
        foreach($return as $docInd => $vals) {
            $valNumToUpdate = $vals['mechNum'] - 1;
            
            $newdata = array('$set' => array("mechNum" => strval($valNumToUpdate)));
            $matchData = '{"_id":"' . $docInd . '"}';
            
            $return2 = $mongoOps->update($matchData, $newdata, 'MONGO_COLL_MARKUPS');
        }
        
        $mechNum++;
    }
}

?>
