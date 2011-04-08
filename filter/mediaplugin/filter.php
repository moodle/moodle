<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 *  Media plugin filtering
 *
 *  This filter will replace any links to a media file with
 *  a media plugin that plays that media inline
 *
 * @package    filter
 * @subpackage mediaplugin
 * @copyright  2004 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/filelib.php');

if (!defined('FILTER_MEDIAPLUGIN_VIDEO_WIDTH')) {
    /**
     * Default media width, some plugins may use automatic sizes or accept resize parameters.
     * This can be defined in config.php.
     */
    define('FILTER_MEDIAPLUGIN_VIDEO_WIDTH', 400);
}

if (!defined('FILTER_MEDIAPLUGIN_VIDEO_HEIGHT')) {
    /**
     * Default video height, plugins that know aspect ration
     * should calculate it themselves using the FILTER_MEDIAPLUGIN_VIDEO_HEIGHT
     * This can be defined in config.php.
     */
    define('FILTER_MEDIAPLUGIN_VIDEO_HEIGHT', 300);
}


//TODO: we should use /u modifier in regex, unfortunately it may not work properly on some misconfigured servers, see lib/filter/urltolink/filter.php ...

//TODO: we should migrate to proper config_plugin settings ...


/**
 * Automatic media embedding filter class.
 *
 * It is highly recommended to configure servers to be compatible with our slasharguments,
 * otherwise the "?d=600x400" may not work.
 *
 * @package    filter
 * @subpackage mediaplugin
 * @copyright  2004 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filter_mediaplugin extends moodle_text_filter {

    function filter($text, array $options = array()) {
        global $CFG;

        if (!is_string($text) or empty($text)) {
            // non string data can not be filtered anyway
            return $text;
        }
        if (stripos($text, '</a>') === false) {
            // performance shortcut - all regexes bellow end with the </a> tag,
            // if not present nothing can match
            return $text;
        }

        $newtext = $text; // we need to return the original value if regex fails!

        // YouTube and Vimeo are great because the files are not served by Moodle server

        if (!empty($CFG->filter_mediaplugin_enable_youtube)) {
            $search = '/<a\s[^>]*href="(https?:\/\/www\.youtube(-nocookie)?\.com)\/watch\?v=([a-z0-9\-_]+)[^"#]*(#d=([\d]{1,4})x([\d]{1,4}))?"[^>]*>([^>]*)<\/a>/is';
            $newtext = preg_replace_callback($search, 'filter_mediaplugin_youtube_callback', $newtext);

            $search = '/<a\s[^>]*href="(https?:\/\/www\.youtube(-nocookie)?\.com)\/v\/([a-z0-9\-_]+)[^"#]*(#d=([\d]{1,4})x([\d]{1,4}))?[^>]*>([^>]*)<\/a>/is';
            $newtext = preg_replace_callback($search, 'filter_mediaplugin_youtube_callback', $newtext);

            $search = '/<a\s[^>]*href="(https?:\/\/www\.youtube(-nocookie)?\.com)\/view_play_list\?p=([a-z0-9\-_]+)[^"#]*(#d=([\d]{1,4})x([\d]{1,4}))?[^>]*>([^>]*)<\/a>/is';
            $newtext = preg_replace_callback($search, 'filter_mediaplugin_youtube_playlist_callback', $newtext);

            $search = '/<a\s[^>]*href="(https?:\/\/www\.youtube(-nocookie)?\.com)\/p\/([a-z0-9\-_]+)[^"#]*(#d=([\d]{1,4})x([\d]{1,4}))?[^>]*>([^>]*)<\/a>/is';
            $newtext = preg_replace_callback($search, 'filter_mediaplugin_youtube_playlist_callback', $newtext);
        }

        if (!empty($CFG->filter_mediaplugin_enable_vimeo)) {
            $search = '/<a\s[^>]*href="http:\/\/vimeo\.com\/([0-9]+)[^"#]*(#d=([\d]{1,4})x([\d]{1,4}))?[^>]*>([^>]*)<\/a>/is';
            $newtext = preg_replace_callback($search, 'filter_mediaplugin_vimeo_callback', $newtext);
        }


        // HTML 5 audio and video tags are the future! If only if vendors decided to use just one audio and video format...

        if (!empty($CFG->filter_mediaplugin_enable_html5audio)) {
            $search = '/<a\s[^>]*href="([^"#\?]+\.(ogg|oga|aac|m4a)([#\?][^"]*)?)"[^>]*>([^>]*)<\/a>/is';
            $newtext = preg_replace_callback($search, 'filter_mediaplugin_html5audio_callback', $newtext);
        }

        if (!empty($CFG->filter_mediaplugin_enable_html5video)) {
            $search = '/<a\s[^>]*href="([^"#\?]+\.(m4v|webm|ogv|mp4)([#\?][^"]*)?)"[^>]*>([^>]*)<\/a>/is';
            $newtext = preg_replace_callback($search, 'filter_mediaplugin_html5video_callback', $newtext);
        }


        // Flash stuff

        if (!empty($CFG->filter_mediaplugin_enable_mp3)) {
            $search = '/<a\s[^>]*href="([^"#\?]+\.mp3)"[^>]*>([^>]*)<\/a>/is';
            $newtext = preg_replace_callback($search, 'filter_mediaplugin_mp3_callback', $newtext);
        }

        if ((!empty($options['noclean']) or !empty($CFG->allowobjectembed)) and !empty($CFG->filter_mediaplugin_enable_swf)) {
            $search = '/<a\s[^>]*href="([^"#\?]+\.swf)([#\?]d=([\d]{1,4})x([\d]{1,4}))?"[^>]*>([^>]*)<\/a>/is';
            $newtext = preg_replace_callback($search, 'filter_mediaplugin_swf_callback', $newtext);
        }

        if (!empty($CFG->filter_mediaplugin_enable_flv)) {
            $search = '/<a\s[^>]*href="([^"#\?]+\.(flv|f4v)([#\?][^"]*)?)"[^>]*>([^>]*)<\/a>/is';
            $newtext = preg_replace_callback($search, 'filter_mediaplugin_flv_callback', $newtext);
        }


        // The rest of legacy formats - these should not be used if possible

        if (!empty($CFG->filter_mediaplugin_enable_wmp)) {
            $search = '/<a\s[^>]*href="([^"#\?]+\.(wmv|avi))(\?d=([\d]{1,4})x([\d]{1,4}))?"[^>]*>([^>]*)<\/a>/is';
            $newtext = preg_replace_callback($search, 'filter_mediaplugin_wmp_callback', $newtext);
        }

        if (!empty($CFG->filter_mediaplugin_enable_qt)) {
            // HTML5 filtering may steal mpeg 4 formats
            $search = '/<a\s[^>]*href="([^"#\?]+\.(mpg|mpeg|mov|mp4|m4v|m4a))(\?d=([\d]{1,4})x([\d]{1,4}))?"[^>]*>([^>]*)<\/a>/is';
            $newtext = preg_replace_callback($search, 'filter_mediaplugin_qt_callback', $newtext);
        }

        if (!empty($CFG->filter_mediaplugin_enable_rm)) {
            // hopefully nobody is using this any more!!
            // rpm is redhat packaging format these days, it is better to prevent these in default installs

            $search = '/<a\s[^>]*href="([^"#\?]+\.(ra|ram|rm|rv))"[^>]*>([^>]*)<\/a>/is';
            $newtext = preg_replace_callback($search, 'filter_mediaplugin_real_callback', $newtext);
        }


        if (empty($newtext) or $newtext === $text) {
            // error or not filtered
            unset($newtext);
            return $text;
        }


        return $newtext;
    }
}


///===========================
/// utility functions


/**
 * Parse list of alternative URLs
 * @param string $url urls separated with '#', size specified as ?d=640x480 or #d=640x480
 * @return array (urls, width, height)
 */
