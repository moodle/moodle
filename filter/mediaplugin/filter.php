<?php // $id$
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

function mediaplugin_filter($courseid, $text) {
    global $CFG, $THEME;

    static $count = 0;

    $count++;

    include "defaultsettings.php";

    if ($CFG->filter_mediaplugin_enable_mp3) {
        static $c;

        if (empty($c)) {
            if (!empty($THEME->filter_mediaplugin_colors)) {
                $c = $THEME->filter_mediaplugin_colors;   // You can set this up in your theme/xxx/config.php
            } else {
                $c = 'bgColour=000000&amp;btnColour=ffffff&amp;btnBorderColour=cccccc&amp;iconColour=000000&amp;iconOverColour=00cc00&amp;trackColour=cccccc&amp;handleColour=ffffff&amp;loaderColour=ffffff&amp;waitForPlay=yes&amp;';
            }
        }
        $search = '/<a(.*?)href=\"([^<]+)\.mp3\"([^>]*)>(.*?)<\/a>/is';

        // We're using the UFO technique to attain XHTML Strict 1.0
        // See: http://www.bobbyvandersluis.com/ufo/

        $replace = '<script type="text/javascript">'."\n".
                   '//<![CDATA['."\n".
                   'var FO'.$count.' = { movie:"'.$CFG->wwwroot.'/filter/mediaplugin/mp3player.swf?src=\\2.mp3",'.
                   'width:"90", height:"15", majorversion:"6", build:"40", flashvars:"'.$c.'", quality: "high" };'."\n".
                   'UFO.create(FO'.$count.', "filtermp3'.$count.'");'."\n".
                   '//]]>'."\n".
                   '</script>'."\n".
                   '\\0&nbsp;<span class="mediaplugin mp3" id="filtermp3'.$count.'">'.
                   '('.get_string('mp3audio', 'mediaplugin').')'.
                   '</span>'."\n";
    
        $text = preg_replace($search, $replace, $text);
    }

    if ($CFG->filter_mediaplugin_enable_swf) {
        $search = array(
                '/<a(.*?)href=\"([^<]+)\.swf\?d=([\d]{1,3}%?)x([\d]{1,3}%?)\"([^>]*)>(.*?)<\/a>/is',
                '/<a(.*?)href=\"([^<]+)\.swf\"([^>]*)>(.*?)<\/a>/is'
                );

        $replace = array();

        $replace[0] = '<script type="text/javascript">'."\n".
                      '//<![CDATA['."\n".
                      'var FO'.$count.' = { movie:"\\2.swf", width:"\\3", height:"\\4", majorversion:"6", build:"40", '.
                      'allowscriptaccess:"never", quality: "high" };'."\n".
                      'UFO.create(FO'.$count.', "filterswf'.$count.'");'."\n".
                      '//]]>'."\n".
                      '</script>'."\n".
                      '\\0&nbsp;<span class="mediaplugin swf" id="filterswf'.$count.'">'.
                      '('.get_string('flashanimation', 'mediaplugin').')'.
                      '</span>'."\n";

        $replace[1] = '<script type="text/javascript">'."\n".
                      '//<![CDATA['."\n".
                      'var FO'.$count.' = { movie:"\\2.swf", width:"400", height:"300", majorversion:"6", build:"40", '.
                      'allowscriptaccess:"never", quality: "high" };'."\n".
                      'UFO.create(FO'.$count.', "filterswf'.$count.'");'."\n".
                      '//]]>'."\n".
                      '</script>'."\n".
                      '\\0&nbsp;<span class="mediaplugin swf" id="filterswf'.$count.'">'.
                      '('.get_string('flashanimation', 'mediaplugin').')'.
                      '</span>'."\n";

        $text = preg_replace($search, $replace, $text);

    }

    if ($CFG->filter_mediaplugin_enable_flv) {

        $replace = array();

        $search = array(
                '/<a(.*?)href=\"([^<]+)\.flv\?d=([\d]{1,3}%?)x([\d]{1,3}%?)\"([^>]*)>(.*?)<\/a>/is',
                '/<a(.*?)href=\"([^<]+)\.flv\"([^>]*)>(.*?)<\/a>/is'
                );

        $replace[0] = '<script type="text/javascript">'."\n".
                      '//<![CDATA['."\n".
                      'var FO'.$count.' = { movie:"'.$CFG->wwwroot.'/filter/mediaplugin/flvplayer.swf?file=\\2.flv", '.
                      'width:"\\3", height:"\\4", majorversion:"6", build:"40", '.
                      'allowscriptaccess:"never", quality: "high" };'."\n".
                      'UFO.create(FO'.$count.', "filterflv'.$count.'");'."\n".
                      '//]]>'."\n".
                      '</script>'."\n".
                      '\\0&nbsp;<span class="mediaplugin flv" id="filterflv'.$count.'">'.
                      '('.get_string('flashvideo', 'mediaplugin').')'.
                      '</span>'."\n";

        $replace[1] = '<script type="text/javascript">'."\n".
                      '//<![CDATA['."\n".
                      'var FO'.$count.' = { movie:"'.$CFG->wwwroot.'/filter/mediaplugin/flvplayer.swf?file=\\2.flv", '.
                      'width:"480", height:"360", majorversion:"6", build:"40", '.
                      'allowscriptaccess:"never", quality: "high" };'."\n".
                      'UFO.create(FO'.$count.', "filterflv'.$count.'");'."\n".
                      '//]]>'."\n".
                      '</script>'."\n".
                      '\\0&nbsp;<span class="mediaplugin flv" id="filterflv'.$count.'">'.
                      '('.get_string('flashvideo', 'mediaplugin').')'.
                      '</span>'."\n";

        $text = preg_replace($search, $replace, $text);
    }

    if ($CFG->filter_mediaplugin_enable_mov) {
        $search = '/<a(.*?)href=\"([^<]+)\.mov\"([^>]*)>(.*?)<\/a>/is';

        $replace  = '\\0<p class="mediaplugin mov"><object classid="CLSID:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B"';
        $replace .= '        codebase="http://www.apple.com/qtactivex/qtplugin.cab" ';
        $replace .= '        height="300" width="400"';
        $replace .= '        id="quicktime" type="application/x-oleobject">';
        $replace .= '<param name="src" value="\\2.mov" />';
        $replace .= '<param name="autoplay" value="false" />';
        $replace .= '<param name="loop" value="true" />';
        $replace .= '<param name="controller" value="true" />';
        $replace .= '<param name="scale" value="aspect" />';
        $replace .= '</object></p>';

        $text = preg_replace($search, $replace, $text);
    }

    if ($CFG->filter_mediaplugin_enable_wmv) {
        $search = '/<a(.*?)href=\"([^<]+)\.wmv\"([^>]*)>(.*?)<\/a>/is';

        $replace  = '\\0<p class="mediaplugin wmv"><object classid="CLSID:22D6f312-B0F6-11D0-94AB-0080C74C7E95"';
        $replace .= ' codebase="http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=5,1,52,701" ';
        $replace .= ' standby="Loading Microsoft� Windows� Media Player components..." ';
        $replace .= ' id="msplayer" type="application/x-oleobject">';
        $replace .= '<param name="Filename" value="\\2.wmv" />';
        $replace .= '<param name="ShowControls" value="true" />';
        $replace .= '<param name="AutoRewind" value="true" />';
        $replace .= '<param name="AutoStart" value="false" />';
        $replace .= '<param name="Autosize" value="true" />';
        $replace .= '<param name="EnableContextMenu" value="true" />';
        $replace .= '<param name="TransparentAtStart" value="false" />';
        $replace .= '<param name="AnimationAtStart" value="false" />';
        $replace .= '<param name="ShowGotoBar" value="false" />';
        $replace .= '<param name="EnableFullScreenControls" value="true" />';
        $replace .= '</object></p>';

        $text = preg_replace($search, $replace, $text);
    }

    if ($CFG->filter_mediaplugin_enable_mpg) {
        $search = '/<a(.*?)href=\"([^<]+)\.(mpe?g)\"([^>]*)>(.*?)<\/a>/is';

        $replace = '\\0<p class="mediaplugin mpg"><object width="240" height="180">';
        $replace .= '<param name="src" value="\\2.\\3" />';
        $replace .= '<param name="controller" value="true" />';
        $replace .= '<param name="autoplay" value="false" />';
        $replace .= '</object></p>';
        
        $text = preg_replace($search, $replace, $text);
    }

    if ($CFG->filter_mediaplugin_enable_avi) {
        $search = '/<a(.*?)href=\"([^<]+)\.avi\"([^>]*)>(.*?)<\/a>/is';

        $replace = '\\0<p class="mediaplugin avi"><object width="240" height="180">';
        $replace .= '<param name="src" value="\\2.avi" />';
        $replace .= '<param name="controller" value="true" />';
        $replace .= '<param name="autoplay" value="false" />';
        $replace .= '</object></p>';
    
        $text = preg_replace($search, $replace, $text);
    }
    
    if ($CFG->filter_mediaplugin_enable_ram) {
        $search = '/<a(.*?)href=\"([^<]+)\.ram\"([^>]*)>(.*?)<\/a>/is';

        $replace  = '\\0<p class="mediaplugin ram"><script type="text/javascript">//<![CDATA['."\n".
        'document.write(\'<object width="240" height="180">';
        $replace .= '<param name="src" value="\\2.ram" />';
        $replace .= '<param name="autoStart" value="true" />';
        $replace .= '<param name="controls" value="imagewindow" />';
        $replace .= '<param name="console" value="video" />';
        $replace .= '<param name="loop" value="true" />';
        $replace .= '<embed src="\\2.ram" width=240" height="180" loop="true" type="audio/x-pn-realaudio-plugin" controls="imagewindow" console="video" autostart="true" />';
        $replace .= '<\/object><br />';

        $replace .= '<object width="240" height="30">';
        $replace .= '<param name="src" value="\\2.ram" />';
        $replace .= '<param name="autoStart" value="true" />';
        $replace .= '<param name="controls" value="ControlPanel" />';
        $replace .= '<param name="console" value="video" />';
        $replace .= '<embed src="\\2.ram" width="240" height="30" controls="ControlPanel" type="audio/x-pn-realaudio-plugin" console="video" autostart="true" />';
        $replace .= '<\/object>\')'."\n".'//]]>'."\n".'</script></p>';

        $text = preg_replace($search, $replace, $text);
    }
     
    if ($CFG->filter_mediaplugin_enable_rpm) {
        $search = '/<a(.*?)href=\"([^<]+)\.rpm\"([^>]*)>(.*?)<\/a>/is';

        $replace  = '\\0<p class="mediaplugin rpm"><script type="text/javascript">//<![CDATA['."\n".
        'document.write(\'<object width="240" height="180">';
        $replace .= '<param name="src" value="\\2.rpm" />';
        $replace .= '<param name="autostart" value="true" />';
        $replace .= '<param name="controls" value="imagewindow" />';
        $replace .= '<param name="console" value="video" />';
        $replace .= '<param name="loop" value="true" />';
        $replace .= '<embed src="\\2.rpm" width=240" height="180" loop="true" type="audio/x-pn-realaudio-plugin" controls="imagewindow" console="video" autostart="true" />';
        $replace .= '</object><br />';

        $replace .= '<object width="240" height="30">';
        $replace .= '<param name="src" value="\\2.rpm" />';
        $replace .= '<param name="autostart" value="true" />';
        $replace .= '<param name="controls" value="ControlPanel" />';
        $replace .= '<param name="console" value="video" />';
        $replace .= '<embed src="\\2.rpm" width="240" height="30" controls="ControlPanel" type="audio/x-pn-realaudio-plugin" console="video" autostart="true" />';
        $replace .= '</object>\')'."\n".'//]]>'."\n".'</script></p>';

        $text = preg_replace($search, $replace, $text);
    }
    
    if ($CFG->filter_mediaplugin_enable_rm) {
        $search = '/<a(.*?)href=\"([^<]+)\.rm\"([^>]*)>(.*?)<\/a>/is';

        $replace  = '\\0<p class="mediaplugin rm"><script type="text/javascript">//<![CDATA['."\n".
        'document.write(\'<object width="240" height="180">';
        $replace .= '<param name="src" value="\\2.rm" />';
        $replace .= '<param name="autostart" value="true" />';
        $replace .= '<param name="controls" value="imagewindow" />';
        $replace .= '<param name="console" value="video" />';
        $replace .= '<param name="loop" value="true" />';
        $replace .= '<embed src="\\2.rm" width=240" height="180" loop="true" type="audio/x-pn-realaudio-plugin" controls="imagewindow" console="video" autostart="true" />';
        $replace .= '</object><br />';

        $replace .= '<object width="240" height="30">';
        $replace .= '<param name="src" value="\\2.rm" />';
        $replace .= '<param name="autostart" value="true" />';
        $replace .= '<param name="controls" value="ControlPanel" />';
        $replace .= '<param name="console" value="video" />';
        $replace .= '<embed src="\\2.rm" width="240" height="30" controls="ControlPanel" type="audio/x-pn-realaudio-plugin" console="video" autostart="true" />';
        $replace .= '</object>\')'."\n".'//]]>'."\n".'</script></p>';

        $text = preg_replace($search, $replace, $text);
    }

    return $text;
}


?>
