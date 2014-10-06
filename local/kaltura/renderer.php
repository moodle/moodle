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
 * My Media display library
 *
 * @package    local
 * @subpackage mymedia
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once(dirname(dirname(dirname(__FILE__))) . '/lib/tablelib.php');

class local_kaltura_renderer extends plugin_renderer_base {

    /**
     * Generate the HTML for the iframe
     *
     * @return string The HTML iframe
     */
    public function render_recent_courses() {
        $html = '<div class="resourcecontent resourcegeneral">
                    <iframe id="resourceobject" src="courses.php?action=recent_courses" width="700" height="700"></iframe>
                </div>';

        return $html;
    }

    /**
     * Generate the HTML for the course search textbox
     *
     * @return string The HTML for the coruse search textbox
     */
    public function render_course_search() {
        global $PAGE;

        $jsmodule = array(
            'name' => 'local_kaltura',
            'fullpath' => '/local/kaltura/js/kaltura.js'
        );

        $PAGE->requires->js_init_call('M.local_kaltura.search_course', array(), true, $jsmodule);

        $html = '<div id="kaltura_course" style="margin-bottom: 10px">
                    <label for="kaltura_search_txt">'.get_string('course_name', 'local_kaltura').'</label>
                    <input id="kaltura_search_txt" type="text" size="30" />
                    <input id="kaltura_search_btn" type="button" value="'.get_string('search', 'local_kaltura').'" />
                    <input id="kaltura_clear_btn" type="button" value="'.get_string('clear', 'local_kaltura').'" />
                </div>';

        return $html;
    }

    /**
     * Render the Moodle courses
     *
     * @param array $courses An array of courses
     * @param string $query The course being searched for
     * @param string $action The action being performed: search or recent course listing
     * @return mixed The HTML generated courses on success; false, otherwise
     */
    public function render_courses($courses, $query = null, $action) {
        $html = '';
        $report_url = get_config(KALTURA_PLUGIN_NAME, 'report_uri');

        if (!is_array($courses)) {
            return $html;
        }

        if (count($courses) == 0 && $action == 'search') {
            return get_string('no_course_result', 'local_kaltura', $query);
        }

        if (count($courses) == 0 && $action == 'recent_courses') {
            return get_string('no_recent_course', 'local_kaltura');
        }

        if ($action == 'search') {
            $html .= '<p>'.get_string('found_course', 'local_kaltura', count($courses)).'</p>';
        }

        if ($action == 'recent_courses') {
            $html .= '<p>'.get_string('recent_course_view', 'local_kaltura').'</p>';
        }

        foreach ($courses as $course) {
            $session = local_kaltura_generate_weak_kaltura_session($course->id, $course->fullname);
            if (empty($session)) {
                return false;
            }
            if (empty($course->shortname)) {
                $html .= "<a href='{$report_url}/index.php/plugin/CategoryMediaReportAction?hpks={$session}'>".$course->fullname."</a><br />";
            } else {
                $html .= "<a href='{$report_url}/index.php/plugin/CategoryMediaReportAction?hpks={$session}'>".$course->fullname."</a>".' ('.$course->shortname.')'."<br />";
            }
        }

        return $html;
    }

}
