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
 * Manager for media files
 *
 * @package   core_media
 * @copyright 2016 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Manager for media files.
 *
 * Used in file resources, media filter, and any other places that need to
 * output embedded media.
 *
 * Usage:
 * $manager = core_media_manager::instance();
 *
 *
 * @package   core_media
 * @copyright 2016 Marina Glancy
 * @author    2011 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_media_manager {
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
     * Option: When the request for media players came from a text filter this option will contain the
     * original HTML snippet, usually one of the tags: <a> or <video> or <audio>
     *
     * Players that support other HTML5 features such as tracks may find them in this option.
     */
    const OPTION_ORIGINAL_TEXT = 'originaltext';

    /** @var array Array of available 'player' objects */
    private $players;

    /** @var string Regex pattern for links which may contain embeddable content */
    private $embeddablemarkers;

    /** @var core_media_manager caches a singleton instance */
    static protected $instance;

    /** @var moodle_page page this instance was initialised for */
    protected $page;

    /**
     * Returns a singleton instance of a manager
     *
     * Note as of Moodle 3.2.2, this will call setup for you.
     *
     * @return core_media_manager
     */
    public static function instance($page = null) {
        // Use the passed $page if given, otherwise the $PAGE global.
        if (!$page) {
            global $PAGE;
            $page = $PAGE;
        }
        if (self::$instance === null || ($page && self::$instance->page !== $page)) {
            self::$instance = new self($page);
        }
        return self::$instance;
    }

    /**
     * Construct a new core_media_manager instance
     *
     * @param moodle_page $page The page we are going to add requirements to.
     * @see core_media_manager::instance()
     */
    protected function __construct($page) {
        if ($page) {
            $this->page = $page;
            $players = $this->get_players();
            foreach ($players as $player) {
                $player->setup($page);
            }
        } else {
            debugging('Could not determine the $PAGE. Media plugins will not be set up', DEBUG_DEVELOPER);
        }
    }

    /**
     * Setup page requirements.
     *
     * This should must only be called once per page request.
     *
     * This function will be deprecated in Moodle 3.3, The setup is now done in ::instance() so there is no need to call this.
     * @param moodle_page $page The page we are going to add requirements to.
     * @see core_media_manager::instance()
     */
    public function setup($page) {
        // No need to call ::instance from here, because the instance has already be set up.
    }

    /**
     * Resets cached singleton instance. To be used after $CFG->media_plugins_sortorder is modified
     */
    public static function reset_caches() {
        self::$instance = null;
    }

    /**
     * Obtains the list of core_media_player objects currently in use to render
     * items.
     *
     * The list is in rank order (highest first) and does not include players
     * which are disabled.
     *
     * @return core_media_player[] Array of core_media_player objects in rank order
     */
    protected function get_players() {
        // Save time by only building the list once.
        if (!$this->players) {
            $sortorder = \core\plugininfo\media::get_enabled_plugins();

            $this->players = [];
            foreach ($sortorder as $name) {
                $classname = "media_" . $name . "_plugin";
                if (class_exists($classname)) {
                    $this->players[] = new $classname();
                }
            }
        }
        return $this->players;
    }

    /**
     * Renders a media file (audio or video) using suitable embedded player.
     *
     * See embed_alternatives function for full description of parameters.
     * This function calls through to that one.
     *
     * When using this function you can also specify width and height in the
     * URL by including ?d=100x100 at the end. If specified in the URL, this
     * will override the $width and $height parameters.
     *
     * @param moodle_url $url Full URL of media file
     * @param string $name Optional user-readable name to display in download link
     * @param int $width Width in pixels (optional)
     * @param int $height Height in pixels (optional)
     * @param array $options Array of key/value pairs
     * @return string HTML content of embed
     */
    public function embed_url(moodle_url $url, $name = '', $width = 0, $height = 0,
                              $options = array()) {

        // Get width and height from URL if specified (overrides parameters in
        // function call).
        $rawurl = $url->out(false);
        if (preg_match('/[?#]d=([\d]{1,4}%?)x([\d]{1,4}%?)/', $rawurl, $matches)) {
            $width = $matches[1];
            $height = $matches[2];
            $url = new moodle_url(str_replace($matches[0], '', $rawurl));
        }

        // Defer to array version of function.
        return $this->embed_alternatives(array($url), $name, $width, $height, $options);
    }

    /**
     * Renders media files (audio or video) using suitable embedded player.
     * The list of URLs should be alternative versions of the same content in
     * multiple formats. If there is only one format it should have a single
     * entry.
     *
     * If the media files are not in a supported format, this will give students
     * a download link to each format. The download link uses the filename
     * unless you supply the optional name parameter.
     *
     * Width and height are optional. If specified, these are suggested sizes
     * and should be the exact values supplied by the user, if they come from
     * user input. These will be treated as relating to the size of the video
     * content, not including any player control bar.
     *
     * For audio files, height will be ignored. For video files, a few formats
     * work if you specify only width, but in general if you specify width
     * you must specify height as well.
     *
     * The $options array is passed through to the core_media_player classes
     * that render the object tag. The keys can contain values from
     * core_media::OPTION_xx.
     *
     * @param array $alternatives Array of moodle_url to media files
     * @param string $name Optional user-readable name to display in download link
     * @param int $width Width in pixels (optional)
     * @param int $height Height in pixels (optional)
     * @param array $options Array of key/value pairs
     * @return string HTML content of embed
     */
    public function embed_alternatives($alternatives, $name = '', $width = 0, $height = 0,
                                       $options = array()) {

        // Get list of player plugins.
        $players = $this->get_players();

        // Set up initial text which will be replaced by first player that
        // supports any of the formats.
        $out = core_media_player::PLACEHOLDER;

        // Loop through all players that support any of these URLs.
        foreach ($players as $player) {
            $supported = $player->list_supported_urls($alternatives, $options);
            if ($supported) {
                // Embed.
                $text = $player->embed($supported, $name, $width, $height, $options);

                // Put this in place of the 'fallback' slot in the previous text.
                $out = str_replace(core_media_player::PLACEHOLDER, $text, $out);

                // Check if we need to continue looking for players.
                if (strpos($out, core_media_player::PLACEHOLDER) === false) {
                    break;
                }
            }
        }

        if (!empty($options[self::OPTION_FALLBACK_TO_BLANK]) && $out === core_media_player::PLACEHOLDER) {
            // In case of OPTION_FALLBACK_TO_BLANK and no player matched do not fallback to link, just return empty string.
            return '';
        }

        // Remove 'fallback' slot from final version and return it.
        $fallback = $this->fallback_to_link($alternatives, $name, $options);
        $out = str_replace(core_media_player::PLACEHOLDER, $fallback, $out);
        $out = str_replace(core_media_player::LINKPLACEHOLDER, $fallback, $out);
        if (!empty($options[self::OPTION_BLOCK]) && $out !== '') {
            $out = html_writer::tag('div', $out, array('class' => 'resourcecontent'));
        }
        return $out;
    }

    /**
     * Returns links to the specified URLs unless OPTION_NO_LINK is passed.
     *
     * @param array $urls URLs of media files
     * @param string $name Display name; '' to use default
     * @param array $options Options array
     * @return string HTML code for embed
     */
    protected function fallback_to_link($urls, $name, $options) {
        // If link is turned off, return empty.
        if (!empty($options[self::OPTION_NO_LINK])) {
            return '';
        }

        // Build up link content.
        $output = '';
        foreach ($urls as $url) {
            if (strval($name) !== '' && $output === '') {
                $title = $name;
            } else {
                $title = $this->get_filename($url);
            }
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

    /**
     * Checks whether a file can be embedded. If this returns true you will get
     * an embedded player; if this returns false, you will just get a download
     * link.
     *
     * This is a wrapper for can_embed_urls.
     *
     * @param moodle_url $url URL of media file
     * @param array $options Options (same as when embedding)
     * @return bool True if file can be embedded
     */
    public function can_embed_url(moodle_url $url, $options = array()) {
        return $this->can_embed_urls(array($url), $options);
    }

    /**
     * Checks whether a file can be embedded. If this returns true you will get
     * an embedded player; if this returns false, you will just get a download
     * link.
     *
     * @param array $urls URL of media file and any alternatives (moodle_url)
     * @param array $options Options (same as when embedding)
     * @return bool True if file can be embedded
     */
    public function can_embed_urls(array $urls, $options = array()) {
        // Check all players to see if any of them support it.
        foreach ($this->get_players() as $player) {
            // First player that supports it, return true.
            if ($player->list_supported_urls($urls, $options)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Obtains a list of markers that can be used in a regular expression when
     * searching for URLs that can be embedded by any player type.
     *
     * This string is used to improve peformance of regex matching by ensuring
     * that the (presumably C) regex code can do a quick keyword check on the
     * URL part of a link to see if it matches one of these, rather than having
     * to go into PHP code for every single link to see if it can be embedded.
     *
     * @return string String suitable for use in regex such as '(\.mp4|\.flv)'
     */
    public function get_embeddable_markers() {
        if (empty($this->embeddablemarkers)) {
            $markers = '';
            foreach ($this->get_players() as $player) {
                foreach ($player->get_embeddable_markers() as $marker) {
                    if ($markers !== '') {
                        $markers .= '|';
                    }
                    $markers .= preg_quote($marker);
                }
            }
            $this->embeddablemarkers = $markers;
        }
        return $this->embeddablemarkers;
    }

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
    public function split_alternatives($combinedurl, &$width, &$height) {
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
    public function get_extension(moodle_url $url) {
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
    public function get_filename(moodle_url $url) {
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
    public function get_mimetype(moodle_url $url) {
        return mimeinfo('type', $this->get_filename($url));
    }

}