function filter_mediaplugin_parse_alternatives($url, $defaultwidth = 0, $defaultheight = 0) {
    $urls = explode('#', $url);
    $width  = $defaultwidth;
    $height = $defaultheight;
    $returnurls = array();

    foreach ($urls as $url) {
        $matches = null;

        if (preg_match('/^d=([\d]{1,4})x([\d]{1,4})$/i', $url, $matches)) { // #d=640x480
            $width  = $matches[1];
            $height = $matches[2];
            continue;
        }
        if (preg_match('/\?d=([\d]{1,4})x([\d]{1,4})$/i', $url, $matches)) { // old style file.ext?d=640x480
            $width  = $matches[1];
            $height = $matches[2];
            $url = str_replace($matches[0], '', $url);
        }

        $url = str_replace('&amp;', '&', $url);
        $url = clean_param($url, PARAM_URL);
        if (empty($url)) {
            continue;
        }

        $returnurls[] = $url;
    }

    return array($returnurls, $width, $height);
}

/**
 * Should the current tag be ignored in this filter?
 * @param string $tag
 * @return bool
 */
function filter_mediaplugin_ignore($tag) {
    if (preg_match('/class="[^"]*nomediaplugin/i', $tag)) {
        return true;
    } else {
        false;
    }
}

///===========================
/// callback filter functions


