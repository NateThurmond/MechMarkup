var critZoom = {zoomLevel: 1.65, xOff: .32, yOff: 0.2545};
var critArmor = {zoomLevel: 2.1, xOff: .47, yOff: -0.5045};
var weaponArea = {zoomLevel: 2.6, xOff: -0.54, yOff: 0.7245};
var mechArmor = {zoomLevel: 2.4, xOff: -0.575, yOff: -0.6995};
var pilotSection = {zoomLevel: 3, xOff: -0.675, yOff: -0.06};


$(document).ready(function() {
    
    var isMobile = detectMobile();
    var halfPageWidth = 0;
    var halfPageHeight = 0;
    var pdfHeight = 0;
    var pdfWidth = 0;
    var zoomModeActive = false;
    var zoomModeSet = false;
    var zoomArea = "critZoom";

    $("#canvas").panzoom({
        disablePan: true
    });            
    
    $('#zoomIn').click(function() {
        zoomModeSet = true;
        $('#zoomIn').attr('disabled', true);
    });
    
    $('#zoomOut').click(function() {
        zoomModeSet = false;
        zoomModeActive = false;
        
        $('#zoomIn').attr('disabled', false);
        $("#canvas").panzoom("zoom", 1, { silent: true });
        $("#canvas").css('position', 'static');
        $("#canvas").css('top', '0px');
    });
    
    $('#pen').customSelect({customClass:'penSelect'});
    $('#circle').customSelect({customClass:'circleSelect'});
    $('#drawColor').customSelect({customClass:'drawColorSelect'});
    $('#eraser').customSelect({customClass:'eraserSelect'});

    viewMechs = {};
    mechNumMod = 0;
    mechNumTotal = 0;
    disableDraw = false;
    
    $.getJSON('./phpScripts/fetchViews.php', function(data) {
        var viewMechsAll = data[pageTitle];
        
        if ((viewMechsAll == null) || (viewMechsAll == "")) {
            viewMechs = "";
        }
        else {            
            var viewMechsSplit = viewMechsAll.split(',');
            mechNumTotal = viewMechsSplit.length;
            
            for (mechs in viewMechsSplit) {
                var mechParts = viewMechsSplit[mechs].split('|');
                var mechName = mechParts[1];
                var mechIDref = mechParts[0];
                var mechID = mechParts[0].split('_')[0];
                var mechNum = mechParts[0].split('_')[1];
                var imageRef = 'phpScripts/getPDF.php?mechID=' + mechID;
                
                var indMech = {mechName:mechName, refID: mechID, imageRef:imageRef, mechNum:mechNum, numRefID: mechIDref};
                viewMechs[mechNum] = indMech;
            }
        }
        
        forcePortrait();
    });
    
    
    $('#disableDraw').click(function() {
        
        disableDraw = ! disableDraw;
        
        if (disableDraw) {
            $('.overlay').remove();
            $('#previewCanvas').append('<div class="overlay" style="top:81px; height:'
                + $('#previewCanvas').height() + 'px; width: ' + $(window).width() + 'px;"></div>');
            
            $('head').remove('#userZoom');
            $('head').append('<meta id="userZoom" name="viewport" content="user-scalable=1">');
            
            $(".overlay").swipe({
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
            
            $('#clearCanvas').prop('disabled', true);
        }
        else {
            $('.overlay').remove();
            
            $('head').remove('#userZoom');
            $('head').append('<meta id="userZoom" name="viewport" content="user-scalable=0">');
            
            $('#clearCanvas').prop('disabled', false);
        }
        
        saveMarkup();
    });
    
    
    $('#clearCanvas').click(function() {
        if (confirm('Clear Image?')) {
            var mycanvas = document.getElementById("canvas");
            var ctx = mycanvas.getContext("2d");
            ctx.clearRect(0, 0, mycanvas.width, mycanvas.height);
        }
    });


    /*   VARIABLES USED FOR BOTH MOBILE AND BROWSER BASED DRAWING  */
    var canvas=document.getElementById("canvas");
    var ctx=canvas.getContext("2d");
    var lastX;
    var lastY;
    var mouseX;
    var mouseY;
    var canvasOffset=$("#canvas").offset();
    var offsetX=canvasOffset.left;
    var offsetY=canvasOffset.top;
    var isMouseDown=false;
    
    /*   VARIABLES SET BY USERS */
    var drawColor = "#FF0000";
    var penWidth = 2;
    var circleWidth = 4;
    var eraserWidth = 6;
    var mode="pen";
    
    
    /* Handlers for draw control buttons/selects */
    $('#pen').bind('click change', function() {
        mode="pen";
        penWidth = $("#pen option:selected").val();
        
        saveMarkup();
    });
    $('#circle').bind('click change', function() {
        mode="circle";
        circleWidth = $("#circle option:selected").val();
        
        saveMarkup();
    });
    $('#drawColor').bind('click change', function() {
        mode="pen";
        drawColor = $("#drawColor option:selected").val();
        
        saveMarkup();
    });
    $('#eraser').bind('click change', function() {
        mode="eraser";
        eraserWidth = $("#eraser option:selected").val();
        
        saveMarkup();
    });


    /*   CODE FOR MOBILE DRAWING         */	
    function handleMouseDown(X, Y){
      mouseX=parseInt(X - offsetX);
      mouseY=parseInt(Y - offsetY);
    
      // Put your mousedown stuff here
      lastX=mouseX;
      lastY=mouseY;
      isMouseDown=true;
    };
    
    function handleMouseUp(X, Y){
      mouseX=parseInt(X-offsetX);
      mouseY=parseInt(Y-offsetY);
    
      // Put your mouseup stuff here
      isMouseDown=false;
    };
    
    function handleMouseOut(X, Y){
      mouseX=parseInt(X-offsetX);
      mouseY=parseInt(Y-offsetY);
    
      // Put your mouseOut stuff here
      isMouseDown=false;
    };
    
    function handleMouseMove(X, Y){
        
      mouseX=parseInt(X - offsetX);
      mouseY=parseInt(Y - offsetY);
    
      // Put your mousemove stuff here
      if(isMouseDown){
        ctx.beginPath();
        if(mode=="pen") {
            
          ctx.lineWidth = penWidth;
          ctx.strokeStyle = drawColor;	
            
          ctx.globalCompositeOperation="source-over";
          ctx.moveTo(lastX,lastY);
          ctx.lineTo(mouseX,mouseY);
          ctx.stroke(); 
        }
        else if (mode=="circle") {
            ctx.globalCompositeOperation="source-over";
            ctx.fillStyle = drawColor;
            ctx.arc(lastX,lastY,circleWidth,0,Math.PI*2,false);
            ctx.fill(); 
        }
        else if(mode=="eraser") {
          ctx.globalCompositeOperation="destination-out";
          ctx.arc(lastX,lastY,eraserWidth,0,Math.PI*2,false);
          ctx.fill();
        }
        lastX=mouseX;
        lastY=mouseY;
        ctx.globalCompositeOperation="source-over";
      }
    };
    

    $('#nextMech').click(function() {
        $('#zoomOut').click();
        
        if (viewMechs != "") {
            $('#canvas').animate({
                opacity: 0, // animate slideUp
                marginLeft: '-1000px'
                }, 400, 'linear', function() {
            
                $('#canvas').css('opacity', '100');
                $('#canvas').css('marginLeft', '0px');
            });
            // Update the current markup for the view
            var mycanvas = document.getElementById("canvas");
            var image    = mycanvas.toDataURL("image/jpg");
            var currentMech = (mechNumMod.mod(mechNumTotal)) + 1;
            
            updateMarkup(currentMech, image, function() {
                // get the next mech background
                mechNumMod++;
                var nextMech = (mechNumMod.mod(mechNumTotal)) + 1;
                var imageRef = viewMechs[nextMech]['imageRef'];
                $('#canvas').css('background-image', 'url(' + imageRef + ')');
    
                // get the next mech markup
                var ctx = document.getElementById("canvas").getContext("2d");
                fetchMarkup(pageNum, nextMech, ctx, mycanvas);
            });
        }
    });
    
    $('#prevMech').click(function() {
        $('#zoomOut').click();
        
        if (viewMechs != "") {
            $('#canvas').animate({
                opacity: 0, // animate slideUp
                marginLeft: '1000px'
                }, 400, 'linear', function() {
            
                $('#canvas').css('opacity', '100');
                $('#canvas').css('marginLeft', '0px');
            });
            // Update the current markup for the view
            var mycanvas = document.getElementById("canvas");
            var image    = mycanvas.toDataURL("image/jpg");
            var currentMech = (mechNumMod.mod(mechNumTotal)) + 1;
            
            updateMarkup(currentMech, image, function() {
                // get the prev mech background
                mechNumMod--;
                var prevMech = (mechNumMod.mod(mechNumTotal)) + 1;
                var imageRef = viewMechs[prevMech]['imageRef'];
                $('#canvas').css('background-image', 'url(' + imageRef + ')');
                
                // get the prev mech markup
                var ctx = document.getElementById("canvas").getContext("2d");
                fetchMarkup(pageNum, prevMech, ctx, mycanvas);
            });
        }
    });
    
    
    function resizePreview() {
        
        $('#fillerDiv').remove();
        $('#previewCanvas').remove('#fillerDiv');
        $('#previewCanvas').css('height', $(window).height() - $('#floatBar').height());
        
        var heightToStretch = $('#previewCanvas').height();
        var widthToStretch = $('body').width();
        var aspectRatio = (widthToStretch / heightToStretch);
        
        if (aspectRatio < 0.7727) {
            var heightToStretchNew = (widthToStretch * 1.294) - 0;
            var heightDiff = ((heightToStretch - heightToStretchNew) / 2) - 0;
            heightToStretch = heightToStretchNew;
        }
        
        pdfHeight = heightToStretch;
        pdfWidth = pdfHeight * 0.7727;
        
        $('#canvas').attr('height', heightToStretch);
        $('#canvas').attr('width', widthToStretch);
        $('#canvas').css('min-height', heightToStretch + 'px');
        $('#canvas').css('max-height', heightToStretch + 'px');
        $('#canvas').css('min-width', widthToStretch + 'px');
        $('#canvas').css('max-width', widthToStretch + 'px');
        $('#canvas').css('display', 'block');
        $('#canvas').css('background-color', '#DFE2DB');
        
        var nextMech = (mechNumMod % mechNumTotal) + 1;
        if (viewMechs != "") {
            var imageRef = viewMechs[nextMech]['imageRef'];
            $('#canvas').css('background-image', 'url(' + imageRef + ')');
        }
        
        var mycanvas = document.getElementById("canvas");
        var ctx = document.getElementById("canvas").getContext("2d");
        fetchMarkup(pageNum, "1", ctx, mycanvas);
        
        var marginTop = ($(window).height() - $('#floatBar').height() - $('#canvas').height()) / 2;
        
        halfPageWidth = widthToStretch / 2;
        halfPageHeight = ($('#canvas').height() / 2) + $('#floatBar').height();
        
        if (marginTop > 0) {
            marginTop = marginTop - 13;
            $('#previewCanvas').prepend('<div id="fillerDiv" style="float:top; min-height: ' + marginTop + 'px;"></div>');
            marginTop = marginTop - 2;
        }
        else {
            marginTop = -2;
        }
        
        if (isMobile) {
            $('#canvas').bind('touchstart', function(e) {
                if (zoomModeSet) {
                    calcZoomScope(e.originalEvent.touches[0].clientX, e.originalEvent.touches[0].clientY);
                }
                else {
                    var offsets = calcXY(e.originalEvent.touches[0].clientX, e.originalEvent.touches[0].clientY,
                        halfPageWidth, halfPageHeight, marginTop, zoomModeActive);
                    handleMouseDown(offsets[0], offsets[1]);
                }
            })
            $('#canvas').bind('touchmove', function(e) {
                if (! zoomModeSet) {
                    var offsets = calcXY(e.originalEvent.touches[0].clientX, e.originalEvent.touches[0].clientY,
                        halfPageWidth, halfPageHeight, marginTop, zoomModeActive);
                    handleMouseMove(offsets[0], offsets[1]);
                }
            })
            $('#canvas').bind('click', function(e) {
                if (! zoomModeSet) {
                    var offsets = calcXY(e.clientX, e.clientY, halfPageWidth, halfPageHeight, marginTop, zoomModeActive);
                    handleMouseMove(offsets[0], offsets[1]);
                }
            })
        }
        else {
            $('#canvas').bind('mousedown', function(e) {
                if (zoomModeSet) {
                    calcZoomScope(e.clientX, e.clientY);
                }
                else {
                    var offsets = calcXY(e.clientX, e.clientY, halfPageWidth, halfPageHeight, marginTop, zoomModeActive);
                    handleMouseDown(offsets[0], offsets[1]);
                }
            })
            $('#canvas').bind('mousemove', function(e) {
                if (! zoomModeSet) {
                    var offsets = calcXY(e.clientX, e.clientY, halfPageWidth, halfPageHeight, marginTop, zoomModeActive);
                    handleMouseMove(offsets[0], offsets[1]);
                }
            })
            $('#canvas').bind('mouseup', function(e) {
                if (! zoomModeSet) {
                    var offsets = calcXY(e.clientX, e.clientY, halfPageWidth, halfPageHeight, marginTop, zoomModeActive);
                    handleMouseUp(offsets[0], offsets[1]);
                }
            })
            $('#canvas').bind('mouseout', function(e) {
                if (! zoomModeSet) {
                    var offsets = calcXY(e.clientX, e.clientY, halfPageWidth, halfPageHeight, marginTop, zoomModeActive);
                    handleMouseOut(offsets[0], offsets[1]);
                }
            })
        }
    };
    
    
    $(window).swipe({
        swipe:function(event, direction, distance, duration, fingerCount) {
            switch(direction) {
                case "up":
                    event.preventDefault();
                    break;
                case "down":
                    event.preventDefault();
                    break;
            }
        }
    });
    
    
    $('.viewTitle').click(function() {
        
        var linkID = this.id.split('_')[0];
        
        if (viewMechs == "") {
            location.replace(linkID + '.php');
        }
        else {       
            // Update the current markup for the view
            var mycanvas = document.getElementById("canvas");
            var image    = mycanvas.toDataURL("image/jpg");
            var currentMech = (mechNumMod.mod(mechNumTotal)) + 1;
            
            updateMarkup(currentMech, image, function() {
                // get the prev mech background
                mechNumMod--;
                var prevMech = (mechNumMod.mod(mechNumTotal)) + 1;
                var imageRef = viewMechs[prevMech]['imageRef'];
                $('#canvas').css('background-image', 'url(' + imageRef + ')');
                
                // get the prev mech markup
                var ctx = document.getElementById("canvas").getContext("2d");
                fetchMarkup(pageNum, prevMech, ctx, mycanvas);
                
                location.replace(linkID + '.php');
            });
        }
    });
    
    
    function forcePortrait() {
        if ((window.innerHeight < window.innerWidth) && isMobile) {
            alert("Portrait mode works best");
            window.setTimeout(function() {
                forcePortrait();
            }, 200);
        }
        else {
            resizePreview();
        }
    };
    
    function saveMarkup() {
        if (viewMechs != "") {
            // Update the current markup for the view
            var mycanvas = document.getElementById("canvas");
            var image    = mycanvas.toDataURL("image/jpg");
            var currentMech = (mechNumMod.mod(mechNumTotal)) + 1;
            
            updateMarkup(currentMech, image, function() {});
        }
    };
    
    function updateMarkup(currentMech, image, callback) {    
        var markupToUpdate = {markup:image};
        
        $.post("phpScripts/updateMarkups.php?viewNum=" + pageNum +
           "&mechNum=" + currentMech, JSON.stringify(markupToUpdate))
        .done(function(data) {
            callback();
        });
    };
    
    function calcZoomScope(passedX, passedY) {
        var calcX = passedX - (($(window).width() - pdfWidth) / 2);
        var calcY = passedY - $('#floatBar').height();
        if ($('#fillerDiv').height() != null) {
            calcY -= $('#fillerDiv').height();
        }
        
        if ((calcX < (pdfWidth * 0.65)) && (calcY > (pdfHeight * 0.47))) {
            zoomArea = "critZoom";
        }
        else if ((calcX > (pdfWidth * 0.65)) && (calcY > (pdfHeight * 0.47))) {
            zoomArea = "critArmor";
        }
        else if ((calcX < (pdfWidth * 0.41)) && (calcY < (pdfHeight * 0.47))) {
            zoomArea = "weaponArea";
        }
        else if ((calcX > (pdfWidth * 0.65)) && (calcY < (pdfHeight * 0.47))) {
            zoomArea = "mechArmor";
        }
        else if ((calcX < (pdfWidth * 0.65)) && (calcX > (pdfWidth * 0.41)) &&
                 (calcY < (pdfHeight * 0.47))) {
            zoomArea = "pilotSection";
        }
        
        if (zoomArea != "") {
            $("#canvas").panzoom("zoom", window[zoomArea]['zoomLevel'], {});
            $("#canvas").css('position', 'relative');
            $("#canvas").css('top', - (pdfHeight * window[zoomArea]['xOff']) + 'px');
            $("#canvas").css('left', (pdfWidth * window[zoomArea]['yOff']) + 'px');
            
            setTimeout(function(){
                zoomModeActive = true;
                zoomModeSet = false;
            }, 500);
        }
    };
    
    function calcXY(clientX, clientY, halfPageWidth, halfPageHeight, marginTop, zoomModeActive) {
        var offsetX = clientX;
        var offsetY = clientY - marginTop;
        
        if (zoomModeActive) {
            if (clientX > halfPageWidth) {
                offsetX = halfPageWidth + ((clientX - halfPageWidth) / window[zoomArea]['zoomLevel']) - ((pdfWidth * window[zoomArea]['yOff']) / window[zoomArea]['zoomLevel']);
            }
            else {
                offsetX = halfPageWidth - ((halfPageWidth - clientX) / window[zoomArea]['zoomLevel']) - ((pdfWidth * window[zoomArea]['yOff']) / window[zoomArea]['zoomLevel']);
            }
            
            if (marginTop > 0) {
                marginTop = marginTop / window[zoomArea]['zoomLevel'];
            }
            
            if (clientY > halfPageHeight) {
                offsetY = halfPageHeight + ((clientY - halfPageHeight) / window[zoomArea]['zoomLevel']) - (marginTop) + ((pdfHeight * window[zoomArea]['xOff']) / window[zoomArea]['zoomLevel']);
            }
            else {
                offsetY = halfPageHeight - ((halfPageHeight - clientY) / window[zoomArea]['zoomLevel']) - (marginTop) + ((pdfHeight * window[zoomArea]['xOff']) / window[zoomArea]['zoomLevel']);
            }
        }
        
        return [offsetX, offsetY];
    };
    
    function fetchMarkup(pageNum, mechNum, ctx, mycanvas) {
        $.getJSON('./phpScripts/fetchMarkups.php?viewNum=' + pageNum + '&mechNum=' + mechNum,
            function(imageSrc) {
                    
                ctx.clearRect(0, 0, mycanvas.width, mycanvas.height);

                if (! $.isEmptyObject(imageSrc)) {
                    var photo = new Image();
                    
                    photo.onload = function() {
                        ctx.drawImage(photo, 0, 0);
                    };
                    photo.src = imageSrc;
                }
        });
    };
    
    function detectMobile() {
        if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
            return true;
        }
        else {
            return false;
        }
    };
    
    Number.prototype.mod = function(n) {
        return ((this%n)+n)%n;
    };
    
    
});