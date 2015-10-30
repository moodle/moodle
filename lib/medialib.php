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
 * Classes for handling embedded media (mainly audio and video).
 *
 * These are used only from within the core media renderer.
 *
 * To embed media from Moodle code, do something like the following:
 *
 * $mediarenderer = $PAGE->get_renderer('core', 'media');
 * echo $mediarenderer->embed_url(new moodle_url('http://example.org/a.mp3'));
 *
 * You do not need to require this library file manually. Getting the renderer
 * (the first line above) requires this library file automatically.
 *
 * @package core_media
 * @copyright 2012 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if (!defined('CORE_MEDIA_VIDEO_WIDTH')) {
    // Default video width if no width is specified; some players may do something
    // more intelligent such as use real video width.
    // May be defined in config.php if required.
    define('CORE_MEDIA_VIDEO_WIDTH', 400);
}
if (!defined('CORE_MEDIA_VIDEO_HEIGHT')) {
    // Default video height. May be defined in config.php if required.
    define('CORE_MEDIA_VIDEO_HEIGHT', 300);
}
if (!defined('CORE_MEDIA_AUDIO_WIDTH')) {
    // Default audio width if no width is specified.
    // May be defined in config.php if required.
    define('CORE_MEDIA_AUDIO_WIDTH', 300);
}


/**
 * Constants and static utility functions for use with core_media_renderer.
 *
 * @copyright 2011 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class core_media {
    /**
     * Option: Disable text link fallback.
     *
     * Use this option if you are going to print a visible link anyway so it is
     * pointless to have one as fallback.
     *
     * To enable, set value to true.
     */
    const OPTION_NO_LINK = 'nolink';

    /**
     * Option: When embedding, if there is no matching embed, do not use the
     * default link fallback player; instead return blank.
     *
     * This is different from OPTION_NO_LINK because this option still uses the
     * fallback link if there is some kind of embedding. Use this option if you
     * are going to check if the return value is blank and handle it specially.
     *
     * To enable, set value to true.
     */
    const OPTION_FALLBACK_TO_BLANK = 'embedorblank';

    /**
     * Option: Enable players which are only suitable for use when we trust the
     * user who embedded the content.
     *
     * At present, this option enables the SWF player.
     *
     * To enable, set value to true.
     */
    const OPTION_TRUSTED = 'trusted';

    /**
     * Option: Put a div around the output (if not blank) so that it displays
     * as a block using the 'resourcecontent' CSS class.
     *
     * To enable, set value to true.
     */
    const OPTION_BLOCK = 'block';

    /**
     * Given a string containing multiple URLs separated by #, this will split
     * it into an array of moodle_url objects suitable for using when calling
     * embed_alternatives.
     *
     * Note that the input string should NOT be html-escaped (i.e. if it comes
     * from html, call html_entity_decode first).
     *
     * @param string $combinedurl String of 1 or more alternatives separated by #
     * @param int $width Output variable: width (will be set to 0 if not specified)
     * @param int $height Output variable: height (0 if not specified)
     * @return array Array of 1 or more moodle_url objects
     */
    public static function split_alternatives($combinedurl, &$width, &$height) {
        $urls = explode('#', $combinedurl);
        $width = 0;
        $height = 0;
        $returnurls = array();

        foreach ($urls as $url) {
            $matches = null;

            // You can specify the size as a separate part of the array like
            // #d=640x480 without actually including a url in it.
            if (preg_match('/^d=([\d]{1,4})x([\d]{1,4})$/i', $url, $matches)) {
                $width  = $matches[1];
                $height = $matches[2];
                continue;
            }

            // Can also include the ?d= as part of one of the URLs (if you use
            // more than one they will be ignored except the last).
            if (preg_match('/\?d=([\d]{1,4})x([\d]{1,4})$/i', $url, $matches)) {
                $width  = $matches[1];
                $height = $matches[2];

                // Trim from URL.
                $url = str_replace($matches[0], '', $url);
            }

            // Clean up url.
            $url = clean_param($url, PARAM_URL);
            if (empty($url)) {
                continue;
            }

            // Turn it into moodle_url object.
            $returnurls[] = new moodle_url($url);
        }

        return $returnurls;
    }

    /**
     * Returns the file extension for a URL.
     * @param moodle_url $url URL
     */
    public static function get_extension(moodle_url $url) {
        // Note: Does not use core_text (. is UTF8-safe).
        $filename = self::get_filename($url);
        $dot = strrpos($filename, '.');
        if ($dot === false) {
            return '';
        } else {
            return strtolower(substr($filename, $dot + 1));
        }
    }

    /**
     * Obtains the filename from the moodle_url.
     * @param moodle_url $url URL
     * @return string Filename only (not escaped)
     */
    public static function get_filename(moodle_url $url) {
        global $CFG;

        // Use the 'file' parameter if provided (for links created when
        // slasharguments was off). If not present, just use URL path.
        $path = $url->get_param('file');
        if (!$path) {
            $path = $url->get_path();
        }

        // Remove everything before last / if present. Does not use textlib as / is UTF8-safe.
        $slash = strrpos($path, '/');
        if ($slash !== false) {
            $path = substr($path, $slash + 1);
        }
        return $path;
    }

    /**
     * Guesses MIME type for a moodle_url based on file extension.
     * @param moodle_url $url URL
     * @return string MIME type
     */
    public static function get_mimetype(moodle_url $url) {
        return mimeinfo('type', self::get_filename($url));
    }
}


