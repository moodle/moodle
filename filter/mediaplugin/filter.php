<?php // $Id$
//////////////////////////////////////////////////////////////
//  Media plugin filtering
//
//  This filter will replace any links to a media file with
//  a media plugin that plays that media inline
//
//  To activate this filter, add a line like this to your
//  list of filters in your Filter configuration:
//
//  filter/mediaplugin/filter.php
//
//////////////////////////////////////////////////////////////

/// This is the filtering function itself.  It accepts the
/// courseid and the text to be filtered (in HTML form).

require_once($CFG->libdir.'/filelib.php');

function mediaplugin_filter($courseid, $text) {
    global $CFG;

    include 'defaultsettings.php';

    // We're using the UFO technique for flash to attain XHTML Strict 1.0
    // See: http://www.bobbyvandersluis.com/ufo/

    if ($CFG->filter_mediaplugin_enable_mp3) {
        $search = '/<a.*?href="([^<]+\.mp3)"[^>]*>.*?<\/a>/is';
        $text = preg_replace_callback($search, 'mediaplugin_filter_mp3_callback', $text);
    }

    if ($CFG->filter_mediaplugin_enable_swf) {
        $search = '/<a.*?href="([^<]+\.swf)(\?d=([\d]{1,3}%?)x([\d]{1,3}%?))?"[^>]*>.*?<\/a>/is';
        $text = preg_replace_callback($search, 'mediaplugin_filter_swf_callback', $text);
    }

    if ($CFG->filter_mediaplugin_enable_flv) {
        $search = '/<a.*?href="([^<]+\.flv)(\?d=([\d]{1,3}%?)x([\d]{1,3}%?))?"[^>]*>.*?<\/a>/is';
        $text = preg_replace_callback($search, 'mediaplugin_filter_flv_callback', $text);
    }

    if ($CFG->filter_mediaplugin_enable_mov) {
        $search = '/<a.*?href="([^<]+\.mov)(\?d=([\d]{1,3}%?)x([\d]{1,3}%?))?"[^>]*>.*?<\/a>/is';
        $text = preg_replace_callback($search, 'mediaplugin_filter_qt_callback', $text);
    }

    if ($CFG->filter_mediaplugin_enable_wmv) {
        $search = '/<a.*?href="([^<]+\.wmv)(\?d=([\d]{1,3}%?)x([\d]{1,3}%?))?"[^>]*>.*?<\/a>/is';
        $text = preg_replace_callback($search, 'mediaplugin_filter_wmp_callback', $text);
    }

    if ($CFG->filter_mediaplugin_enable_mpg) {
        $search = '/<a.*?href="([^<]+\.mpe?g)(\?d=([\d]{1,3}%?)x([\d]{1,3}%?))?"[^>]*>.*?<\/a>/is';
        $text = preg_replace_callback($search, 'mediaplugin_filter_qt_callback', $text);
    }

    if ($CFG->filter_mediaplugin_enable_avi) {
        $search = '/<a.*?href="([^<]+\.avi)(\?d=([\d]{1,3}%?)x([\d]{1,3}%?))?"[^>]*>.*?<\/a>/is';
        $text = preg_replace_callback($search, 'mediaplugin_filter_wmp_callback', $text);
    }

    if ($CFG->filter_mediaplugin_enable_ram) {
        $search = '/<a.*?href="([^<]+\.ram)"[^>]*>.*?<\/a>/is';
        $text = preg_replace_callback($search, 'mediaplugin_filter_real_callback', $text);
    }

    if ($CFG->filter_mediaplugin_enable_rpm) {
        $search = '/<a.*?href="([^<]+\.rpm)"[^>]*>.*?<\/a>/is';
        $text = preg_replace_callback($search, 'mediaplugin_filter_real_callback', $text);
    }

    if ($CFG->filter_mediaplugin_enable_rm) {
        $search = '/<a.*?href="([^<]+\.rm)"[^>]*>.*?<\/a>/is';
        $text = preg_replace_callback($search, 'mediaplugin_filter_real_callback', $text);
    }

    return $text;
}

