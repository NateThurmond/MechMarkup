<!DOCTYPE html>
<html>
    <head>
        <title>Mech Markup</title>
        <link rel="icon" href="images/mechIcon.png">
        <link rel="stylesheet" href="css/mainPage.css" type="text/css" charset="utf-8" >
        
        <script src="js/jquery-1.12.2.min.js"></script>
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
                            <p>All Mechs</p>
                        </div>
                    </div>
                </div>
                <div class="pageIndicator"><div class="arrow-down filledArrow"></div></div>
            </div>
            
            <!-- View 1 -->
            <div class="tabContainer">
                <div class="viewTitle">View 1</div>
                <div class='menuBarTabs notfilled'>
                    <div class='menuBarTabsContainer'>
                        <div>
                            <p class='removeWhenFilled'>Drag Mechs Here</p>
                        </div>
                    </div>
                </div>
                <div class="pageIndicator"><div class="arrow-down"></div></div>
            </div>
            
            <!-- View 2 -->
            <div class="tabContainer">
                <div class="viewTitle">View 2</div>
                <div class='menuBarTabs notfilled'>
                    <div class='menuBarTabsContainer'>
                        <div>
                            <p class='removeWhenFilled'>Drag Mechs Here</p>
                        </div>
                    </div>
                </div>
                <div class="pageIndicator"><div class="arrow-down"></div></div>
            </div>
            
            <!-- View 3 -->
            <div class="tabContainer">
                <div class="viewTitle">View 3</div>
                <div class='menuBarTabs notfilled'>
                    <div class='menuBarTabsContainer'>
                        <div>
                            <p class='removeWhenFilled'>Drag Mechs Here</p>
                        </div>
                    </div>
                </div>
                <div class="pageIndicator"><div class="arrow-down"></div></div>
            </div>
            
            <!-- View 4 -->
            <div class="tabContainer">
                <div class="viewTitle">View 4</div>
                <div class='menuBarTabs notfilled'>
                    <div class='menuBarTabsContainer'>
                        <div>
                            <p class='removeWhenFilled'>Drag Mechs Here</p>
                        </div>
                    </div>
                </div>
                <div class="pageIndicator"><div class="arrow-down"></div></div>
            </div>
            
            <!-- Upload -->
            <div class="tabContainer uploadClass">
                <div class="uploadTitle">Upload Mech</div>
                <div id="uploadImage"></div>
            </div>
            
        </div>
        
        <!-- Main controls -->
        <div id="mainControls">
            
            <!-- Sort controls -->
            <div id="sortBy">
                <h5>Sort By</h5>
                <select id="sortKey">
                    <option>Name</option>
                    <option>Weight</option>
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
                <h5>Filter Current View</h5>
                <input id="filter_Name" type="text" placeholder="Name" />
                <input id="filter_Weight" type="text" placeholder="Weight" />
                <input id="filter_Walk" type="text" placeholder="Walk" />
                <input id="filter_Run" type="text" placeholder="Run" />
                <input id="Filter_Jump" type="text" placeholder="Jump" />
                <input id="Filter_Weapons" type="text" placeholder="Weapons" />
                <input id="Filter_Tags" type="text" placeholder="Tags" />
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
    
    <script>
        $(document).ready(function() {
            
            $.getJSON('phpScripts/getAllMechs.php', function(allMechs) {
                
                for (mech in allMechs) {
                    
                    var mechID = mech;
                    var mechStats = allMechs[mech];
                    mechStats['_id'] = mechID;
                    
                    var parentElement = $('#mechListings');
                    var elemToAppend = '<div class="mechBox"><div class="mechTitle"><input type="checkbox" id="checkBox_' +
                        mechID + '"/><h5>' + mechStats['mechName'] + '</h5></div><div class="mechStats" id="mechStats_' + mechID + '"><div><label>Weight: </label><p>' +
                        mechStats['tonnage'] + '</p></div><div><label>Walk: </label><p>' +
                        mechStats['walk'] + '</p></div><div><label>Run: </label><p>' + mechStats['run'] + '</p></div><div><label>Jump: </label><p>' +
                        mechStats['jump'] + '</p></div><div><label>Weapons: </label><p>' + mechStats['weapons'] + '</p></div><div><label>Tags: </label><p>' +
                        mechStats['tags'] + '</p></div></div></div>';  
                        
                    parentElement.append(elemToAppend);
                    
                    $('#checkBox_' + mechID).data(mechStats);
                    $('#mechStats_' + mechID).data(mechStats);
                    
                    console.log($('#checkBox_' + mechID).data());
                    console.log($('#mechStats_' + mechID).data());
                }
            });
        })
    </script>
</html>