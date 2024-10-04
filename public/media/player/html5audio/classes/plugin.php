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
 * Main class for plugin 'media_html5audio'
 *
 * @package   media_html5audio
 * @copyright 2016 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Player that creates HTML5 <audio> tag.
 *
 * @package   media_html5audio
 * @copyright 2016 Marina Glancy
 * @author    2011 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class media_html5audio_plugin extends core_media_player_native {
    public function embed($urls, $name, $width, $height, $options) {

        if (array_key_exists(core_media_manager::OPTION_ORIGINAL_TEXT, $options) &&
            preg_match('/^<(video|audio)\b/i', $options[core_media_manager::OPTION_ORIGINAL_TEXT], $matches)) {
            // We already had media tag, do nothing here.
            return $options[core_media_manager::OPTION_ORIGINAL_TEXT];
        }

        // Build array of source tags.
        $sources = array();
        foreach ($urls as $url) {
            $params = ['src' => $url];
            $ext = core_media_manager::instance()->get_extension($url);
            if ($ext !== 'aac') {
                // Some browsers get confused by mimetype on source for AAC files.
                $mimetype = core_media_manager::instance()->get_mimetype($url);
                $params['type'] = $mimetype;
            }
            $sources[] = html_writer::empty_tag('source', $params);
        }

        $sources = implode("\n", $sources);
        $title = $this->get_name($name, $urls);
        // Escape title but prevent double escaping.
        $title = s(preg_replace(['/&amp;/', '/&gt;/', '/&lt;/'], ['&', '>', '<'], $title));

        // Default to not specify size (so it can be changed in css).
        $size = '';
        if ($width) {
            $size = 'width="' . $width . '"';
        }

        // We don't want fallback to another player because list_supported_urls() is already smart.
        // Otherwise we could end up with nested <audio> tags. Fallback to link only.
        $fallback = self::LINKPLACEHOLDER;

        return <<<OET
<audio controls="true" $size class="mediaplugin mediaplugin_html5audio" preload="none" title="$title">
$sources
$fallback
</audio>
OET;
    }

    public function get_supported_extensions() {
        global $CFG;
        require_once($CFG->libdir . '/filelib.php');
        return file_get_typegroup('extension', 'html_audio');
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

    /**
     * Default rank
     * @return int
     */
    public function get_rank() {
        return 20;
    }
}

