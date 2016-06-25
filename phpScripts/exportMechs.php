<?php

require_once('../config/config.php');
require_once('../phpScripts/sqlPrepare.php');
require_once('./class.MongoOps.php');
date_default_timezone_set('America/Chicago');
set_time_limit(300);


if (isset($_COOKIE["uuid"]) && isset($_GET['pass'])) {
    
    $uuid = (string)$_COOKIE["uuid"];
    $pass = (string)$_GET['pass'];
    
    if ($pass == MECH_SAVE_PASS) {
    
        $conn = mysqli_connect('localhost', MONGO_USER, MONGO_PASS, MONGO_DB);
        
        $findUserSQL = "SELECT * FROM `views` WHERE `uuid` = '" . $uuid . "'";
        $findUser = mysqli_query($conn, $findUserSQL);
        $findUserResult = mysqli_fetch_assoc($findUser);
        
        $conn->close();
        
        if (count($findUserResult) == 0) {
            
            echo json_encode('No mechs listed in views');
        }
        else {
            $currentDate = date("Y-m-d");
            $saveDir = WORKING_DIR . "/savedMechs/" . $currentDate;
            if (! file_exists($saveDir)) {
                mkdir($saveDir, 0777);
            }
            
            $mongoOps = new mongoOps();
            $userViews = new stdClass();
            
            for($i = 1; $i <= 4; $i++) {
                $userViews->{$i} = new stdClass();
                
                for($q = 1; $q <= 5; $q++) {
                    $userViews->{$i}->{$q} = new stdClass();
                }
                
                $dbVal = $findUserResult['view'.$i];
                $mechs = explode(',', $dbVal);
                
                $mechCounter = 1;
                foreach($mechs as $mech) {
                    
                    $mechID = explode('_', $mech)[0];
                    $mechName = explode('|', $mech)[1];
                    
                    if ($mechID != "") {
                        $userViews->{$i}->{$mechCounter}->mechID = $mechID;
                        $userViews->{$i}->{$mechCounter}->mechName = $mechName;
                        $userViews->{$i}->{$mechCounter}->page = strval($i);
                        $userViews->{$i}->{$mechCounter}->mechNumber = strval($mechCounter);
                    }
                    
                    $mechCounter++;
                }
            }
            
            foreach($userViews as $pageNum => $mechs) {
                foreach($mechs as $mechNum => $mechVals) {
                    if (isset($mechVals->mechID)) {
                        $mechID = $mechVals->mechID;
                        $mechName = $mechVals->mechName;
                        $page = $mechVals->page;
                        $mechNumber = $mechVals->mechNumber;
                        
                        $commandFindMarkup = '{"userUuid":"' . $uuid . '","viewNum":"' . $pageNum . '","mechNum":"' . $mechNum . '"}';
                        $cursor = $mongoOps->find($commandFindMarkup, '{"markup":1}', 'MONGO_COLL_MARKUPS');
                        $finalReturn = $mongoOps->objFromCursor($cursor);
                        
                        if (json_encode($finalReturn) != "{}") {
                            $keys = array_keys((array)$finalReturn);
                            $markup = base64_decode(explode(',', $finalReturn->{$keys[0]}['markup'])[1]);
                            file_put_contents('/tmp/testMarkup.png', $markup);
                        }
                        
                        $cursor = $mongoOps->find('{"refID":"' . $mechID . '"}', '{"image":1}', 'MONGO_COLL_PDFS');
                        $finalReturns = $mongoOps->objFromCursor($cursor);
                        
                        foreach((array)$finalReturns as $ind => $val) {
                            
                            $image = $val['image'];
                            $image_decoded = base64_decode($image);
                            file_put_contents('/tmp/testMechPDF.jpg', $image_decoded);
                        }
                        
                        $jpeg = imagecreatefromjpeg('/tmp/testMechPDF.jpg');
                        $png = imagecreatefrompng('/tmp/testMarkup.png');

                        $height = $height2 = 792;
                        $width = $width2 = 612;
                        
                        list($width_orig, $height_orig) = getimagesize('/tmp/testMechPDF.jpg');
                        
                        $ratio_orig = $width_orig/$height_orig;
                        
                        if ($width/$height > $ratio_orig) {
                           $width = $height*$ratio_orig;
                        } else {
                           $height = $width/$ratio_orig;
                        }
                        
                        list($width_orig2, $height_orig2) = getimagesize('/tmp/testMarkup.png');
                        
                        $adjustedX = 0;
                        if (($width_orig2 / $height_orig2) > 0.7727) {
                            $marginSizeTotal = $width_orig2 - ($height_orig2 * 0.7727);
                            $adjustedX = ($marginSizeTotal / 2);
                        }
                        
                        $out = imagecreatetruecolor($width, $height);
                        imagecopyresampled($out, $jpeg, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
                        imagecopyresampled($out, $png, 0, 0, $adjustedX, 0, $width2, $height2, $width_orig2-($marginSizeTotal), $height_orig2); 
                        
                        imagejpeg($out, $saveDir . "/" . $uuid . "_" . $page . "_" . $mechNumber . "_" . $mechName . '.jpg', 100);
                        unlink('/tmp/testMarkup.png');
                        unlink('/tmp/testMechPDF.jpg');
                    }
                }
            }
            
            echo json_encode('Mechs saved successfully. Can be accessed from /savedMechs.');
        }
    }
    else {
        echo json_encode('Incorrect password');
    }
}

?>