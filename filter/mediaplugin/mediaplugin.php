<?php // $id$
//////////////////////////////////////////////////////////////
//  Media plugin filtering
// 
//  This filter will replace any links to a media file with 
//  a media plugin that plays that media inline
//
//  To activate this filter, add his to your list of filters
//  in  Admin -> Configuration -> Variables
//
//       filter/mediaplugin/mediaplugin.php';
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

    $search = '/<a(.*)href=\"(.*)\.mp3\"(.*)>(.*)<\/a>/i';

    $replace = '\\0 <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"';
    $replace .= 'codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" ';
    $replace .= 'width="200" height="70" id="mp3player" align="">';
    $replace .= "<param name=movie value=\"$CFG->wwwroot/lib/mp3player/mp3player.swf?src=\\2.mp3&autostart=no\">";
    $replace .= '<param name=quality value=high>';
    $replace .= '<param name=bgcolor value="#333333">';
    $replace .= "<embed src=\"$CFG->wwwroot/lib/mp3player/mp3player.swf?src=\\2.mp3&autostart=no\" ";
    $replace .= " quality=high bgcolor=\"#333333\" width=\"400\" height=\"50\" name=\"mp3player\" ";
    $replace .= ' type="application/x-shockwave-flash" ';
    $replace .= ' pluginspage="http://www.macromedia.com/go/getflashplayer">';
    $replace .= '</embed>';
    $replace .= '</object>';

    return preg_replace($search, $replace, $text);
}


?>
