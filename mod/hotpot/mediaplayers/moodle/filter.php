<?php // $id$
//////////////////////////////////////////////////////////////
//  Media plugin filtering
//
//  This filter will replace any links to a media file with
//  a media plugin that plays that media inline
//
//////////////////////////////////////////////////////////////

/// This is the filtering function itself.  It accepts the
/// courseid and the text to be filtered (in HTML form).

function hotpot_mediaplayer_moodle(&$hotpot, $text) {
    global $CFG, $THEME;

    if ($CFG->filter_mediaplugin_enable_mp3) {
        static $c;

        if (empty($c)) {
            if (!empty($THEME->filter_mediaplugin_colors)) {
                $c = $THEME->filter_mediaplugin_colors;   // You can set this up in your theme/xxx/config.php
            } else {
                $c = 'bgColour=000000&amp;btnColour=ffffff&amp;btnBorderColour=cccccc&amp;iconColour=000000&amp;iconOverColour=00cc00&amp;trackColour=cccccc&amp;handleColour=ffffff&amp;loaderColour=ffffff&amp;waitForPlay=yes&amp;';
            }
        }
        // $c = htmlentities($c);  // Commented out pending bug 5223
        $search = '/<a(.*?)href=\"([^<]+)\.mp3\"([^>]*)>(.*?)<\/a>/is';

        $replace  = '\\0&nbsp;<object class="mediaplugin mp3" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"';
        $replace .= ' codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" ';
        $replace .= ' width="90" height="15" id="mp3player">';
        $replace .= " <param name=\"movie\" value=\"$CFG->wwwroot/filter/mediaplugin/mp3player.swf?src=\\2.mp3\" />";
        $replace .= ' <param name="quality" value="high" />';
        $replace .= ' <param name="bgcolor" value="#333333" />';
        $replace .= ' <param name="flashvars" value="'.$c.'" />';
        $replace .= " <embed src=\"$CFG->wwwroot/filter/mediaplugin/mp3player.swf?src=\\2.mp3\" ";
        $replace .= "  quality=\"high\" bgcolor=\"#333333\" width=\"90\" height=\"15\" name=\"mp3player\" ";
        $replace .= ' type="application/x-shockwave-flash" ';
        $replace .= ' flashvars="'.$c.'" ';
        $replace .= ' pluginspage="http://www.macromedia.com/go/getflashplayer">';
        $replace .= '</embed>';
        $replace .= '</object>&nbsp;';

        $text = preg_replace($search, $replace, $text);
    }

    if ($CFG->filter_mediaplugin_enable_swf) {
        $search = array(
                '/<a(.*?)href=\"([^<]+)\.swf\?d=([\d]{1,3}%?)x([\d]{1,3}%?)\"([^>]*)>(.*?)<\/a>/is',
                '/<a(.*?)href=\"([^<]+)\.swf\"([^>]*)>(.*?)<\/a>/is'
                );

        $replace = array();

        $replace[0]  = '\\0<p class="mediaplugin swf"><object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"';
        $replace[0] .= ' codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" ';
        $replace[0] .= ' width="\\3" height="\\4" id="mp3player">';
        $replace[0] .= " <param name=\"movie\" value=\"\\2.swf\" />";
        $replace[0] .= ' <param name="quality" value="high" />';
        $replace[0] .= ' <param name="AllowScriptAccess" value="never" />';
        $replace[0] .= " <embed src=\"\\2.swf\" ";
        $replace[0] .= '  quality="high" width="\\3" height="\\4" name="flashfilter" AllowScriptAccess="never" ';
        $replace[0] .= ' type="application/x-shockwave-flash" ';
        $replace[0] .= ' pluginspage="http://www.macromedia.com/go/getflashplayer">';
        $replace[0] .= '</embed>';
        $replace[0] .= '</object></p>';

        $replace[1]  = '\\0<p class="mediaplugin swf"><object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"';
        $replace[1] .= ' codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" ';
        $replace[1] .= ' width="400" height="300" id="mp3player">';
        $replace[1] .= " <param name=\"movie\" value=\"\\2.swf\" />";
        $replace[1] .= ' <param name="quality" value="high" />';
        $replace[1] .= ' <param name="AllowScriptAccess" value="never" />';
        $replace[1] .= " <embed src=\"\\2.swf\" ";
        $replace[1] .= '  quality="high" width="400" height="300" name="flashfilter" AllowScriptAccess="never" ';
        $replace[1] .= ' type="application/x-shockwave-flash" ';
        $replace[1] .= ' pluginspage="http://www.macromedia.com/go/getflashplayer">';
        $replace[1] .= '</embed>';
        $replace[1] .= '</object></p>';

        $text = preg_replace($search, $replace, $text);

    }

    if ($CFG->filter_mediaplugin_enable_flv) {
        $search = '/<a(.*?)href=\"([^<]+)\.flv\"([^>]*)>(.*?)<\/a>/is';

        $replace  = '\\0&nbsp;<object class="mediaplugin flv" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"';
        $replace .= ' codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" ';
        $replace .= ' width="480" height="360" id="flvplayer">';
        $replace .= " <param name=\"movie\" value=\"$CFG->wwwroot/filter/mediaplugin/flvplayer.swf?file=\\2.flv\" />";
        $replace .= ' <param name="quality" value="high" />';
        $replace .= ' <param name="bgcolor" value="#FFFFFF" />';
        $replace .= " <embed src=\"$CFG->wwwroot/filter/mediaplugin/flvplayer.swf?file=\\2.flv\" ";
        $replace .= "  quality=\"high\" bgcolor=\"#FFFFFF\" width=\"480\" height=\"360\" name=\"flvplayer\" ";
        $replace .= ' type="application/x-shockwave-flash" ';
        $replace .= ' pluginspage="http://www.macromedia.com/go/getflashplayer">';
        $replace .= '</embed>';
        $replace .= '</object>&nbsp;';

        $text = preg_replace($search, $replace, $text);
    }

    if ($CFG->filter_mediaplugin_enable_mov) {
        $search = '/<a(.*?)href=\"([^<]+)\.mov\"([^>]*)>(.*?)<\/a>/is';

        $replace  = '\\0<p class="mediaplugin mov"><object classid="CLSID:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B"';
        $replace .= '        codebase="http://www.apple.com/qtactivex/qtplugin.cab" ';
        $replace .= '        height="300" width="400"';
        $replace .= '        id="quicktime" type="application/x-oleobject">';
        $replace .= "<param name=\"src\" value=\"\\2.mov\" />";
        $replace .= '<param name="autoplay" value="false" />';
        $replace .= '<param name="loop" value="true" />';
        $replace .= '<param name="controller" value="true" />';
        $replace .= '<param name="scale" value="aspect" />';
        $replace .= "\n<embed src=\"\\2.mov\" name=\"quicktime\" type=\"video/quicktime\" ";
        $replace .= ' height="300" width="400" scale="aspect" ';
        $replace .= ' autoplay="false" controller="true" loop="true" ';
        $replace .= ' pluginspage="http://quicktime.apple.com/">';
        $replace .= '</embed>';
        $replace .= '</object></p>';

        $text = preg_replace($search, $replace, $text);
    }

    if ($CFG->filter_mediaplugin_enable_wmv) {
        $search = '/<a(.*?)href=\"([^<]+)\.wmv\"([^>]*)>(.*?)<\/a>/is';

        $replace  = '\\0<p class="mediaplugin wmv"><object classid="CLSID:22D6f312-B0F6-11D0-94AB-0080C74C7E95"';
        $replace .= ' codebase="http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=5,1,52,701" ';
        $replace .= ' standby="Loading Microsoft� Windows� Media Player components..." ';
        $replace .= ' id="msplayer" type="application/x-oleobject">';
        $replace .= "<param name=\"Filename\" value=\"\\2.wmv\" />";
        $replace .= '<param name="ShowControls" value="true" />';
        $replace .= '<param name="AutoRewind" value="true" />';
        $replace .= '<param name="AutoStart" value="false" />';
        $replace .= '<param name="Autosize" value="true" />';
        $replace .= '<param name="EnableContextMenu" value="true" />';
        $replace .= '<param name="TransparentAtStart" value="false" />';
        $replace .= '<param name="AnimationAtStart" value="false" />';
        $replace .= '<param name="ShowGotoBar" value="false" />';
        $replace .= '<param name="EnableFullScreenControls" value="true" />';
        $replace .= "\n<embed src=\"\\2.wmv\" name=\"msplayer\" type=\"video/x-ms\" ";
        $replace .= ' ShowControls="1" AutoRewind="1" AutoStart="0" Autosize="0" EnableContextMenu="1"';
        $replace .= ' TransparentAtStart="0" AnimationAtStart="0" ShowGotoBar="0" EnableFullScreenControls="1"';
        $replace .= ' pluginspage="http://www.microsoft.com/Windows/Downloads/Contents/Products/MediaPlayer/">';
        $replace .= '</embed>';
        $replace .= '</object></p>';

        $text = preg_replace($search, $replace, $text);
    }

    if ($CFG->filter_mediaplugin_enable_mpg) {
        $search = '/<a(.*?)href=\"([^<]+)\.(mpe?g)\"([^>]*)>(.*?)<\/a>/is';

        $replace = '\\0<p class="mediaplugin mpg"><object width="240" height="180">';
        $replace .= '<param name="src" value="\\2.\\3" />';
        $replace .= '<param name="controller" value="true" />';
        $replace .= '<param name="autoplay" value="false" />';
        $replace .= '<embed src="\\2.\\3" width="240" height="180" controller="true" autoplay="false"> </embed>';
        $replace .= '</object></p>';

        $text = preg_replace($search, $replace, $text);
    }

    if ($CFG->filter_mediaplugin_enable_avi) {
        $search = '/<a(.*?)href=\"([^<]+)\.avi\"([^>]*)>(.*?)<\/a>/is';

        $replace = '\\0<p class="mediaplugin avi"><object width="240" height="180">';
        $replace .= '<param name="src" value="\\2.avi" />';
        $replace .= '<param name="controller" value="true" />';
        $replace .= '<param name="autoplay" value="false" />';
        $replace .= '<embed src="\\2.avi" width="240" height="180" controller="true" autoplay="false"> </embed>';
        $replace .= '</object></p>';

        $text = preg_replace($search, $replace, $text);
    }

    if ($CFG->filter_mediaplugin_enable_ram) {
        $search = '/<a(.*?)href=\"([^<]+)\.ram\"([^>]*)>(.*?)<\/a>/is';

        $replace  = '\\0<p class="mediaplugin ram"><object width="240" height="180">';
        $replace .= '<param name="src" value="\\2.ram" />';
        $replace .= '<param name="autostart" value="true" />';
        $replace .= '<param name="controls" value="imagewindow" />';
        $replace .= '<param name="console" value="video" />';
        $replace .= '<param name="loop" value="true" />';
        $replace .= '<embed src="\\2.ram" width="240" height="180" loop="true" type="audio/x-pn-realaudio-plugin" controls="imagewindow" console="video" autostart="true">';
        $replace .= '</embed>';
        $replace .= '</object><br />';

        $replace .= '<object width="320" height="30">';
        $replace .= '<param name="src" value="\\2.ram" />';
        $replace .= '<param name="autostart" value="true" />';
        $replace .= '<param name="controls" value="ControlPanel" />';
        $replace .= '<param name="console" value="video" />';
        $replace .= '<embed src="\\2.ram" width="240" height="30" controls="ControlPanel" type="audio/x-pn-realaudio-plugin" console="video" autostart="true">';
        $replace .= '</embed>';
        $replace .= '</object></p>';

        $text = preg_replace($search, $replace, $text);
    }

    if ($CFG->filter_mediaplugin_enable_rpm) {
        $search = '/<a(.*?)href=\"([^<]+)\.rpm\"([^>]*)>(.*?)<\/a>/is';

        $replace  = '\\0<p class="mediaplugin rpm"><object width="240" height="180">';
        $replace .= '<param name="src" value="\\2.rpm" />';
        $replace .= '<param name="autostart" value="true" />';
        $replace .= '<param name="controls" value="imagewindow" />';
        $replace .= '<param name="console" value="video" />';
        $replace .= '<param name="loop" value="true" />';
        $replace .= '<embed src="\\2.rpm" width="240" height="180" loop="true" type="audio/x-pn-realaudio-plugin" controls="imagewindow" console="video" autostart="true">';
        $replace .= '</embed>';
        $replace .= '</object><br />';

        $replace .= '<object width="320" height="30">';
        $replace .= '<param name="src" value="\\2.rpm" />';
        $replace .= '<param name="autostart" value="true" />';
        $replace .= '<param name="controls" value="ControlPanel" />';
        $replace .= '<param name="console" value="video" />';
        $replace .= '<embed src="\\2.rpm" width="240" height="30" controls="ControlPanel" type="audio/x-pn-realaudio-plugin" console="video" autostart="true">';
        $replace .= '</embed>';
        $replace .= '</object></p>';

        $text = preg_replace($search, $replace, $text);
    }

    if ($CFG->filter_mediaplugin_enable_rm) {
        $search = '/<a(.*?)href=\"([^<]+)\.rm\"([^>]*)>(.*?)<\/a>/is';

        $replace  = '\\0<p class="mediaplugin rm"><object width="240" height="180">';
        $replace .= '<param name="src" value="\\2.rm" />';
        $replace .= '<param name="autostart" value="true" />';
        $replace .= '<param name="controls" value="imagewindow" />';
        $replace .= '<param name="console" value="video" />';
        $replace .= '<param name="loop" value="true" />';
        $replace .= '<embed src="\\2.rm" width="240" height="180" loop="true" type="audio/x-pn-realaudio-plugin" controls="imagewindow" console="video" autostart="true">';
        $replace .= '</embed>';
        $replace .= '</object><br />';

        $replace .= '<object width="320" height="30">';
        $replace .= '<param name="src" value="\\2.rm" />';
        $replace .= '<param name="autostart" value="true" />';
        $replace .= '<param name="controls" value="ControlPanel" />';
        $replace .= '<param name="console" value="video" />';
        $replace .= '<embed src="\\2.rm" width="240" height="30" controls="ControlPanel" type="audio/x-pn-realaudio-plugin" console="video" autostart="true">';
        $replace .= '</embed>';
        $replace .= '</object></p>';

        $text = preg_replace($search, $replace, $text);
    }

    return $text;
}
?>