/**
 * Replace audio links with audio tag.
 *
 * @param array $link
 * @return string
 */
function filter_mediaplugin_html5audio_callback(array $link) {
    global $CFG;

    if (filter_mediaplugin_ignore($link[0])) {
        return $link[0];
    }

    $info = trim($link[4]);
    if (empty($info) or strpos($info, 'http') === 0) {
        $info = get_string('fallbackaudio', 'filter_mediaplugin');
    }

    list($urls, $ignorewidth, $ignoredheight) = filter_mediaplugin_parse_alternatives($link[1]);

    $fallbackurl  = null;
    $fallbackmime = null;
    $sources      = array();
    $fallbacklink = null;

    foreach ($urls as $url) {
        $mimetype = mimeinfo('type', $url);
        if (strpos($mimetype, 'audio/') !== 0) {
            continue;
        }
        $sources[] = html_writer::tag('source', '', array('src' => $url, 'type' => $mimetype));

        if ($fallbacklink === null) {
            $fallbacklink = html_writer::link($url.'#', $info); // the extra '#' prevents linking in mp3 filter bellow
        }
        if ($fallbackurl === null) {
            if ($mimetype === 'audio/mp3' or $mimetype === 'audio/aac') {
                $fallbackurl  = str_replace('&', '&amp;', $url);
                $fallbackmime = $mimetype;
            }
        }
    }
    if (!$sources) {
        return $link[0];
    }

    if ($fallbackmime !== null) {
        // fallback to quicktime
        $fallback = <<<OET
<object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" codebase="http://www.apple.com/qtactivex/qtplugin.cab" width="200" height="20">
 <param name="pluginspage" value="http://www.apple.com/quicktime/download/" />
 <param name="src" value="$fallbackurl" />
 <param name="controller" value="true" />
 <param name="loop" value="false" />
 <param name="autoplay" value="false" />
 <param name="autostart" value="false" />
 <param name="scale" value="aspect" />
 $fallbacklink
<!--[if !IE]>-->
  <object data="$fallbackurl" type="$fallbackmime" width="200" height="20">
   <param name="src" value="$fallbackurl" />
   <param name="pluginurl" value="http://www.apple.com/quicktime/download/" />
   <param name="controller" value="true" />
   <param name="loop" value="false" />
   <param name="autoplay" value="false" />
   <param name="autostart" value="false" />
   <param name="scale" value="aspect" />
    $fallbacklink
  </object>
<!--<![endif]-->
</object>
OET;
    } else {
        $fallback = $fallbacklink;
    }

    $sources = implode("\n", $sources);
    $title = s($info);

    // audio players are supposed to be inline elements
    $output = <<<OET
<audio controls="true" width="200" class="mediaplugin mediaplugin_html5audio" preload="no" title="$title">
$sources
$fallback
</audio>
OET;

    return $output;
}

