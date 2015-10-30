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
 * Core Report class of graphs reporting plugin
 *
 * @package    scormreport_graphs
 * @copyright  2012 Ankit Kumar Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
/**
 * Main class to control the graphs reporting
 *
 * @package    scormreport_graphs
 * @copyright  2012 Ankit Kumar Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class scorm_graphs_report extends scorm_default_report {
    /**
     * Displays the full report
     *
     * @param stdClass $scorm full SCORM object
     * @param stdClass $cm - full course_module object
     * @param stdClass $course - full course object
     * @param string $download - type of download being requested
     */
    function display($scorm, $cm, $course, $download) {
        global $DB, $OUTPUT, $PAGE;

        if ($groupmode = groups_get_activity_groupmode($cm)) {   // Groups are being used
            groups_print_activity_menu($cm, new moodle_url($PAGE->url));
        }

        if ($scoes = $DB->get_records('scorm_scoes', array("scorm"=>$scorm->id), 'sortorder, id')) {
            foreach ($scoes as $sco) {
                if ($sco->launch != '') {
                    $imageurl = new moodle_url('/mod/scorm/report/graphs/graph.php',
                            array('scoid' => $sco->id));
                    $graphname = $sco->title;
                    echo $OUTPUT->heading($graphname, 3);
                    echo html_writer::tag('div', html_writer::empty_tag('img',
                            array('src' => $imageurl, 'alt' => $graphname)),
                            array('class' => 'graph'));
                }
            }
        }
    }
}
