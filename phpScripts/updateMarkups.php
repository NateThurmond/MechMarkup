<?php

require_once('./class.MongoOps.php');
date_default_timezone_set('UTC');
$now = date('Y-m-d H:i:s');

if (isset($_COOKIE["uuid"]) && isset($_GET['viewNum']) && isset($_GET['mechNum'])) {
    
    $uuid = (string)$_COOKIE["uuid"];
    $viewNum = (string)$_GET['viewNum'];
    $mechNum = (string)$_GET['mechNum'];
    $createdAt = strtotime($now);
    
    $post = file_get_contents('php://input');
    $postFields = json_decode($post, true);
    $markup = $postFields['markup'];
    
    if ((isset($markup) && $markup != "")) {
        $mongoOps = new mongoOps();
        
        $commandRemUser = $mongoOps->decodeCommand('{"userUuid":"' . $uuid . '","viewNum":"' . $viewNum . '","mechNum":"' . $mechNum . '"}');
        $cursor = $mongoOps->remove($commandRemUser, 'MONGO_COLL_MARKUPS');
         
        // Remove any documents from collection older than 2 days.
        // can be removed when using ttl with newwer mongo version
        $timeToRemove = $createdAt - 172800;
        $commandRemTime = $mongoOps->decodeCommand('{"createdAt":{"$lt":' . $timeToRemove . '}}');
        $cursor2 = $mongoOps->remove($commandRemTime, 'MONGO_COLL_MARKUPS');
        
        // Insert the new markup for this user for this page for this mech
        $commandInsUser = $mongoOps->decodeCommand('{"userUuid":"' . $uuid . '", "viewNum":"'
            . $viewNum . '", "mechNum":"' . $mechNum . '", "createdAt":' . $createdAt . ', "markup":"' . $markup . '"}');
        $cursor3 = $mongoOps->insert($commandInsUser, 'MONGO_COLL_MARKUPS');
    }
}

?>
