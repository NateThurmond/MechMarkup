$(document).ready(function() {
  
    var keyupTimeoutID = 0;
    
    // Link Handlers
    $('#index').click(function() {
        location.replace('index.php');
    });
    
    $('.viewTitle').click(function() {
        if (this.id != "exportMechs") {
            var linkID = this.id.split('_')[0];
            location.replace(linkID + '.php');
        }
    });
    
    $.getJSON('./phpScripts/fetchViews.php', function(data) {
       for(view in data) {
        
            var viewID = view;
            var viewNum = viewID.substr(viewID.length - 1);
        
            if (data[view] != "") {
                
                $('#' + viewID + ' div').empty();
                
                var mechArray = data[view].split(',');
                for(mech in mechArray) {
                    var mechDetails = mechArray[mech].split('|');
                    var mechName = mechDetails[1];
                    var mechID = mechDetails[0].split('_')[0];
                    
                    var elemToAppend = '<p class="viewMech" id="viewMech_' + viewNum + '_' + mechID
                    + '"><strong class="viewMechRemove" id="viewMechRemove_' + mechID + '">&nbsp;</strong>' + mechName + '</p>';
                    
                    $('#' + viewID + ' div').append(elemToAppend);
                }
            }
       }
    });
    
    // Link to Upload page
    $('#uploadImage').click(function() {
        window.location='upload.php';
    });
    
    // button to export the mechviews
    $('#exportMechs').click(function() {
        var pass = prompt('Enter password to save mechs');
        
        if (pass != "" && pass != null) {
            $.getJSON('./phpScripts/exportMechs.php?pass=' + pass, function(saveResp) {
                alert(saveResp);    
            });
        }
    });
    
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
        
        $('.filterInput').on('input', function() {
            clearTimeout(keyupTimeoutID);
            keyupTimeoutID = setTimeout(function() {
              
                var filerObj = {};
              
                $('.filterInput').each(function() {
                    var elemID = this.id;
                    var filterID = elemID.split('filter_')[1];
                    
                    filerObj[filterID] = $(this).val().toLowerCase();
                })
                
                hideElements(filerObj);
                
            }, 1000);
        });
        
        $('.menuBarView').click(function(e) {
            var classArr = e.target.classList;
            
            var viewID = this.id;
            var viewNum = viewID.substr(viewID.length - 1);
            var mechCounter = 0;
            
            if (! $(this).find('p').hasClass('removeWhenFilled')) {
                var mechCounter = $(this).find('p').length;
            }

            if ($.inArray( 'viewMechRemove', classArr) >= 0) {
                var removeTag = e.target.id;
                removeTag = removeTag.replace('viewMechRemove_', '');
                
                var removeIndex = $('#viewMech_' + viewNum + '_' + removeTag).index();
                $.post('./phpScripts/reOrderMarkups.php?viewNum=' + viewNum + '&mechNum=' + (removeIndex + 1), function() {});
                
                $('#viewMech_' + viewNum + '_' + removeTag).remove();
                var elemCount = $('#' + viewID + ' div p').length;
                if (elemCount == 0) {
                    $('#' + viewID + ' div').append('<p class="removeWhenFilled">Click to add</p>');
                }
            }                    
            else if ($('#preview').is(":visible")) {
                var mechID = $('#previewImage').data('refID');
                var mechData = $('#checkBox_' + mechID).data();
                
                if (mechCounter < 5) {
                    if (mechCounter == 0) {
                        $('#' + viewID).find('div p').remove();
                    }
                    $('#' + viewID + ' div').append('<p class="viewMech" id="viewMech_' + viewNum + '_' + mechData['_id']
                        + '" class="viewMech"><strong class="viewMechRemove" id="viewMechRemove_'
                        + mechData['_id'] + '" >&nbsp</strong>' + mechData['mechName'] + '</p>');
                    
                    $('#viewMech_' + mechData['_id']).data(mechData);
                }
            }
            else if ($.inArray( 'viewMechRemove', classArr) == -1) {
                
                $('.mechBox:visible').each(function() {
                    
                    var mechChecked = $(this).find('input:checkbox:checked').prop('checked');
                    var mechData = $(this).find('input:checkbox:checked').data();
                    
                    if ((mechChecked) && (mechCounter < 5)) {
                        if (mechCounter == 0) {
                            $('#' + viewID).find('div p').remove();
                        }
                        $('#' + viewID + ' div').append('<p class="viewMech" id="viewMech_' + viewNum + '_' + mechData['_id']
                            + '" class="viewMech"><strong class="viewMechRemove" id="viewMechRemove_'
                            + mechData['_id'] + '" >&nbsp</strong>' + mechData['mechName'] + '</p>');
                        
                        $('#viewMech_' + mechData['_id']).data(mechData);
                        
                        mechCounter++;
                    }
                });
            }
            
            // Update views in database
            updateViews();
        });
        
        $('#sortKey, #sortOrder').change(function() {
            var sortKeyOption = $('#sortKey').find(":selected").text().toLowerCase();
            var sortOrder = $('#sortOrder').find(":selected").text();
            if (sortKeyOption == "name") {
                sortKeyOption = "mechName";
            }
            
            var elementsToSort = [];
            
            $('.mechBox').each(function() {
                var mechData = $(this).find('input:checkbox').data();
                elementsToSort.push(mechData[sortKeyOption] + "__" + mechData['_id']);
            });
            
            function sortNumber(a,b) {
                a = a.split("__")[0];
                b = b.split("__")[0];
                return a - b;
            }
            
            if (sortKeyOption == "mechName") {
                elementsToSort.sort();
            }
            else {
                elementsToSort.sort(sortNumber);
            }
            
            if (sortOrder == "Desc") {
                elementsToSort.reverse();
            }
            
            for (sortKey in elementsToSort) {
                var mechID = elementsToSort[sortKey].split("__")[1];
                
                var cloneElement = $('#mechBox_' + mechID).clone(true, true);
                $('#mechBox_' + mechID).remove();
                $('#mechListings').append(cloneElement);
            }
        });
        
        $('#unCheckAll').click(function() {
            $('.mechTitle input:checkbox').each(function() {   
                $(this).prop('checked', false);
            })
        });
        
        $('.mechStats').click(function() {
            var mechDetails = $(this).data();
            var mechID = mechDetails['_id'];
            resizePreview(mechID);
        });
        
        $('#closePreview').click(function() {
            
            // Reset back to static for scrolling through listings
            $('#mechListings').css('position', 'static');
            $('#mechListings').css('display', 'block');
            $('#mechListings').css('margin-left', '7%');
            
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
                $('#previewImage').data('refID', nextMechID);
            }
            else {
                var firstMechID = $('.mechBox:visible:first').children('.mechTitle').find(':checkbox').data('_id');
                var imageRef = 'phpScripts/getPDF.php?mechID=' + firstMechID;
                $('#previewImage').data('refID', firstMechID);
            }
            
            $('#previewImage').animate({
                opacity: 0, // animate slideUp
                }, 150, function() {
            
                $('#previewImage').css('background-image', 'url(' + imageRef + ')');
                $('#previewImage').animate({opacity: 1}, 550);
            });
        });
        
        $('#prevMech').click(function() {
            var currentMechID = $('#previewImage').data('refID');
            var prevMechID = $('#mechBox_' + currentMechID).prev('.mechBox:visible')
            .children('.mechTitle').find(':checkbox').data('_id');
            
            if ((prevMechID != "undefined") && (prevMechID != null)) {
                var imageRef = 'phpScripts/getPDF.php?mechID=' + prevMechID;
                $('#previewImage').data('refID', prevMechID);
            }
            else {
                var lastMechID = $('.mechBox:visible:last').children('.mechTitle').find(':checkbox').data('_id');
                var imageRef = 'phpScripts/getPDF.php?mechID=' + lastMechID;
                $('#previewImage').data('refID', lastMechID);
            }
            
            $('#previewImage').animate({
                opacity: 0, // animate slideUp
                }, 150, function() {
            
                $('#previewImage').css('background-image', 'url(' + imageRef + ')');
                $('#previewImage').animate({opacity: 1}, 550);
            });
        });
    };
    
    
    function resizePreview(mechID) {
        
        // Reset to static to get correct portions for preview
        $('#mechListings').css('position', 'static');
        $('#mechListings').css('display', 'block');
        $('#mechListings').css('margin-left', '7%');
        
        // default page ratio for pdf is 215.9 mm x 279.4 mm  -  0.7727272727272727
        //    1.294117647058824
        var previewData = {"refID":mechID};
        $('#preview').css('height', '100px');
        $('#preview').css('width', '100px');
        $('#previewImage').css('margin-top', '0px');
        $('#previewImage').css('opacity', 0);
        
        var heightToStretch = $(window).height() - $('#menuBar').height() - 0;
        var widthToStretch = $('body').width();
        var aspectRatio = (widthToStretch / heightToStretch);
        
        if (aspectRatio < 0.7727) {
            var heightToStretchNew = (widthToStretch * 1.294) - 0;
            var heightDiff = ((heightToStretch - heightToStretchNew) / 2) - 0;
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
        $('#previewImage').animate({opacity: 1}, 600);
        
        // Needed to prevent scrolling through listings on preview screen
        $('#mechListings').css('position', 'fixed');
        $('#mechListings').css('display', 'fixed');
        $('#mechListings').css('margin-left', '100%');
    };
    
    
    $("#preview").swipe({
        swipe:function(event, direction, distance, duration, fingerCount) {
            if ((distance > ($(window).width() / 2)) && (fingerCount >= 1)) {
                switch(direction) {
                    case "right":
                        $('#prevMech').click();
                        break;
                    case "left":
                        $('#nextMech').click();
                        break;
                }
            }
        }
    });
    
    
    function getMechCheckValue(mechID) {
        return $('#checkBox_' + mechID).prop('checked')
    };
    
    
    function hideElements(filerObj) {
        
        $('.mechBox').each(function() {
          var mechBoxData = $(this).find('.mechTitle input:checkbox').data();
          var hideElement = false;
          for (var field in mechBoxData) {
              var fieldValue = mechBoxData[field].toLowerCase();
              if ((fieldValue.indexOf(filerObj[field]) < 0) && (filerObj[field] != "") && (filerObj[field] != null)) {
                  hideElement = true;
                  break;
              }
          }
          
          if (hideElement) {
              $(this).css('display', 'none');
          }
          else {
              $(this).css('display', 'inline-block');
          }
          
        });
    };
    
    
    function updateViews() {
        // Update views in database
        var viewsToUpdate = {};
        $('.menuBarView').each(function() {
            var viewID = this.id;
            var viewNum = viewID.substr(viewID.length - 1);
            var viewMechStr = "";
            var viewMechCounter = 0;
            
            $(this).find('p').each(function() {
                var viewMechTagID = $(this).attr('id');
                var viewMechTagName = $(this).html().split('</strong>')[1];
                if (viewMechTagID != null) {
                    viewMechCounter++;
                    var viewMechID = viewMechTagID.split(viewNum + '_')[1];
                    viewMechStr += viewMechID + "_" + viewMechCounter + "|" + viewMechTagName + ",";
                }
            })
            
            viewMechStr = viewMechStr.replace(/,\s*$/, "");
            viewsToUpdate[viewID] = viewMechStr;
        });
        
        $.post("phpScripts/updateViews.php", JSON.stringify(viewsToUpdate))
        .done(function( data ) {
          if (data != "updated views") {
            console.log('Error updating views');
          }
        });
    };
    
    
    window.addEventListener("resize", function() {
        if ($('#preview').css('display') == "block") {
            resizePreview($('#previewImage').data('refID'));
        }
    }, false);
    
    
});