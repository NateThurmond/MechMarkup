<?php

require_once('../config/config.php');
require_once('../phpScripts/sqlPrepare.php');

// Establish MySQL connection
$conn = mysqli_connect(MYSQL_HOST_LOCAL, MONGO_USER, MONGO_PASS, MONGO_DB);

// Check for connection errors
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (!isset($_COOKIE["uuid"])) {
    // Set new UUID cookie
    setcookie("uuid", uniqid(), time() + 172800);   // 2-day expiration

    // Delete old rows older than 2 days
    $deleteOldRowsSQL = 'DELETE FROM views WHERE timestamp < (NOW() - INTERVAL 2 DAY)';
    $deleteOldRows = mysqli_query($conn, $deleteOldRowsSQL);

    // Close the connection
    mysqli_close($conn);

    // Return empty views in JSON format
    echo '{"view1":"","view2":"","view3":"","view4":""}';
    exit(0);
} else {
    // Retrieve UUID from cookie
    $uuid = $_COOKIE["uuid"];

    // Prepare and execute the SQL query to fetch the user's views
    $findUserSQL = "SELECT * FROM `views` WHERE `uuid` = ?";
    $findUser = mysqli_prepare($conn, $findUserSQL);
    mysqli_stmt_bind_param($findUser, 's', $uuid);  // Bind the UUID parameter
    mysqli_stmt_execute($findUser);

    // Get the result
    $result = mysqli_stmt_get_result($findUser);
    $findUserResult = mysqli_fetch_assoc($result);

    if (!$findUserResult) {  // If no result found
        echo '{"view1":"","view2":"","view3":"","view4":""}';
    } else {
        // Prepare the user views object
        $userViews = new stdClass();
        $userViews->view1 = $findUserResult['view1'];
        $userViews->view2 = $findUserResult['view2'];
        $userViews->view3 = $findUserResult['view3'];
        $userViews->view4 = $findUserResult['view4'];

        // Output the views as a JSON object
        echo json_encode($userViews);
    }

    // Close the statement and connection
    mysqli_stmt_close($findUser);
    mysqli_close($conn);
}

?>