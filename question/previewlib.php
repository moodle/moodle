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
 * Library functions used by question/preview.php.
 *
 * @package    core_question
 * @subpackage questionengine
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @deprecated since Moodle 4.0
 * @see qbank_previewquestion\form\preview_options_form
 * @see qbank_previewquestion\output\question_preview_options
 */

use qbank_previewquestion\question_preview_options;

/**
 * Called via pluginfile.php -> question_pluginfile to serve files belonging to
 * a question in a question_attempt when that attempt is a preview.
 *
 * @package  core_question
 * @category files
 * @param stdClass $course course settings object
 * @param stdClass $context context object
 * @param string $component the name of the component we are serving files for.
 * @param string $filearea the name of the file area.
 * @param int $qubaid the question_usage this image belongs to.
 * @param int $slot the relevant slot within the usage.
 * @param array $args the remaining bits of the file path.
 * @param bool $forcedownload whether the user must be forced to download the file.
 * @param array $fileoptions
 * @return void false if file not found, does not return if found - justsend the file
 * @deprecated since Moodle 4.0
 * @see qbank_previewquestion\helper::question_preview_question_pluginfile()
 * @todo Final deprecation on Moodle 4.4 MDL-72438
 */
function question_preview_question_pluginfile($course, $context, $component,
        $filearea, $qubaid, $slot, $args, $forcedownload, $fileoptions) {
    debugging('Function question_preview_question_pluginfile() has been deprecated and moved to qbank_previewquestion plugin,
     please use qbank_previewquestion\helper::question_preview_question_pluginfile() instead.', DEBUG_DEVELOPER);
    qbank_previewquestion\helper::question_preview_question_pluginfile($course, $context,
            $component, $filearea, $qubaid, $slot, $args, $forcedownload, $fileoptions);
}

/**
 * The the URL to use for actions relating to this preview.
 * @param int $questionid the question being previewed.
 * @param int $qubaid the id of the question usage for this preview.
 * @param question_preview_options $options the options in use.
 * @param context $context
 * @deprecated since Moodle 4.0
 * @see qbank_previewquestion\helper::question_preview_action_url()
 * @todo Final deprecation on Moodle 4.4 MDL-72438
 */
function question_preview_action_url($questionid, $qubaid,
        question_preview_options $options, $context) {
    debugging('Function question_preview_action_url() has been deprecated and moved to qbank_previewquestion plugin,
     please use qbank_previewquestion\helper::question_preview_action_url() instead.', DEBUG_DEVELOPER);
    qbank_previewquestion\helper::question_preview_action_url($questionid, $qubaid, $options, $context);
}

/**
 * The the URL to use for actions relating to this preview.
 * @param int $questionid the question being previewed.
 * @param context $context the current moodle context.
 * @param int $previewid optional previewid to sign post saved previewed answers.
 * @deprecated since Moodle 4.0
 * @see qbank_previewquestion\helper::question_preview_form_url()
 * @todo Final deprecation on Moodle 4.4 MDL-72438
 */
function question_preview_form_url($questionid, $context, $previewid = null) {
    debugging('Function question_preview_form_url() has been deprecated and moved to qbank_previewquestion plugin,
     please use qbank_previewquestion\helper::question_preview_form_url() instead.', DEBUG_DEVELOPER);
    qbank_previewquestion\helper::question_preview_form_url($questionid, $context, $previewid);
}

/**
 * Delete the current preview, if any, and redirect to start a new preview.
 * @param int $previewid
 * @param int $questionid
 * @param object $displayoptions
 * @param object $context
 * @deprecated since Moodle 4.0
 * @see qbank_previewquestion\helper::restart_preview()
 * @todo Final deprecation on Moodle 4.4 MDL-72438
 */
function restart_preview($previewid, $questionid, $displayoptions, $context) {
    debugging('Function restart_preview() has been deprecated and moved to qbank_previewquestion plugin,
     please use qbank_previewquestion\helper::restart_preview() instead.', DEBUG_DEVELOPER);
    qbank_previewquestion\helper::restart_preview($previewid, $questionid, $displayoptions, $context);
}
