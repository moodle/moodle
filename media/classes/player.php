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
 * Base class for media players
 *
 * @package   core_media
 * @copyright 2016 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Base class for media players.
 *
 * Media players return embed HTML for a particular way of playing back audio
 * or video (or another file type).
 *
 * @package   core_media
 * @copyright 2016 Marina Glancy
 * @author    2011 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class core_media_player {
    /**
     * Placeholder text used to indicate where the fallback content is placed
     * within a result.
     */
    const PLACEHOLDER = '<!--FALLBACK-->';

    /**
     * Placeholder text used to indicate where the link fallback is placed.
     * No other players will apply to it but it will be converted to the link in the
     * end (unless prevented by OPTION_NO_LINK).
     */
    const LINKPLACEHOLDER = '<!--LINKFALLBACK-->';

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
    abstract public function embed($urls, $name, $width, $height, $options);

    /**
     * Gets the list of file extensions supported by this media player.
     *
     * Note: This is only required for the default implementations of
     * list_supported_urls(), get_embeddable_markers() and supports().
     * If you override these functions to determine
     * supported URLs in some way other than by extension, then this function
     * is not necessary.
     *
     * @return array Array of strings (extension not including dot e.g. '.mp3')
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
     * Default handling calls the get_supported_extensions function, so players
     * only need to override this if they don't implement get_supported_extensions.
     *
     * This is used to improve performance when matching links in the media filter.
     *
     * @return array Array of keywords to add to the embeddable markers list
     */
    public function get_embeddable_markers() {
        return $this->get_supported_extensions();
    }

    /**
     * Returns human-readable string of supported file/link types for the "Manage media players" page
     * @param array $usedextensions extensions that should NOT be highlighted
     * @return string
     */
    public function supports($usedextensions = []) {
        $out = [];
        if ($extensions = $this->get_supported_extensions()) {
            $video = $audio = $other = [];
            foreach ($extensions as $key => $extension) {
                $displayextension = $extension;
                if (!in_array($extension, $usedextensions)) {
                    $displayextension = '<strong>'.$extension.'</strong>';
                }
                if (file_extension_in_typegroup('file.'.$extension, 'audio')) {
                    $audio[] = $displayextension;
                } else if (file_extension_in_typegroup('file.'.$extension, 'video')) {
                    $video[] = $displayextension;
                } else {
                    $other[] = $displayextension;
                }
            }
            if ($video) {
                $out[] = get_string('videoextensions', 'core_media', join(', ', $video));
            }
            if ($audio) {
                $out[] = get_string('audioextensions', 'core_media', join(', ', $audio));
            }
            if ($other) {
                $out[] = get_string('extensions', 'core_media', join(', ', $other));
            }
        }
        return join('<br>', $out);
    }

    /**
     * Gets the ranking of this player. This is an integer used to decide which
     * player to use (after applying other considerations such as which ones
     * the user has disabled).
     *
     * This function returns the default rank that can be adjusted by the administrator
     * on the Manage media players page.
     *
     * @return int Rank (higher is better)
     */
    abstract public function get_rank();

    /**
     * @deprecated since Moodle 3.2
     */
    public function is_enabled() {
        throw new coding_exception('core_media_player::is_enabled() can not be used anymore.');
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
            $ext = core_media_manager::instance()->get_extension($url);
            if (in_array('.' . $ext, $extensions) || in_array($ext, $extensions)) {
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
        $name = core_media_manager::instance()->get_filename($url);

        // If there is more than one url, strip the extension as we could be
        // referring to a different one or several at once.
        if (count($urls) > 1) {
            $name = preg_replace('~\.[^.]*$~', '', $name);
        }

        return $name;
    }

    /**
     * @deprecated since Moodle 3.2
     */
    public static function compare_by_rank() {
        throw new coding_exception('core_media_player::compare_by_rank() can not be used anymore.');
    }

    /**
     * Utility function that sets width and height to defaults if not specified
     * as a parameter to the function (will be specified either if, (a) the calling
     * code passed it, or (b) the URL included it).
     * @param int $width Width passed to function (updated with final value)
     * @param int $height Height passed to function (updated with final value)
     */
    protected static function pick_video_size(&$width, &$height) {
        global $CFG;
        if (!$width) {
            $width = $CFG->media_default_width;
            $height = $CFG->media_default_height;
        }
    }

    /**
     * Setup page requirements.
     *
     * The typical javascript requirements MUST not take action on the content
     * directly. They are meant to load the required libraries and listen
     * to events in order to know when to take action. The role of this method
     * is not to provide a way for plugins to look for content to embed on the
     * page. The {@link self::embed()} method is meant to be used for that.
     *
     * @param moodle_page $page The page we are going to add requirements to.
     * @since Moodle 3.2
     */
    public function setup($page) {
        // Override is need be.
    }

}
