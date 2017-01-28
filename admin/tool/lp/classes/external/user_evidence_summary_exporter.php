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
 * Class for exporting user evidence with all competencies.
 *
 * @package    tool_lp
 * @copyright  2016 Serge Gauthier - <serge.gauthier.2@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_lp\external;
defined('MOODLE_INTERNAL') || die();

use moodle_url;
use renderer_base;
use core_files\external\stored_file_exporter;
use core_competency\external\performance_helper;

/**
 * Class for exporting user evidence with all competencies.
 *
 * @copyright  2016 Serge Gauthier - <serge.gauthier.2@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_evidence_summary_exporter extends \core\external\persistent_exporter {

    protected static function define_class() {
        return \core_competency\user_evidence::class;
    }

    protected static function define_other_properties() {
        return array(
            'canmanage' => array(
                'type' => PARAM_BOOL
            ),
            'filecount' => array(
                'type' => PARAM_INT
            ),
            'files' => array(
                'type' => stored_file_exporter::read_properties_definition(),
                'multiple' => true
            ),
            'hasurlorfiles' => array(
                'type' => PARAM_BOOL
            ),
            'urlshort' => array(
                'type' => PARAM_TEXT
            ),
            'competencycount' => array(
                'type' => PARAM_INT
            ),
            'usercompetencies' => array(
                'type' => user_evidence_competency_summary_exporter::read_properties_definition(),
                'optional' => true,
                'multiple' => true
            ),
            'userhasplan' => array(
                'type' => PARAM_BOOL
            ),
        );
    }

    protected function get_other_values(renderer_base $output) {
        $urlshort = '';
        $url = $this->persistent->get('url');
        if (!empty($url)) {
            $murl = new moodle_url($url);
            $shorturl = preg_replace('@^https?://(www\.)?@', '', $murl->out(false));
            $urlshort = shorten_text($shorturl, 30, true);
        }

        $files = array();
        $storedfiles = $this->persistent->get_files();
        if (!empty($storedfiles)) {
            foreach ($storedfiles as $storedfile) {
                $fileexporter = new stored_file_exporter($storedfile, array('context' => $this->related['context']));
                $files[] = $fileexporter->export($output);
            }
        }

        $userevidencecompetencies = array();
        $usercompetencies = $this->persistent->get_user_competencies();
        $helper = new performance_helper();
        foreach ($usercompetencies as $usercompetency) {
            $competency = $usercompetency->get_competency();

            $context = $helper->get_context_from_competency($competency);
            $framework = $helper->get_framework_from_competency($competency);
            $scale = $helper->get_scale_from_competency($competency);

            $related = array('competency' => $competency,
                             'usercompetency' => $usercompetency,
                             'scale' => $scale,
                             'context' => $context);

            $userevidencecompetencysummaryexporter = new user_evidence_competency_summary_exporter(null, $related);

            $userevidencecompetencies[] = $userevidencecompetencysummaryexporter->export($output);
        }

        $values = array(
            'canmanage' => $this->persistent->can_manage(),
            'filecount' => count($files),
            'files' => $files,
            'userhasplan' => $this->persistent->user_has_plan(),
            'hasurlorfiles' => !empty($files) || !empty($url),
            'urlshort' => $urlshort,
            'competencycount' => count($userevidencecompetencies),
            'usercompetencies' => $userevidencecompetencies
        );

        return $values;
    }

}
