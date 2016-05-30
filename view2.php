<?php

include('phpScripts/cookie.php');

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Mech Markup View 2</title>
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta id="userZoom" name="viewport" content="user-scalable=0">
        
        <link rel="icon" href="images/mechIcon.png">
        <link rel="stylesheet" href="css/mainPage.css" type="text/css" charset="utf-8" >
        <link rel="stylesheet" href="css/view.css" type="text/css" charset="utf-8" >
        
        <script src="js/jquery-1.12.2.min.js"></script>
        <script src="js/jquery.touchSwipe.js"></script>
        <script src='js/jquery.customSelect.min.js'></script>
        <script src="js/panzoom.js"></script>
    </head>
    <body>
        
        <div id='floatBar'>
        
            <!-- Menu Bar -->
            <div id='menuBar'>
                
                <!-- Home Tab -->
                <div class="tabContainer">
                    <div class="viewTitle" id="index_">All Mechs</div>
                </div>
                
                <!-- View 1 -->
                <div class="tabContainer">
                    <div class="viewTitle" id="view1_">View 1</div>
                </div>
                
                <!-- View 2 -->
                <div class="tabContainer">
                    <div class="viewTitle" id="view2_" style="color: yellow;">View 2</div>
                </div>
                
                <!-- View 3 -->
                <div class="tabContainer">
                    <div class="viewTitle" id="view3_">View 3</div>
                </div>
                
                <!-- View 4 -->
                <div class="tabContainer">
                    <div class="viewTitle" id="view4_">View 4</div>
                </div>
                
            </div>
            
            <div id='previewControls'>
                <div id='controlLeft'>
                    <div id="prevMech"></div>
                </div>
                
                <div id='controlCenter'>
                    <select id="pen" class="drawOption">
                        <option value="2">2</option>
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="20">20</option>
                    </select>
                    <select id="circle" class="drawOption">
                        <option value="4">5</option>
                        <option value="6">10</option>
                        <option value="8">15</option>
                        <option value="12">20</option>
                        <option value="16">25</option>
                    </select>
                    <select id="text" class="drawOption">
                        <option value="6">5</option>
                        <option value="12">10</option>
                        <option value="18">15</option>
                        <option value="24">20</option>
                        <option value="30">25</option>
                    </select>
                    <select id="drawColor" class="drawOption">
                        <option value="#ff0000">Red</option>
                        <option value="#000000">Black</option>
                        <option value="#0000ff">Blue</option>
                        <option value="#008000">Green</option>
                        <option value="#ffff00">Yellow</option>
                    </select>
                    <select id="eraser" class="drawOption">
                        <option value="6">5</option>
                        <option value="12">10</option>
                        <option value="18">15</option>
                        <option value="24">20</option>
                        <option value="30">25</option>
                    </select>
                    
                    <button id="clearCanvas"></button>
                    
                    <button id="disableDraw">Draw On/Off</button>
                    
                    <button id="zoom_1" class="zoomButton">Zoom 1</button>
                    <button id="zoom_2" class="zoomButton">Zoom 2</button>
                    <button id="zoom_3" class="zoomButton">Zoom 3</button>
                    <button id="zoom_4" class="zoomButton">Zoom 4</button>
                    <button id="zoom_5" class="zoomButton">Zoom 5</button>
                </div>
                
                <div id='controlRight'>
                    <button id="nextMech"></button>
                </div>
            </div>
        
        </div>
        
        <!-- PDF view -->
        <div id='previewCanvas'>
            <div class="overlay"></div>
        </div>
        
    </body>
    
    <script>
        pageNum = "2";
        pageTitle = "view2";
    </script>
    <script src='js/view.js'></script>
    
</html>
