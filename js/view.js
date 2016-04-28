$(document).ready(function() {
    
    $('#pen').customSelect({customClass:'penSelect'});
    $('#circle').customSelect({customClass:'circleSelect'});
    $('#drawColor').customSelect({customClass:'drawColorSelect'});
    $('#eraser').customSelect({customClass:'eraserSelect'});

    viewMechs = {};
    mechNumMod = 0;
    mechNumTotal = 0;
    
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

    
    function detectMobile() {
        if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
            return true;
        }
        else {
            return false;
        }
    };
    
    $('#disableDraw').click(function() {
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
    });
    
    $('#enableDraw').click(function() {
        $('.overlay').remove();
        
        $('head').remove('#userZoom');
        $('head').append('<meta id="userZoom" name="viewport" content="user-scalable=0">');
        
        
        $('#clearCanvas').prop('disabled', false);
    });
    
    
    $('#clearCanvas').click(function() {
        var mycanvas = document.getElementById("canvas");
        var ctx = mycanvas.getContext("2d");
        ctx.clearRect(0, 0, mycanvas.width, mycanvas.height);
    });


    /*   VARIABLES USED FOR BOTH MOBILE AND BROWSER BASED DRAWING  */
    var isMobile = detectMobile();
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
    var circleWidth = 5;
    var eraserWidth = 5;
    var mode="pen";
    
    
    /* Handlers for draw control buttons/selects */
    $('#pen').bind('click change', function() {
        mode="pen";
        penWidth = $("#pen option:selected").val();
    });
    $('#circle').bind('click change', function() {
        mode="circle";
        circleWidth = $("#circle option:selected").val();
    });
    $('#drawColor').bind('click change', function() {
        mode="pen";
        drawColor = $("#drawColor option:selected").val();
    });
    $('#eraser').bind('click change', function() {
        mode="eraser";
        eraserWidth = $("#eraser option:selected").val();
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
        if (viewMechs != "") {
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
        if (viewMechs != "") {
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
        
        if (marginTop > 0) {
            marginTop = marginTop - 13;
            $('#previewCanvas').prepend('<div id="fillerDiv" style="float:top; min-height: ' + marginTop + 'px;"></div>');
            marginTop = marginTop - 7;
        }
        else {
            marginTop = -7;
        }
        
        if (isMobile) {
            $('#canvas').bind('touchstart', function(e) {
                handleMouseDown(e.originalEvent.touches[0].clientX, e.originalEvent.touches[0].clientY - marginTop);
            })
            $('#canvas').bind('touchmove', function(e) {
                handleMouseMove(e.originalEvent.touches[0].clientX, e.originalEvent.touches[0].clientY - marginTop);
            })
            $('#canvas').bind('click', function(e) {
                handleMouseMove(e.clientX, e.clientY - marginTop);
            })
        }
        else {
            $('#canvas').bind('mousedown', function(e) {
                handleMouseDown(e.clientX, e.clientY - marginTop);
            })
            $('#canvas').bind('mousemove', function(e) {
                handleMouseMove(e.clientX, e.clientY - marginTop);
            })
            $('#canvas').bind('mouseup', function(e) {
                handleMouseUp(e.clientX, e.clientY - marginTop);
            })
            $('#canvas').bind('mouseout', function(e) {
                handleMouseOut(e.clientX, e.clientY - marginTop);
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
        if ((window.orientation != null) && (window.orientation != 0) && (Math.abs(window.orientation) != 180)) {
            alert("Portrait mode works best");
            window.setTimeout(function() {
                forcePortrait();
            }, 200);
        }
        else {
            resizePreview();
        }
    };
    
    function updateMarkup(currentMech, image, callback) {
        
        var markupToUpdate = {markup:image};
        
        $.post("phpScripts/updateMarkups.php?viewNum=" + pageNum +
           "&mechNum=" + currentMech, JSON.stringify(markupToUpdate))
        .done(function( data ) {
            callback();
        });
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
    
    Number.prototype.mod = function(n) {
        return ((this%n)+n)%n;
    };
    
    
});