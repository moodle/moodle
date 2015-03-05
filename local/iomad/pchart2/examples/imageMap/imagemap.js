 /*
     imageMap - JS to handle image maps over pChart graphix

     Version     : 2.1.3
     Made by     : Jean-Damien POGOLOTTI
     Last Update : 22/08/11

     This file can be distributed under the license you can find at :

                       http://www.pchart.net/license

     You can find the whole class documentation on the pChart web site.
 */

 var cX				= 0;
 var cY				= 0;
 var LastcX			= null;
 var LastcY			= null;
 var rX				= 0;
 var rY				= 0;
 var currentStatus              = false;
 var initialized		= false;
 var currentTooltipDivID	= "";
 var currentTitle		= "";
 var currentMessage		= "";
 var currentAlpha		= 0;
 var timerID			= null;
 var timerInterval		= 10;
 var timerStep			= 5;
 var currentTimerMode		= 0;
 var timerLock			= false;
 var SmoothMove			= false;
 var SmoothMoveFactor		= 5;
 var imageMapRandomSeed		= true;
 var delimiter			= String.fromCharCode(1);

 /* Create an image map */
 function createMap(imageMapID)
  {
   var testMAP = document.getElementById(imageMapID);
   if ( testMAP != null ) { testMAP.parentNode.removeChild(testMAP); }

   var element = document.createElement("MAP");

   element.id   = imageMapID;
   element.name = imageMapID;
   document.body.appendChild(element);
  }

 /* Create the tooltip div */
 function createTooltipDiv(TooltipDivID)
  {
   var testDiv = document.getElementById(TooltipDivID);
   if ( testDiv != null ) { return(0); }

   var element = document.createElement("DIV");

   element.id             = TooltipDivID;
   element.innerHTML      = "";
   element.style.display = "inline-block";
   element.style.position = "absolute";
   element.style.opacity  = 0;
   element.style.filter   = "alpha(opacity=0)";

   document.body.appendChild(element);
  }

 /* Hide the tooltip */
 function hideDiv(TooltipDivID)
  {
   var element = document.getElementById(TooltipDivID);

   fadeOut(TooltipDivID);
  }

 /* Show the tooltip */
 function showDiv(TooltipDivID,Color,Title,Message)
  {
   var element = document.getElementById(TooltipDivID);

   if ( currentTooltipDivID != TooltipDivID || currentTitle != Title || currentMessage != Message)
    { createToolTip(TooltipDivID,Color,Title,Message); } 

   if ( !initialized ) { moveDiv(TooltipDivID); initialized = true; }

   fadeIn(TooltipDivID);

   currentTooltipDivID	= TooltipDivID;
   currentTitle		= Title;
   currentMessage	= Message;
  }

 /* Move the div to the mouse location */
 function moveDiv(TooltipDivID)
  {
   var element = document.getElementById(TooltipDivID);

   
if(self.pageYOffset)
    { rX = self.pageXOffset; rY = self.pageYOffset; }
   
else if(document.documentElement && document.documentElement.scrollTop)
    { rX = document.documentElement.scrollLeft; rY = document.documentElement.scrollTop; }
   
else if(document.body)
    { rX = document.body.scrollLeft; rY = document.body.scrollTop; }
   
if(document.all)
    {
 cX += rX; cY += rY;
 }

   if ( SmoothMove && LastcX != null )
    { cX = LastcX - (LastcX-cX)/4; cY = LastcY - (LastcY-cY)/SmoothMoveFactor; }

   element.style.left    = (cX+10) + "px";

   element.style.top     = (cY+10) + "px";

   LastcX = cX; LastcY = cY;
  }

 /* Compute the tooltip HTML code */
 function createToolTip(TooltipDivID,Color,Title,Message)
  {
   var element = document.getElementById(TooltipDivID);

   var HTML = "<div style='border:2px solid #606060'><div style='background-color: #000000; font-family: tahoma; font-size: 11px; color: #ffffff; padding: 4px;'><b>"+Title+" &nbsp;</b></div>";
   HTML    += "<div style='background-color: #808080; border-top: 2px solid #606060; font-family: tahoma; font-size: 10px; color: #ffffff; padding: 2px;'>";
   HTML    += "<table style='border: 0px; padding: 0px; margin: 0px;'><tr valign='top'><td style='padding-top: 4px;'><table style='background-color: "+Color+"; border: 1px solid #000000; width: 9px; height: 9px;  padding: 0px; margin: 0px; margin-right: 2px;'><tr><td></td></tr></table></td><td>"+Message+"</td></tr></table>";
   HTML    += "</div></div>";

   element.innerHTML = HTML;
  }

 /* Bind an image map to a picture */
 function bindMap(imageID,imageMapID)
  {
   var image = document.getElementById(imageID);
   image.useMap = "#"+imageMapID;
  }

 /* Add an area to the specified image map */
 function addArea(imageMapID,shapeType,coordsList,actionOver,actionOut)
  {
   var maps    = document.getElementById(imageMapID);
   var element = document.createElement("AREA");

   element.shape  = shapeType;
   element.coords = coordsList;
   element.onmouseover = function() { eval(actionOver); };
   element.onmouseout  = function() { eval(actionOut); };
   maps.appendChild(element);
  }

 /* Retrieve the current cursor position Mozilla */
 function UpdateCursorPosition(e)
  {
   cX = e.pageX; cY = e.pageY;
   if ( currentStatus || timerID != null ) { moveDiv(currentTooltipDivID); }
  }


 /* Retrieve the current cursor position IE */
 function UpdateCursorPositionDocAll(e)
  {
   cX = event.clientX; cY = event.clientY;
   if ( currentStatus || timerID != null ) { moveDiv(currentTooltipDivID); }
  }

 /* Fade general functions */
 function fadeIn(TooltipDivID)  { currentTimerMode = 1; initialiseTimer(TooltipDivID); } function fadeOut(TooltipDivID) { currentTimerMode = 2; initialiseTimer(TooltipDivID); } function initialiseTimer(TooltipDivID)
  { if ( timerID == null ) { timerID = setInterval("fade('"+TooltipDivID+"')",timerInterval); } }



 /* Handle fading */
 function fade(TooltipDivID)
  {
   var element = document.getElementById(TooltipDivID);

   currentStatus = true;
   if ( currentTimerMode == 1 ) /* Fade in */
    {
     currentAlpha = currentAlpha + timerStep;
     if ( currentAlpha >= 100 ) { currentAlpha = 100; clearInterval(timerID); timerID = null; }
    }
   else if ( currentTimerMode == 2 ) /* Fade out */
    {
     currentAlpha = currentAlpha - timerStep;
     if ( currentAlpha <= 0 ) { currentStatus = false; currentAlpha = 0; clearInterval(timerID); timerID = null; }
    }

   element.style.opacity = currentAlpha * .01;
   element.style.filter = 'alpha(opacity=' +currentAlpha + ')';
  }

 /* Add a picture element that need ImageMap parsing */
 function addImage(PictureID,ImageMapID,ImageMapURL)
  {
   createTooltipDiv('testDiv');
   createMap(ImageMapID);
   bindMap(PictureID,ImageMapID);

   setTimeout("checkLoadingStatus('"+PictureID+"','"+ImageMapID+"','"+ImageMapURL+"')", 200);   
  }

 /* Check the loading status of the image */
 function checkLoadingStatus(PictureID,ImageMapID,ImageMapURL)
  {
   var element = document.getElementById(PictureID);

   if ( element.complete == true )
    downloadImageMap(PictureID,ImageMapID,ImageMapURL);
   else
    setTimeout("checkLoadingStatus('"+PictureID+"','"+ImageMapID+"','"+ImageMapURL+"')", 200);
  }

 /* Download the image map when the picture is loaded */
 function downloadImageMap(PictureID,ImageMapID,ImageMapURL)
  {
   var xmlhttp=false;   
   /*@cc_on @*/  
   /*@if (@_jscript_version >= 5)  
    try { xmlhttp = new ActiveXObject("Msxml2.XMLHTTP"); } catch (e) { try { xmlhttp = new ActiveXObject("Microsoft.XMLHTTP"); } catch (E) { xmlhttp = false; } }  
   @end @*/  
  
   if (!xmlhttp && typeof XMLHttpRequest!='undefined')   
    { try { xmlhttp = new XMLHttpRequest(); } catch (e) { xmlhttp=false; } }   
  
   if (!xmlhttp && window.createRequest)   
    { try { xmlhttp = window.createRequest(); } catch (e) { xmlhttp=false; } }   
  
   if ( imageMapRandomSeed )
    {
     randomSeed = "Seed=" + Math.floor(Math.random()*1000);
     if ( ImageMapURL.indexOf("?",0) != -1 ) { ImageMapURL = ImageMapURL + "&" + randomSeed; } else { ImageMapURL = ImageMapURL + "?" + randomSeed; }
    }

   xmlhttp.open("GET", ImageMapURL,true);

   xmlhttp.onreadystatechange=function()
    { if (xmlhttp.readyState==4) { parseZones(ImageMapID,xmlhttp.responseText); } }   
   xmlhttp.send(null)   
  }

 /* Process the image map & create the zones */
 function parseZones(ImageMapID,SerializedZones)
  {
   var Zones = SerializedZones.split("\r\n");

   for(i=0;i<=Zones.length-2;i++)
    {
     var Options = Zones[i].split(delimiter);
     addArea(ImageMapID,Options[0],Options[1],'showDiv("testDiv","'+Options[2]+'","'+Options[3]+'","'+Options[4].replace('"','')+'");','hideDiv("testDiv");');
    }
  }

 /* Attach the onMouseMove() event to the document body */
 if(document.all)
  { document.onmousemove = UpdateCursorPositionDocAll; }

 else
  { document.onmousemove = UpdateCursorPosition; }
