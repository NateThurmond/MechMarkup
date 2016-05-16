<?php

include('phpScripts/cookie.php');

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Mech Markup</title>
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        
        <link rel="icon" href="images/mechIcon.png">
        <link rel="stylesheet" href="css/mainPage.css" type="text/css" charset="utf-8" >
        
        <script src="js/jquery-1.12.2.min.js"></script>
        <script src="js/jquery.touchSwipe.js"></script>
        <!--<script src="js/googleAnalytics.js"></script>-->
    </head>
    <body>
        
        <!-- Menu Bar -->
        <div id='menuBar'>
            
            <!-- Home Tab -->
            <div class="tabContainer">
                <div class="viewTitle"></div>
                <div class='menuBarTabs filled' id='allMechs'>
                    <div class='menuBarTabsContainer'>
                        <div>
                            <p id="index">All Mechs</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- View 1 -->
            <div class="tabContainer">
                <div class="viewTitle" id="view1_">View 1</div>
                <div class='menuBarTabs notfilled'>
                    <div class='menuBarTabsContainer menuBarView' id='view1'>
                        <div>
                            <p class='removeWhenFilled'>Click to add</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- View 2 -->
            <div class="tabContainer">
                <div class="viewTitle" id="view2_">View 2</div>
                <div class='menuBarTabs notfilled'>
                    <div class='menuBarTabsContainer menuBarView' id='view2'>
                        <div>
                            <p class='removeWhenFilled'>Click to add</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- View 3 -->
            <div class="tabContainer">
                <div class="viewTitle" id="view3_">View 3</div>
                <div class='menuBarTabs notfilled'>
                    <div class='menuBarTabsContainer menuBarView' id='view3'>
                        <div>
                            <p class='removeWhenFilled'>Click to add</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- View 4 -->
            <div class="tabContainer">
                <div class="viewTitle" id="view4_">View 4</div>
                <div class='menuBarTabs notfilled'>
                    <div class='menuBarTabsContainer menuBarView' id='view4'>
                        <div>
                            <p class='removeWhenFilled'>Click to add</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Upload -->
            <div class="tabContainer uploadClass">
                <div class="uploadTitle">Upload Mech</div>
                <div id="uploadImage"></div>
            </div>
            
        </div>
        
        <!-- PDF view -->
        <div id='preview'>
            <div id='previewControls'>
                <div id='controlLeft'>
                    <div id="closePreview"></div>
                </div>
                <div id='controlCenter'>
                    <div id="prevMech"></div>
                    <button id="nextMech"></button>
                </div>
                <div id='controlRight'>
                    
                </div>
            </div>
            <div id='previewImage'></div>
        </div>
        
        <!-- Main controls -->
        <div id="mainControls">
            
            <!-- Sort controls -->
            <div id="sortBy">
                <!--<h5>Sort By</h5>-->
                <select id="sortKey">
                    <option>Name</option>
                    <option>Tonnage</option>
                    <option>Walk</option>
                    <option>Run</option>
                    <option>Jump</option>
                </select>
                <select id="sortOrder">
                    <option>Asc</option>
                    <option>Desc</option>
                </select>
            </div>
            
            <!-- Vertical Divider -->
            <div class="verticalLine"></div>
            
            <!-- Filter controls -->
            <div id="filterControls">
                <!--<h5>Filter Current View</h5>-->
                <input class="filterInput" id="filter_mechName" type="text" placeholder="Name" />
                <input class="filterInput" id="filter_tonnage" type="text" placeholder="Weight" />
                <input class="filterInput" id="filter_walk" type="text" placeholder="Walk" />
                <input class="filterInput" id="filter_run" type="text" placeholder="Run" />
                <input class="filterInput" id="filter_jump" type="text" placeholder="Jump" />
                <input class="filterInput" id="filter_weapons" type="text" placeholder="Weapons" />
                <input class="filterInput" id="filter_tags" type="text" placeholder="Tags" />
            </div>
            
            <!-- Vertical Divider -->
            <div class="verticalLine"></div>
            
            <!-- UnSelect Control -->
            <div id="unselectAll">
                <button id="unCheckAll">Uncheck All</button>
            </div>    
        </div>
        
        <!-- Mech Listings -->
        <div id="mechListings">
            
            <!-- Individual mechs here - content replace from javascript -->
            
        </div>
        
    </body>
    
    <script src='js/mainPage.js'></script>
   
</html>
