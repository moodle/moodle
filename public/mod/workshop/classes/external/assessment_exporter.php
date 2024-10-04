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
 * Class for exporting assessment data.
 *
 * @package    mod_workshop
 * @copyright  2017 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_workshop\external;
defined('MOODLE_INTERNAL') || die();

use core\external\exporter;
use renderer_base;
use core_external\util as external_util;
use core_external\external_files;

/**
 * Class for exporting assessment data.
 *
 * @copyright  2017 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assessment_exporter extends exporter {

    protected static function define_properties() {

        return array(
            'id' => array(
                'type' => PARAM_INT,
                'description' => 'The primary key of the record.',
            ),
            'submissionid' => array(
                'type' => PARAM_INT,
                'description' => 'The id of the assessed submission',
            ),
            'reviewerid' => array(
                'type' => PARAM_INT,
                'description' => 'The id of the reviewer who makes this assessment',
            ),
            'weight' => array(
                'type' => PARAM_INT,
                'default' => 1,
                'description' => 'The weight of the assessment for the purposes of aggregation',
            ),
            'timecreated' => array(
                'type' => PARAM_INT,
                'null' => NULL_ALLOWED,
                'default' => 0,
                'description' => 'If 0 then the assessment was allocated but the reviewer has not assessed yet.
                    If greater than 0 then the timestamp of when the reviewer assessed for the first time',
            ),
            'timemodified' => array(
                'type' => PARAM_INT,
                'null' => NULL_ALLOWED,
                'default' => 0,
                'description' => 'If 0 then the assessment was allocated but the reviewer has not assessed yet.
                    If greater than 0 then the timestamp of when the reviewer assessed for the last time',
            ),
            'grade' => array(
                'type' => PARAM_FLOAT,
                'null' => NULL_ALLOWED,
                'description' => 'The aggregated grade for submission suggested by the reviewer.
                    The grade 0..100 is computed from the values assigned to the assessment dimensions fields. If NULL then it has not been aggregated yet.',
            ),
            'gradinggrade' => array(
                'type' => PARAM_FLOAT,
                'null' => NULL_ALLOWED,
                'description' => 'The computed grade 0..100 for this assessment. If NULL then it has not been computed yet.',
            ),
            'gradinggradeover' => array(
                'type' => PARAM_FLOAT,
                'null' => NULL_ALLOWED,
                'description' => 'Grade for the assessment manually overridden by a teacher.
                    Grade is always from interval 0..100. If NULL then the grade is not overriden.',
            ),
            'gradinggradeoverby' => array(
                'type' => PARAM_INT,
                'null' => NULL_ALLOWED,
                'description' => 'The id of the user who has overridden the grade for submission.',
            ),
            'feedbackauthor' => array(
                'type' => PARAM_RAW,
                'null' => NULL_ALLOWED,
                'description' => 'The comment/feedback from the reviewer for the author.',
            ),
            'feedbackauthorformat' => array(
                'choices' => array(FORMAT_HTML, FORMAT_MOODLE, FORMAT_PLAIN, FORMAT_MARKDOWN),
                'type' => PARAM_INT,
                'default' => FORMAT_MOODLE,
                'description' => 'Feedback text format.',
            ),
            'feedbackauthorattachment' => array(
                'type' => PARAM_INT,
                'null' => NULL_ALLOWED,
                'default' => 0,
                'description' => 'Are there some files attached to the feedbackauthor field?
                    Sets to 1 by file_postupdate_standard_filemanager().',
            ),
            'feedbackreviewer' => array(
                'type' => PARAM_RAW,
                'null' => NULL_ALLOWED,
                'description' => 'The comment/feedback from the teacher for the reviewer.
                    For example the reason why the grade for assessment was overridden',
                'optional' => true,
            ),
            'feedbackreviewerformat' => array(
                'choices' => array(FORMAT_HTML, FORMAT_MOODLE, FORMAT_PLAIN, FORMAT_MARKDOWN),
                'type' => PARAM_INT,
                'default' => FORMAT_MOODLE,
                'description' => 'Feedback text format.',
            ),

            'feedbackauthorformat' => array(
                'choices' => array(FORMAT_HTML, FORMAT_MOODLE, FORMAT_PLAIN, FORMAT_MARKDOWN),
                'type' => PARAM_INT,
                'default' => FORMAT_MOODLE,
                'description' => 'Feedback text format.',
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
            'feedbackcontentfiles' => array(
                'type' => external_files::get_properties_for_exporter(),
                'multiple' => true,
            ),
            'feedbackattachmentfiles' => array(
                'type' => external_files::get_properties_for_exporter(),
                'multiple' => true,
            ),
        );
    }

    protected function get_other_values(renderer_base $output) {
        $context = $this->related['context'];

        $values['feedbackcontentfiles'] =
                external_util::get_area_files($context->id, 'mod_workshop', 'overallfeedback_content', $this->data->id);
        $values['feedbackattachmentfiles'] =
                external_util::get_area_files($context->id, 'mod_workshop', 'overallfeedback_attachment', $this->data->id);

        return $values;
    }

    /**
     * Get the formatting parameters for the content.
     *
     * @return array
     */
    protected function get_format_parameters_for_feedbackauthor() {
        return [
            'component' => 'mod_workshop',
            'filearea' => 'overallfeedback_content',
            'itemid' => $this->data->id,
        ];
    }
}