/**
 * Replace ogg video links with video tag.
 *
 * Please note this is not going to work in all browsers,
 * it is also not xhtml strict.
 *
 * @param array $link
 * @return string
 */
function filter_mediaplugin_html5video_callback(array $link) {

    if (filter_mediaplugin_ignore($link[0])) {
        return $link[0];
    }

    $info = trim($link[4]);
    if (empty($info) or strpos($info, 'http') === 0) {
        $info = get_string('fallbackvideo', 'filter_mediaplugin');
    }

    list($urls, $width, $height) = filter_mediaplugin_parse_alternatives($link[1], FILTER_MEDIAPLUGIN_VIDEO_WIDTH, 0);

    $fallbackurl  = null;
    $fallbackmime = null;
    $sources      = array();
    $fallbacklink = null;

    foreach ($urls as $url) {
        $mimetype = mimeinfo('type', $url);
        if (strpos($mimetype, 'video/') !== 0) {
            continue;
        }
        $source = html_writer::tag('source', '', array('src' => $url, 'type' => $mimetype));
        if ($mimetype === 'video/mp4') {
            // better add m4v as first source, it might be a bit more compatible with problematic browsers
            array_unshift($sources, $source);
        } else {
            $sources[] = $source;
        }

        if ($fallbacklink === null) {
            $fallbacklink = html_writer::link($url.'#', $info); // the extra '#' prevents linking in mp3 filter bellow
        }
        if ($fallbackurl === null) {
            if ($mimetype === 'video/mp4') {
                $fallbackurl  = str_replace('&', '&amp;', $url);
                $fallbackmime = $mimetype;
            }
        }
    }
    if (!$sources) {
        return $link[0];
    }

    if ($fallbackmime !== null) {
        $qtheight = ($height == 0) ? FILTER_MEDIAPLUGIN_VIDEO_HEIGHT : ($height + 15);
        // fallback to quicktime
        $fallback = <<<OET
<object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" codebase="http://www.apple.com/qtactivex/qtplugin.cab" width="$width" height="$qtheight">
 <param name="pluginspage" value="http://www.apple.com/quicktime/download/" />
 <param name="src" value="$fallbackurl" />
 <param name="controller" value="true" />
 <param name="loop" value="false" />
 <param name="autoplay" value="false" />
 <param name="autostart" value="false" />
 <param name="scale" value="aspect" />
 $fallbacklink
<!--[if !IE]>-->
  <object data="$fallbackurl" type="$fallbackmime" width="$width" height="$qtheight">
   <param name="src" value="$fallbackurl" />
   <param name="pluginurl" value="http://www.apple.com/quicktime/download/" />
   <param name="controller" value="true" />
   <param name="loop" value="false" />
   <param name="autoplay" value="false" />
   <param name="autostart" value="false" />
   <param name="scale" value="aspect" />
    $fallbacklink
  </object>
<!--<![endif]-->
</object>
OET;
    } else {
        $fallback = $fallbacklink;
    }

    $sources = implode("\n", $sources);
    $title = s($info);

    if (empty($height)) {
        // automatic height
        $size = "width=\"$width\"";
    } else {
        $size = "width=\"$width\" height=\"$height\"";
    }

    $output = <<<OET
<span class="mediaplugin mediaplugin_html5video">
<video controls="true" $size preload="metadata" title="$title">
$sources
$fallback
</video>
</span>
OET;

    return $output;
}

/**
 * Replace mp3 links with small audio player.
 *
 * @param  $link
 * @return string
 */
