<?php

require_once('./class.MongoOps.php');
date_default_timezone_set('UTC');
$now = date('Y-m-d H:i:s');

if (isset($_COOKIE["uuid"]) && isset($_GET['viewNum']) && isset($_GET['mechNum'])) {
    
    $uuid = (string)$_COOKIE["uuid"];
    $viewNum = (string)$_GET['viewNum'];
    $mechNum = (string)$_GET['mechNum'];
    
    $mongoOps = new mongoOps();
    $commandFindMarkup = '{"userUuid":"' . $uuid . '","viewNum":"' . $viewNum . '","mechNum":"' . $mechNum . '"}';

    $cursor = $mongoOps->find($commandFindMarkup, '{"markup":1}', 'MONGO_COLL_MARKUPS');
    $finalReturn = $mongoOps->objFromCursor($cursor);
    
    if (json_encode($finalReturn) == "{}") {
        echo json_encode($finalReturn);
    }
    else {
        reset($finalReturn); 
        $key = key($finalReturn);
        
        echo json_encode($finalReturn->{$key}['markup']);
    }
}

?>
