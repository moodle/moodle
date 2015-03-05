<?php if ( isset($_GET["Action"])) { $Script = $_GET["Script"]; goCheck($Script); } ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
 <title>pChart 2.x - examples rendering</title>
 <meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
 <style>
  body       { background-color: #F0F0F0; font-family: tahoma; font-size: 14px; height: 100%; overflow: auto;}
  table      { margin: 0px; padding: 0px; border: 0px; }
  tr         { margin: 0px; padding: 0px; border: 0px; }
  td         { font-family: tahoma; font-size: 11px; margin: 0px; padding: 0px; border: 0px; }
  div.folder { cursor: hand; cursor: pointer; }
  a.smallLinkGrey:link     { text-decoration: none; color: #6A6A6A; }
  a.smallLinkGrey:visited  { text-decoration: none; color: #6A6A6A; }
  a.smallLinkGrey:hover    { text-decoration: underline; color: #6A6A6A; }
  a.smallLinkBlack:link    { text-decoration: none; color: #000000; }
  a.smallLinkBlack:visited { text-decoration: none; color: #000000; }
  a.smallLinkBlack:hover   { text-decoration: underline; color: #000000; }
 </style>
</head>
<?php checkPHPVersion(); ?>
<body>

<?php
 /* Files that we don't want to see in the tree */
 $Exclusion = array(".","..","index.php","buildAll.cmd","pictures","resources","delayedLoader","sandbox","imageMap");

 /* Determine the current package version */
 $FileHandle  = fopen("../readme.txt", "r");
 for ($i=0; $i<=5; $i++) { $buffer = fgets($FileHandle, 4096); }
 fclose($FileHandle);
 $Values  = preg_split("/:/",$buffer);
 $Values  = preg_split("/ /",$Values[1]);
 $Version = strip_tags($Values[1]);

 /* Build a list of the examples & categories */
 $DirectoryHandle = opendir(".");
  {
   $Tree = "";
   while (($FileName = readdir($DirectoryHandle)) !== false)
   {
    if ( !in_array($FileName,$Exclusion))
     {
      $FileHandle  = fopen($FileName, "r");
      $buffer      = fgets($FileHandle, 4096);
      $buffer      = fgets($FileHandle, 4096);
      fclose($FileHandle);

      if ( preg_match("/CAT:/",$buffer) )
       {
        $Categorie = str_replace(" /* CAT:","",$buffer);
        $Categorie = str_replace("*/","",$Categorie);
        $Categorie = trim($Categorie);
       }
      else
       { $Categorie = "z_root"; }

      $FileShortName = str_replace("example.","",$FileName);
      $FileShortName = str_replace(".php","",$FileShortName);
      $FileShortName = trim($FileShortName);

      $Tree[$Categorie][]=array("FileName"=>$FileName,"FileShortName"=>$FileShortName);
     }
   }
  closedir($DirectoryHandle);

  ksort($Tree);
?>

<table style='border: 2px solid #FFFFFF;'><tr><td>
<div style='font-size: 11px; padding: 2px; color: #FFFFFF; background-color: #666666; border-bottom: 3px solid #484848;'>&nbsp;Navigation</div>
<table style='padding: 1px; background-color: #E0E0E0; border: 1px solid #D0D0D0; border-top: 1px solid #FFFFFF;'><tr>
 <td width=16><img src='resources/application_view_tile.png' width=16 height=16 alt=''/></td>
 <td width=100>&nbsp;<b>Examples</b></td>
 <td width=16><img src='resources/application_view_list.png' width=16 height=16 alt=''/></td>
 <td width=100>&nbsp;<a class=smallLinkGrey href='sandbox/'>Sandbox</a></td>
 <td width=16><img src='resources/application_view_list.png' width=16 height=16 alt=''/></td>
 <td width=100>&nbsp;<a class=smallLinkGrey href='delayedLoader/'>Delayed loader</a></td>
 <td width=16><img src='resources/application_view_list.png' width=16 height=16 alt=''/></td>
 <td width=100>&nbsp;<a class=smallLinkGrey href='imageMap/'>Image Map</a></td>
</tr></table>
</td></tr></table>

<br/>
<table><tr><td valign='top'>

<table style='border: 2px solid #FFFFFF;'><tr><td>
<div style='font-size: 11px; padding: 2px; color: #FFFFFF; background-color: #666666; border-bottom: 3px solid #484848; width: 222px;'>&nbsp;Release <?php echo $Version; ?></div>
<div style='border: 3px solid #D0D0D0; border-top: 1px solid #FFFFFF; background-color: #FAFAFA; width: 220px; overflow: auto'>
<div style='padding: 1px; padding-bottom: 3px; color: #000000; background-color:#D0D0D0;'>
 <table><tr>
  <td><img src='resources/application_view_list.png' width=16 height=16 alt=''/></td>
  <td>&nbsp;Examples folder contents</td>
 </tr></table>
</div>
<?php
  $ID = 1; if ( isset($Tree["z_root"]) ) { $ID = 2; }
  foreach($Tree as $Key => $Elements)
   {
    if ( $ID == count($Tree) ) { $Icon = "dash-explorer-last.png"; $SubIcon = "dash-explorer-blank.png"; } else { $Icon = "dash-explorer.png"; $SubIcon = "dash-explorer-noleaf.png"; }
    if ( $Key != "z_root" )
     {
      echo "<table  noborder cellpadding=0 cellspacing=0>\r\n";
      echo " <tr valign=middle>\r\n";
      echo "  <td><img src='resources/".$Icon."' width=16 height=20 alt=''/></td>\r\n";
      echo "  <td><img src='resources/folder.png' width=16 height=16 alt=''/></td>\r\n";
      echo "  <td><div class=folder id='".$Key."_main' onclick='showHideMenu(".chr(34).$Key.chr(34).");'>&nbsp;".$Key."</div></td>\r\n";
      echo " </tr>\r\n";
      echo "</table>\r\n";

      echo "<table id='".$Key."' style='display: none;' noborder cellpadding=0 cellspacing=0><tr>\r\n";
      foreach($Elements as $SubKey => $Element)
       {
        $FileName      = $Element["FileName"];
        $FileShortName = $Element["FileShortName"];

        if ( $SubKey == count($Elements)-1 ) { $Icon = "dash-explorer-last.png"; } else { $Icon = "dash-explorer.png"; }

        echo " <tr valign=middle>\r\n";
        echo "  <td><img src='resources/".$SubIcon."' width=16 height=20 alt=''/></td>\r\n";
        echo "  <td><img src='resources/".$Icon."' width=16 height=20 alt=''/></td>\r\n";
        echo "  <td><img src='resources/application_view_tile.png' width=16 height=16 alt=''/></td>\r\n";
        echo "  <td><div id='".$FileName."'>&nbsp;<a class=smallLinkGrey href='#' onclick='render(".chr(34).$FileName.chr(34).");'>".$FileShortName."</a></div></td>\r\n";
        echo " </tr>\r\n";
       }
      echo "</table>\r\n";

     }
    $ID++;
   }
 }
?>
</div>
</td></tr></table>

</td><td width=20></td><td valign='top' style='padding-top: 5px; font-size: 12px;'>

<table><tr>
 <td><img src='resources/chart_bar.png' width=16 height=16 alt=''/></td>
 <td>&nbsp;Rendering area</td>
</tr></table>

<div style='display:table-cell; padding: 10px; border: 2px solid #FFFFFF; vertical-align: middle; overflow: auto; background-image: url("resources/dash.png");'>
 <div style='font-size: 10px;' id=render>
  <table><tr><td><img src='resources/accept.png' width=16 height=16 alt=""/></td><td>Click on an example to render it!</td></tr></table>
 </div>
</div>

<br/><br/>

<table><tr>
 <td><img src='resources/application_view_list.png' width=16 height=16 alt=''/></td>
 <td>&nbsp;Source area</td>
</tr></table>

<div style='display:table-cell; padding: 10px;  border: 2px solid #FFFFFF; vertical-align: middle; overflow: auto; background-image: url("resources/dash.png");'>
 <div style='font-size: 10px;' id=source style='width: 700px;'>
  <table><tr><td><img src='resources/accept.png' width=16 height=16 alt=""/></td><td>Click on an example to get its source!</td></tr></table>
 </div>
</div>

</td></tr></table>
</body>
<script>
 URL        = "";
 SourceURL  = "";
 LastOpened = "";
 LastScript = "";

 function showHideMenu(Element)
  {
   if ( document.getElementById(Element).style.display == "none"  )
    {
     if ( LastOpened != "" && LastOpened != Element ) { showHideMenu(LastOpened); }

     document.getElementById(Element).style.display = "inline";
     document.getElementById(Element+"_main").style.fontWeight = "bold";
     LastOpened = Element;
    }
   else
    {
     document.getElementById(Element).style.display = "none";
     document.getElementById(Element+"_main").style.fontWeight = "normal";
     LastOpened = "";
    }
  }

 function render(PictureName)
  {
   if ( LastScript != "" ) { document.getElementById(LastScript).style.fontWeight = "normal"; }
   document.getElementById(PictureName).style.fontWeight = "bold";
   LastScript = PictureName;

   opacity("render",100,0,100);

   RandomKey = Math.random(100);
   URL       = PictureName + "?Seed=" + RandomKey;
   SourceURL = PictureName;

   ajaxRender(URL);
  }

 function StartFade()
  {
   Loader     = new Image();   
   Loader.src = URL;   
   setTimeout("CheckLoadingStatus()", 200);   
  }

 function CheckLoadingStatus()   
  {   
   if ( Loader.complete == true )   
    {
     changeOpac(0, "render");
     HTMLResult = "<center><img src='" + URL + "' alt=''/></center>";
     document.getElementById("render").innerHTML = HTMLResult;

     opacity("render",0,100,100);
     view(SourceURL);
    }
   else  
    setTimeout("CheckLoadingStatus()", 200);   
  }   

 function changeOpac(opacity, id)   
  {   
   var object = document.getElementById(id).style;   
   object.opacity = (opacity / 100);   
   object.MozOpacity = (opacity / 100);   
   object.KhtmlOpacity = (opacity / 100);   
   object.filter = "alpha(opacity=" + opacity + ")";   
  }   

 function wait()
  {
   HTMLResult = "<center><img src='resources/wait.gif' width=24 height=24 alt=''/><br>Rendering</center>";
   document.getElementById("render").innerHTML = HTMLResult;
   changeOpac(20, "render");
  }

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
     setTimeout("wait()",(timer * speed));
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

 function ajaxRender(URL)
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

   xmlhttp.onreadystatechange=function() { if (xmlhttp.readyState==4) { StartFade();  } }   
   xmlhttp.send(null)   
  }

 function view(URL)
  {
   var xmlhttp=false;   
   /*@cc_on @*/  
   /*@if (@_jscript_version >= 5)  
    try { xmlhttp = new ActiveXObject("Msxml2.XMLHTTP"); } catch (e) { try { xmlhttp = new ActiveXObject("Microsoft.XMLHTTP"); } catch (E) { xmlhttp = false; } }  
   @end @*/  
  
   URL = "index.php?Action=View&Script=" + URL;

   if (!xmlhttp && typeof XMLHttpRequest!='undefined')   
    { try { xmlhttp = new XMLHttpRequest(); } catch (e) { xmlhttp=false; } }   
  
   if (!xmlhttp && window.createRequest)   
    { try { xmlhttp = window.createRequest(); } catch (e) { xmlhttp=false; } }   
  
   xmlhttp.open("GET", URL,true);

   xmlhttp.onreadystatechange=function() { if (xmlhttp.readyState==4) { Result = xmlhttp.responseText; document.getElementById("source").innerHTML = Result.replace("/\<BR\>/");  } }   
   xmlhttp.send(null)   
  }
</script>
</html>
<?php
 function checkPHPVersion()
  {
   $PHPVersion	= phpversion();
   $Values	= preg_split("/\./",$PHPVersion);
   $PHPMajor	= $Values[0];
   $PHPMinor	= $Values[1];

   $GDVersion	= NULL;
   if (extension_loaded('gd'))
    {
     if (function_exists('gd_info'))
      {
       $GDVersionInfo = gd_info();
       preg_match('/\d/', $GDVersionInfo['GD Version'], $Match);
       $GDVersion = $Match[0];
      }
    }
 
   if ( $PHPMajor < 4 || $GDVersion < 2 )
    {
?>
<body>
 <div style='width: 300px; background-color: #FB8B8E; border: 1px solid #CB5B5E; margin: 10px; font-family: tahoma;'>
  <div style='background-color: #CB5B5E; color: #FFFFFF; padding: 4px; font-family: tahoma; font-size: 11px;'>
   <B>Warning</B>
  </div>
  <div style='padding: 4px; font-family: tahoma; font-size: 11px;' align='justify'>
   It seems that you're not meeting the pChart minimal server requirements:
   <br><br>
   &nbsp;&nbsp;-&nbsp;PHP must be at least <b>4.x</b><br/>
   &nbsp;&nbsp;-&nbsp;GD version <b>2.x</b><br/>
  </div>
 </div>
</body>
<html>
<?php
     exit();
    }
  }

 function goCheck($Script)
  {
   $Script = stripslashes($Script);
   $Script = preg_replace("/\//","",$Script);
   $Script = preg_replace("/\:/","",$Script);

   if ( file_exists($Script) ) 
    { highlight_file($Script); }
   else
    { echo "Script source code cannot be fetched."; }
   exit();
  }

 function size($Value)
  {
   if ( $Value < 1024 ) { return($Value." o."); }
   if ( $Value >= 1024 && $Value < 1024000 ) { return(floor($Value/1024)." ko."); }
   return(floor($Value/1024000))." mo.";
  }

 function left($value,$NbChar)  
  { return substr($value,0,$NbChar); }  
 
 function right($value,$NbChar)  
  { return substr($value,strlen($value)-$NbChar,$NbChar); }  
 
 function mid($value,$Depart,$NbChar)  
  { return substr($value,$Depart-1,$NbChar); }  
?>