function filter_mediaplugin_mp3_callback($link) {
    static $count = 0;

    if (filter_mediaplugin_ignore($link[0])) {
        return $link[0];
    }

    $count++;
    $id = 'filter_mp3_'.time().'_'.$count; //we need something unique because it might be stored in text cache

    $url = $link[1];
    $rawurl = str_replace('&amp;', '&', $url);

    $info = trim($link[2]);
    if (empty($info) or strpos($info, 'http') === 0) {
        $info = get_string('mp3audio', 'filter_mediaplugin');

    }
    $printlink = html_writer::link($rawurl, $info, array('class'=>'mediafallbacklink'));

    //note: when flash or javascript not available only the $printlink is displayed,
    //      audio players are supposed to be inline elements

    $output = html_writer::tag('span', $printlink, array('id'=>$id, 'class'=>'mediaplugin mediaplugin_mp3'));
    $output .= html_writer::script(js_writer::function_call('M.util.add_audio_player', array($id, $rawurl, true))); // we can not use standard JS init because this may be cached

    return $output;
}

/**
 * Replace swf links with embedded flash objects.
 *
 * Please note this is not a secure and is recommended to be disabled on production systems.
 *
 * @deprecated
 * @param  $link
 * @return string
 */
function filter_mediaplugin_swf_callback($link) {

    if (filter_mediaplugin_ignore($link[0])) {
        return $link[0];
    }

    $width  = empty($link[3]) ? FILTER_MEDIAPLUGIN_VIDEO_WIDTH  : $link[3];
    $height = empty($link[4]) ? FILTER_MEDIAPLUGIN_VIDEO_HEIGHT : $link[4];

    $url = $link[1];
    $rawurl = str_replace('&amp;', '&', $url);

    $info = trim($link[5]);
    if (empty($info) or strpos($info, 'http') === 0) {
        $info = get_string('flashanimation', 'filter_mediaplugin');

    }
    $printlink = html_writer::link($rawurl, $info, array('class'=>'mediafallbacklink'));

    $output = <<<OET
<span class="mediaplugin mediaplugin_swf">
  <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="$width" height="$height">
    <param name="movie" value="$url" />
    <param name="autoplay" value="true" />
    <param name="loop" value="true" />
    <param name="controller" value="true" />
    <param name="scale" value="aspect" />
    <param name="base" value="." />
    <param name="allowscriptaccess" value="never" />
<!--[if !IE]>-->
    <object type="application/x-shockwave-flash" data="$url" width="$width" height="$height">
      <param name="controller" value="true" />
      <param name="autoplay" value="true" />
      <param name="loop" value="true" />
      <param name="scale" value="aspect" />
      <param name="base" value="." />
      <param name="allowscriptaccess" value="never" />
<!--<![endif]-->
$printlink
<!--[if !IE]>-->
    </object>
<!--<![endif]-->
  </object>
</span>
OET;

    return $output;

}

/**
 * Replace flv links with flow player.
 *
 * @param  $link
 * @return string
 */
