<?php

require_once('../config/config.php');
require_once('./class.MongoOps.php');


if (isset($GLOBALS["HTTP_RAW_POST_DATA"])) {
    
    $mechDataRaw = $GLOBALS['HTTP_RAW_POST_DATA'];
    $mechDataAll = json_decode($mechDataRaw);
    
    $mechName = $mechDataAll->mechName;
    $tonnage = $mechDataAll->tonnage;
    $tags = $mechDataAll->tags;
    $walk = $mechDataAll->walk;
    $run = $mechDataAll->run;
    $jump = $mechDataAll->jump;
    $weapons = $mechDataAll->weapons;
    
    $userName = $mechDataAll->userName;
    $password = $mechDataAll->password;
    
    if (($userName == MONGO_USER) && ($password == MONGO_PASS)) {
        
        $pdf = $mechDataAll->pdf;
        $pdf = str_replace('data:application/pdf;base64,', '', $pdf);
        $pdf = base64_decode($pdf);
        
        $strArray = [$mechName, $weapons, $pdf];
        $intArray = [$walk, $run, $jump, $tonnage];
        $noErrors = true;
        
        foreach($strArray as $str) {
            if (($str == null) || ($str == "") || (strlen($str) < 3)) {
                $noErrors = false;
            }
        }
        foreach($intArray as $ourInt) {
            if (($ourInt == null) || ($ourInt == "") || (intval($ourInt) < 0) ||
                (intval($ourInt) > 100) || (! is_numeric($ourInt))) {
                $noErrors = false;
            }
        }
        
        if ($noErrors) {
            
            $tmpJPG = '/tmp/' . uniqid() . ".jpg";
            $tmpPDF = '/tmp/' . uniqid() . ".pdf";
            file_put_contents($tmpPDF, $pdf);
            
            $command = 'gs -dNOPAUSE -dBATCH -sDEVICE=jpeg -r144 -sOutputFile=' . $tmpJPG . ' ' . $tmpPDF;
            shell_exec($command);
            
            $jpgToWrite = file_get_contents($tmpJPG);
            $jpgToWrite = base64_encode($jpgToWrite);
            
            unlink($tmpJPG);
            unlink($tmpPDF);
            
            $mongoOps = new mongoOps();
            
            $mechData = array(
                'mechName' => (string)$mechName,
                'tags' => (string)$tags,
                'tonnage' => (string)$tonnage,
                'walk' => (string)$walk,
                'run' => (string)$run,
                'jump' => (string)$jump,
                'weapons' => (string)$weapons
            );
            
            $mongoOps->insert($mechData, 'MONGO_COLL_MECHS');
            $createdMechID =  $mechData['_id'];
            $createdMechID = strval($createdMechID);
            
            $mechSheet = array(
                'refID' => $createdMechID,
                'image' => $jpgToWrite
            );
            
            $mongoOps->insert($mechSheet, 'MONGO_COLL_PDFS');
            
            echo json_encode('Successfully Uploaded Mech');

        }
        else {
            echo json_encode('Invalid input, Mech name and weapons must be strings longer than 3 characters. Walk, Run, Jump, and Weight must be integers in proper range.');
            exit(0);
        }
    }
    else {
        echo json_encode('Incorrect Username or password');
        exit(0);
    }
}

?>
