<?php

require_once('../config/config.php');
require_once('../phpScripts/sqlPrepare.php');
date_default_timezone_set('UTC');
$now = date('Y-m-d H:i:s');


if (! isset($_COOKIE["uuid"]))
{
    setcookie("uuid", uniqid(), time()+172800);   //  2 day
}

$uuid = $_COOKIE["uuid"];


$post = file_get_contents('php://input');
$postFields = json_decode($post, true);

$validKeys = ['view1','view2','view3','view4'];
foreach($validKeys as $key) {
    if (isset($postFields[$key])) {
        $$key = $postFields[$key];
    }
}

if (isset($uuid) && isset($view1) && isset($view2) && isset($view3) && isset($view4)) {

    $conn = mysqli_connect('localhost', MONGO_USER, MONGO_PASS, MONGO_DB);
    $findUserSQL = "SELECT `uuid` FROM `views` WHERE `uuid` = '" . $uuid . "'";
    $findUser = mysqli_query($conn, $findUserSQL);
    $findUserResult = mysqli_fetch_assoc($findUser);
    
    if (count($findUserResult) == 0) {
        
        $insertUserSQL = "INSERT INTO `views`(`uuid`, `view1`, `view2`, `view3`, `view4`, `timestamp`) VALUES (?, ?, ?, ?, ?, ?)";
        $insertUser = mysqli_prepare($conn, $insertUserSQL);
        $insertUserResult = bindExecute($insertUser, [$uuid, $view1, $view2, $view3, $view4, $now]);
        mysqli_stmt_close($insertUser);
        
        if (! $insertUserResult) {
            echo "Error inserting views";
        }
        else {
            echo "updated views";
        }
    }
    else {
        $updateUserSQL = "UPDATE `views` SET `view1`=?, `view2`=?, `view3`=?, `view4`=?, `timestamp`=? WHERE `uuid` = ?";
        $updateUser = mysqli_prepare($conn, $updateUserSQL);
        $updateUserResult = bindExecute($updateUser, [$view1, $view2, $view3, $view4, $now, $uuid]);
        mysqli_stmt_close($updateUser);
        
        if (! $updateUserResult) {
            echo "Error updating views";
        }
        else {
            echo "updated views";
        }
    }
    
    $deleteOldRowsSQL = 'delete from views where timestamp < (NOW() - INTERVAL 2 DAY)';
    $deleteOldRows = mysqli_query($conn, $deleteOldRowsSQL);
    
    $conn->close();
}

?>
