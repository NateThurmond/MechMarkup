
var critZoom = {zoomLevel: 1.65, xOff: .32, yOff: 0.2545};
var critArmor = {zoomLevel: 2.1, xOff: .47, yOff: -0.5045};
var weaponArea = {zoomLevel: 2.6, xOff: -0.54, yOff: 0.7245};
var mechArmor = {zoomLevel: 2.4, xOff: -0.575, yOff: -0.6995};
var pilotSection = {zoomLevel: 3, xOff: -0.675, yOff: -0.06};

var elemental_group1 = {zoomLevel: 1.6, xOff: -0.14, yOff: 0.2745};
var elemental_group2 = {zoomLevel: 1.6, xOff: .22, yOff: 0.2745};
var elemental_attacks = {zoomLevel: 2.1, xOff: -0.375, yOff: -0.5595};
var elemental_swarm = {zoomLevel: 2.4, xOff: .57, yOff: -0.6195};


$(document).ready(function() {
    
    var isMobile = detectMobile();
    var halfPageWidth = 0;
    var halfPageHeight = 0;
    var pdfHeight = 0;
    var pdfWidth = 0;
    var zoomModeActive = false;
    var zoomArea = "critZoom";
    
    /*   VARIABLES SET BY USERS */
    var drawColor = "#FF0000";
    var penWidth = 2;
    var circleWidth = 4;
    var eraserWidth = 6;
    var textWidth = 6;
    var textToFill = "";
    var mode="pen";
    
    viewMechs = true;
    mechTypes = {};
    mechNumMod = 0;
    mechNumTotal = 0;
    disableDraw = false;
    
    /*   VARIABLES USED FOR BOTH MOBILE AND BROWSER BASED DRAWING  */
    var canvas="";
    var ctx="";
    var lastX;
    var lastY;
    var mouseX;
    var mouseY;
    var canvasOffset="";
    var offsetX=0;
    var offsetY=0;
    var isMouseDown=false;
    
    $('#pen').customSelect({customClass:'penSelect'});
    $('#circle').customSelect({customClass:'circleSelect'});
    $('#text').customSelect({customClass:'textSelect'});
    $('#drawColor').customSelect({customClass:'drawColorSelect'});
    $('#eraser').customSelect({customClass:'eraserSelect'});
    
    $.getJSON('./phpScripts/fetchViews.php', function(data) {
        var viewMechsAll = data[pageTitle];
        
        if ((viewMechsAll == null) || (viewMechsAll == "")) {
            viewMechs = false;
        }
        else {            
            var viewMechsSplit = viewMechsAll.split(',');
            mechNumTotal = viewMechsSplit.length;
            
            for (mechs in viewMechsSplit) {
                var mechParts = viewMechsSplit[mechs].split('|');
                var mechName = mechParts[1].toLowerCase();
                var mechID = mechParts[0].split('_')[0];
                var mechNum = mechParts[0].split('_')[1];
                var imageRef = 'phpScripts/getPDF.php?mechID=' + mechID;
                
                if (mechName.indexOf('elemental') >= 0) {
                    mechTypes[mechID] = "elemental";
                }
                else {
                    mechTypes[mechID] = "mech";
                }
                
                var canvasElem = document.createElement('canvas');
                canvasElem.id = "canvas_" + mechNum;
                canvasElem.style.backgroundImage = 'url(' + imageRef + ')';
                
                if (mechNum == 1) {
                    canvasElem.className = "canvas canvasSelected";
                }
                else {
                    canvasElem.className = "canvas";
                }
                
                $('#previewCanvas').append(canvasElem);
                
                $("#" + canvasElem.id).panzoom({
                    disablePan: true
                });
            }
            
            canvasOffset=$("#canvas_1").offset();
            offsetX=canvasOffset.left;
            offsetY=canvasOffset.top;
        }
        
        forcePortrait();
    });
    
    
    function resizePreview() {
        
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
        
        $('.canvas').attr('height', heightToStretch);
        $('.canvas').attr('width', widthToStretch);
        $('.canvas').css('min-height', heightToStretch + 'px');
        $('.canvas').css('max-height', heightToStretch + 'px');
        $('.canvas').css('min-width', widthToStretch + 'px');
        $('.canvas').css('max-width', widthToStretch + 'px');
        $('.canvas').css('background-color', '#DFE2DB');
        
        halfPageWidth = widthToStretch / 2;
        halfPageHeight = ($('#canvas_1').height() / 2) + $('#floatBar').height();
        
        var marginTop = ($(window).height() - $('#floatBar').height() - $('#canvas_1').height()) / 2;
        
        if (marginTop > 0) {
            marginTop = marginTop - 13;
            $('#previewCanvas').prepend('<div id="fillerDiv" style="float:top; min-height: ' + marginTop + 'px;"></div>');
            marginTop = marginTop - 2;
        }
        else {
            marginTop = -2;
        }
        
        $('.overlay').css('top', '81px');
        $('.overlay').css('height', $('#previewCanvas').height() + 'px');
        $('.overlay').css('width', $(window).width() + 'px');
        
        
        if (viewMechs != false) {
            
            canvas=document.getElementById("canvas_1");
            ctx=canvas.getContext("2d");
            fetchMarkup(pageNum, "1", ctx, canvas);
            
            if (isMobile) {
                $('.canvas').bind('touchstart', function(e) {
                    var offsets = calcXY(e.originalEvent.touches[0].clientX, e.originalEvent.touches[0].clientY,
                        halfPageWidth, halfPageHeight, marginTop, zoomModeActive);
                    handleMouseDown(offsets[0], offsets[1]);
                })
                $('.canvas').bind('touchmove', function(e) {
                    var offsets = calcXY(e.originalEvent.touches[0].clientX, e.originalEvent.touches[0].clientY,
                        halfPageWidth, halfPageHeight, marginTop, zoomModeActive);
                    handleMouseMove(offsets[0], offsets[1]);
                })
                $('.canvas').bind('click', function(e) {
                    var offsets = calcXY(e.clientX, e.clientY, halfPageWidth, halfPageHeight, marginTop, zoomModeActive);
                    handleMouseMove(offsets[0], offsets[1]);
                })
            }
            else {
                $('.canvas').bind('mousedown', function(e) {
                    var offsets = calcXY(e.clientX, e.clientY, halfPageWidth, halfPageHeight, marginTop, zoomModeActive);
                    handleMouseDown(offsets[0], offsets[1]);
                })
                $('.canvas').bind('mousemove', function(e) {
                    var offsets = calcXY(e.clientX, e.clientY, halfPageWidth, halfPageHeight, marginTop, zoomModeActive);
                    handleMouseMove(offsets[0], offsets[1]);
                })
                $('.canvas').bind('mouseup', function(e) {
                    var offsets = calcXY(e.clientX, e.clientY, halfPageWidth, halfPageHeight, marginTop, zoomModeActive);
                    handleMouseUp(offsets[0], offsets[1]);
                })
                $('.canvas').bind('mouseout', function(e) {
                    var offsets = calcXY(e.clientX, e.clientY, halfPageWidth, halfPageHeight, marginTop, zoomModeActive);
                    handleMouseOut(offsets[0], offsets[1]);
                })
            }
        
            mechHandlers();
        }
    };
    
    
    function mechHandlers() {
        
        $('#clearCanvas').click(function() {
            if (confirm('Clear Image?')) {
                
                var currentMech = (mechNumMod.mod(mechNumTotal)) + 1;
                var mycanvas = document.getElementById("canvas_" + currentMech);
                var ctx = mycanvas.getContext("2d");
                ctx.clearRect(0, 0, mycanvas.width, mycanvas.height);
            }
        });
        
        $('.zoomButton').click(function() {
            
            var zoomID = this.id;
            var zoomNum = zoomID.substr(zoomID.length - 1);
            
            $('.zoomButton').each(function() {
                if (this.id != zoomID) {
                    $(this).removeClass('zoomHighlight');
                }
            })
            
            $(this).toggleClass("zoomHighlight");
            
            if ($(this).hasClass('zoomHighlight')) {
                zoomModeActive = true;
                calcZoomScope(zoomNum);
            }
            else {
                zoomOut();
            }
        });
        
        $('#nextMech').click(function() {
            zoomOut();
            var currentMech = (mechNumMod.mod(mechNumTotal)) + 1;
            mechNumMod++;
            var nextMech = (mechNumMod.mod(mechNumTotal)) + 1;
            
              // Update the current markup for the view
            var mycanvas = document.getElementById("canvas_" + currentMech);
            var image    = mycanvas.toDataURL("image/jpg");
            
            updateMarkup(currentMech, image, function() {
                // get the prev mech markup
                var ctx = document.getElementById("canvas_" + nextMech).getContext("2d");
                fetchMarkup(pageNum, nextMech, ctx, mycanvas);
                
                 $('.canvasSelected').animate({
                    opacity: 0, // animate slideUp
                    marginLeft: '-800px'
                    }, 350, function() {
                
                    $('.canvas').css('opacity', '100');
                    $('.canvas').css('marginLeft', '0px');
                    
                    $('.canvasSelected').removeClass('canvasSelected');
                    $('#canvas_' + nextMech).addClass('canvasSelected');
                });
            });

            canvas=document.getElementById("canvas_" + nextMech);
            ctx=canvas.getContext("2d");
        });
        
        $('#prevMech').click(function() {
            zoomOut();
            var currentMech = (mechNumMod.mod(mechNumTotal)) + 1;
            mechNumMod--;
            var prevMech = (mechNumMod.mod(mechNumTotal)) + 1;
            
            // Update the current markup for the view
            var mycanvas = document.getElementById("canvas_" + currentMech);
            var image    = mycanvas.toDataURL("image/jpg");
            
            updateMarkup(currentMech, image, function() {
                // get the prev mech markup
                var ctx = document.getElementById("canvas_" + prevMech).getContext("2d");
                fetchMarkup(pageNum, prevMech, ctx, mycanvas);
                
                $('.canvasSelected').animate({
                    opacity: 0, // animate slideUp
                    marginLeft: '800px'
                    }, 350, function() {
                
                    $('.canvas').css('opacity', '100');
                    $('.canvas').css('marginLeft', '0px');
                    
                    $('.canvasSelected').removeClass('canvasSelected');
                    $('#canvas_' + prevMech).addClass('canvasSelected');
                });
            });

            canvas=document.getElementById("canvas_" + prevMech);
            ctx=canvas.getContext("2d");
        });
        
        $('#disableDraw').click(function() {  
            disableDraw = ! disableDraw;
            
            if (disableDraw) {
                $('.overlay').css('display', 'block');
                $('head').remove('#userZoom');
                $('head').append('<meta id="userZoom" name="viewport" content="user-scalable=1">');
                $('#clearCanvas').prop('disabled', true);
            }
            else {
                $('.overlay').css('display', 'none');
                $('head').remove('#userZoom');
                $('head').append('<meta id="userZoom" name="viewport" content="user-scalable=0">');
                $('#clearCanvas').prop('disabled', false);
            }
        });
        
        /* Handlers for draw control buttons/selects */
        $('#pen').bind('click', function() {
            mode="pen";
            penWidth = $("#pen option:selected").val();
            
            $('.drawOption').css('background-color', '#b3b3b3');
            $('.penSelect').css('background-color', '#99ccff');
            $('.drawOption option').css('background-color', 'white');
        });
        $('#pen').bind('change', function() {
            mode="pen";
            penWidth = $("#pen option:selected").val();
            saveMarkup();
        });
        $('#circle').bind('click', function() {
            mode="circle";
            circleWidth = $("#circle option:selected").val();
            
            $('.drawOption').css('background-color', '#b3b3b3');
            $('.circleSelect').css('background-color', '#99ccff');
            $('.drawOption option').css('background-color', 'white');
        });
        $('#circle').bind('change', function() {
            mode="circle";
            circleWidth = $("#circle option:selected").val();
            saveMarkup();
        });
        $('#text').bind('click', function() {
            mode="text";
            textWidth = $("#text option:selected").val();
            
            $('.drawOption').css('background-color', '#b3b3b3');
            $('.textSelect').css('background-color', '#99ccff');
            $('.drawOption option').css('background-color', 'white');
        });
        $('#text').bind('change', function() {
            mode="text";
            textWidth = $("#text option:selected").val();
            saveMarkup();
        });
        $('#drawColor').bind('click', function() {
            drawColor = $("#drawColor option:selected").val();
            
            $('.drawOption').css('background-color', '#b3b3b3');
            $('.' + mode + 'Select').css('background-color', '#99ccff');
            $('.drawOption option').css('background-color', 'white');
        });
        $('#drawColor').bind('change', function() {
            drawColor = $("#drawColor option:selected").val();
            saveMarkup();
        });
        $('#eraser').bind('click', function() {
            mode="eraser";
            eraserWidth = $("#eraser option:selected").val();
            
            $('.drawOption').css('background-color', '#b3b3b3');
            $('.eraserSelect').css('background-color', '#99ccff');
            $('.drawOption option').css('background-color', 'white');
        });
        $('#eraser').bind('change', function() {
            mode="eraser";
            eraserWidth = $("#eraser option:selected").val();
            saveMarkup();
        });
    }
    
    
    $('.viewTitle').click(function() {
        var linkID = this.id.split('_')[0];
        
        if (viewMechs == false) {
            location.replace(linkID + '.php');
        }
        else {
            var currentMech = (mechNumMod.mod(mechNumTotal)) + 1;
            
            // Update the current markup for the view
            var mycanvas = document.getElementById("canvas_" + currentMech);
            var image    = mycanvas.toDataURL("image/jpg");
            
            updateMarkup(currentMech, image, function() {
                location.replace(linkID + '.php');
            });
        }
    });
    
    function calcZoomScope(zoomNum) {
        var mechID = $('.canvasSelected').css('background-image').split('?mechID=')[1].replace('")', '').replace(')', '');
        var mechType = mechTypes[mechID];
        
        if (mechType == 'elemental') {
            switch (zoomNum) {
                case "1":
                case "5": zoomArea = "elemental_group1"; break;
                case "2": zoomArea = "elemental_group2"; break;
                case "3": zoomArea = "elemental_attacks"; break;
                case "4": zoomArea = "elemental_swarm"; break;
            }
        }
        else {
            switch (zoomNum) {
                case "1": zoomArea = "weaponArea"; break;
                case "2": zoomArea = "pilotSection"; break;
                case "3": zoomArea = "mechArmor"; break;
                case "4": zoomArea = "critZoom"; break;
                case "5": zoomArea = "critArmor"; break;
            }
        }
        
        if (zoomArea != "") {
            $(".canvasSelected").panzoom("zoom", window[zoomArea]['zoomLevel'], {});
            $(".canvasSelected").css('position', 'relative');
            $(".canvasSelected").css('top', - (pdfHeight * window[zoomArea]['xOff']) + 'px');
            $(".canvasSelected").css('left', (pdfWidth * window[zoomArea]['yOff']) + 'px');
        }
    };
    
    function zoomOut() {
        $('.zoomButton').each(function() {
            $(this).removeClass('zoomHighlight');
        })
        
        zoomModeActive = false;
        $(".canvasSelected").panzoom("zoom", 1, { silent: true });
        $(".canvasSelected").css('position', 'static');
        $(".canvasSelected").css('top', '0px');
    }
    
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
    
    /*   CODE FOR MOBILE DRAWING         */	
    function handleMouseDown(X, Y) {
      mouseX=parseInt(X - offsetX);
      mouseY=parseInt(Y - offsetY);
    
      // Put your mousedown stuff here
      lastX=mouseX;
      lastY=mouseY;
      isMouseDown=true;
    };
    
    function handleMouseUp(X, Y) {
      mouseX=parseInt(X-offsetX);
      mouseY=parseInt(Y-offsetY);
    
      // Put your mouseup stuff here
      isMouseDown=false;
    };
    
    function handleMouseOut(X, Y) {
      mouseX=parseInt(X-offsetX);
      mouseY=parseInt(Y-offsetY);
    
      // Put your mouseOut stuff here
      isMouseDown=false;
    };
    
    function handleMouseMove(X, Y) {
        
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
        else if (mode=="text") {
            isMouseDown = false;
            
            textToFill = prompt("Please enter your text");
            if (textToFill != null) {
                ctx.font = textWidth + "px Verdana";
                ctx.fillStyle = drawColor;
                ctx.fillText(textToFill, lastX,lastY);
            }
            textToFill = "";
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
        
    function saveMarkup() {
        if (viewMechs != false) {
            // Update the current markup for the view
            var currentMech = (mechNumMod.mod(mechNumTotal)) + 1;
            var mycanvas = document.getElementById("canvas_" + currentMech);
            var image    = mycanvas.toDataURL("image/jpg");
            
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