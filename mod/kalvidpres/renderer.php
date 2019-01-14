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
 * Kaltura video presentation renderer file.
 *
 * @package    mod_kalvidpres
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  (C) 2014 Remote Learner.net Inc http://www.remote-learner.net
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');
}

require_once(dirname(dirname(dirname(__FILE__))).'/local/kaltura/locallib.php');

class mod_kalvidpres_renderer extends plugin_renderer_base {
    /**
     * This function displays the iframe markup.
     * @param object $kalvidpres A Kaltura video resrouce instance object.
     * @param int $courseid A course id.
     * @return string HTML markup.
     */
    public function display_iframe($kalvidpres, $courseid) {
        $params = array(
            'courseid' => $courseid,
            'height' => $kalvidpres->height,
            'width' => $kalvidpres->width,
            'withblocks' => 0,
            'source' => $kalvidpres->source
        );
        $url = new moodle_url('/mod/kalvidpres/lti_launch.php', $params);

        $attr = array(
            'id' => 'contentframe',
            'height' => '100%',
            'width' => $kalvidpres->width,
            'src' => $url->out(false),
            'allowfullscreen' => 'true',
            'allow' => 'autoplay *; fullscreen *; encrypted-media *; camera *; microphone *;',
        );

        $output = html_writer::tag('iframe', '', $attr);
        $output = html_writer::tag('div', $output, array('id' => 'kalvid_content'));
        return $output;
    }
}
