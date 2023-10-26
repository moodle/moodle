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
 * Class for exporting partial workshop data.
 *
 * @package    mod_workshop
 * @copyright  2017 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_workshop\external;
defined('MOODLE_INTERNAL') || die();

use core\external\exporter;
use renderer_base;
use external_util;
use external_files;

/**
 * Class for exporting partial workshop data (some fields are only viewable by admins).
 *
 * @copyright  2017 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class workshop_summary_exporter extends exporter {

    protected static function define_properties() {

        return array(
            'id' => array(
                'type' => PARAM_INT,
                'description' => 'The primary key of the record.',
            ),
            'course' => array(
                'type' => PARAM_INT,
                'description' => 'Course id this workshop is part of.',
            ),
            'name' => array(
                'type' => PARAM_TEXT,
                'description' => 'Workshop name.',
            ),
            'intro' => array(
                'default' => '',
                'type' => PARAM_RAW,
                'description' => 'Workshop introduction text.',
                'null' => NULL_ALLOWED,
            ),
            'introformat' => array(
                'choices' => array(FORMAT_HTML, FORMAT_MOODLE, FORMAT_PLAIN, FORMAT_MARKDOWN),
                'type' => PARAM_INT,
                'default' => FORMAT_MOODLE,
                'description' => 'Workshop intro text format.',
            ),
            'lang' => array(
                'type' => PARAM_LANG,
                'description' => 'Forced activity language',
                'null' => NULL_ALLOWED,
            ),
            'instructauthors' => array(
                'type' => PARAM_RAW,
                'description' => 'Instructions for the submission phase.',
                'optional' => true,
                'null' => NULL_ALLOWED,
            ),
            'instructauthorsformat' => array(
                'choices' => array(FORMAT_HTML, FORMAT_MOODLE, FORMAT_PLAIN, FORMAT_MARKDOWN),
                'type' => PARAM_INT,
                'default' => FORMAT_MOODLE,
                'description' => 'Instructions text format.',
            ),
            'instructreviewers' => array(
                'type' => PARAM_RAW,
                'description' => 'Instructions for the assessment phase.',
                'optional' => true,
                'null' => NULL_ALLOWED,
            ),
            'instructreviewersformat' => array(
                'choices' => array(FORMAT_HTML, FORMAT_MOODLE, FORMAT_PLAIN, FORMAT_MARKDOWN),
                'type' => PARAM_INT,
                'default' => FORMAT_MOODLE,
                'description' => 'Instructions text format.',
            ),
            'timemodified' => array(
                'type' => PARAM_INT,
                'description' => 'The timestamp when the module was modified.',
                'optional' => true,
            ),
            'phase' => array(
                'type' => PARAM_INT,
                'default' => 0,
                'description' => 'The current phase of workshop (0 = not available, 1 = submission, 2 = assessment, 3 = closed).',
                'optional' => true,
            ),
            'useexamples' => array(
                'type' => PARAM_BOOL,
                'default' => false,
                'description' => 'Optional feature: students practise evaluating on example submissions from teacher.',
                'optional' => true,
            ),
            'usepeerassessment' => array(
                'type' => PARAM_BOOL,
                'default' => false,
                'description' => 'Optional feature: students perform peer assessment of others\' work.',
                'optional' => true,
            ),
            'useselfassessment' => array(
                'type' => PARAM_BOOL,
                'default' => false,
                'description' => 'Optional feature: students perform self assessment of their own work.',
                'optional' => true,
            ),
            'grade' => array(
                'type' => PARAM_FLOAT,
                'default' => 80,
                'description' => 'The maximum grade for submission.',
                'optional' => true,
            ),
            'gradinggrade' => array(
                'type' => PARAM_FLOAT,
                'default' => 20,
                'description' => 'The maximum grade for assessment.',
                'optional' => true,
            ),
            'strategy' => array(
                'type' => PARAM_PLUGIN,
                'description' => 'The type of the current grading strategy used in this workshop.',
                'optional' => true,
            ),
            'evaluation' => array(
                'type' => PARAM_PLUGIN,
                'description' => 'The recently used grading evaluation method.',
                'optional' => true,
            ),
            'gradedecimals' => array(
                'type' => PARAM_INT,
                'default' => 0,
                'description' => 'Number of digits that should be shown after the decimal point when displaying grades.',
                'optional' => true,
            ),
            'submissiontypetext' => array (
                'type' => PARAM_INT,
                'default' => 1,
                'description' => 'Indicates whether text is required as part of each submission. ' .
                        '0 for no, 1 for optional, 2 for required.',
                'optional' => true
            ),
            'submissiontypefile' => array (
                'type' => PARAM_INT,
                'default' => 1,
                'description' => 'Indicates whether a file upload is required as part of each submission. ' .
                        '0 for no, 1 for optional, 2 for required.',
                'optional' => true
            ),
            'nattachments' => array(
                'type' => PARAM_INT,
                'default' => 1,
                'description' => 'Maximum number of submission attachments.',
                'optional' => true,
            ),
            'submissionfiletypes' => array(
                'type' => PARAM_RAW,
                'description' => 'Comma separated list of file extensions.',
                'optional' => true,
                'null' => NULL_ALLOWED,
            ),
            'latesubmissions' => array(
                'type' => PARAM_BOOL,
                'default' => false,
                'description' => 'Allow submitting the work after the deadline.',
                'optional' => true,
            ),
            'maxbytes' => array(
                'type' => PARAM_INT,
                'default' => 100000,
                'description' => 'Maximum size of the one attached file.',
                'optional' => true,
            ),
            'examplesmode' => array(
                'type' => PARAM_INT,
                'default' => 0,
                'description' => '0 = example assessments are voluntary, 1 = examples must be assessed before submission,
                    2 = examples are available after own submission and must be assessed before peer/self assessment phase.',
                'optional' => true,
            ),
            'submissionstart' => array(
                'type' => PARAM_INT,
                'default' => 0,
                'description' => '0 = will be started manually, greater than 0 the timestamp of the start of the submission phase.',
                'optional' => true,
            ),
            'submissionend' => array(
                'type' => PARAM_INT,
                'default' => 0,
                'description' => '0 = will be closed manually, greater than 0 the timestamp of the end of the submission phase.',
                'optional' => true,
            ),
            'assessmentstart' => array(
                'type' => PARAM_INT,
                'default' => 0,
                'description' => '0 = will be started manually, greater than 0 the timestamp of the start of the assessment phase.',
                'optional' => true,
            ),
            'assessmentend' => array(
                'type' => PARAM_INT,
                'default' => 0,
                'description' => '0 = will be closed manually, greater than 0 the timestamp of the end of the assessment phase.',
                'optional' => true,
            ),
            'phaseswitchassessment' => array(
                'type' => PARAM_BOOL,
                'default' => false,
                'description' => 'Automatically switch to the assessment phase after the submissions deadline.',
                'optional' => true,
            ),
            'conclusion' => array(
                'type' => PARAM_RAW,
                'description' => 'A text to be displayed at the end of the workshop.',
                'optional' => true,
                'null' => NULL_ALLOWED,
            ),
            'conclusionformat' => array(
                'choices' => array(FORMAT_HTML, FORMAT_MOODLE, FORMAT_PLAIN, FORMAT_MARKDOWN),
                'type' => PARAM_INT,
                'default' => FORMAT_MOODLE,
                'description' => 'Workshop conclusion text format.',
            ),
            'overallfeedbackmode' => array(
                'type' => PARAM_INT,
                'default' => 1,
                'description' => 'Mode of the overall feedback support.',
                'optional' => true,
            ),
            'overallfeedbackfiles' => array(
                'type' => PARAM_INT,
                'default' => 0,
                'description' => 'Number of allowed attachments to the overall feedback.',
                'optional' => true,
            ),
            'overallfeedbackfiletypes' => array(
                'type' => PARAM_RAW,
                'description' => 'Comma separated list of file extensions.',
                'optional' => true,
                'null' => NULL_ALLOWED,
            ),
            'overallfeedbackmaxbytes' => array(
                'type' => PARAM_INT,
                'default' => 100000,
                'description' => 'Maximum size of one file attached to the overall feedback.',
                'optional' => true,
            ),
        );
    }

    protected static function define_related() {
        return array(
            'context' => 'context'
        );
    }

    protected static function define_other_properties() {
        return array(
            'coursemodule' => array(
                'type' => PARAM_INT
            ),
            'introfiles' => array(
                'type' => external_files::get_properties_for_exporter(),
                'multiple' => true
            ),
            'instructauthorsfiles' => array(
                'type' => external_files::get_properties_for_exporter(),
                'multiple' => true,
                'optional' => true
            ),
            'instructreviewersfiles' => array(
                'type' => external_files::get_properties_for_exporter(),
                'multiple' => true,
                'optional' => true
            ),
            'conclusionfiles' => array(
                'type' => external_files::get_properties_for_exporter(),
                'multiple' => true,
                'optional' => true
            ),
        );
    }

    protected function get_other_values(renderer_base $output) {
        $context = $this->related['context'];

        $values = array(
            'coursemodule' => $context->instanceid,
        );

        $values['introfiles'] = external_util::get_area_files($context->id, 'mod_workshop', 'intro', false, false);

        if (!empty($this->data->instructauthors)) {
            $values['instructauthorsfiles'] = external_util::get_area_files($context->id, 'mod_workshop', 'instructauthors');
        }

        if (!empty($this->data->instructreviewers)) {
            $values['instructreviewersfiles'] = external_util::get_area_files($context->id, 'mod_workshop', 'instructreviewers');
        }

        if (!empty($this->data->conclusion)) {
            $values['conclusionfiles'] = external_util::get_area_files($context->id, 'mod_workshop', 'conclusion');
        }

        return $values;
    }

    /**
     * Get the formatting parameters for the intro.
     *
     * @return array with the formatting parameters
     */
    protected function get_format_parameters_for_intro() {
        return [
            'component' => 'mod_workshop',
            'filearea' => 'intro',
            'options' => array('noclean' => true),
        ];
    }

    /**
     * Get the formatting parameters for the instructauthors.
     *
     * @return array with the formatting parameters
     */
    protected function get_format_parameters_for_instructauthors() {
        return [
            'component' => 'mod_workshop',
            'filearea' => 'instructauthors',
            'itemid' => 0
        ];
    }

    /**
     * Get the formatting parameters for the instructreviewers.
     *
     * @return array with the formatting parameters
     */
    protected function get_format_parameters_for_instructreviewers() {
        return [
            'component' => 'mod_workshop',
            'filearea' => 'instructreviewers',
            'itemid' => 0
        ];
    }

    /**
     * Get the formatting parameters for the conclusion.
     *
     * @return array with the formatting parameters
     */
    protected function get_format_parameters_for_conclusion() {
        return [
            'component' => 'mod_workshop',
            'filearea' => 'conclusion',
            'itemid' => 0
        ];
    }
}
