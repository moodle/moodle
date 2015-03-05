 /*
     delayedLoader - JS to delay out of sight pictures rendering

     Version     : 2.0.2
     Made by     : Jean-Damien POGOLOTTI
     Last Update : 10/12/10

     This file can be distributed under the license you can find at :

                       http://www.pchart.net/license

     You can find the whole class documentation on the pChart web site.
 */

 var PictureCache  = new Array();
 var PictureCount  = 0;
 var WaitPicture   = "wait.gif";
 var DivClassName  = "pChart";
 var DefaultWidth  = 70;
 var DefaultHeight = 230;
 var DefaultAlt    = "pChart rendered picture";


 /* Do the DOM document processing */
 function loaderInit()
  {
   WindowSize   = getWindowSize();
   WindowHeight = WindowSize[1];
   Offset       = getScrollXY();
   HeightOffset = Offset[1];

   /* Enumerate the tags */
   Links = document.getElementsByTagName("a");   
   for (i = 0; i < Links.length; i++)   
    {   
     className = Links[i].className;   

     if ( className == DivClassName )   
      {   
       ObjectWidth  = Links[i].getAttribute("data-pchart-width");
       ObjectHeight = Links[i].getAttribute("data-pchart-height");
       ObjectID     = Links[i].id;
       ObjectTop    = Links[i].offsetTop;
       ObjectURL    = Links[i].href;
       ObjectAlt    = Links[i].getAttribute("data-pchart-alt");

       if ( ObjectWidth == null )  { ObjectWidth  = DefaultWidth; }
       if ( ObjectHeight == null ) { ObjectHeight = DefaultHeight; }
       if ( ObjectAlt == null )    { ObjectAlt    = DefaultAlt; }

       if (ObjectID == "") { ObjectID = "pChart-"+i; Links[i].id = ObjectID; }

       PictureCache[PictureCount]    = new Array();
       PictureCache[PictureCount][0] = ObjectID;
       PictureCache[PictureCount][1] = ObjectTop;
       PictureCache[PictureCount][2] = ObjectURL;
       PictureCache[PictureCount][3] = ObjectAlt;
       PictureCache[PictureCount][4] = ObjectWidth;
       PictureCache[PictureCount][5] = ObjectHeight;

       PictureCount++;
      }   
    }   

   /* Replace the <A> tags by <DIV> ones and attach the loader */
   for(i=0;i<PictureCount;i++)
    {
     ATag    = document.getElementById(PictureCache[i][0]);
     DivTag  = document.createElement("div");
     DivID   = "pChart-Div"+i; PictureCache[i][0] = DivID;

     DivTag.setAttribute("id", DivID);
     DivTag.style.width  = PictureCache[i][4];
     DivTag.style.height = PictureCache[i][5];
     DivTag.style.backgroundColor = "#E0E0E0";

     DivTag2  = ATag.parentNode.replaceChild(DivTag, ATag);

     DivTop = DivTag.offsetTop;
     PictureCache[i][1] = DivTop;

     changeOpac(50, i);
     changeContent("<img src='"+WaitPicture+"' width=24 height=24 alt=''/>",i);

     if ( HeightOffset + WindowHeight > PictureCache[i][1] ) { triggerVisible(i); }
    }
  }

 /* Replace the contents of the delayed loading DIV */
 function changeContent(html, id)
  { DivID = PictureCache[id][0]; document.getElementById(DivID).innerHTML = html; }

 /* Trigger the picture rendering when the pChart DIV became visible */
 function triggerVisible(PictureID)
  {
   if ( !PictureCache[PictureID][6] == true )
    {
     PictureCache[PictureID][6] = true;
     ajaxRender(PictureCache[PictureID][2],PictureID);
    }
  }

 /* Catch the navigator window scrolling event */
 function scrollEvent()
  {
   WindowSize   = getWindowSize();
   WindowHeight = WindowSize[1];
   Offset       = getScrollXY();
   HeightOffset = Offset[1];

   for(i=0;i<=PictureCount-1;i++) { if ( HeightOffset + WindowHeight > PictureCache[i][1] ) { triggerVisible(i); } }
  }

 /* Cross browser X/Y window offset gatherer */
 function getScrollXY()
  {
   var scrOfX = 0, scrOfY = 0;

   if( typeof( window.pageYOffset ) == 'number' )
    { scrOfY = window.pageYOffset; scrOfX = window.pageXOffset; }
   else if( document.body && ( document.body.scrollLeft || document.body.scrollTop ) )
    { scrOfY = document.body.scrollTop; scrOfX = document.body.scrollLeft; }
   else if( document.documentElement && ( document.documentElement.scrollLeft || document.documentElement.scrollTop ) )
    { scrOfY = document.documentElement.scrollTop; scrOfX = document.documentElement.scrollLeft; }

   return [ scrOfX, scrOfY ];
  }

 /* Cross browser X/Y window size gatherer */
 function getWindowSize()
  {
   var myWidth = 0, myHeight = 0;

   if( typeof( window.innerWidth ) == 'number' )
    { myWidth = window.innerWidth; myHeight = window.innerHeight; }
   else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) )
    { myWidth = document.documentElement.clientWidth; myHeight = document.documentElement.clientHeight; }
   else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) )
    { myWidth = document.body.clientWidth; myHeight = document.body.clientHeight; }

   return [ myWidth, myHeight ];
  }

 /* Cross browser alpha transparency changer */
 function changeOpac(opacity, id)   
  {
   DivID = PictureCache[id][0];

   var object = document.getElementById(DivID).style;   
   object.opacity = (opacity / 100);   
   object.MozOpacity = (opacity / 100);   
   object.KhtmlOpacity = (opacity / 100);   
   object.filter = "alpha(opacity=" + opacity + ")";   
  }   

 /* Shade in-out function */
 function opacity(id, opacStart, opacEnd, millisec)
  {
   var speed = Math.round(millisec / 100);
   var timer = 0;

   if(opacStart > opacEnd)
    {
     for(i = opacStart; i >= opacEnd; i--)
      {
       setTimeout("changeOpac(" + i + ",'" + id + "')",(timer * speed));
       timer++;
      }
    }
   else if(opacStart < opacEnd)
    {
     for(i = opacStart; i <= opacEnd; i++)
      {
       setTimeout("changeOpac(" + i + ",'" + id + "')",(timer * speed));
       timer++;
      }
    }
  }

 /* Start the loader */
 function StartFade(PictureID)
  {
   Loader     = new Image();
   URL        = PictureCache[PictureID][2];
   Loader.src = URL;   
   setTimeout("CheckLoadingStatus("+PictureID+")", 200);   
  }

 /* check the picture loading status */
 function CheckLoadingStatus(PictureID)   
  {
   DivID = PictureCache[PictureID][0];
   URL   = PictureCache[PictureID][2];
   Alt   = PictureCache[PictureID][3];

   if ( Loader.complete == true )   
    {
     changeOpac(0, PictureID);
     HTMLResult = "<center><img src='" + URL + "' alt='"+Alt+"'/></center>";
     document.getElementById(DivID).innerHTML = HTMLResult;

     opacity(PictureID,0,100,100);
    }
   else  
    setTimeout("CheckLoadingStatus("+PictureID+")", 200);   
  }   

 /* Compute the pChart picture in background */
 function ajaxRender(URL,PictureID)
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
  
   xmlhttp.open("GET", URL,true);

   xmlhttp.onreadystatechange=function()
    { if (xmlhttp.readyState==4) { StartFade(PictureID); } }   
   xmlhttp.send(null)   
  }