/**
 * Base class for media players.
 *
 * Media players return embed HTML for a particular way of playing back audio
 * or video (or another file type).
 *
 * In order to make the code more lightweight, this is not a plugin type
 * (players cannot have their own settings, database tables, capabilities, etc).
 * These classes are used only by core_media_renderer in outputrenderers.php.
 * If you add a new class here (in core code) you must modify the
 * get_players_raw function in that file to include it.
 *
 * If a Moodle installation wishes to add extra player objects they can do so
 * by overriding that renderer in theme, and overriding the get_players_raw
 * function. The new player class should then of course be defined within the
 * custom theme or other suitable location, not in this file.
 *
 * @copyright 2011 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class core_media_player {
    /**
     * Placeholder text used to indicate where the fallback content is placed
     * within a result.
     */
    const PLACEHOLDER = '<!--FALLBACK-->';

    /**
     * Generates code required to embed the player.
     *
     * The returned code contains a placeholder comment '<!--FALLBACK-->'
     * (constant core_media_player::PLACEHOLDER) which indicates the location
     * where fallback content should be placed in the event that this type of
     * player is not supported by user browser.
     *
     * The $urls parameter includes one or more alternative media formats that
     * are supported by this player. It does not include formats that aren't
     * supported (see list_supported_urls).
     *
     * The $options array contains key-value pairs. See OPTION_xx constants
     * for documentation of standard option(s).
     *
     * @param array $urls URLs of media files
     * @param string $name Display name; '' to use default
     * @param int $width Optional width; 0 to use default
     * @param int $height Optional height; 0 to use default
     * @param array $options Options array
     * @return string HTML code for embed
     */
    public abstract function embed($urls, $name, $width, $height, $options);

    /**
     * Gets the list of file extensions supported by this media player.
     *
     * Note: This is only required for the default implementation of
     * list_supported_urls. If you override that function to determine
     * supported URLs in some way other than by extension, then this function
     * is not necessary.
     *
     * @return array Array of strings (extension not including dot e.g. 'mp3')
     */
    public function get_supported_extensions() {
        return array();
    }

    /**
     * Lists keywords that must be included in a url that can be embedded with
     * this player. Any such keywords should be added to the array.
     *
     * For example if this player supports FLV and F4V files then it should add
     * '.flv' and '.f4v' to the array. (The check is not case-sensitive.)
     *
     * Default handling calls the get_supported_extensions function and adds
     * a dot to each of those values, so players only need to override this
     * if they don't implement get_supported_extensions.
     *
     * This is used to improve performance when matching links in the media filter.
     *
     * @return array Array of keywords to add to the embeddable markers list
     */
    public function get_embeddable_markers() {
        $markers = array();
        foreach ($this->get_supported_extensions() as $extension) {
            $markers[] = '.' . $extension;
        }
        return $markers;
    }

    /**
     * Gets the ranking of this player. This is an integer used to decide which
     * player to use (after applying other considerations such as which ones
     * the user has disabled).
     *
     * Rank must be unique (no two players should have the same rank).
     *
     * Rank zero has a special meaning, indicating that this 'player' does not
     * really embed the video.
     *
     * Rank is not a user-configurable value because it needs to be defined
     * carefully in order to ensure that the embedding fallbacks actually work.
     * It might be possible to have some user options which affect rank, but
     * these would be best defined as e.g. checkboxes in settings that have
     * a particular effect on the rank of a couple of plugins, rather than
     * letting users generally alter rank.
     *
     * Note: Within medialib.php, players are listed in rank order (highest
     * rank first).
     *
     * @return int Rank (higher is better)
     */
    public abstract function get_rank();

    /**
     * @return bool True if player is enabled
     */
    public function is_enabled() {
        global $CFG;

        // With the class core_media_player_html5video it is enabled
        // based on $CFG->core_media_enable_html5video.
        $setting = str_replace('_player_', '_enable_', get_class($this));
        return !empty($CFG->{$setting});
    }

    /**
     * Given a list of URLs, returns a reduced array containing only those URLs
     * which are supported by this player. (Empty if none.)
     * @param array $urls Array of moodle_url
     * @param array $options Options (same as will be passed to embed)
     * @return array Array of supported moodle_url
     */
    public function list_supported_urls(array $urls, array $options = array()) {
        $extensions = $this->get_supported_extensions();
        $result = array();
        foreach ($urls as $url) {
            if (in_array(core_media::get_extension($url), $extensions)) {
                $result[] = $url;
            }
        }
        return $result;
    }

    /**
     * Obtains suitable name for media. Uses specified name if there is one,
     * otherwise makes one up.
     * @param string $name User-specified name ('' if none)
     * @param array $urls Array of moodle_url used to make up name
     * @return string Name
     */
    protected function get_name($name, $urls) {
        // If there is a specified name, use that.
        if ($name) {
            return $name;
        }

        // Get filename of first URL.
        $url = reset($urls);
        $name = core_media::get_filename($url);

        // If there is more than one url, strip the extension as we could be
        // referring to a different one or several at once.
        if (count($urls) > 1) {
            $name = preg_replace('~\.[^.]*$~', '', $name);
        }

        return $name;
    }

    /**
     * Compares by rank order, highest first. Used for sort functions.
     * @param core_media_player $a Player A
     * @param core_media_player $b Player B
     * @return int Negative if A should go before B, positive for vice versa
     */
    public static function compare_by_rank(core_media_player $a, core_media_player $b) {
        return $b->get_rank() - $a->get_rank();
    }

    /**
     * Utility function that sets width and height to defaults if not specified
     * as a parameter to the function (will be specified either if, (a) the calling
     * code passed it, or (b) the URL included it).
     * @param int $width Width passed to function (updated with final value)
     * @param int $height Height passed to function (updated with final value)
     */
    protected static function pick_video_size(&$width, &$height) {
        if (!$width) {
            $width = CORE_MEDIA_VIDEO_WIDTH;
            $height = CORE_MEDIA_VIDEO_HEIGHT;
        }
    }
}


