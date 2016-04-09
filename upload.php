<!DOCTYPE html>
<html>
    <head>
        <title>Mech Markup - Upload Mech</title>
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        
        <link rel="icon" href="images/mechIcon.png">
        <link rel="stylesheet" href="css/upload.css" type="text/css" charset="utf-8" >
        
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
            
            <!-- Upload -->
            <div class="tabContainer uploadClass">
                <div class="uploadTitle">Upload Mech</div>
                <div id="uploadImage"></div>
            </div>
            
        </div>
        
        <div id="uploadMainContainer">
            
            <label class="reqLabel">* </label><label> Required</label>
            
            <br><br>
            
            <div>
                <label class="reqLabel">* &nbsp</label>
                <input type="text" placeholder="UserName" id="userName"/>
            </div>
            <div>
                <label class="reqLabel">* &nbsp</label>
                <input type="text" placeholder="Password" id="password"/>
            </div>
            
            <br>
            
            <div>
                <label class="reqLabel">* &nbsp</label>
                <input type="text" placeholder="Mech Name e.g. Com-2D Commando" id="mechName" />
            </div>
            <div>
                <label class="reqLabel">* &nbsp</label>
                <input type="number" min="20" max="100" placeholder="Weight e.g. 35" id="tonnage" />
            </div>
            <div>
                <label class="reqLabel">* &nbsp</label>
                <input type="number" min="3" max="15" placeholder="Walk e.g. 4" id="walk" />
            </div>
            <div>
                <label class="reqLabel">* &nbsp</label>
                <input type="number" min="5" max="25" placeholder="Run e.g. 6" id="run" />
            </div>
            <div>
                <label class="reqLabel">* &nbsp</label>
                <input type="number" min="0" max="15" placeholder="Jump e.g. 4" id="jump" />
            </div>
            <div>
                <label class="nonReqLabel">* &nbsp</label>
                <input type="text" placeholder="Tags e.g. Commando, Com-2D" id="tags" />
            </div>
            <div>
                <label class="reqLabel">* &nbsp</label>
                <input type="text" placeholder="Weapons e.g. medium laser|ppc|srm 4" id="weapons" />
            </div>
            
            <br>
            
            <label class="reqLabel">* &nbsp</label>
            <label>Mech Sheet Upload - PDFs only</label><br><br>
            <label>&nbsp&nbsp&nbsp&nbsp</label>
            <input type="file" id='pdfUpload' accept="application/pdf" onchange="loadFile(event);"/>
            
            <br>
            
            <button id="submitMech">Upload Mech</button>
        </div>
        
    </body>
    
    <!-- Script containing all the handlers for uploading a mech -->
    <script src="js/upload.js"></script>
    
</html>
