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
 * @package    qbank_previewquestion
 * @copyright  Alex Smith {@link http://maths.york.ac.uk/serving_maths}
 * @author     2021 Safat Shahin <safatshahin@catalyst-au.net> and numerous contributors.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/questionlib.php');

use \core\notification;
use qbank_previewquestion\form\preview_options_form;
use qbank_previewquestion\question_preview_options;
use qbank_previewquestion\helper;
use core_question\local\bank\version_options;

/**
 * The maximum number of variants previewable. If there are more variants than this for a question
 * then we only allow the selection of the first x variants.
 *
 * @var integer
 */
define('QUESTION_PREVIEW_MAX_VARIANTS', 100);

\core_question\local\bank\helper::require_plugin_enabled('qbank_previewquestion');

// Get and validate question id.
$id = required_param('id', PARAM_INT);
$returnurl = optional_param('returnurl', null, PARAM_LOCALURL);
$restartversion = optional_param('restartversion', question_preview_options::ALWAYS_LATEST, PARAM_INT);

$question = question_bank::load_question($id);

if ($returnurl) {
    $returnurl = new moodle_url($returnurl);
}

// Were we given a particular context to run the question in?
// This affects things like filter settings, or forced theme or language.
if ($cmid = optional_param('cmid', 0, PARAM_INT)) {
    $cm = get_coursemodule_from_id(false, $cmid);
    require_login($cm->course, false, $cm);
    $context = context_module::instance($cmid);
} else {
    require_login();
    $category = $DB->get_record('question_categories', ['id' => $question->category], '*', MUST_EXIST);
    $context = context::instance_by_id($category->contextid);
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
$options->versioninfo = false;
$PAGE->set_url(helper::question_preview_url($id, $options->behaviour, $options->maxmark,
        $options, $options->variant, $context, null, $restartversion));

// Get and validate existing preview, or start a new one.
$previewid = optional_param('previewid', 0, PARAM_INT);

if ($previewid) {
    try {
        $quba = question_engine::load_questions_usage_by_activity($previewid);

    } catch (Exception $e) {
        // This may not seem like the right error message to display, but
        // actually from the user point of view, it makes sense.
        throw new moodle_exception('submissionoutofsequencefriendlymessage', 'question',
                helper::question_preview_url($question->id, $options->behaviour,
                        $options->maxmark, $options, $options->variant, $context, null, $restartversion), null, $e);
    }

    if ($quba->get_owning_context()->instanceid != $USER->id) {
        throw new moodle_exception('notyourpreview', 'question');
    }

    $slot = $quba->get_first_question_number();
    $usedquestion = $quba->get_question($slot, false);
    if ($usedquestion->id != $question->id) {
        throw new moodle_exception('questionidmismatch', 'question');
    }
    $question = $usedquestion;
    $options->variant = $quba->get_variant($slot);

} else {
    $quba = question_engine::make_questions_usage_by_activity(
            'core_question_preview', context_user::instance($USER->id));
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
}
$options->behaviour = $quba->get_preferred_behaviour();
$options->maxmark = $quba->get_question_max_mark($slot);

// Load the question versions and convert to a simple array for select menu.
$versions = version_options::get_version_menu_options($question->id);
$versionids = helper::load_versions($question->questionbankentryid);
// Create the settings form, and initialise the fields.
$optionsform = new preview_options_form(helper::question_preview_form_url($question->id, $context, $previewid, $returnurl),
        [
            'quba' => $quba,
            'maxvariant' => $maxvariant,
            'versions' => $versions,
            'restartversion' => $restartversion,
        ]);
$optionsform->set_data($options);

// Process change of settings, if that was requested.
if ($newoptions = $optionsform->get_submitted_data()) {
    // Set user preferences.
    $options->save_user_preview_options($newoptions);
    if (!isset($newoptions->variant)) {
        $newoptions->variant = $options->variant;
    }
    $questionid = helper::get_restart_id($versionids, $restartversion);
    if (isset($newoptions->saverestart)) {
        helper::restart_preview($previewid, $questionid, $newoptions, $context, $returnurl, $newoptions->restartversion);
    }
}

// Prepare a URL that is used in various places.
$actionurl = helper::question_preview_action_url($question->id, $quba->get_id(), $options, $context, $returnurl, $restartversion);

// Process any actions from the buttons at the bottom of the form.
if (data_submitted() && confirm_sesskey()) {

    try {

        if (optional_param('restart', false, PARAM_BOOL)) {
            $questionid = helper::get_restart_id($versionids, $restartversion);
            helper::restart_preview($previewid, $questionid, $options, $context, $returnurl, $restartversion);

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

            $mdlscrollto = optional_param('mdlscrollto', '', PARAM_RAW);
            if ($mdlscrollto !== '') {
                $actionurl->param('mdlscrollto', (int) $mdlscrollto);
            }
            redirect($actionurl);
        }

    } catch (question_out_of_sequence_exception $e) {
        throw new moodle_exception('submissionoutofsequencefriendlymessage', 'question', $actionurl);

    } catch (Exception $e) {
        // This sucks, if we display our own custom error message, there is no way
        // to display the original stack trace.
        $debuginfo = '';
        if (!empty($e->debuginfo)) {
            $debuginfo = $e->debuginfo;
        }
        throw new moodle_exception('errorprocessingresponses', 'question', $actionurl,
                $e->getMessage(), $debuginfo);
    }
}

if ($question->length) {
    $displaynumber = '1';
} else {
    $displaynumber = 'i';
}
$restartdisabled = [];
$finishdisabled = [];
$filldisabled = [];
if ($quba->get_question_state($slot)->is_finished()) {
    $finishdisabled = ['disabled' => 'disabled'];
    $filldisabled = ['disabled' => 'disabled'];
}
// If question type cannot give us a correct response, disable this button.
if (is_null($quba->get_correct_response($slot))) {
    $filldisabled = ['disabled' => 'disabled'];
}
if (!$previewid) {
    $restartdisabled = ['disabled' => 'disabled'];
}

// Prepare technical info to be output.
$qa = $quba->get_question_attempt($slot);
$technical = [];
$technical[] = get_string('behaviourbeingused', 'question',
        question_engine::get_behaviour_name($qa->get_behaviour_name()));
$technical[] = get_string('technicalinfominfraction', 'question', $qa->get_min_fraction());
$technical[] = get_string('technicalinfomaxfraction', 'question', $qa->get_max_fraction());
$technical[] = get_string('technicalinfovariant', 'question', $qa->get_variant());
$technical[] = get_string('technicalinfoquestionsummary', 'question', s($qa->get_question_summary()));
$technical[] = get_string('technicalinforightsummary', 'question', s($qa->get_right_answer_summary()));
$technical[] = get_string('technicalinforesponsesummary', 'question', s($qa->get_response_summary()));
$technical[] = get_string('technicalinfostate', 'question', '' . $qa->get_state());

// Start output.
$title = get_string('previewquestion', 'question', format_string($question->name));
$headtags = question_engine::initialise_js() . $quba->render_question_head_html($slot);
$PAGE->set_title($title);
$PAGE->set_heading($title);
echo $OUTPUT->header();

$previewdata = [];

$previewdata['questionicon'] = print_question_icon($question);
$previewdata['questionidumber'] = $question->idnumber;
$previewdata['questiontitle'] = $question->name;
$versioninfo = new \core_question\output\question_version_info($question);
$previewdata['versiontitle'] = $versioninfo->export_for_template($OUTPUT);
if ($versioninfo->version !== $versioninfo->latestversion) {
    if ($restartversion == question_preview_options::ALWAYS_LATEST) {
        $newerversionparams = (object) [
            'currentversion' => $question->version,
            'latestversion' => max($versionids),
            'restartbutton' => $OUTPUT->render_from_template('qbank_previewquestion/restartbutton', []),
        ];
        $newversionurl = clone $actionurl;
        $newversionurl->param('restart', 1);
        $previewdata['newerversionurl'] = $newversionurl;
        $previewdata['newerversion'] = get_string('newerversion', 'qbank_previewquestion', $newerversionparams);
    }
}

$previewdata['actionurl'] = $actionurl;
$previewdata['session'] = sesskey();
$previewdata['slot'] = $slot;
// Output of the question.
$previewdata['question'] = $quba->render_question($slot, $options, $displaynumber);
$previewdata['restartdisabled'] = html_writer::attributes($restartdisabled);
$previewdata['finishdisabled'] = html_writer::attributes($finishdisabled);
$previewdata['filldisabled'] = html_writer::attributes($filldisabled);
// Output the technical info.
$previewdata['techinfo'] = print_collapsible_region_start('', 'techinfo', get_string('technicalinfo', 'question'),
        'core_question_preview_techinfo_collapsed', true, true, $OUTPUT->help_icon('technicalinfo', 'question'));
foreach ($technical as $info) {
    $previewdata['techinfo'] .= html_writer::tag('p', $info, ['class' => 'notifytiny']);
}
$previewdata['techinfo'] .= print_collapsible_region_end(true);

// Display the settings form.
$previewdata['options'] = $optionsform->render();

list($comment, $extraelements) = helper::get_preview_extra_elements($question, $COURSE->id);

if (!empty($comment)) {
    $previewdata['comments'] = $comment;
}

if (!empty($extraelements)) {
    $elements = [];
    foreach ($extraelements as $extraelement) {
        $element = new stdClass();
        $element->extrapreviewelements = $extraelement;
        $elements[] = $element;
    }
    $previewdata['extrapreviewelements'] = $elements;
}

$previewdata['redirect'] = false;
if (!is_null($returnurl)) {
    $previewdata['redirect'] = true;
    $previewdata['redirecturl'] = $returnurl;
}
$closeurl = new moodle_url('/question/edit.php', ['cmid' => $context->instanceid]);
echo $PAGE->get_renderer('qbank_previewquestion')->render_preview_page($previewdata);

// Log the preview of this question.
$event = \core\event\question_viewed::create_from_question_instance($question, $context);
$event->trigger();

$PAGE->requires->js_call_amd('qbank_previewquestion/preview', 'init', [$previewdata['redirect'], $closeurl->__toString()]);
echo $OUTPUT->footer();
