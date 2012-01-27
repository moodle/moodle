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
 * This page displays a preview of a question
 *
 * The preview uses the option settings from the activity within which the question
 * is previewed or the default settings if no activity is specified. The question session
 * information is stored in the session as an array of subsequent states rather
 * than in the database.
 *
 * @package    moodlecore
 * @subpackage questionengine
 * @copyright  Alex Smith {@link http://maths.york.ac.uk/serving_maths} and
 *      numerous contributors.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(__FILE__) . '/../config.php');
require_once($CFG->libdir . '/questionlib.php');
require_once(dirname(__FILE__) . '/previewlib.php');

/**
 * The maximum number of variants previewable. If there are more variants than this for a question
 * then we only allow the selection of the first x variants.
 * @var integer
 */
define('QUESTION_PREVIEW_MAX_VARIANTS', 100);

// Get and validate question id.
$id = required_param('id', PARAM_INT);
$question = question_bank::load_question($id);

// Were we given a particular context to run the question in?
// This affects things like filter settings, or forced theme or language.
if ($cmid = optional_param('cmid', 0, PARAM_INT)) {
    $cm = get_coursemodule_from_id(false, $cmid);
    require_login($cm->course, false, $cm);
    $context = get_context_instance(CONTEXT_MODULE, $cmid);

} else if ($courseid = optional_param('courseid', 0, PARAM_INT)) {
    require_login($courseid);
    $context = get_context_instance(CONTEXT_COURSE, $courseid);

} else {
    require_login();
    $category = $DB->get_record('question_categories',
            array('id' => $question->category), '*', MUST_EXIST);
    $context = get_context_instance_by_id($category->contextid);
    $PAGE->set_context($context);
    // Note that in the other cases, require_login will set the correct page context.
}
question_require_capability_on($question, 'use');
$PAGE->set_pagelayout('popup');

// Get and validate display options.
$maxvariant = min($question->get_num_variants(), QUESTION_PREVIEW_MAX_VARIANTS);
$options = new question_preview_options($question);
$options->load_user_defaults();
$options->set_from_request();
$PAGE->set_url(question_preview_url($id, $options->behaviour, $options->maxmark,
        $options, $options->variant, $context));

// Get and validate exitsing preview, or start a new one.
$previewid = optional_param('previewid', 0, PARAM_INT);

if ($previewid) {
    if (!isset($SESSION->question_previews[$previewid])) {
        print_error('notyourpreview', 'question');
    }

    try {
        $quba = question_engine::load_questions_usage_by_activity($previewid);

    } catch (Exception $e) {
        // This may not seem like the right error message to display, but
        // actually from the user point of view, it makes sense.
        print_error('submissionoutofsequencefriendlymessage', 'question',
                question_preview_url($question->id, $options->behaviour,
                $options->maxmark, $options, $options->variant, $context), null, $e);
    }

    $slot = $quba->get_first_question_number();
    $usedquestion = $quba->get_question($slot);
    if ($usedquestion->id != $question->id) {
        print_error('questionidmismatch', 'question');
    }
    $question = $usedquestion;
    $options->variant = $quba->get_variant($slot);

} else {
    $quba = question_engine::make_questions_usage_by_activity(
            'core_question_preview', $context);
    $quba->set_preferred_behaviour($options->behaviour);
    $slot = $quba->add_question($question, $options->maxmark);

    if ($options->variant) {
        $options->variant = min($maxvariant, max(1, $options->variant));
    } else {
        $options->variant = rand(1, $maxvariant);
    }

    $quba->start_question($slot, $options->variant);

    $transaction = $DB->start_delegated_transaction();
    question_engine::save_questions_usage_by_activity($quba);
    $transaction->allow_commit();

    $SESSION->question_previews[$quba->get_id()] = true;
}
$options->behaviour = $quba->get_preferred_behaviour();
$options->maxmark = $quba->get_question_max_mark($slot);

// Create the settings form, and initialise the fields.
$optionsform = new preview_options_form(question_preview_form_url($question->id, $context),
        array('quba' => $quba, 'maxvariant' => $maxvariant));
$optionsform->set_data($options);

// Process change of settings, if that was requested.
if ($newoptions = $optionsform->get_submitted_data()) {
    // Set user preferences
    $options->save_user_preview_options($newoptions);
    if (!isset($newoptions->variant)) {
        $newoptions->variant = $options->variant;
    }
    restart_preview($previewid, $question->id, $newoptions, $context);
}

