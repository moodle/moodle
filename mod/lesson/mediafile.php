<?php
    // This file plays the mediafile set in lesson settings
    // Almost all of this code is from /mod/resource/type/file/resource.class.php so major props for resource
    // If there is a way to use the resource class instead of this code, please change to do so
    // because I could not figure it out

    require_once('../../config.php');
    require_once($CFG->libdir.'/filelib.php');
    
    $id = required_param('id', PARAM_INT);    // Course Module ID
    $printclose = optional_param('printclose', 0, PARAM_INT);
    
    if (! $cm = get_record('course_modules', 'id', $id)) {
        error('Course Module ID was incorrect');
    }

    if (! $course = get_record('course', 'id', $cm->course)) {
        error('Course is misconfigured');
    }

    if (! $lesson = get_record('lesson', 'id', $cm->instance)) {
        error('Course module is incorrect');
    }
    
    if ($printclose) {  // this is for framesets
        if ($lesson->mediaclose) {
        echo '<center>
            <form>
            <input type="button" onclick="top.close();" value="'.get_string("closewindow").'" />
            </form>
            </center>';
        }
        exit();
    }

    require_login($course->id, false, $cm);
    
    // get the mimetype
    //$path_parts = pathinfo('http://www.apple.com');  //$lesson->mediafile
    $mimetype = mimeinfo("type", $lesson->mediafile);  //$path_parts['basename']

    //print_header();

    if (substr_count($lesson->mediafile, '//') == 1) {
        // OK, taking a leap of faith here.  We are assuming that teachers are cool
        // and thus the mediafile is a url
        $fullurl = $lesson->mediafile;        
    } else {
        // get the full url to the file while taking into consideration $CFG->slasharguments    
        if ($CFG->slasharguments) {
            $relativeurl = "/file.php/{$course->id}/{$lesson->mediafile}";
        } else {
            $relativeurl = "/file.php?file=/{$course->id}/{$lesson->mediafile}";
        }
        $fullurl = "$CFG->wwwroot$relativeurl";
    }
    
    

    // find the correct type and print it out
    if ($mimetype == "audio/mp3") {    // It's an MP3 audio file
    
        if (!empty($THEME->resource_mp3player_colors)) {
            $c = $THEME->resource_mp3player_colors;   // You can set this up in your theme/xxx/config.php
        } else {
            $c = 'bgColour=000000&btnColour=ffffff&btnBorderColour=cccccc&iconColour=000000&'.
                 'iconOverColour=00cc00&trackColour=cccccc&handleColour=ffffff&loaderColour=ffffff&'.
                 'font=Arial&fontColour=3333FF&buffer=10&waitForPlay=no&autoPlay=yes';
        }
        $c .= '&volText='.get_string('vol', 'resource').'&panText='.get_string('pan','resource');
        $c = htmlentities($c);
        echo '<div class="mp3player" align="center">';
        echo '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"';
        echo '        codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" ';
        echo '        width="600" height="70" id="mp3player" align="">';
        echo '<param name="movie" value="'.$CFG->wwwroot.'/lib/mp3player/mp3player.swf?src='.$fullurl.'">';
        echo '<param name="quality" value="high">';
        echo '<param name="bgcolor" value="#333333">';
        echo '<param name="flashvars" value="'.$c.'&amp;" />';
        echo '<embed src="'.$CFG->wwwroot.'/lib/mp3player/mp3player.swf?src='.$fullurl.'" ';
        echo ' quality="high" bgcolor="#333333" width="600" height="70" name="mp3player" ';
        echo ' type="application/x-shockwave-flash" ';
        echo ' flashvars="'.$c.'&amp;" ';
        echo ' pluginspage="http://www.macromedia.com/go/getflashplayer">';
        echo '</embed>';
        echo '</object>';
        echo '</div>';

    } else if (substr($mimetype, 0, 10) == "video/x-ms") {   // It's a Media Player file
    
        echo "<center><p>";
        echo '<object classid="CLSID:22D6f312-B0F6-11D0-94AB-0080C74C7E95"';
        echo '        codebase="http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=5,1,52,701" ';
        echo '        standby="Loading Microsoft® Windows® Media Player components..." ';
        echo '        id="msplayer" align="" type="application/x-oleobject">';
        echo "<param name=\"Filename\" value=\"$fullurl\">";
        echo '<param name="ShowControls" value="true" />';
        echo '<param name="AutoRewind" value="true" />';
        echo '<param name="AutoStart" value="true" />';
        echo '<param name="Autosize" value="true" />';
        echo '<param name="EnableContextMenu" value="true" />';
        echo '<param name="TransparentAtStart" value="false" />';
        echo '<param name="AnimationAtStart" value="false" />';
        echo '<param name="ShowGotoBar" value="false" />';
        echo '<param name="EnableFullScreenControls" value="true" />';
        echo "\n<embed src=\"$fullurl\" name=\"msplayer\" type=\"$mimetype\" ";
        echo ' ShowControls="1" AutoRewind="1" AutoStart="1" Autosize="0" EnableContextMenu="1"';
        echo ' TransparentAtStart="0" AnimationAtStart="0" ShowGotoBar="0" EnableFullScreenControls="1"';
        echo ' pluginspage="http://www.microsoft.com/Windows/Downloads/Contents/Products/MediaPlayer/">';
        echo '</embed>';
        echo '</object>';
        echo "</p></center>";

    } else if ($mimetype == "video/quicktime") {   // It's a Quicktime file
    
        echo "<center><p>";
        echo '<object classid="CLSID:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B"';
        echo '        codebase="http://www.apple.com/qtactivex/qtplugin.cab" ';
        echo '        height="450" width="600"';
        echo '        id="quicktime" align="" type="application/x-oleobject">';
        echo "<param name=\"src\" value=\"$fullurl\" />";
        echo '<param name="autoplay" value="true" />';
        echo '<param name="loop" value="true" />';
        echo '<param name="controller" value="true" />';
        echo '<param name="scale" value="aspect" />';
        echo "\n<embed src=\"$fullurl\" name=\"quicktime\" type=\"$mimetype\" ";
        echo ' height="450" width="600" scale="aspect"';
        echo ' autoplay="true" controller="true" loop="true" ';
        echo ' pluginspage="http://quicktime.apple.com/">';
        echo '</embed>';
        echo '</object>';
        echo "</p></center>";
    
    } else if ($mimetype == "application/x-shockwave-flash") {   // It's a flash file
    
        error('Flash is not supported yet');
    
    } else if ($mimetype == "audio/x-pn-realaudio") {   // It's a realmedia file
    	
		echo '<object id="rvocx" classid="clsid:CFCDAA03-8BE4-11cf-B84B-0020AFBBCCFA" width="600" height="50">';
        echo "<param name=\"src\" value=\"$fullurl\">";
        echo '<param name="console" value="video">';  // not sure what the console param should equal
        echo '<param name="controls" value="ControlPanel">';
        echo '<param name="autostart" value="true">';
        echo '<param name="loop" value="true">';
        echo '<embed name="rvocx" src="'.$fullurl.'" height="50" width="600" autostart="true" loop="true" nojava="true" console="video" controls="ControlPanel"></embed>';
        echo '<noembed></noembed>';
        echo '</object>';

    } else if (is_url($lesson->mediafile) or $mimetype == 'text/html' or $mimetype == 'text/plain') {
        // might be dangerous to handle all of these in the same fasion.  It is being set by a teacher though.
        echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Frameset//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd\">\n";
        echo "<html dir=\"ltr\">\n";
        echo '<head>';
        echo '<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />';
        echo "<title>{$course->shortname}</title></head>\n";
        if ($lesson->mediaclose) {
            echo "<frameset rows=\"90%,*\">";
            echo "<frame src=\"$fullurl\" />";
            echo "<frame src=\"mediafile.php?id=$cm->id&printclose=1\" />";
            echo "</frameset>";
        } else {
            echo "<frameset rows=\"100%\">";
            echo "<frame src=\"$fullurl\" />";
            echo "</frameset>";        
        }
        exit();

    } else {
        error('Unsupported mime type: '.$mimetype);
    }
    
    function is_url($test_url) {
        // the following is barrowed from resource code.  Thanks!
        if (strpos($test_url, '://')) {     // eg http:// https:// ftp://  etc
            return true;
        }
        if (strpos($test_url, '/') === 0) { // Starts with slash
            return true;
        }
        return false;
    }

    if ($lesson->mediaclose) {
       echo '<p>';
       close_window_button();
       echo '</p>';
    }
?>