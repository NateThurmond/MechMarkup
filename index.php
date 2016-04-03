<!DOCTYPE html>
<html>
    <head>
        <title>Mech Markup</title>
        <link rel="icon" href="images/mechIcon.png">
        <link rel="stylesheet" href="css/mainPage.css">
        
        <script src="js/jquery-1.12.2.min.js"></script>
    </head>
    <body>
        <div id='menuBar'>
            <div class='menuBarTabs' id='allMechs'>
                <div class='menuBarTabsContainer'>
                    <div>
                        <p>All Mechs</p>
                    </div>
                </div>
            </div>
        </div>
    </body>
    
    <script>
        $(document).ready(function() {
            
            $.getJSON('phpScripts/getMechListings.php', function(allMechs) {
                console.log(allMechs);    
            });
        })
    </script>
</html>