<?php

require_once('./class.MongoOps.php');

$mongoOps = new mongoOps();
$cursor = $mongoOps->find('{"_id":{"$gte":"56ff62bd24319f47ccbedccf"}}', '{"mechName":1,"tags":1,"tonnage":1,"walk":1,"run":1,"jump":1,"weapons":1}', 'MONGO_COLL_MECHS');
$finalReturns = $mongoOps->objFromCursor($cursor);

echo json_encode($finalReturns);

?>