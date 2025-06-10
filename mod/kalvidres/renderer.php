<?php
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
 * Kaltura video resource renderer file.
 *
 * @package    mod_kalvidres
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  (C) 2014 Remote Learner.net Inc http://www.remote-learner.net
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');
}

require_once(dirname(dirname(dirname(__FILE__))).'/local/kaltura/locallib.php');

class mod_kalvidres_renderer extends plugin_renderer_base {
    /**
     * This function displays the title of the video in bold.
     * @param string $title The title of the video.
     * @return string HTML markup.
     */
    public function display_mod_info($title) {
        $output = '';

        $attr = array('for' => 'video_name');
        $output .= html_writer::start_tag('b');
        $output .= html_writer::tag('div', $title);
        $output .= html_writer::end_tag('b');
        $output .= html_writer::empty_tag('br');

        return $output;
    }

    /**
     * This function displays the iframe markup.
     * @param object $kalvidres A Kaltura video resource instance object.
     * @param int $courseid A course id.
     * @return string HTML markup.
     */
    public function display_iframe($kalvidres, $courseid) {
        $params = array(
            'courseid' => $courseid,
            'height' => $kalvidres->height,
            'width' => $kalvidres->width,
            'withblocks' => 0,
            'source' => $kalvidres->source
        );
        $url = new moodle_url('/mod/kalvidres/lti_launch.php', $params);

        $attr = array(
            'id' => 'contentframe',
            'class' => 'kaltura-player-iframe',
            'height' => '100%',
            'width' => $kalvidres->width,
            'src' => $url->out(false),
            'allowfullscreen' => 'true',
            'allow' => 'autoplay *; fullscreen *; encrypted-media *; camera *; microphone *;',
        );

        $iframe = html_writer::tag('iframe', '', $attr);
        $iframeContainer = html_writer::tag('div', $iframe, array(
            'class' => 'kaltura-player-container'
        ));

        return $iframeContainer;
    }
}