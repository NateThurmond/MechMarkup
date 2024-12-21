<?php

require_once('../config/config.php');
require_once('../phpScripts/sqlPrepare.php');
date_default_timezone_set('UTC');
$now = date('Y-m-d H:i:s');

// Check for the uuid cookie
if (!isset($_COOKIE["uuid"])) {
    $newCook = uniqid();
    setcookie("uuid", $newCook, time() + 172800);   // 2 days expiration
    $_COOKIE["uuid"] = $newCook;
}

$uuid = $_COOKIE["uuid"];

$post = file_get_contents('php://input');
$postFields = json_decode($post, true);

$validKeys = ['view1', 'view2', 'view3', 'view4'];

// Assign post data to corresponding variables dynamically
foreach ($validKeys as $key) {
    if (isset($postFields[$key])) {
        $$key = $postFields[$key];  // Dynamic variable assignment, though can be replaced with an array
    }
}

// Proceed only if required variables are set
if (isset($uuid, $view1, $view2, $view3, $view4)) {

    // Establish database connection
    $conn = mysqli_connect(MYSQL_HOST_LOCAL, MONGO_USER, MONGO_PASS, MONGO_DB);

    // Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Check if the UUID already exists in the database
    $findUserSQL = "SELECT `uuid` FROM `views` WHERE `uuid` = ?";
    $findUser = mysqli_prepare($conn, $findUserSQL);
    mysqli_stmt_bind_param($findUser, 's', $uuid);  // Binding the UUID parameter
    mysqli_stmt_execute($findUser);
    $result = mysqli_stmt_get_result($findUser);
    $findUserResult = mysqli_fetch_assoc($result);

    if (!$findUserResult) {  // No rows found, so insert
        $insertUserSQL = "INSERT INTO `views`(`uuid`, `view1`, `view2`, `view3`, `view4`, `timestamp`) VALUES (?, ?, ?, ?, ?, ?)";
        $insertUser = mysqli_prepare($conn, $insertUserSQL);
        mysqli_stmt_bind_param($insertUser, 'ssssss', $uuid, $view1, $view2, $view3, $view4, $now); // Bind parameters
        $insertUserResult = mysqli_stmt_execute($insertUser);

        if (!$insertUserResult) {
            echo "Error inserting views: " . mysqli_error($conn);
        } else {
            echo "updated views";
        }
        mysqli_stmt_close($insertUser);
    } else {  // UUID exists, update
        $updateUserSQL = "UPDATE `views` SET `view1`=?, `view2`=?, `view3`=?, `view4`=?, `timestamp`=? WHERE `uuid` = ?";
        $updateUser = mysqli_prepare($conn, $updateUserSQL);
        mysqli_stmt_bind_param($updateUser, 'ssssss', $view1, $view2, $view3, $view4, $now, $uuid); // Bind parameters
        $updateUserResult = mysqli_stmt_execute($updateUser);

        if (!$updateUserResult) {
            echo "Error updating views: " . mysqli_error($conn);
        } else {
            echo "updated views";
        }
        mysqli_stmt_close($updateUser);
    }

    // Delete old rows older than 2 days
    $deleteOldRowsSQL = "DELETE FROM `views` WHERE `timestamp` < (NOW() - INTERVAL 2 DAY)";
    $deleteOldRowsResult = mysqli_query($conn, $deleteOldRowsSQL);
    if (!$deleteOldRowsResult) {
        echo "Error deleting old rows: " . mysqli_error($conn);
    }

    // Close the connection
    mysqli_close($conn);
}

?>