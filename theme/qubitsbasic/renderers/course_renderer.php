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
 * Renderers to align Moodle's HTML with that expected by Bootstrap
 *
 * @package    theme_qubitsbasic
 * @copyright  2023 Qubits Dev Team.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . "/course/renderer.php");
require_once($CFG->dirroot. '/course/lib.php');
use core_course\external\course_summary_exporter;

class theme_qubitsbasic_core_course_renderer extends core_course_renderer {

    private function pre_course_category(){
        global $CFG, $DB;
        $data = new stdClass;
        $data->course_id = [];
        $data->qubitssitename = $CFG->cursitesettings->name;
        $data->site_id = $siteid = $CFG->cursitesettings->id;
        $qubitsdbcourses = $DB->get_record("local_qubits_course", array('site_id' => $siteid));
        if($qubitsdbcourses){
            $data->course_id = explode(",", $qubitsdbcourses->course_id);
        }
        $filters["search"] = optional_param('search', '', PARAM_RAW);
        $filters["page"] = optional_param('page', 0, PARAM_INT);
        $filters["perpage"] = optional_param('perpage', 10, PARAM_INT);
        $filters["sortcolumn"] = optional_param('sortcolumn', "", PARAM_TEXT);
        $filters["sortdir"] = optional_param('sortdir', "asc", PARAM_TEXT);
        return array($data, $filters);
    }

    public function course_category($category) {
        global $OUTPUT, $CFG, $DB;
        list($data, $filters) = $this->pre_course_category();
        $courseids = empty($data->course_id) ? array() : $data->course_id;
        $asearch = array("recursive" => true, "search" => $filters["search"]);
        $aoptions = $this->process_filters($filters);
        $allcourses = core_course_category::get(0)->search_courses($asearch, $aoptions);
        $tenantcourses = array();
        foreach($allcourses as $onecourse){
            $onecourseid = $onecourse->id;
            if( $CFG->cursitesettings->ismainsite=="yes" || in_array($onecourseid, $courseids) !== false ){
                $courseimage = course_summary_exporter::get_course_image($onecourse);
                if (!$courseimage) {
                    $courseimage = $OUTPUT->get_generated_image_for_id($onecourse->id);
                }

                $tenantcourses[] = array(
                    "id" => $onecourse->id,
                    "fullname" => $onecourse->fullname,
                    "shortname" => $onecourse->shortname,
                    'viewlink' => new moodle_url("/course/view.php", array("id" => $onecourse->id )),
                    "courseimage" => $courseimage
                );
            }
        }
        $totalcount = count($tenantcourses);
        $filters["siteid"] = $data->site_id;
        $url = new moodle_url($CFG->wwwroot . '/course/index.php', $filters);
        $pagebar = $OUTPUT->paging_bar($totalcount, $filters["page"], $filters["perpage"], $url);
        $limit = $filters["perpage"];
        $offset = $filters["page"] * $filters["perpage"];
        $curpagetenantcourses = array_slice($tenantcourses, $offset, $limit);

        $templatecontext = [
            "tenantcourses" => $curpagetenantcourses,
            "pagebar" => $pagebar,
            "siteid" => $data->site_id,
            "sortcolumn" => $filters["sortcolumn"],
            "sortdir" => $filters["sortdir"],
            "search" => $filters["search"],
        ];
        $output = $this->render_from_template('course/courselist', $templatecontext);
        return $output;
    }

    private function process_filters($filters){
        $aoptions = array();
        $sortdir = ($filters["sortdir"]=="desc") ? -1 : 1 ;
        switch ($filters["sortcolumn"]) {
            case "coursefullname":
                $aoptions["sort"] = array("fullname" => $sortdir);
                break;
            case "courseshortname":
                $aoptions["sort"] = array("shortname" => $sortdir);
                break;
            default:
                $aoptions["sort"] = array();
                break;
        }
        return $aoptions;
    }

}