function filter_mediaplugin_flv_callback($link) {
    static $count = 0;

    if (filter_mediaplugin_ignore($link[0])) {
        return $link[0];
    }

    $count++;
    $id = 'filter_flv_'.time().'_'.$count; //we need something unique because it might be stored in text cache

    list($urls, $width, $height) = filter_mediaplugin_parse_alternatives($link[1], 0, 0);

    $autosize = false;
    if (!$width and !$height) {
        $width    = FILTER_MEDIAPLUGIN_VIDEO_WIDTH;
        $height   = FILTER_MEDIAPLUGIN_VIDEO_HEIGHT;
        $autosize = true;
    }

    $flashurl = null;
    $sources  = array();

    foreach ($urls as $url) {
        $mimetype = mimeinfo('type', $url);
        if (strpos($mimetype, 'video/') !== 0) {
            continue;
        }
        $source = html_writer::tag('source', '', array('src' => $url, 'type' => $mimetype));
        if ($mimetype === 'video/mp4') {
            // better add m4v as first source, it might be a bit more compatible with problematic browsers
            array_unshift($sources, $source);
        } else {
            $sources[] = $source;
        }

        if ($flashurl === null) {
            $flashurl  = str_replace('&', '&amp;', $url);
        }
    }
    if (!$sources) {
        return $link[0];
    }

    $info = trim($link[4]);
    if (empty($info) or strpos($info, 'http') === 0) {
        $info = get_string('fallbackvideo', 'filter_mediaplugin');
    }
    $printlink = html_writer::link($flashurl.'#', $info, array('class'=>'mediafallbacklink')); // the '#' prevents the QT filter

    $title = s($info);

    if (count($sources) > 1) {
        $sources = implode("\n", $sources);

        // html 5 fallback
        $printlink = <<<OET
<video controls="true" width="$width" height="$height" preload="metadata" title="$title">
$sources
$printlink
</video>
<noscript><br />
$printlink
</noscript>
OET;
    }

    // note: no need to print "this is flv link" because it is printed automatically if JS or Flash not available

    $output = html_writer::tag('span', $printlink, array('id'=>$id, 'class'=>'mediaplugin mediaplugin_flv'));
    $output .= html_writer::script(js_writer::function_call('M.util.add_video_player', array($id, $flashurl, $width, $height, $autosize))); // we can not use standard JS init because this may be cached

    return $output;
}

/**
 * Replace real media links with real player.
 *
 * Note: hopefully nobody is using this obsolete format any more.
 *
 * @deprectated
 * @param  $link
 * @return string
 */
function filter_mediaplugin_real_callback($link) {

    if (filter_mediaplugin_ignore($link[0])) {
        return $link[0];
    }

    $url      = $link[1];
    $rawurl   = str_replace('&amp;', '&', $url);

    //Note: the size is hardcoded intentionally because this does not work anyway!

    $width  = FILTER_MEDIAPLUGIN_VIDEO_WIDTH;
    $height = FILTER_MEDIAPLUGIN_VIDEO_HEIGHT;

    $info = trim($link[3]);
    if (empty($info) or strpos($info, 'http') === 0) {
        $info = get_string('fallbackvideo', 'filter_mediaplugin');
    }
    $printlink = html_writer::link($rawurl, $info, array('class'=>'mediafallbacklink'));

    return <<<OET
<span class="mediaplugin mediaplugin_real">
  $printlink <br />
  <object title="$info" classid="clsid:CFCDAA03-8BE4-11cf-B84B-0020AFBBCCFA" data="$url" width="$width" height="$height"">
    <param name="src" value="$url" />
    <param name="controls" value="All" />
<!--[if !IE]>-->
    <object title="$info" type="audio/x-pn-realaudio-plugin" data="$url" width="$width" height="$height">
     <param name="src" value="$url" />
      <param name="controls" value="All" />
<!--<![endif]-->
<!--[if !IE]>-->
    </object>
<!--<![endif]-->
  </object>
</span>
OET;
}

/**
 * Change links to YouTube into embedded YouTube videos
 *
 * Note: resizing via url is not supported, user can click the fullscreen button instead
 *
 * @param  $link
 * @return string
 */
function filter_mediaplugin_youtube_callback($link) {
    global $CFG;

    if (filter_mediaplugin_ignore($link[0])) {
        return $link[0];
    }

    $site    = $link[1];
    $videoid = $link[3];

    $info = trim($link[7]);
    if (empty($info) or strpos($info, 'http') === 0) {
        $info = get_string('siteyoutube', 'filter_mediaplugin');
    }
    $info = s($info);

    $width  = empty($link[5]) ? FILTER_MEDIAPLUGIN_VIDEO_WIDTH  : $link[5];
    $height = empty($link[6]) ? FILTER_MEDIAPLUGIN_VIDEO_HEIGHT : $link[6];

    if (false and empty($CFG->xmlstrictheaders)) {
        // TODO: remove this once iframe playback starts to work properly in iPads
        return <<<OET
<iframe title="$info" width="$width" height="$height" src="$site/embed/$videoid?rel=0" frameborder="0" allowfullscreen></iframe>
OET;
    }

    //NOTE: we can not use any link fallback because it breaks built-in player on iOS devices

    $output = <<<OET
<span class="mediaplugin mediaplugin_youtube">
<object title="$info" type="application/x-shockwave-flash" data="$site/v/$videoid&amp;fs=1&amp;rel=0" width="$width" height="$height">
 <param name="movie" value="$site/v/$videoid&amp;fs=1&amp;rel=0" />
 <param name="FlashVars" value="playerMode=embedded" />
 <param name="allowFullScreen" value="true" />
</object>
</span>
OET;

    return $output;
}

