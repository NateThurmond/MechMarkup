var mechData = {'pdf':''};

var loadFile = function(event) {

    mechData['pdf'] = event.target.files[0];
    
    var reader = new FileReader();
    reader.onload = function(){
        mechData['pdf'] = reader.result;
    };

    reader.readAsDataURL(event.target.files[0]);
};


$(document).ready(function() {
    
    // Link to Main page
    $('.menuBarTabsContainer').click(function() {
        window.location='index.php';
    });
    
    
    $('#submitMech').click(function() {
        
        var alerts = false;
        
        $('body').find('input[type="text"]').each(function() {
            mechData[this.id] = this.value;
            if (((this.value == null) || (this.value == "")) && (this.id != "tags")) {
                alerts = true;
            }
        });
        $('body').find('input[type="number"]').each(function() {
            mechData[this.id] = this.value;
            if ((this.value == null) || (this.value == "") || (isNaN(this.value))) {
                alerts = true;
            }
        });

        if (mechData['pdf'] == "") {
            alert('Must upload Mech Sheet pdf');
        }
        else if (alerts) {
            alert('Not all data completed or incorrect. Check values and make sure to use integers only for Weight, Walk, Run, and Jump.');
        }
        else {
            if (window.XMLHttpRequest) {    xmlhttp23=new XMLHttpRequest();  }

            xmlhttp23.onreadystatechange=function() {
                if (xmlhttp23.readyState===4 && xmlhttp23.status===200) {
                    var message = JSON.parse(xmlhttp23.response);
                    alert(message);
                }
            };

            // Update images
            xmlhttp23.open("POST","phpScripts/uploadMech.php?asd=asd", true);
            xmlhttp23.setRequestHeader('Content-Type', 'application/upload');
            xmlhttp23.send(JSON.stringify(mechData));
        }
        
    });
    
    
});