/**
 * Base class for players which handle external links (YouTube etc).
 *
 * As opposed to media files.
 *
 * @copyright 2011 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class core_media_player_external extends core_media_player {
    /**
     * Array of matches from regular expression - subclass can assume these
     * will be valid when the embed function is called, to save it rerunning
     * the regex.
     * @var array
     */
    protected $matches;

    /**
     * Part of a regular expression, including ending ~ symbol (note: these
     * regexes use ~ instead of / because URLs and HTML code typically include
     * / symbol and makes harder to read if you have to escape it).
     * Matches the end part of a link after you have read the 'important' data
     * including optional #d=400x300 at end of url, plus content of <a> tag,
     * up to </a>.
     * @var string
     */
    const END_LINK_REGEX_PART = '[^#]*(#d=([\d]{1,4})x([\d]{1,4}))?~si';

    public function embed($urls, $name, $width, $height, $options) {
        return $this->embed_external(reset($urls), $name, $width, $height, $options);
    }

    /**
     * Obtains HTML code to embed the link.
     * @param moodle_url $url Single URL to embed
     * @param string $name Display name; '' to use default
     * @param int $width Optional width; 0 to use default
     * @param int $height Optional height; 0 to use default
     * @param array $options Options array
     * @return string HTML code for embed
     */
    protected abstract function embed_external(moodle_url $url, $name, $width, $height, $options);

    public function list_supported_urls(array $urls, array $options = array()) {
        // These only work with a SINGLE url (there is no fallback).
        if (count($urls) != 1) {
            return array();
        }
        $url = reset($urls);

        // Check against regex.
        if (preg_match($this->get_regex(), $url->out(false), $this->matches)) {
            return array($url);
        }

        return array();
    }

    /**
     * Returns regular expression used to match URLs that this player handles
     * @return string PHP regular expression e.g. '~^https?://example.org/~'
     */
    protected function get_regex() {
        return '~^unsupported~';
    }

    /**
     * Annoyingly, preg_match $matches result does not always have the same
     * number of parameters - it leaves out optional ones at the end. WHAT.
     * Anyway, this function can be used to fix it.
     * @param array $matches Array that should be adjusted
     * @param int $count Number of capturing groups (=6 to make $matches[6] work)
     */
    protected static function fix_match_count(&$matches, $count) {
        for ($i = count($matches); $i <= $count; $i++) {
            $matches[$i] = false;
        }
    }
}


