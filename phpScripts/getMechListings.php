<?php

require_once('../config/config.php');

// Useful commands
/*
    echo 'Current PHP version: ' . phpversion();
    print_r(get_loaded_extensions());
    echo extension_loaded("mongo") ? "loaded\n" : "not loaded\n";
    $connection = new Mongo("mongodb://" . MONGO_USER . ":" . MONGO_PASS . "@" . MONGO_HOST);
    $db->setReadPreference(MongoClient::RP_NEAREST, array());
*/

$connection = new Mongo('mongodb://127.0.0.1:27017');
$db = $connection->selectDB(MONGO_DB);
$collection = $db->selectCollection(MONGO_COLL);

$command = '{}';
$returnKeys = '{"mechName":1,"tags":1,"tonnage":1,"walk":1,"run":1,"jump":1,"weapons":1}';

$cursor = $collection->find(json_decode($command, true), json_decode($returnKeys, true));

$finalReturns = new stdClass();

foreach($cursor as $doc) {
    $finalReturns->{$doc['_id']} = $doc;
    unset($finalReturns->{$doc['_id']}['_id']);
}

echo json_encode($finalReturns);

?>