///===========================
/// callback filter functions

function mediaplugin_filter_mp3_callback($link) {
    global $CFG, $THEME;

    if (!empty($THEME->filter_mediaplugin_colors)) {
        $c = $THEME->filter_mediaplugin_colors;   // You can set this up in your theme/xxx/config.php
    } else {
        $c = 'bgColour=000000&btnColour=ffffff&btnBorderColour=cccccc&iconColour=000000&'.
             'iconOverColour=00cc00&trackColour=cccccc&handleColour=ffffff&loaderColour=ffffff&'.
             'waitForPlay=yes';
    }
    $c = htmlentities($c);

    static $count = 0;
    $count++;
    $id = 'filter_mp3_'.time().$count; //we need something unique because it might be stored in text cache

    $url = addslashes_js($link[1]);

    return $link[0].
'<span class="mediaplugin mediaplugin_mp3" id="'.$id.'">('.get_string('mp3audio', 'mediaplugin').')</span>
<script type="text/javascript" src="'.$CFG->wwwroot.'/lib/ufo.js"></script>
<script type="text/javascript">
//<![CDATA[
  var FO = { movie:"'.$CFG->wwwroot.'/filter/mediaplugin/mp3player.swf?src='.$url.'",
    width:"90", height:"15", majorversion:"6", build:"40", flashvars:"'.$c.'", quality: "high" };
  UFO.create(FO, "'.$id.'");
//]]>
</script>';
}

function mediaplugin_filter_swf_callback($link) {
    static $count = 0;
    $count++;
    $id = 'filter_swf_'.time().$count; //we need something unique because it might be stored in text cache

    $width  = empty($link[3]) ? '400' : $link[3];
    $height = empty($link[4]) ? '300' : $link[4];
    $url = addslashes_js($link[1]);

    return $link[0].
'<span class="mediaplugin mediaplugin_swf" id="'.$id.'">('.get_string('flashanimation', 'mediaplugin').')</span>
<script type="text/javascript">
//<![CDATA[
  var FO = { movie:"'.$url.'", width:"'.$width.'", height:"'.$height.'", majorversion:"6", build:"40",
    allowscriptaccess:"never", quality: "high" };
  UFO.create(FO, "'.$id.'");
//]]>
</script>';
}

function mediaplugin_filter_flv_callback($link) {
    global $CFG;

    static $count = 0;
    $count++;
    $id = 'filter_flv_'.time().$count; //we need something unique because it might be stored in text cache

    $width  = empty($link[3]) ? '480' : $link[3];
    $height = empty($link[4]) ? '360' : $link[4];
    $url = addslashes_js($link[1]);

    return $link[0].
'<span class="mediaplugin mediaplugin_flv" id="'.$id.'">('.get_string('flashvideo', 'mediaplugin').')</span>
<script type="text/javascript">
//<![CDATA[
  var FO = { movie:"'.$CFG->wwwroot.'/filter/mediaplugin/flvplayer.swf?file='.$url.'",
    width:"'.$width.'", height:"'.$height.'", majorversion:"6", build:"40",
    allowscriptaccess:"never", quality: "high" };
  UFO.create(FO, "'.$id.'");
//]]>
</script>';
}

