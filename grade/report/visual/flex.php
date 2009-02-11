<?php
///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999 onwards  Martin Dougiamas  http://moodle.com       //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/**
* HTML for the visual report.
* Based on generated code from flex builder.
*/
?>

<div style="vertical-align: top; text-align: center;">
<script src="AC_OETags.js" language="javascript"></script>
<script language="JavaScript" type="text/javascript">
<!--
// -----------------------------------------------------------------------------
// Major version of Flash required
var requiredMajorVersion = 9;
// Minor version of Flash required
var requiredMinorVersion = 0;
// Minor version of Flash required
var requiredRevision = 28;
// -----------------------------------------------------------------------------


// Version check for the Flash Player that has the ability to start Player Product Install (6.0r65)
var hasProductInstall = DetectFlashVer(6, 0, 65);

// Version check based upon the values defined in globals
var hasRequestedVersion = DetectFlashVer(requiredMajorVersion, requiredMinorVersion, requiredRevision);

if ( hasProductInstall && !hasRequestedVersion ) {
	// DO NOT MODIFY THE FOLLOWING FOUR LINES
	// Location visited after installation is complete if installation is required
	var MMPlayerType = (isIE == true) ? "ActiveX" : "PlugIn";
	var MMredirectURL = window.location;
    document.title = document.title.slice(0, 47) + " - Flash Player Installation";
    var MMdoctitle = document.title;

	AC_FL_RunContent(
        "flashVars", "<?php echo $flashvarshtml; ?>",
		"framerate", "<?php echo $visual->framerate; ?>",
		"src", "playerProductInstall",
		"FlashVars", "MMredirectURL="+MMredirectURL+'&MMplayerType='+MMPlayerType+'&MMdoctitle='+MMdoctitle+"",
		"width", "<?php echo $visual->width; ?>",
		"height", "<?php echo $visual->height; ?>",
		"align", "middle",
		"id", "flare_visualization",
		"quality", "<?php echo $visual->quality ?>",
		"bgcolor", "#<?php echo $visual->backgroundcolor ?>",
		"name", "flare_visualization",
		"allowScriptAccess","sameDomain",
		"type", "application/x-shockwave-flash",
		"pluginspage", "http://www.adobe.com/go/getflashplayer"
	);
} else if (hasRequestedVersion) {
	// if we've detected an acceptable version
	// embed the Flash Content SWF when all tests are passed
	AC_FL_RunContent(
            "flashVars", "<?php echo $flashvarshtml; ?>",
			"framerate", "<?php echo $visual->framerate; ?>",
			"src", "flare_visualization",
			"width", "<?php echo $visual->width; ?>",
			"height", "<?php echo $visual->height; ?>",
			"align", "middle",
			"id", "flare_visualization",
			"quality", "<?php echo $visual->quality ?>",
			"bgcolor", "#<?php echo $visual->backgroundcolor ?>",
			"name", "flare_visualization",
			"allowScriptAccess","sameDomain",
			"type", "application/x-shockwave-flash",
			"pluginspage", "http://www.adobe.com/go/getflashplayer"
	);
  } else {  // flash is too old or we can't detect the plugin
    var alternateContent = 'Alternate HTML content should be placed here. '
  	+ 'This content requires the Adobe Flash Player. '
   	+ '<a href=http://www.adobe.com/go/getflash/>Get Flash</a>';
    document.write(alternateContent);  // insert non-flash content
  }
// -->
</script>
<noscript>
  	<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
			id="flare_visualization" width="<?php echo $visual->width; ?>" height="<?php echo $visual->height; ?>"
			codebase="http://fpdownload.macromedia.com/get/flashplayer/current/swflash.cab">
            <param name="flashVars" value="<?php echo $flashvarshtml;?>" />
			
            <param name="movie" value="flare_visualization.swf" />
			<param name="quality" value="<?php echo $visual->quality ?>" />
			<param name="bgcolor" value="#<?php echo $visual->backgroundcolor ?>" />
			<param name="allowScriptAccess" value="sameDomain" />
			<embed src="flare_visualization.swf" quality="high" bgcolor="#<?php echo $visual->backgroundcolor ?>"
			    flashVars="<?php echo $flashvarshtml; ?>" 
				framerate="<?php echo $visual->framerate ?>"
				width="<?php echo $visual->width; ?>" height="<?php echo $visual->height; ?>" name="flare_visualization" align="middle"
				play="true"
				loop="false"
				quality="<?php echo $visual->quality ?>"
				allowScriptAccess="sameDomain"
				type="application/x-shockwave-flash"
				pluginspage="http://www.adobe.com/go/getflashplayer">
			</embed>
	</object>
</noscript>
</div>