/**
 * Player that embeds Vimeo links.
 *
 * @copyright 2011 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_media_player_vimeo extends core_media_player_external {
    protected function embed_external(moodle_url $url, $name, $width, $height, $options) {
        $videoid = $this->matches[1];
        $info = s($name);

        // Note: resizing via url is not supported, user can click the fullscreen
        // button instead. iframe embedding is not xhtml strict but it is the only
        // option that seems to work on most devices.
        self::pick_video_size($width, $height);

        $output = <<<OET
<span class="mediaplugin mediaplugin_vimeo">
<iframe title="$info" src="https://player.vimeo.com/video/$videoid"
  width="$width" height="$height" frameborder="0"></iframe>
</span>
OET;

        return $output;
    }

    protected function get_regex() {
        // Initial part of link.
        $start = '~^https?://vimeo\.com/';
        // Middle bit: either watch?v= or v/.
        $middle = '([0-9]+)';
        return $start . $middle . core_media_player_external::END_LINK_REGEX_PART;
    }

    public function get_rank() {
        return 1010;
    }

    public function get_embeddable_markers() {
        return array('vimeo.com/');
    }
}

/**
 * Player that creates YouTube embedding.
 *
 * @copyright 2011 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_media_player_youtube extends core_media_player_external {
    protected function embed_external(moodle_url $url, $name, $width, $height, $options) {
        $videoid = end($this->matches);

        $info = trim($name);
        if (empty($info) or strpos($info, 'http') === 0) {
            $info = get_string('siteyoutube', 'core_media');
        }
        $info = s($info);

        self::pick_video_size($width, $height);

        $params = '';
        $start = self::get_start_time($url);
        if ($start > 0) {
            $params .= "start=$start&";
        }

        $listid = $url->param('list');
        // Check for non-empty but valid playlist ID.
        if (!empty($listid) && !preg_match('/[^a-zA-Z0-9\-_]/', $listid)) {
            // This video is part of a playlist, and we want to embed it as such.
            $params .= "list=$listid&";
        }

        return <<<OET
<span class="mediaplugin mediaplugin_youtube">
<iframe title="$info" width="$width" height="$height"
  src="https://www.youtube.com/embed/$videoid?{$params}rel=0&wmode=transparent" frameborder="0" allowfullscreen="1"></iframe>
</span>
OET;

    }

    /**
     * Check for start time parameter.  Note that it's in hours/mins/secs in the URL,
     * but the embedded player takes only a number of seconds as the "start" parameter.
     * @param moodle_url $url URL of video to be embedded.
     * @return int Number of seconds video should start at.
     */
    protected static function get_start_time($url) {
        $matches = array();
        $seconds = 0;

        $rawtime = $url->param('t');
        if (empty($rawtime)) {
            $rawtime = $url->param('start');
        }

        if (is_numeric($rawtime)) {
            // Start time already specified as a number of seconds; ensure it's an integer.
            $seconds = $rawtime;
        } else if (preg_match('/(\d+?h)?(\d+?m)?(\d+?s)?/i', $rawtime, $matches)) {
            // Convert into a raw number of seconds, as that's all embedded players accept.
            for ($i = 1; $i < count($matches); $i++) {
                if (empty($matches[$i])) {
                    continue;
                }
                $part = str_split($matches[$i], strlen($matches[$i]) - 1);
                switch ($part[1]) {
                    case 'h':
                        $seconds += 3600 * $part[0];
                        break;
                    case 'm':
                        $seconds += 60 * $part[0];
                        break;
                    default:
                        $seconds += $part[0];
                }
            }
        }

        return intval($seconds);
    }

    protected function get_regex() {
        // Regex for standard youtube link
         $link = '(youtube(-nocookie)?\.com/(?:watch\?v=|v/))';
        // Regex for shortened youtube link
        $shortlink = '((youtu|y2u)\.be/)';

        // Initial part of link.
         $start = '~^https?://(www\.)?(' . $link . '|' . $shortlink . ')';
        // Middle bit: Video key value
        $middle = '([a-z0-9\-_]+)';
        return $start . $middle . core_media_player_external::END_LINK_REGEX_PART;
    }

    public function get_rank() {
        // I decided to make the link-embedding ones (that don't handle file
        // formats) have ranking in the 1000 range.
        return 1001;
    }

    public function get_embeddable_markers() {
        return array('youtube.com', 'youtube-nocookie.com', 'youtu.be', 'y2u.be');
    }
}


