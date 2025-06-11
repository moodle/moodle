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
 * Class for exporting lpmonitoring_competency_detail data.
 *
 * @package    report_lpmonitoring
 * @author     Serge Gauthier <serge.gauthier.2@umontreal.ca>
 * @copyright  2016 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace report_lpmonitoring\external;

use renderer_base;
use core_competency\user_evidence;
use core_competency\user_competency;
use tool_lp\external\competency_path_exporter;
use report_lpmonitoring\external\linked_course_exporter;
use report_lpmonitoring\external\scale_competency_item_exporter;
use report_lpmonitoring\external\report_user_evidence_summary_exporter;


/**
 * Class for exporting lpmonitoring_competency_detail data.
 *
 * @author     Serge Gauthier <serge.gauthier.2@umontreal.ca>
 * @copyright  2016 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class lpmonitoring_competency_detail_exporter extends \core\external\exporter {

    /**
     * Return the list of additional properties used only for display.
     *
     * @return array other properties
     */
    public static function define_other_properties() {
        return [
            'competencyid' => [
                'type' => PARAM_INT,
            ],
            'scaleid' => [
                'type' => PARAM_INT,
            ],
            'isproficient' => [
                'type' => PARAM_BOOL,
            ],
            'isnotproficient' => [
                'type' => PARAM_BOOL,
            ],
            'isnotrated' => [
                'type' => PARAM_BOOL,
            ],
            'finalgradename' => [
                'type' => PARAM_RAW,
                'default' => null,
                'null' => NULL_ALLOWED,
            ],
            'finalgradecolor' => [
                'type' => PARAM_RAW,
                'default' => null,
                'null' => NULL_ALLOWED,
            ],
            'cangrade' => [
                'type' => PARAM_BOOL,
            ],
            'hasevidence' => [
                'type' => PARAM_BOOL,
            ],
            'hasrating' => [
                'type' => PARAM_BOOL,
            ],
            'hasratingincms' => [
                'type' => PARAM_BOOL,
            ],
            'nbevidence' => [
                'type' => PARAM_INT,
            ],
            'listevidence' => [
                'type' => report_user_evidence_summary_exporter::read_properties_definition(),
                'multiple' => true,
            ],
            'nbcoursestotal' => [
                'type' => PARAM_INT,
            ],
            'nbcoursesrated' => [
                'type' => PARAM_INT,
            ],
            'nbcmstotal' => [
                'type' => PARAM_INT,
            ],
            'nbcmsrated' => [
                'type' => PARAM_INT,
            ],
            'listtotalcourses' => [
                'type' => linked_course_exporter::read_properties_definition(),
                'multiple' => true,
            ],
            'listtotalcms' => [
                'type' => linked_cm_exporter::read_properties_definition(),
                'multiple' => true,
            ],
            'scalecompetencyitems' => [
                'type' => scale_competency_item_exporter::read_properties_definition(),
                'multiple' => true,
            ],
            'competencypath' => [
                'type' => competency_path_exporter::read_properties_definition(),
                'multiple' => true,
            ],
        ];
    }

    /**
     * Get the additional values to inject while exporting.
     *
     * @param renderer_base $output The renderer.
     * @return array Keys are the property names, values are their values.
     */
    protected function get_other_values(renderer_base $output) {

        $data = $this->data;
        $result = new \stdClass();

        $result->competencyid = $data->competency->get('id');
        $shoulddisplay = isset($data->displayrating) ? $data->displayrating : true;
        $uc = (isset($data->usercompetency)) ? $data->usercompetency : $data->usercompetencyplan;
        // Set the scaleid.
        $result->scaleid = $data->competency->get_scale()->id;
        // Proficiency and final grade.
        $proficiency = $uc->get('proficiency');
        $result->isnotrated = false;
        $result->isproficient = false;
        $result->isnotproficient = false;
        if (!isset($proficiency) || !$shoulddisplay) {
            $result->isnotrated = true;
        } else {
            if ($proficiency) {
                $result->isproficient = true;
            } else {
                $result->isnotproficient = true;
            }
            $grade = $uc->get('grade');
            $result->finalgradename = $data->scale[$grade];
            $result->finalgradecolor = $data->reportscaleconfig[$grade - 1]->color;
        }

        // If user can grade.
        $result->cangrade = user_competency::can_grade_user($uc->get('userid'));

        // Prior learning evidences.
        $result->nbevidence = count($data->userevidences);
        $result->hasevidence = $result->nbevidence > 0 ? true : false;
        $result->listevidence = [];
        foreach ($data->userevidences as $userevidence) {
            $userevidencerecord = new user_evidence($userevidence->id);
            $context = $userevidencerecord->get_context();
            $userevidencesummaryexporter = new report_user_evidence_summary_exporter($userevidencerecord,
                    ['context' => $context]);
            $result->listevidence[] = $userevidencesummaryexporter->export($output);
        }

        // Liste of courses linked to the competency.
        $result->nbcoursestotal = 0;
        $result->nbcoursesrated = 0;
        $result->listtotalcourses = [];

        foreach ($data->courses as $coursedata) {
            $relatedinfo = new \stdClass();
            $relatedinfo->userid = $data->userid;
            $relatedinfo->competencyid = $data->competency->get('id');
            $totalcourseexporter = new linked_course_exporter($coursedata, ['relatedinfo' => $relatedinfo]);
            $totalcourse = $totalcourseexporter->export($output);
            if ($totalcourse->rated) {
                $result->nbcoursesrated++;
            }
            $result->nbcoursestotal++;
            $result->listtotalcourses[] = $totalcourse;
        }
        $result->hasrating = $result->nbcoursesrated > 0 ? true : false;

        // List of courses modules linked to the competency.
        $result->nbcmstotal = 0;
        $result->nbcmsrated = 0;
        $result->listtotalcms = [];

        foreach ($data->cms as $cmdata) {
            $relatedinfo = new \stdClass();
            $relatedinfo->userid = $data->userid;
            $relatedinfo->competencyid = $data->competency->get('id');
            $totalcmexporter = new linked_cm_exporter($cmdata, ['relatedinfo' => $relatedinfo]);
            $totalcm = $totalcmexporter->export($output);
            if ($totalcm->rated) {
                $result->nbcmsrated++;
            }
            $result->nbcmstotal++;
            $result->listtotalcms[] = $totalcm;
        }
        $result->hasratingincms = $result->nbcmsrated > 0 ? true : false;

        // Information for each scale value.
        $result->scalecompetencyitems = [];
        foreach ($data->scale as $id => $scalename) {
            $scaleinfo = new \stdClass();
            $scaleinfo->value = $id;
            $scaleinfo->name = $scalename;
            $scaleinfo->color = $data->reportscaleconfig[$id - 1]->color;

            $relatedinfo = new \stdClass();
            $relatedinfo->userid = $data->userid;
            $relatedinfo->competencyid = $data->competency->get('id');

            $scalecompetencyitemexporter = new scale_competency_item_exporter($scaleinfo, [
                'courses' => $data->courses,
                'relatedinfo' => $relatedinfo,
                'cms' => $data->cms,
            ]);
            $result->scalecompetencyitems[] = $scalecompetencyitemexporter->export($output);
        }

        // Competency path.
        $competencypathexporter = new competency_path_exporter([
            'ancestors' => $data->competency->get_ancestors(),
            'framework' => $data->framework,
            'context' => $data->framework->get_context(),
        ]);
        $result->competencypath = [];
        $result->competencypath[] = $competencypathexporter->export($output);

        return (array) $result;
    }

}
