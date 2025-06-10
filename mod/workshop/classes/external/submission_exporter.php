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
 * Class for exporting submission data.
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
 * Class for exporting submission data.
 *
 * @copyright  2017 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class submission_exporter extends exporter {

    protected static function define_properties() {

        return array(
            'id' => array(
                'type' => PARAM_INT,
                'description' => 'The primary key of the record.',
            ),
            'workshopid' => array(
                'type' => PARAM_INT,
                'description' => 'The id of the workshop instance.',
            ),
            'example' => array(
                'type' => PARAM_BOOL,
                'null' => NULL_ALLOWED,
                'default' => false,
                'description' => 'Is this submission an example from teacher.',
            ),
            'authorid' => array(
                'type' => PARAM_INT,
                'description' => 'The author of the submission.',
            ),
            'timecreated' => array(
                'type' => PARAM_INT,
                'description' => 'Timestamp when the work was submitted for the first time.',
            ),
            'timemodified' => array(
                'type' => PARAM_INT,
                'description' => 'Timestamp when the submission has been updated.',
            ),
            'title' => array(
                'type' => PARAM_RAW,
                'description' => 'The submission title.',
            ),
            'content' => array(
                'type' => PARAM_RAW,
                'null' => NULL_ALLOWED,
                'description' => 'Submission text.',
            ),
            'contentformat' => array(
                'choices' => array(FORMAT_HTML, FORMAT_MOODLE, FORMAT_PLAIN, FORMAT_MARKDOWN),
                'type' => PARAM_INT,
                'default' => FORMAT_MOODLE,
                'description' => 'Submission text format.',
            ),
            'contenttrust' => array(
                'type' => PARAM_INT,
                'default' => 0,
                'description' => 'The trust mode of the data.',
            ),
            'attachment' => array(
                'type' => PARAM_INT,
                'null' => NULL_ALLOWED,
                'default' => 0,
                'description' => 'Used by File API file_postupdate_standard_filemanager.',
            ),
            'grade' => array(
                'type' => PARAM_FLOAT,
                'null' => NULL_ALLOWED,
                'description' => 'Aggregated grade for the submission. The grade is a decimal number from interval 0..100.
                    If NULL then the grade for submission has not been aggregated yet.',
                'optional' => true,
            ),
            'gradeover' => array(
                'type' => PARAM_FLOAT,
                'null' => NULL_ALLOWED,
                'description' => 'Grade for the submission manually overridden by a teacher. Grade is always from interval 0..100.
                    If NULL then the grade is not overriden.',
                'optional' => true,
            ),
            'gradeoverby' => array(
                'type' => PARAM_INT,
                'null' => NULL_ALLOWED,
                'description' => 'The id of the user who has overridden the grade for submission.',
                'optional' => true,
            ),
            'feedbackauthor' => array(
                'type' => PARAM_RAW,
                'null' => NULL_ALLOWED,
                'description' => 'Teacher comment/feedback for the author of the submission, for example describing the reasons
                    for the grade overriding.',
                'optional' => true,
            ),
            'feedbackauthorformat' => array(
                'choices' => array(FORMAT_HTML, FORMAT_MOODLE, FORMAT_PLAIN, FORMAT_MARKDOWN),
                'type' => PARAM_INT,
                'default' => FORMAT_MOODLE,
                'description' => 'Feedback text format.',
            ),
            'timegraded' => array(
                'type' => PARAM_INT,
                'null' => NULL_ALLOWED,
                'description' => 'The timestamp when grade or gradeover was recently modified.',
                'optional' => true,
            ),
            'published' => array(
                'type' => PARAM_BOOL,
                'null' => NULL_ALLOWED,
                'default' => false,
                'description' => 'Shall the submission be available to other when the workshop is closed.',
            ),
            'late' => array(
                'type' => PARAM_INT,
                'default' => 0,
                'description' => 'Has this submission been submitted after the deadline or during the assessment phase?',
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
            'contentfiles' => array(
                'type' => external_files::get_properties_for_exporter(),
                'multiple' => true,
                'optional' => true
            ),
            'attachmentfiles' => array(
                'type' => external_files::get_properties_for_exporter(),
                'multiple' => true,
                'optional' => true
            ),
        );
    }

    protected function get_other_values(renderer_base $output) {
        $context = $this->related['context'];

        if (!empty($this->data->content)) {
            $values['contentfiles'] =
                external_util::get_area_files($context->id, 'mod_workshop', 'submission_content', $this->data->id);
        }

        $values['attachmentfiles'] =
                external_util::get_area_files($context->id, 'mod_workshop', 'submission_attachment', $this->data->id);

        return $values;
    }

    /**
     * Get the formatting parameters for the content.
     *
     * @return array
     */
    protected function get_format_parameters_for_content() {
        return [
            'component' => 'mod_workshop',
            'filearea' => 'submission_content',
            'itemid' => $this->data->id,
        ];
    }
}
