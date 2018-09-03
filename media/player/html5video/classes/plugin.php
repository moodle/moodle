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
 * Main class for plugin 'media_html5video'
 *
 * @package   media_html5video
 * @copyright 2016 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Player that creates HTML5 <video> tag.
 *
 * @package   media_html5video
 * @copyright 2016 Marina Glancy
 * @author 2011 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class media_html5video_plugin extends core_media_player_native {
    public function embed($urls, $name, $width, $height, $options) {

        if (array_key_exists(core_media_manager::OPTION_ORIGINAL_TEXT, $options) &&
            preg_match('/^<(video|audio)\b/i', $options[core_media_manager::OPTION_ORIGINAL_TEXT], $matches)) {
            // We already had media tag, do nothing here.
            return $options[core_media_manager::OPTION_ORIGINAL_TEXT];
        }

        // Special handling to make videos play on Android devices pre 2.3.
        // Note: I tested and 2.3.3 (in emulator) works without, is 533.1 webkit.
        $oldandroid = core_useragent::is_webkit_android() &&
            !core_useragent::check_webkit_android_version('533.1');

        // Build array of source tags.
        $sources = array();
        foreach ($urls as $url) {
            $mimetype = core_media_manager::instance()->get_mimetype($url);
            $source = html_writer::empty_tag('source', array('src' => $url, 'type' => $mimetype));
            if ($mimetype === 'video/mp4') {
                if ($oldandroid) {
                    // Old Android fails if you specify the type param.
                    $source = html_writer::empty_tag('source', array('src' => $url));
                }

                // Better add m4v as first source, it might be a bit more
                // compatible with problematic browsers.
                array_unshift($sources, $source);
            } else {
                $sources[] = $source;
            }
        }

        $sources = implode("\n", $sources);
        $title = $this->get_name($name, $urls);
        // Escape title but prevent double escaping.
        $title = s(preg_replace(['/&amp;/', '/&gt;/', '/&lt;/'], ['&', '>', '<'], $title));

        self::pick_video_size($width, $height);
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

        // We don't want fallback to another player because list_supported_urls() is already smart.
        // Otherwise we could end up with nested <video> tags. Fallback to link only.
        $fallback = self::LINKPLACEHOLDER;
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
        global $CFG;
        require_once($CFG->libdir . '/filelib.php');
        return file_get_typegroup('extension', 'html_video');
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
        }
    }

    /**
     * Default rank
     * @return int
     */
    public function get_rank() {
        return 50;
    }
}