/**
 * Change YouTube playlist into embedded YouTube playlist videos
 *
 * Note: resizing via url is not supported, user can click the fullscreen button instead
 *
 * @param  $link
 * @return string
 */
function filter_mediaplugin_youtube_playlist_callback($link) {
    global $CFG;

    if (filter_mediaplugin_ignore($link[0])) {
        return $link[0];
    }

    $site     = $link[1];
    $playlist = $link[3];

    $info = trim($link[7]);
    if (empty($info) or strpos($info, 'http') === 0) {
        $info = get_string('siteyoutube', 'filter_mediaplugin');
    }
    $printlink = html_writer::link("$site/view_play_list\?p=$playlist", $info, array('class'=>'mediafallbacklink'));
    $info = s($info);

    $width  = empty($link[5]) ? FILTER_MEDIAPLUGIN_VIDEO_WIDTH  : $link[5];
    $height = empty($link[6]) ? FILTER_MEDIAPLUGIN_VIDEO_HEIGHT : $link[6];

    // TODO: iframe HTML 5 video not implemented and object does work on iOS devices

    $output = <<<OET
<span class="mediaplugin mediaplugin_youtube">
<object title="$info" type="application/x-shockwave-flash" data="$site/p/$playlist&amp;fs=1&amp;rel=0" width="$width" height="$height">
 <param name="movie" value="$site/v/$playlist&amp;fs=1&amp;rel=0" />
 <param name="FlashVars" value="playerMode=embedded" />
 <param name="allowFullScreen" value="true" />
$printlink</object>
</span>
OET;

    return $output;
}

/**
 * Change links to Vimeo into embedded Vimeo videos
 *
 * @param  $link
 * @return string
 */
function filter_mediaplugin_vimeo_callback($link) {
    global $CFG;

    if (filter_mediaplugin_ignore($link[0])) {
        return $link[0];
    }

    $videoid = $link[1];
    $info    = s(strip_tags($link[5]));

    //Note: resizing via url is not supported, user can click the fullscreen button instead
    //      iframe embedding is not xhtml strict but it is the only option that seems to work on most devices

    $width  = empty($link[3]) ? FILTER_MEDIAPLUGIN_VIDEO_WIDTH  : $link[3];
    $height = empty($link[4]) ? FILTER_MEDIAPLUGIN_VIDEO_HEIGHT : $link[4];

    $output = <<<OET
<span class="mediaplugin mediaplugin_vimeo">
<iframe title="$info" src="http://player.vimeo.com/video/$videoid" width="$width" height="$height" frameborder="0"></iframe>
</span>
OET;

    return $output;
}

/**
 * Embed video using window media player if available
 *
 * This does not work much outside of IE, hopefully not many ppl use it these days.
 *
 * @param  $link
 * @return string
 */
