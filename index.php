<!DOCTYPE html>
<html>
    <head>
        <title>Mech Markup</title>
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        
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
            </div>
            
            <!-- View 1 -->
            <div class="tabContainer">
                <div class="viewTitle">View 1</div>
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
                <div class="viewTitle">View 2</div>
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
                <div class="viewTitle">View 3</div>
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
                <div class="viewTitle">View 4</div>
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
                <button id="closePreview">Close</button>
                <button id="prevMech">Prev</button>
                <button id="nextMech">Next</button>
                <input id="previewCheck" type="checkbox" />
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
                    var mechMovement = mechStats['walk'] + " / " + mechStats['run'] + " / " + mechStats['jump'];
                    
                    var parentElement = $('#mechListings');
                    var elemToAppend = '<div class="mechBox" id="mechBox_' + mechID +'"><div class="mechTitle"><input type="checkbox" id="checkBox_' +
                        mechID + '"/><h5>' + mechStats['mechName'] + '</h5></div><div class="mechStats" id="mechStats_' + mechID + '"><div class="mechTonnage"><label>Weight: </label><p>' +
                        mechStats['tonnage'] + '</p><div><label>Movement: </label><p>&nbsp' + mechMovement + '</p><div></div><div class="mechWalk"><label>Walk: </label><p>' +
                        mechStats['walk'] + '</p></div><div class="mechRun"><label>Run: </label><p>' + mechStats['run'] + '</p></div><div class="mechJump"><label>Jump: </label><p>' +
                        mechStats['jump'] + '</p></div><div class="mechWeapons"><label>Weapons: </label><p>' + mechStats['weapons'] + '</p></div class="mechTags"><div><label>Tags: </label><p>' +
                        mechStats['tags'] + '</p></div></div></div>';  
                        
                    parentElement.append(elemToAppend);
                    
                    $('#checkBox_' + mechID).data(mechStats);
                    $('#mechStats_' + mechID).data(mechStats);
                }
                
                pillBoxHandlers();
            });
            
            
            function pillBoxHandlers() {
                
                $('.menuBarView').click(function() {
                    
                    var viewID = this.id;
                    var viewNum = viewID.substr(viewID.length - 1);
                    
                    if ($(this).find('p').hasClass('removeWhenFilled')) {
                        var mechCounter = 0;
                    }
                    else {
                        var mechCounter = $(this).find('p').length;
                    }
                    
                    $('.mechBox:visible').each(function() {
                        
                        var mechChecked = $(this).find('input:checkbox:checked').prop('checked');
                        var mechData = $(this).find('input:checkbox:checked').data();
                        
                        if ((mechChecked) && (mechCounter < 5)) {
                            if (mechCounter == 0) {
                                $('#' + viewID).find('div p').remove();
                            }
                            $('#' + viewID + ' div').append('<p class="viewMech" id="viewMech_' + mechData['_id']
                                + '" class="viewMech"><strong class="viewMechRemove">&nbsp</strong>' + mechData['mechName'] + '</p>');
                            
                            $('#viewMech_' + mechData['_id']).data(mechData);
                            
                            mechCounter++;
                        }
                    });
                });
                
                $('#sortKey, #sortOrder').change(function() {
                    var sortKeyOption = $(this).find(":selected").text().toLowerCase();
                    var sortOrder = $('#sortOrder').find(":selected").text();
                    if (sortKeyOption == "name") {
                        sortKeyOption = "mechName";
                    }
                    
                    var elementsToSort = {};
                    
                    $('.mechBox').each(function() {
                        var mechData = $(this).find('input:checkbox').data();
                        //console.log(mechData);
                        elementsToSort[mechData[sortKeyOption]] = mechData['_id'];
                    });
                    
                    orderKeys(elementsToSort);
                    console.log(elementsToSort);
                });
                
                $('#unCheckAll').click(function() {
                    $('.mechTitle input:checkbox').each(function() {   
                        $(this).prop('checked', false);
                    })
                });

                $('#previewCheck').click(function() {
                    var currentMechID = $('#previewImage').data('refID');
                    $('#checkBox_' + currentMechID).prop('checked', $(this).prop('checked'));
                });
                
                $('.mechStats').click(function() {
                    var mechDetails = $(this).data();
                    var mechID = mechDetails['_id'];
                    resizePreview(mechID);
                });
                
                $('#closePreview').click(function() {
                    
                    $('#previewImage').removeData();
                    
                    $('#preview').css('display', 'none');
                    $('#preview').css('height', '100px');
                    $('#preview').css('width', '100px');
                    $('#previewImage').css('margin-top', '0px');
                });
                
                $('#nextMech').click(function() {
                    var currentMechID = $('#previewImage').data('refID');
                    var nextMechID = $('#mechBox_' + currentMechID).next('.mechBox:visible')
                    .children('.mechTitle').find(':checkbox').data('_id');
                    
                    if ((nextMechID != "undefined") && (nextMechID != null)) {
                        var imageRef = 'phpScripts/getPDF.php?mechID=' + nextMechID;
                        $('#previewImage').css('background-image', 'url(' + imageRef + ')');
                        $('#previewImage').data('refID', nextMechID);
                        $('#previewCheck').prop('checked', getMechCheckValue(nextMechID));
                    }
                    else {
                        var firstMechID = $('.mechBox:visible:first').children('.mechTitle').find(':checkbox').data('_id');
                        var imageRef = 'phpScripts/getPDF.php?mechID=' + firstMechID;
                        $('#previewImage').css('background-image', 'url(' + imageRef + ')');
                        $('#previewImage').data('refID', firstMechID);
                        $('#previewCheck').prop('checked', getMechCheckValue(firstMechID));
                    }
                });
                
                $('#prevMech').click(function() {
                    var currentMechID = $('#previewImage').data('refID');
                    var prevMechID = $('#mechBox_' + currentMechID).prev('.mechBox:visible')
                    .children('.mechTitle').find(':checkbox').data('_id');
                    
                    if ((prevMechID != "undefined") && (prevMechID != null)) {
                        var imageRef = 'phpScripts/getPDF.php?mechID=' + prevMechID;
                        $('#previewImage').css('background-image', 'url(' + imageRef + ')');
                        $('#previewImage').data('refID', prevMechID);
                        $('#previewCheck').prop('checked', getMechCheckValue(prevMechID));
                    }
                    else {
                        var lastMechID = $('.mechBox:visible:last').children('.mechTitle').find(':checkbox').data('_id');
                        var imageRef = 'phpScripts/getPDF.php?mechID=' + lastMechID;
                        $('#previewImage').css('background-image', 'url(' + imageRef + ')');
                        $('#previewImage').data('refID', lastMechID);
                        $('#previewCheck').prop('checked', getMechCheckValue(lastMechID));
                    }
                });
            }
            
            
            function resizePreview(mechID) {
                // default page ratio for pdf is 215.9 mm x 279.4 mm  -  0.7727272727272727
                //    1.294117647058824
                var previewData = {"refID":mechID};
                $('#preview').css('height', '100px');
                $('#preview').css('width', '100px');
                $('#previewImage').css('margin-top', '0px');
                $('#previewCheck').prop('checked', getMechCheckValue(mechID));
                
                var heightToStretch = $(window).height() - $('#menuBar').height() - 5;
                var widthToStretch = $('body').width();
                var aspectRatio = (widthToStretch / heightToStretch);
                
                if (aspectRatio < 0.7727) {
                    var heightToStretchNew = (widthToStretch * 1.294) - 5;
                    var heightDiff = ((heightToStretch - heightToStretchNew) / 2) - 5;
                    heightToStretch = heightToStretchNew;
                }
                
                $('#preview').css('min-height', heightToStretch + 'px');
                $('#preview').css('max-height', heightToStretch + 'px');
                $('#preview').css('min-width', widthToStretch + 'px');
                $('#preview').css('max-width', widthToStretch + 'px');
                $('#preview').css('display', 'block');
                $('#preview').css('background-color', '#DFE2DB');
                
                $('#previewImage').css('min-height', heightToStretch - 50 + 'px');
                $('#previewImage').css('max-height', heightToStretch - 50 + 'px');
                $('#previewImage').css('min-width', widthToStretch + 'px');
                $('#previewImage').css('max-width', widthToStretch + 'px');
                $('#previewImage').css('display', 'block');
                $('#previewImage').css('background-color', '#DFE2DB');
                $('#previewImage').css('margin-top', heightDiff + 'px');
                $('#previewImage').addClass('slideBackground');
                
                var imageRef = 'phpScripts/getPDF.php?mechID=' + mechID;
                $('#previewImage').css('background-image', 'url(' + imageRef + ')');
                $('#previewImage').data(previewData);
            }
            
            
            function getMechCheckValue(mechID) {
                return $('#checkBox_' + mechID).prop('checked')
            }
            
            
            window.addEventListener("resize", function() {
                if ($('#preview').css('display') == "block") {
                    resizePreview($('#previewImage').data('refID'));
                }
            }, false);
            
            
            function orderKeys(obj, expected) {
                var keys = Object.keys(obj).sort(function keyOrder(k1, k2) {
                    if (k1 < k2) return -1;
                    else if (k1 > k2) return +1;
                    else return 0;
                });
              
                var i, after = {};
                for (i = 0; i < keys.length; i++) {
                    after[keys[i]] = obj[keys[i]];
                    delete obj[keys[i]];
                }
              
                for (i = 0; i < keys.length; i++) {
                    obj[keys[i]] = after[keys[i]];
                }
                return obj;
            }
            
            
        });
    </script>
</html>
