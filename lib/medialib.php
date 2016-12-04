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
 * Deprecated classes and constants.
 *
 * DO NOT INCLUDE THIS FILE
 *
 * use $CFG->media_default_width instead of CORE_MEDIA_VIDEO_WIDTH,
 * $CFG->media_default_height instead of CORE_MEDIA_VIDEO_HEIGHT,
 * core_media_manager::instance() instead of static methods in core_media,
 * core_media_manager::OPTION_zzz instead of core_media::OPTION_zzz
 *
 * New syntax to include media files:
 *
 * $mediamanager = core_media_manager::instance();
 * echo $mediamanager->embed_url(new moodle_url('http://example.org/a.mp3'));
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

debugging('Do not include lib/medialib.php, use $CFG->media_default_width instead of CORE_MEDIA_VIDEO_WIDTH, ' .
    '$CFG->media_default_height instead of CORE_MEDIA_VIDEO_HEIGHT, ' .
    'core_media_manager::instance() instead of static methods in core_media, ' .
    'core_media_manager::OPTION_zzz instead of core_media::OPTION_zzz',
    DEBUG_DEVELOPER);

/**
 * Constants and static utility functions for use with core_media_renderer.
 *
 * @deprecated since Moodle 3.2
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
        return core_media_manager::instance()->split_alternatives($combinedurl, $width, $height);
    }

    /**
     * Returns the file extension for a URL.
     * @param moodle_url $url URL
     */
    public static function get_extension(moodle_url $url) {
        return core_media_manager::instance()->get_extension($url);
    }

    /**
     * Obtains the filename from the moodle_url.
     * @param moodle_url $url URL
     * @return string Filename only (not escaped)
     */
    public static function get_filename(moodle_url $url) {
        return core_media_manager::instance()->get_filename($url);
    }

    /**
     * Guesses MIME type for a moodle_url based on file extension.
     * @param moodle_url $url URL
     * @return string MIME type
     */
    public static function get_mimetype(moodle_url $url) {
        return core_media_manager::instance()->get_mimetype($url);
    }
}