function filter_mediaplugin_wmp_callback($link) {

    if (filter_mediaplugin_ignore($link[0])) {
        return $link[0];
    }

    $url    = $link[1];
    $rawurl = str_replace('&amp;', '&', $url);

    $info = trim($link[6]);
    if (empty($info) or strpos($info, 'http') === 0) {
        $info = get_string('fallbackvideo', 'filter_mediaplugin');
    }
    $printlink = html_writer::link($rawurl, $info, array('class'=>'mediafallbacklink'));

    if (empty($link[4]) or empty($link[5])) {
        $mpsize = '';
        $size = 'width="'.FILTER_MEDIAPLUGIN_VIDEO_WIDTH.'" height="'.(FILTER_MEDIAPLUGIN_VIDEO_HEIGHT+64).'"';
        $autosize = 'true';
    } else {
        $size = 'width="'.$link[4].'" height="'.($link[5] + 15).'"';
        $mpsize = 'width="'.$link[4].'" height="'.($link[5] + 64).'"';
        $autosize = 'false';
    }
    $mimetype = mimeinfo('type', $url);



    return <<<OET
<span class="mediaplugin mediaplugin_wmp">
$printlink <br />
<object classid="CLSID:6BF52A52-394A-11d3-B153-00C04F79FAA6" $mpsize standby="Loading Microsoft(R) Windows(R) Media Player components..." type="application/x-oleobject">
 <param name="Filename" value="$url" />
 <param name="src" value="$url" />
 <param name="url" value="$url" />
 <param name="ShowControls" value="true" />
 <param name="AutoRewind" value="true" />
 <param name="AutoStart" value="false" />
 <param name="Autosize" value="$autosize" />
 <param name="EnableContextMenu" value="true" />
 <param name="TransparentAtStart" value="false" />
 <param name="AnimationAtStart" value="false" />
 <param name="ShowGotoBar" value="false" />
 <param name="EnableFullScreenControls" value="true" />
 <param name="uimode" value="full" />
<!--[if !IE]>-->
  <object data="$url" type="$mimetype" $size>
   <param name="src" value="$url" />
   <param name="controller" value="true" />
   <param name="autoplay" value="false" />
   <param name="autostart" value="false" />
   <param name="resize" value="scale" />
  </object>
<!--<![endif]-->
</object></span>
OET;
}

/**
 * Replace quicktime links with quicktime player.
 *
 * You need to install a quicktime player, it is not available for all browsers+OS combinations.
 *
 * @param  $link
 * @return string
 */
function filter_mediaplugin_qt_callback($link) {

    if (filter_mediaplugin_ignore($link[0])) {
        return $link[0];
    }

    $url    = $link[1];
    $rawurl = str_replace('&amp;', '&', $url);

    $info = trim($link[6]);
    if (empty($info) or strpos($info, 'http') === 0) {
        $info = get_string('fallbackvideo', 'filter_mediaplugin');
    }
    $printlink = html_writer::link($rawurl, $info, array('class'=>'mediafallbacklink'));

    if (empty($link[4]) or empty($link[5])) {
        $size = 'width="'.FILTER_MEDIAPLUGIN_VIDEO_WIDTH.'" height="'.(FILTER_MEDIAPLUGIN_VIDEO_HEIGHT+15).'"';
    } else {
        $size = 'width="'.$link[4].'" height="'.($link[5]+15).'"';
    }
    $mimetype = mimeinfo('type', $url);

    // this is the safest fallback for incomplete or missing browser support for this format
    return <<<OET
<span class="mediaplugin mediaplugin_qt">
$printlink <br />
<object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" codebase="http://www.apple.com/qtactivex/qtplugin.cab" $size>
 <param name="pluginspage" value="http://www.apple.com/quicktime/download/" />
 <param name="src" value="$url" />
 <param name="controller" value="true" />
 <param name="loop" value="true" />
 <param name="autoplay" value="false" />
 <param name="autostart" value="false" />
 <param name="scale" value="aspect" />
<!--[if !IE]>-->
  <object data="$url" type="$mimetype" $size>
   <param name="src" value="$url" />
   <param name="pluginurl" value="http://www.apple.com/quicktime/download/" />
   <param name="controller" value="true" />
   <param name="loop" value="true" />
   <param name="autoplay" value="false" />
   <param name="autostart" value="false" />
   <param name="scale" value="aspect" />
  </object>
<!--<![endif]-->
</object></span>
OET;
}

