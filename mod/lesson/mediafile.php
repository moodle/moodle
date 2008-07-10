<?php  // $Id$
/**
 * This file plays the mediafile set in lesson settings.
 *
 * Almost all of this code is from /mod/resource/type/file/resource.class.php so major props for resource
 *
 *  If there is a way to use the resource class instead of this code, please change to do so
 *
 *
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package lesson
 **/

    require_once('../../config.php');
    require_once($CFG->libdir.'/filelib.php');

    $id = required_param('id', PARAM_INT);    // Course Module ID
    $printclose = optional_param('printclose', 0, PARAM_INT);
    
    if (! $cm = get_coursemodule_from_id('lesson', $id)) {
        error('Course Module ID was incorrect');
    }

    if (! $course = get_record('course', 'id', $cm->course)) {
        error('Course is misconfigured');
    }

    if (! $lesson = get_record('lesson', 'id', $cm->instance)) {
        error('Course module is incorrect');
    }

    require_login($course->id, false, $cm);

    // Get the mimetype
    $mimetype = mimeinfo("type", $lesson->mediafile);

    if (!is_url($lesson->mediafile) and !in_array($mimetype, array('text/plain', 'text/html'))) {
        print_header($course->shortname);
    }

    if ($printclose) {  // this is for framesets
        if ($lesson->mediaclose) {
            print_header($course->shortname);
            echo '<div class="lessonmediafilecontrol">
                <form>
                <div>
                <input type="button" onclick="top.close();" value="'.get_string("closewindow").'" />
                </div>
                </form>
                </div>';
            print_footer();
        }
        exit();
    }

    if (is_url($lesson->mediafile)) {
        $fullurl = $lesson->mediafile;        
    } else {
        $fullurl = get_file_url($course->id .'/'. $lesson->mediafile);
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
        echo '<div class="mp3player" class="lessonmediafilecontrol">';
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
    
        echo "<div class=\"lessonmediafilecontrol\"><p>";
        echo '<object classid="CLSID:22D6f312-B0F6-11D0-94AB-0080C74C7E95"';
        echo '        codebase="http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=5,1,52,701" ';
        echo '        standby="Loading Microsoft(R) Windows(R) Media Player components..." ';
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
        echo "</p></div>";

    } else if ($mimetype == "video/quicktime") {   // It's a Quicktime file
    
        echo "<div class=\"lessonmediafilecontrol\"><p>";
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
        echo "</p></div>";
    
    //} else if ($mimetype == "application/x-shockwave-flash") {   // It's a flash file
    
    //    error('Flash is not supported yet');
    
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
            echo "<frame src=\"mediafile.php?id=$cm->id&amp;printclose=1\" />";
            echo "</frameset>";
        } else {
            echo "<frameset rows=\"100%\">";
            echo "<frame src=\"$fullurl\" />";
            echo "</frameset>";
        }
        echo '</html>';
        exit();

    } else if (in_array($mimetype, array('image/gif','image/jpeg','image/png'))) {  // Image

        echo "<div class=\"lessonmediafilecontrol\"><p>";
        echo '<img class="lessonimage" src="'.s($fullurl).'" alt="" />';
        echo "</p></div>";

    } else {  // Default

        // Get the file name
        $file = pathinfo($lesson->mediafile);
        $filename = basename($file['basename'], '.'.$file['extension']);

        echo "<div class=\"lessonmediafilecontrol\"><p>";
        notify(get_string('clicktodownload', 'lesson'));
        echo "<a href=\"$fullurl\">".format_string($filename).'</a>';
        echo "</p></div>";
        
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
       echo '<div class="lessonmediafilecontrol">';
       close_window_button();
       echo '</div>';
    }
    
    print_footer();
?>
