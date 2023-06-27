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
 * Main class for plugin 'media_swf'
 *
 * @package   media_swf
 * @copyright 2016 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Media player for Flash SWF files.
 *
 * This player contains additional security restriction: it will only be used
 * if you add option core_media_player_swf::ALLOW = true.
 *
 * Code should only set this option if it has verified that the data was
 * embedded by a trusted user (e.g. in trust text).
 *
 * @package   media_swf
 * @copyright 2016 Marina Glancy
 * @author    2011 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class media_swf_plugin extends core_media_player {
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
    <param name="allowfullscreen" value="true" />
<!--[if !IE]><!-->
    <object type="application/x-shockwave-flash" data="$url" width="$width" height="$height">
      <param name="controller" value="true" />
      <param name="autoplay" value="true" />
      <param name="loop" value="false" />
      <param name="scale" value="aspect" />
      <param name="base" value="." />
      <param name="allowscriptaccess" value="never" />
      <param name="allowfullscreen" value="true" />
<!--<![endif]-->
$fallback
<!--[if !IE]><!-->
    </object>
<!--<![endif]-->
  </object>
</span>
OET;

        return $output;
    }

    public function get_supported_extensions() {
        return array('.swf');
    }

    public function list_supported_urls(array $urls, array $options = array()) {
        // Not supported unless the creator is trusted.
        if (empty($options[core_media_manager::OPTION_TRUSTED])) {
            return array();
        }
        return parent::list_supported_urls($urls, $options);
    }

    /**
     * Default rank
     * @return int
     */
    public function get_rank() {
        return 30;
    }
}
