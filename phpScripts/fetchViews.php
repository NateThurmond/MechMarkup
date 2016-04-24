<?php

require_once('../config/config.php');
require_once('../phpScripts/sqlPrepare.php');

$conn = mysqli_connect('localhost', MONGO_USER, MONGO_PASS, MONGO_DB);


if (! isset($_COOKIE["uuid"]))
{
    setcookie("uuid", uniqid(), time()+172800);   //  2 day
    
    $deleteOldRowsSQL = 'delete from views where timestamp < (NOW() - INTERVAL 2 DAY)';
    $deleteOldRows = mysqli_query($conn, $deleteOldRowsSQL);
    $conn->close();
    
    echo '{"view1":"","view2":"","view3":"","view4":""}';
    exit(0);
}
else {
    $uuid = $_COOKIE["uuid"];

    $findUserSQL = "SELECT * FROM `views` WHERE `uuid` = '" . $uuid . "'";
    $findUser = mysqli_query($conn, $findUserSQL);
    $findUserResult = mysqli_fetch_assoc($findUser);
    
    if (count($findUserResult) == 0) {
        
        echo '{"view1":"","view2":"","view3":"","view4":""}';
    }
    else {
        $userViews = new stdClass();
        $userViews->view1 = $findUserResult['view1'];
        $userViews->view2 = $findUserResult['view2'];
        $userViews->view3 = $findUserResult['view3'];
        $userViews->view4 = $findUserResult['view4'];
        
        echo json_encode($userViews);
    }
    
    $conn->close();
}

?>