// Prepare a URL that is used in various places.
$actionurl = question_preview_action_url($question->id, $quba->get_id(), $options, $context);

// Process any actions from the buttons at the bottom of the form.
if (data_submitted() && confirm_sesskey()) {

    try {

        if (optional_param('restart', false, PARAM_BOOL)) {
            restart_preview($previewid, $question->id, $options, $context);

        } else if (optional_param('fill', null, PARAM_BOOL)) {
            $correctresponse = $quba->get_correct_response($slot);
            if (!is_null($correctresponse)) {
                $quba->process_action($slot, $correctresponse);

                $transaction = $DB->start_delegated_transaction();
                question_engine::save_questions_usage_by_activity($quba);
                $transaction->allow_commit();
            }
            redirect($actionurl);

        } else if (optional_param('finish', null, PARAM_BOOL)) {
            $quba->process_all_actions();
            $quba->finish_all_questions();

            $transaction = $DB->start_delegated_transaction();
            question_engine::save_questions_usage_by_activity($quba);
            $transaction->allow_commit();
            redirect($actionurl);

        } else {
            $quba->process_all_actions();

            $transaction = $DB->start_delegated_transaction();
            question_engine::save_questions_usage_by_activity($quba);
            $transaction->allow_commit();

            $scrollpos = optional_param('scrollpos', '', PARAM_RAW);
            if ($scrollpos !== '') {
                $actionurl->param('scrollpos', (int) $scrollpos);
            }
            redirect($actionurl);
        }

    } catch (question_out_of_sequence_exception $e) {
        print_error('submissionoutofsequencefriendlymessage', 'question', $actionurl);

    } catch (Exception $e) {
        // This sucks, if we display our own custom error message, there is no way
        // to display the original stack trace.
        $debuginfo = '';
        if (!empty($e->debuginfo)) {
            $debuginfo = $e->debuginfo;
        }
        print_error('errorprocessingresponses', 'question', $actionurl,
                $e->getMessage(), $debuginfo);
    }
}

if ($question->length) {
    $displaynumber = '1';
} else {
    $displaynumber = 'i';
}
$restartdisabled = '';
$finishdisabled = '';
$filldisabled = '';
if ($quba->get_question_state($slot)->is_finished()) {
    $finishdisabled = ' disabled="disabled"';
    $filldisabled = ' disabled="disabled"';
}
// If question type cannot give us a correct response, disable this button.
if (is_null($quba->get_correct_response($slot))) {
    $filldisabled = ' disabled="disabled"';
}
if (!$previewid) {
    $restartdisabled = ' disabled="disabled"';
}

// Output
$title = get_string('previewquestion', 'question', format_string($question->name));
$headtags = question_engine::initialise_js() . $quba->render_question_head_html($slot);
$PAGE->set_title($title);
$PAGE->set_heading($title);
echo $OUTPUT->header();

// Start the question form.
echo '<form method="post" action="' . $actionurl .
        '" enctype="multipart/form-data" id="responseform">', "\n";
echo '<div>';
echo '<input type="hidden" name="sesskey" value="' . sesskey() . '" />', "\n";
echo '<input type="hidden" name="slots" value="' . $slot . '" />', "\n";
echo '</div>';

// Output the question.
echo $quba->render_question($slot, $options, $displaynumber);

echo '<p class="notifytiny">' . get_string('behaviourbeingused', 'question',
        question_engine::get_behaviour_name(
        $quba->get_question_attempt($slot)->get_behaviour_name())) . '</p>';
// Finish the question form.
echo '<div id="previewcontrols" class="controls">';
echo '<input type="submit" name="restart"' . $restartdisabled .
        ' value="' . get_string('restart', 'question') . '" />', "\n";
echo '<input type="submit" name="fill"' . $filldisabled .
        ' value="' . get_string('fillincorrect', 'question') . '" />', "\n";
echo '<input type="submit" name="finish"' . $finishdisabled .
        ' value="' . get_string('submitandfinish', 'question') . '" />', "\n";
echo '<input type="hidden" name="scrollpos" id="scrollpos" value="" />';
echo '</div>';
echo '</form>';

// Display the settings form.
$optionsform->display();

$PAGE->requires->js_init_call('M.core_question_preview.init', null, false, array(
        'name' => 'core_question_preview',
        'fullpath' => '/question/preview.js',
        'requires' => array('base', 'dom', 'event-delegate', 'event-key', 'core_question_engine'),
        'strings' => array(
            array('closepreview', 'question'),
        )));
echo $OUTPUT->footer();

