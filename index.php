<!DOCTYPE html>
<html>
    <head>
        <title>Mech Markup</title>
        <link rel="icon" href="images/mechIcon.png">
        <link rel="stylesheet" href="css/mainPage.css" type="text/css" charset="utf-8" >
        
        <script src="js/jquery-1.12.2.min.js"></script>
    </head>
    <body>
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
            
        </div>
    </body>
    
    <script>
        $(document).ready(function() {
            
            $.getJSON('phpScripts/getAllMechs.php', function(allMechs) {
                console.log(allMechs);    
            });
        })
    </script>
</html>