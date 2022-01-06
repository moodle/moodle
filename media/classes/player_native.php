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
 * Base class for players which return native HTML5 <video> or <audio> tags
 *
 * @package   core_media
 * @copyright 2016 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Base class for players which return native HTML5 <video> or <audio> tags
 *
 * @package   core_media
 * @copyright 2016 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class core_media_player_native extends core_media_player {
    /**
     * Extracts a value for an attribute
     *
     * @param string $tag html tag which properties are extracted, for example "<video ...>....</video>"
     * @param string $attrname name of the attribute we are looking for
     * @param string $type one of PARAM_* constants to clean the attribute value
     * @return string|null
     */
    public static function get_attribute($tag, $attrname, $type = PARAM_RAW) {
        if (preg_match('/^<[^>]*\b' . $attrname . '="(.*?)"/is', $tag, $matches)) {
            return clean_param(htmlspecialchars_decode($matches[1]), $type);
        } else if (preg_match('~^<[^>]*\b' . $attrname . '[ />]"~is', $tag, $matches)) {
            // Some attributes may not have value, for example this is valid: <video controls>.
            return clean_param("true", $type);
        }
        return null;
    }

    /**
     * Removes an attribute from the media tags
     *
     * @param string $tag html tag which properties are extracted, for example "<video ...>....</video>"
     * @param string|array $attrname
     * @return string
     */
    public static function remove_attributes($tag, $attrname) {
        if (is_array($attrname)) {
            $attrname = join('|', $attrname);
        }
        while (preg_match('/^(<[^>]*\b)(' . $attrname . ')=".*?"(.*)$/is', $tag, $matches)) {
            $tag = $matches[1] . $matches[3];
        }
        while (preg_match('~^(<[^>]*\b)(' . $attrname . ')([ />].*)$~is', $tag, $matches)) {
            // Some attributes may not have value, for example: <video controls>.
            $tag = $matches[1] . $matches[3];
        }
        return $tag;
    }

    /**
     * Adds attributes to the media tags
     *
     * @param string $tag html tag which properties are extracted, for example "<video ...>....</video>"
     * @param array $attributes key-value pairs of attributes to be added
     * @return string
     */
    public static function add_attributes($tag, $attributes) {
        $tag = self::remove_attributes($tag, array_keys($attributes));
        if (!preg_match('/^(<.*?)(>.*)$/s', $tag, $matches)) {
            return $tag;
        }
        $rv = $matches[1];
        foreach ($attributes as $name => $value) {
            $rv .= " $name=\"".s($value).'"';
        }
        $rv .= $matches[2];
        return $rv;
    }

    /**
     * Replaces all embedded <source> tags and src attribute
     *
     * @param string $tag html tag which properties are extracted, for example "<video ...>....</video>"
     * @param string $sources replacement string (expected to contain <source> tags)
     * @return string
     */
    public static function replace_sources($tag, $sources) {
        $tag = self::remove_attributes($tag, 'src');
        $tag = preg_replace(['~</?source\b[^>]*>~i'], '', $tag);
        if (preg_match('/^(<.*?>)([^\0]*)$/ms', $tag, $matches)) {
            $tag = $matches[1].$sources.$matches[2];
        }
        return $tag;
    }

    public function get_supported_extensions() {
        global $CFG;
        require_once($CFG->libdir . '/filelib.php');
        return file_get_typegroup('extension', ['html_video', 'html_audio']);
    }

    public function list_supported_urls(array $urls, array $options = array()) {
        $extensions = $this->get_supported_extensions();
        $result = array();
        foreach ($urls as $url) {
            $ext = core_media_manager::instance()->get_extension($url);
            if (in_array('.' . $ext, $extensions) && core_useragent::supports_html5($ext)) {
                // Unfortunately html5 video does not handle fallback properly.
                // https://www.w3.org/Bugs/Public/show_bug.cgi?id=10975
                // That means we need to do browser detect and not use html5 on
                // browsers which do not support the given type, otherwise users
                // will not even see the fallback link.
                $result[] = $url;
            }
        }
        return $result;
    }
}