/**
 * Player that creates YouTube playlist embedding.
 *
 * @copyright 2011 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_media_player_youtube_playlist extends core_media_player_external {
    public function is_enabled() {
        global $CFG;
        // Use the youtube on/off flag.
        return $CFG->core_media_enable_youtube;
    }

    protected function embed_external(moodle_url $url, $name, $width, $height, $options) {
        $site = $this->matches[1];
        $playlist = $this->matches[3];

        $info = trim($name);
        if (empty($info) or strpos($info, 'http') === 0) {
            $info = get_string('siteyoutube', 'core_media');
        }
        $info = s($info);

        self::pick_video_size($width, $height);

        return <<<OET
<span class="mediaplugin mediaplugin_youtube">
<iframe width="$width" height="$height" src="https://$site/embed/videoseries?list=$playlist" frameborder="0" allowfullscreen="1"></iframe>
</span>
OET;
    }

    protected function get_regex() {
        // Initial part of link.
        $start = '~^https?://(www\.youtube(-nocookie)?\.com)/';
        // Middle bit: either view_play_list?p= or p/ (doesn't work on youtube) or playlist?list=.
        $middle = '(?:view_play_list\?p=|p/|playlist\?list=)([a-z0-9\-_]+)';
        return $start . $middle . core_media_player_external::END_LINK_REGEX_PART;
    }

    public function get_rank() {
        // I decided to make the link-embedding ones (that don't handle file
        // formats) have ranking in the 1000 range.
        return 1000;
    }

    public function get_embeddable_markers() {
        return array('youtube');
    }
}


/**
 * MP3 player inserted using JavaScript.
 *
 * @copyright 2011 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_media_player_mp3 extends core_media_player {
    public function embed($urls, $name, $width, $height, $options) {
        // Use first url (there can actually be only one unless some idiot
        // enters two mp3 files as alternatives).
        $url = reset($urls);

        // Unique id even across different http requests made at the same time
        // (for AJAX, iframes).
        $id = 'core_media_mp3_' . md5(time() . '_' . rand());

        // When Flash or JavaScript are not available only the fallback is displayed,
        // using span not div because players are inline elements.
        $spanparams = array('id' => $id, 'class' => 'mediaplugin mediaplugin_mp3');
        if ($width) {
            $spanparams['style'] = 'width: ' . $width . 'px';
        }
        $output = html_writer::tag('span', core_media_player::PLACEHOLDER, $spanparams);
        // We can not use standard JS init because this may be cached
        // note: use 'small' size unless embedding in block mode.
        $output .= html_writer::script(js_writer::function_call(
                'M.util.add_audio_player', array($id, $url->out(false),
                empty($options[core_media::OPTION_BLOCK]))));

        return $output;
    }

    public function get_supported_extensions() {
        return array('mp3');
    }

    public function get_rank() {
        return 80;
    }
}


/**
 * Flash video player inserted using JavaScript.
 *
 * @copyright 2011 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_media_player_flv extends core_media_player {
    public function embed($urls, $name, $width, $height, $options) {
        // Use first url (there can actually be only one unless some idiot
        // enters two mp3 files as alternatives).
        $url = reset($urls);

        // Unique id even across different http requests made at the same time
        // (for AJAX, iframes).
        $id = 'core_media_flv_' . md5(time() . '_' . rand());

        // Compute width and height.
        $autosize = false;
        if (!$width && !$height) {
            $width = CORE_MEDIA_VIDEO_WIDTH;
            $height = CORE_MEDIA_VIDEO_HEIGHT;
            $autosize = true;
        }

        // Fallback span (will normally contain link).
        $output = html_writer::tag('span', core_media_player::PLACEHOLDER,
                array('id'=>$id, 'class'=>'mediaplugin mediaplugin_flv'));
        // We can not use standard JS init because this may be cached.
        $output .= html_writer::script(js_writer::function_call(
                'M.util.add_video_player', array($id, addslashes_js($url->out(false)),
                $width, $height, $autosize)));
        return $output;
    }

    public function get_supported_extensions() {
        return array('flv', 'f4v');
    }

    public function get_rank() {
        return 70;
    }
}


/**
 * Embeds Windows Media Player using object tag.
 *
 * @copyright 2011 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_media_player_wmp extends core_media_player {
    public function embed($urls, $name, $width, $height, $options) {
        // Get URL (we just use first, probably there is only one).
        $firsturl = reset($urls);
        $url = $firsturl->out(false);

        // Work out width.
        if (!$width || !$height) {
            // Object tag has default size.
            $mpsize = '';
            $size = 'width="' . CORE_MEDIA_VIDEO_WIDTH .
                    '" height="' . (CORE_MEDIA_VIDEO_HEIGHT+64) . '"';
            $autosize = 'true';
        } else {
            $size = 'width="' . $width . '" height="' . ($height + 15) . '"';
            $mpsize = 'width="' . $width . '" height="' . ($height + 64) . '"';
            $autosize = 'false';
        }

        // MIME type for object tag.
        $mimetype = core_media::get_mimetype($firsturl);

        $fallback = core_media_player::PLACEHOLDER;

        // Embed code.
        return <<<OET
<span class="mediaplugin mediaplugin_wmp">
    <object classid="CLSID:6BF52A52-394A-11d3-B153-00C04F79FAA6" $mpsize
            standby="Loading Microsoft(R) Windows(R) Media Player components..."
            type="application/x-oleobject">
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
        <!--<![endif]-->
            $fallback
        <!--[if !IE]>-->
        </object>
        <!--<![endif]-->
    </object>
</span>
OET;
    }

    public function get_supported_extensions() {
        return array('wmv', 'avi');
    }

    public function get_rank() {
        return 60;
    }
}


/**
 * Media player using object tag and QuickTime player.
 *
 * @copyright 2011 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_media_player_qt extends core_media_player {
    public function embed($urls, $name, $width, $height, $options) {
        // Show first URL.
        $firsturl = reset($urls);
        $url = $firsturl->out(true);

        // Work out size.
        if (!$width || !$height) {
            $size = 'width="' . CORE_MEDIA_VIDEO_WIDTH .
                    '" height="' . (CORE_MEDIA_VIDEO_HEIGHT + 15) . '"';
        } else {
            $size = 'width="' . $width . '" height="' . ($height + 15) . '"';
        }

        // MIME type for object tag.
        $mimetype = core_media::get_mimetype($firsturl);

        $fallback = core_media_player::PLACEHOLDER;

        // Embed code.
        return <<<OET
<span class="mediaplugin mediaplugin_qt">
    <object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B"
            codebase="http://www.apple.com/qtactivex/qtplugin.cab" $size>
        <param name="pluginspage" value="http://www.apple.com/quicktime/download/" />
        <param name="src" value="$url" />
        <param name="controller" value="true" />
        <param name="loop" value="false" />
        <param name="autoplay" value="false" />
        <param name="autostart" value="false" />
        <param name="scale" value="aspect" />
        <!--[if !IE]>-->
        <object data="$url" type="$mimetype" $size>
            <param name="src" value="$url" />
            <param name="pluginurl" value="http://www.apple.com/quicktime/download/" />
            <param name="controller" value="true" />
            <param name="loop" value="false" />
            <param name="autoplay" value="false" />
            <param name="autostart" value="false" />
            <param name="scale" value="aspect" />
        <!--<![endif]-->
            $fallback
        <!--[if !IE]>-->
        </object>
        <!--<![endif]-->
    </object>
</span>
OET;
    }

    public function get_supported_extensions() {
        return array('mpg', 'mpeg', 'mov', 'mp4', 'm4v', 'm4a');
    }

    public function get_rank() {
        return 50;
    }
}


/**
 * Media player using object tag and RealPlayer.
 *
 * Hopefully nobody is using this obsolete format any more!
 *
 * @copyright 2011 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_media_player_rm extends core_media_player {
    public function embed($urls, $name, $width, $height, $options) {
        // Show first URL.
        $firsturl = reset($urls);
        $url = $firsturl->out(true);

        // Get name to use as title.
        $info = s($this->get_name($name, $urls));

        // The previous version of this code has the following comment, which
        // I don't understand, but trust it is correct:
        // Note: the size is hardcoded intentionally because this does not work anyway!
        $width = CORE_MEDIA_VIDEO_WIDTH;
        $height = CORE_MEDIA_VIDEO_HEIGHT;

        $fallback = core_media_player::PLACEHOLDER;
        return <<<OET
<span class="mediaplugin mediaplugin_real">
    <object title="$info" classid="clsid:CFCDAA03-8BE4-11cf-B84B-0020AFBBCCFA"
            data="$url" width="$width" height="$height"">
        <param name="src" value="$url" />
        <param name="controls" value="All" />
        <!--[if !IE]>-->
        <object title="$info" type="audio/x-pn-realaudio-plugin"
                data="$url" width="$width" height="$height">
            <param name="src" value="$url" />
            <param name="controls" value="All" />
        <!--<![endif]-->
            $fallback
        <!--[if !IE]>-->
        </object>
        <!--<![endif]-->
  </object>
</span>
OET;
    }

    public function get_supported_extensions() {
        return array('ra', 'ram', 'rm', 'rv');
    }

    public function get_rank() {
        return 40;
    }
}


/**
 * Media player for Flash SWF files.
 *
 * This player contains additional security restriction: it will only be used
 * if you add option core_media_player_swf::ALLOW = true.
 *
 * Code should only set this option if it has verified that the data was
 * embedded by a trusted user (e.g. in trust text).
 *
 * @copyright 2011 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_media_player_swf extends core_media_player {
    public function embed($urls, $name, $width, $height, $options) {
        self::pick_video_size($width, $height);

        $firsturl = reset($urls);
        $url = $firsturl->out(true);

        $fallback = core_media_player::PLACEHOLDER;
        $output = <<<OET
<span class="mediaplugin mediaplugin_swf">
  <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="$width" height="$height">
    <param name="movie" value="$url" />
    <param name="autoplay" value="true" />
    <param name="loop" value="false" />
    <param name="controller" value="true" />
    <param name="scale" value="aspect" />
    <param name="base" value="." />
    <param name="allowscriptaccess" value="never" />
<!--[if !IE]>-->
    <object type="application/x-shockwave-flash" data="$url" width="$width" height="$height">
      <param name="controller" value="true" />
      <param name="autoplay" value="true" />
      <param name="loop" value="false" />
      <param name="scale" value="aspect" />
      <param name="base" value="." />
      <param name="allowscriptaccess" value="never" />
<!--<![endif]-->
$fallback
<!--[if !IE]>-->
    </object>
<!--<![endif]-->
  </object>
</span>
OET;

        return $output;
    }

    public function get_supported_extensions() {
        return array('swf');
    }

    public function list_supported_urls(array $urls, array $options = array()) {
        // Not supported unless the creator is trusted.
        if (empty($options[core_media::OPTION_TRUSTED])) {
            return array();
        }
        return parent::list_supported_urls($urls, $options);
    }

    public function get_rank() {
        return 30;
    }
}


/**
 * Player that creates HTML5 <video> tag.
 *
 * @copyright 2011 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_media_player_html5video extends core_media_player {
    public function embed($urls, $name, $width, $height, $options) {
        // Special handling to make videos play on Android devices pre 2.3.
        // Note: I tested and 2.3.3 (in emulator) works without, is 533.1 webkit.
        $oldandroid = core_useragent::is_webkit_android() &&
                !core_useragent::check_webkit_android_version('533.1');

        // Build array of source tags.
        $sources = array();
        foreach ($urls as $url) {
            $mimetype = core_media::get_mimetype($url);
            $source = html_writer::tag('source', '', array('src' => $url, 'type' => $mimetype));
            if ($mimetype === 'video/mp4') {
                if ($oldandroid) {
                    // Old Android fails if you specify the type param.
                    $source = html_writer::tag('source', '', array('src' => $url));
                }

                // Better add m4v as first source, it might be a bit more
                // compatible with problematic browsers.
                array_unshift($sources, $source);
            } else {
                $sources[] = $source;
            }
        }

        $sources = implode("\n", $sources);
        $title = s($this->get_name($name, $urls));

        if (!$width) {
            // No width specified, use system default.
            $width = CORE_MEDIA_VIDEO_WIDTH;
        }

        if (!$height) {
            // Let browser choose height automatically.
            $size = "width=\"$width\"";
        } else {
            $size = "width=\"$width\" height=\"$height\"";
        }

        $sillyscript = '';
        $idtag = '';
        if ($oldandroid) {
            // Old Android does not support 'controls' option.
            $id = 'core_media_html5v_' . md5(time() . '_' . rand());
            $idtag = 'id="' . $id . '"';
            $sillyscript = <<<OET
<script type="text/javascript">
document.getElementById('$id').addEventListener('click', function() {
    this.play();
}, false);
</script>
OET;
        }

        $fallback = core_media_player::PLACEHOLDER;
        return <<<OET
<span class="mediaplugin mediaplugin_html5video">
<video $idtag controls="true" $size preload="metadata" title="$title">
    $sources
    $fallback
</video>
$sillyscript
</span>
OET;
    }

    public function get_supported_extensions() {
        return array('m4v', 'webm', 'ogv', 'mp4');
    }

    public function list_supported_urls(array $urls, array $options = array()) {
        $extensions = $this->get_supported_extensions();
        $result = array();
        foreach ($urls as $url) {
            $ext = core_media::get_extension($url);
            if (in_array($ext, $extensions)) {
                // Unfortunately html5 video does not handle fallback properly.
                // https://www.w3.org/Bugs/Public/show_bug.cgi?id=10975
                // That means we need to do browser detect and not use html5 on
                // browsers which do not support the given type, otherwise users
                // will not even see the fallback link.
                // Based on http://en.wikipedia.org/wiki/HTML5_video#Table - this
                // is a simplified version, does not take into account old browser
                // versions or manual plugins.
                if ($ext === 'ogv' || $ext === 'webm') {
                    // Formats .ogv and .webm are not supported in IE or Safari.
                    if (core_useragent::is_ie() || core_useragent::is_safari()) {
                        continue;
                    }
                } else {
                    // Formats .m4v and .mp4 are not supported in Opera, or in Firefox before 27.
                    // https://developer.mozilla.org/en-US/docs/Web/HTML/Supported_media_formats
                    // has the details.
                    if (core_useragent::is_opera() || (core_useragent::is_firefox() &&
                            !core_useragent::check_firefox_version(27))) {
                        continue;
                    }
                }

                $result[] = $url;
            }
        }
        return $result;
    }

    public function get_rank() {
        return 20;
    }
}


/**
 * Player that creates HTML5 <audio> tag.
 *
 * @copyright 2011 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_media_player_html5audio extends core_media_player {
    public function embed($urls, $name, $width, $height, $options) {

        // Build array of source tags.
        $sources = array();
        foreach ($urls as $url) {
            $mimetype = core_media::get_mimetype($url);
            $sources[] = html_writer::tag('source', '', array('src' => $url, 'type' => $mimetype));
        }

        $sources = implode("\n", $sources);
        $title = s($this->get_name($name, $urls));

        // Default to not specify size (so it can be changed in css).
        $size = '';
        if ($width) {
            $size = 'width="' . $width . '"';
        }

        $fallback = core_media_player::PLACEHOLDER;

        return <<<OET
<audio controls="true" $size class="mediaplugin mediaplugin_html5audio" preload="no" title="$title">
$sources
$fallback
</audio>
OET;
    }

    public function get_supported_extensions() {
        return array('ogg', 'oga', 'aac', 'm4a', 'mp3');
    }

    public function list_supported_urls(array $urls, array $options = array()) {
        $extensions = $this->get_supported_extensions();
        $result = array();
        foreach ($urls as $url) {
            $ext = core_media::get_extension($url);
            if (in_array($ext, $extensions)) {
                if ($ext === 'ogg' || $ext === 'oga') {
                    // Formats .ogg and .oga are not supported in IE or Safari.
                    if (core_useragent::is_ie() || core_useragent::is_safari()) {
                        continue;
                    }
                } else {
                    // Formats .aac, .mp3, and .m4a are not supported in Opera.
                    if (core_useragent::is_opera()) {
                        continue;
                    }
                    // Formats .mp3 and .m4a were not reliably supported in Firefox before 27.
                    // https://developer.mozilla.org/en-US/docs/Web/HTML/Supported_media_formats
                    // has the details. .aac is still not supported.
                    if (core_useragent::is_firefox() && ($ext === 'aac' ||
                            !core_useragent::check_firefox_version(27))) {
                        continue;
                    }
                }
                // Old Android versions (pre 2.3.3) 'support' audio tag but no codecs.
                if (core_useragent::is_webkit_android() &&
                        !core_useragent::is_webkit_android('533.1')) {
                    continue;
                }

                $result[] = $url;
            }
        }
        return $result;
    }

    public function get_rank() {
        return 10;
    }
}


/**
 * Special media player class that just puts a link.
 *
 * Always enabled, used as the last fallback.
 *
 * @copyright 2011 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_media_player_link extends core_media_player {
    public function embed($urls, $name, $width, $height, $options) {
        // If link is turned off, return empty.
        if (!empty($options[core_media::OPTION_NO_LINK])) {
            return '';
        }

        // Build up link content.
        $output = '';
        foreach ($urls as $url) {
            $title = core_media::get_filename($url);
            $printlink = html_writer::link($url, $title, array('class' => 'mediafallbacklink'));
            if ($output) {
                // Where there are multiple available formats, there are fallback links
                // for all formats, separated by /.
                $output .= ' / ';
            }
            $output .= $printlink;
        }
        return $output;
    }

    public function list_supported_urls(array $urls, array $options = array()) {
        // Supports all URLs.
        return $urls;
    }

    public function is_enabled() {
        // Cannot be disabled.
        return true;
    }

    public function get_rank() {
        return 0;
    }
}