function mediaplugin_filter_real_callback($link, $autostart=false) {
    $url = addslashes_js($link[1]);
    $mimetype = mimeinfo('type', $url);
    $autostart = $autostart ? 'true' : 'false';

// embed kept for now see MDL-8674
    return $link[0].
'<span class="mediaplugin mediaplugin_real">
<script type="text/javascript">
//<![CDATA[
document.write(\'<object classid="clsid:CFCDAA03-8BE4-11cf-B84B-0020AFBBCCFA" width="240" height="180">\\
  <param name="src" value="'.$url.'" />\\
  <param name="autostart" value="'.$autostart.'" />\\
  <param name="controls" value="imagewindow" />\\
  <param name="console" value="video" />\\
  <param name="loop" value="true" />\\
  <embed src="'.$url.'" width=240" height="180" loop="true" type="'.$mimetype.'" controls="imagewindow" console="video" autostart="'.$autostart.'" />\\
  </object><br />\\
  <object classid="clsid:CFCDAA03-8BE4-11cf-B84B-0020AFBBCCFA" width="240" height="30">\\
  <param name="src" value="'.$url.'" />\\
  <param name="autostart" value="'.$autostart.'" />\\
  <param name="controls" value="ControlPanel" />\\
  <param name="console" value="video" />\\
  <embed src="'.$url.'" width="240" height="30" controls="ControlPanel" type="'.$mimetype.'" console="video" autostart="'.$autostart.'" />\\
  </object>\');
//]]>
</script></span>';
}

/**
 * Embed video using window media player if available
 */
function mediaplugin_filter_wmp_callback($link, $autostart=false) {
    $url = $link[1];
    if (empty($link[3]) or empty($link[4])) {
        $mpsize = '';
        $size = 'width="300" height="260"';
        $autosize = 'true';
    } else {
        $size = 'width="'.$link[3].'" height="'.$link[4].'"';
        $mpsize = $size;
        $autosize = 'false';
    }
    $mimetype = mimeinfo('type', $url);
    $autostart = $autostart ? 'true' : 'false';

    return $link[0].
'<span class="mediaplugin mediaplugin_wmp">
<object classid="CLSID:6BF52A52-394A-11d3-B153-00C04F79FAA6" '.$mpsize.'
  standby="Loading Microsoft(R) Windows(R) Media Player components..."
  type="application/x-oleobject">
 <param name="Filename" value="'.$url.'" />
 <param name="src" value="'.$url.'" />
 <param name="url" value="'.$url.'" />
 <param name="ShowControls" value="true" />
 <param name="AutoRewind" value="true" />
 <param name="AutoStart" value="'.$autostart.'" />
 <param name="Autosize" value="'.$autosize.'" />
 <param name="EnableContextMenu" value="true" />
 <param name="TransparentAtStart" value="false" />
 <param name="AnimationAtStart" value="false" />
 <param name="ShowGotoBar" value="false" />
 <param name="EnableFullScreenControls" value="true" />
<!--[if !IE]>-->
  <object data="'.$url.'" type="'.$mimetype.'" '.$size.'>
   <param name="src" value="'.$url.'" />
   <param name="controller" value="true" />
   <param name="autoplay" value="'.$autostart.'" />
   <param name="autostart" value="'.$autostart.'" />
   <param name="resize" value="scale" />
  </object>
<!--<![endif]-->
</object></span>';
}

function mediaplugin_filter_qt_callback($link, $autostart=false) {
    $url = $link[1];
    if (empty($link[3]) or empty($link[4])) {
        $size = 'width="280" height="210"';
    } else {
        $size = 'width="'.$link[3].'" height="'.$link[4].'"';
    }
    $mimetype = mimeinfo('type', $url);
    $autostart = $autostart ? 'true' : 'false';

    return $link[0].
'<span class="mediaplugin mediaplugin_qt">
<object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B"
  codebase="http://www.apple.com/qtactivex/qtplugin.cab" '.$size.'>
 <param name="pluginspage" value="http://www.apple.com/quicktime/download/" />
 <param name="src" value="'.$url.'" />
 <param name="coltroller" value="true" />
 <param name="loop" value="true" />
 <param name="autoplay" value="'.$autostart.'" />
 <param name="autostart" value="'.$autostart.'" />
 <param name="scale" value="aspect" />
<!--[if !IE]>-->
  <object data="'.$url.'" type="'.$mimetype.'" '.$size.'>
   <param name="src" value="'.$url.'" />
   <param name="pluginurl" value="http://www.apple.com/quicktime/download/" />
   <param name="controller" value="true" />
   <param name="loop" value="true" />
   <param name="autoplay" value="'.$autostart.'" />
   <param name="autostart" value="'.$autostart.'" />
   <param name="scale" value="aspect" />
  </object>
<!--<![endif]-->
</object></span>';
}

?>
