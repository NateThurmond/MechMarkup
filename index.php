<?php

require_once('config/config.php');

//echo 'Current PHP version: ' . phpversion();
//print_r(get_loaded_extensions());
//echo extension_loaded("mongo") ? "loaded\n" : "not loaded\n";

//$connection = new Mongo("mongodb://" . MONGO_USER . ":" . MONGO_PASS . "@" . MONGO_HOST);
$connection = new Mongo('mongodb://127.0.0.1:27017');

$db = $connection->selectDB(MONGO_DB);
//$db->setReadPreference(MongoClient::RP_NEAREST, array());
$collection = $db->selectCollection(MONGO_COLL);

$command = '{"walk":"6"}';
$returnKeys = '{"mechName":1}';

$cursor = $collection->find(json_decode($command, true), json_decode($returnKeys, true));

foreach($cursor as $doc) {
    var_dump($doc);
}


?>

<!DOCTYPE html>
<html>
    <head>
        <title>Mech Markup</title>
        <script src="js/jquery-1.12.2.min.js"></script>
    </head>
    <body>
        <h1>HELLO WORLD!</h1>
    </body>
    
    <script>
        $(document).ready(function() {
            
        })
    </script>
</html>