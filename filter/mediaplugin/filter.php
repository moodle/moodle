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

/// These lines are important - the variable must match the name 
/// of the actual function below

    $textfilter_function = 'mediaplugin_filter';

    if (function_exists($textfilter_function)) {
        return;
    }


/// This is the filtering function itself.  It accepts the 
/// courseid and the text to be filtered (in HTML form).

function mediaplugin_filter($courseid, $text) {
    global $CFG;

    if (empty($CFG->filter_mediaplugin_ignore_mp3)) {
        $search = '/<a(.*?)href=\"([^<]+)\.mp3\"([^>]*)>(.*?)<\/a>/i';

        $replace  = '\\0&nbsp;<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"';
        $replace .= ' codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" ';
        $replace .= ' width="35" height="18" id="mp3player" align="">';
        $replace .= " <param name=movie value=\"$CFG->wwwroot/filter/mediaplugin/mp3player.swf?src=\\2.mp3\">";
        $replace .= ' <param name=quality value=high>';
        $replace .= ' <param name=bgcolor value="#333333">';
        $replace .= " <embed src=\"$CFG->wwwroot/filter/mediaplugin/mp3player.swf?src=\\2.mp3\" ";
        $replace .= "  quality=high bgcolor=\"#333333\" width=\"35\" height=\"18\" name=\"mp3player\" ";
        $replace .= ' type="application/x-shockwave-flash" ';
        $replace .= ' pluginspage="http://www.macromedia.com/go/getflashplayer">';
        $replace .= '</embed>';
        $replace .= '</object>&nbsp;';
    
        $text = preg_replace($search, $replace, $text);
    }

    if (empty($CFG->filter_mediaplugin_ignore_swf)) {
        $search = '/<a(.*?)href=\"([^<]+)\.swf\"([^>]*)>(.*?)<\/a>/i';

        $replace  = '\\0<p class="mediaplugin"><object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"';
        $replace .= ' codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" ';
        $replace .= ' width="400" height="300" id="mp3player" align="">';
        $replace .= " <param name=movie value=\"\\2.swf\">";
        $replace .= ' <param name=quality value=high>';
        $replace .= " <embed src=\"\\2.swf\" ";
        $replace .= "  quality=high width=\"400\" height=\"300\" name=\"flashfilter\" ";
        $replace .= ' type="application/x-shockwave-flash" ';
        $replace .= ' pluginspage="http://www.macromedia.com/go/getflashplayer">';
        $replace .= '</embed>';
        $replace .= '</object></p>';
    
        $text = preg_replace($search, $replace, $text);
    }

    if (empty($CFG->filter_mediaplugin_ignore_mov)) {
        $search = '/<a(.*?)href=\"([^<]+)\.mov\"([^>]*)>(.*?)<\/a>/i';

        $replace  = '\\0<p class="mediaplugin"><object classid="CLSID:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B"';
        $replace .= '        codebase="http://www.apple.com/qtactivex/qtplugin.cab" ';
        $replace .= '        height="300" width="400"';
        $replace .= '        id="quicktime" align="" type="application/x-oleobject">';
        $replace .= "<param name=\"src\" value=\"\\2.mov\" />";
        $replace .= '<param name="autoplay" value=false />';
        $replace .= '<param name="loop" value=true />';
        $replace .= '<param name="controller" value=true />';
        $replace .= '<param name="scale" value="aspect" />';
        $replace .= "\n<embed src=\"\\2.mov\" name=\"quicktime\" type=\"video/quicktime\" ";
        $replace .= ' height="300" width="400" scale="aspect" ';
        $replace .= ' autoplay="false" controller="true" loop="true" ';
        $replace .= ' pluginspage="http://quicktime.apple.com/">';
        $replace .= '</embed>';
        $replace .= '</object></p>';

        $text = preg_replace($search, $replace, $text);
    }

    if (empty($CFG->filter_mediaplugin_ignore_wmv)) {
        $search = '/<a(.*?)href=\"([^<]+)\.wmv\"([^>]*)>(.*?)<\/a>/i';

        $replace  = '\\0<p class="mediaplugin"><object classid="CLSID:22D6f312-B0F6-11D0-94AB-0080C74C7E95"';
        $replace .= ' codebase="http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=5,1,52,701" ';
        $replace .= ' standby="Loading Microsoft® Windows® Media Player components..." ';
        $replace .= ' id="msplayer" align="" type="application/x-oleobject">';
        $replace .= "<param name=\"Filename\" value=\"\\2.wmv\">";
        $replace .= '<param name="ShowControls" value=true />';
        $replace .= '<param name="AutoRewind" value=true />';
        $replace .= '<param name="AutoStart" value=false />';
        $replace .= '<param name="Autosize" value=true />';
        $replace .= '<param name="EnableContextMenu" value=true />';
        $replace .= '<param name="TransparentAtStart" value=false />';
        $replace .= '<param name="AnimationAtStart" value=false />';
        $replace .= '<param name="ShowGotoBar" value=false />';
        $replace .= '<param name="EnableFullScreenControls" value=true />';
        $replace .= "\n<embed src=\"\\2.wmv\" name=\"msplayer\" type=\"video/x-ms\" ";
        $replace .= ' ShowControls="1" AutoRewind="1" AutoStart="0" Autosize="0" EnableContextMenu="1"';
        $replace .= ' TransparentAtStart="0" AnimationAtStart="0" ShowGotoBar="0" EnableFullScreenControls="1"';
        $replace .= ' pluginspage="http://www.microsoft.com/Windows/Downloads/Contents/Products/MediaPlayer/">';
        $replace .= '</embed>';
        $replace .= '</object></p>';

        $text = preg_replace($search, $replace, $text);
    }

    return $text;
}


?>
