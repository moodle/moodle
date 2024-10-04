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

namespace qbank_previewquestion;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/editlib.php');

use action_menu;
use comment;
use context_module;
use context;
use core\plugininfo\qbank;
use core_question\local\bank\edit_menu_column;
use core_question\local\bank\view;
use core_question\local\bank\question_edit_contexts;
use moodle_url;
use question_bank;
use question_definition;
use question_display_options;
use question_engine;
use stdClass;

/**
 * Class helper contains all the helper functions.
 *
 * @package    qbank_previewquestion
 * @copyright  2010 The Open University
 * @author     2021 Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {

    /**
     * Called via pluginfile.php -> question_pluginfile to serve files belonging to
     * a question in a question_attempt when that attempt is a preview.
     *
     * @param stdClass $course course settings object
     * @param stdClass $context context object
     * @param string $component the name of the component we are serving files for.
     * @param string $filearea the name of the file area.
     * @param int $qubaid the question_usage this image belongs to.
     * @param int $slot the relevant slot within the usage.
     * @param array $args the remaining bits of the file path.
     * @param bool $forcedownload whether the user must be forced to download the file.
     * @param array $fileoptions options for the stored files
     * @return void false if file not found, does not return if found - justsend the file
     */
    public static function question_preview_question_pluginfile($course, $context, $component,
            $filearea, $qubaid, $slot, $args, $forcedownload, $fileoptions): void {
        global $USER, $DB, $CFG;

        list($context, $course, $cm) = get_context_info_array($context->id);
        require_login($course, false, $cm);

        $quba = question_engine::load_questions_usage_by_activity($qubaid);

        if (!question_has_capability_on($quba->get_question($slot, false), 'use')) {
            send_file_not_found();
        }

        $options = new question_display_options();
        $options->feedback = question_display_options::VISIBLE;
        $options->numpartscorrect = question_display_options::VISIBLE;
        $options->generalfeedback = question_display_options::VISIBLE;
        $options->rightanswer = question_display_options::VISIBLE;
        $options->manualcomment = question_display_options::VISIBLE;
        $options->history = question_display_options::VISIBLE;
        if (!$quba->check_file_access($slot, $options, $component,
                $filearea, $args, $forcedownload)) {
            send_file_not_found();
        }

        $fs = get_file_storage();
        $relativepath = implode('/', $args);
        $fullpath = "/{$context->id}/{$component}/{$filearea}/{$relativepath}";
        if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
            send_file_not_found();
        }

        send_stored_file($file, 0, 0, $forcedownload, $fileoptions);
    }

    /**
     * The the URL to use for actions relating to this preview.
     *
     * @param int $questionid the question being previewed
     * @param int $qubaid the id of the question usage for this preview
     * @param question_preview_options $options the options in use
     * @param context $context module context for the question preview
     * @param moodle_url $returnurl url of the page to return to
     * @param int|null $restartversion version of the question to use when next restarting the preview.
     * @return moodle_url
     */
    public static function question_preview_action_url($questionid, $qubaid,
            question_preview_options $options, $context, $returnurl = null, $restartversion = null): moodle_url {
        $params = [
                'id' => $questionid,
                'previewid' => $qubaid,
        ];

        if ($context->contextlevel !== CONTEXT_MODULE) {
            debugging("Invalid contextlevel: {$context->contextlevel} must be CONTEXT_MODULE");
        }

        $params['cmid'] = $context->instanceid;

        if ($returnurl !== null) {
            $params['returnurl'] = $returnurl;
        }
        if ($restartversion !== null) {
            $params['restartversion'] = $restartversion;
        }
        $params = array_merge($params, $options->get_url_params());
        return new moodle_url('/question/bank/previewquestion/preview.php', $params);
    }

    /**
     * The the URL to use for actions relating to this preview.
     *
     * @param int $questionid the question being previewed
     * @param context $context the current moodle module context
     * @param int $previewid optional previewid to sign post saved previewed answers
     * @param moodle_url $returnurl url of the page to return to
     * @return moodle_url
     */
    public static function question_preview_form_url($questionid, $context, $previewid = null, $returnurl = null): moodle_url {
        $params = [
                'id' => $questionid,
        ];

        if ($context->contextlevel !== CONTEXT_MODULE) {
            debugging("Invalid contextlevel: {$context->contextlevel} must be CONTEXT_MODULE");
        }

        $params['cmid'] = $context->instanceid;

        if ($previewid) {
            $params['previewid'] = $previewid;
        }
        if ($returnurl !== null) {
            $params['returnurl'] = $returnurl;
        }
        return new moodle_url('/question/bank/previewquestion/preview.php', $params);
    }

    /**
     * Delete the current preview, if any, and redirect to start a new preview.
     *
     * @param int $previewid id of the preview while restarting it
     * @param int $questionid id of the question in preview
     * @param object $displayoptions display options for the question in preview
     * @param object $context context of the question for preview
     * @param moodle_url $returnurl url of the page to return to
     * @param int|null $restartversion version of the question to use when next restarting the preview.
     * @return void
     */
    public static function restart_preview($previewid, $questionid, $displayoptions, $context,
        $returnurl = null, $restartversion = null): void {
        global $DB;

        if ($previewid) {
            $transaction = $DB->start_delegated_transaction();
            question_engine::delete_questions_usage_by_activity($previewid);
            $transaction->allow_commit();
        }
        redirect(self::question_preview_url($questionid, $displayoptions->behaviour,
                $displayoptions->maxmark, $displayoptions, $displayoptions->variant,
                $context, $returnurl, $restartversion));
    }

    /**
     * Generate the URL for starting a new preview of a given question with the given options.
     *
     * @param integer $questionid the question to preview
     * @param string $preferredbehaviour the behaviour to use for the preview
     * @param float $maxmark the maximum to mark the question out of
     * @param question_display_options $displayoptions the display options to use
     * @param int $variant the variant of the question to preview. If null, one will
     *      be picked randomly
     * @param object $context context to run the preview in (affects things like
     *      filter settings, theme, lang, etc.) Defaults to $PAGE->context
     * @param moodle_url $returnurl url of the page to return to
     * @param int $restartversion The version of the question to use when restarting the preview.
     * @return moodle_url the URL
     */
    public static function question_preview_url($questionid, $preferredbehaviour = null,
            $maxmark = null, $displayoptions = null, $variant = null, $context = null, $returnurl = null,
            $restartversion = null): moodle_url {

        $params = ['id' => $questionid];

        if (!is_null($restartversion)) {
            $params['restartversion'] = $restartversion;
        }
        if (is_null($context)) {
            global $PAGE;
            $context = $PAGE->context;
        }
        if ($context->contextlevel == CONTEXT_MODULE) {
            $params['cmid'] = $context->instanceid;
        } else if ($context->contextlevel == CONTEXT_COURSE) {
            $params['courseid'] = $context->instanceid;
        }

        if (!is_null($preferredbehaviour)) {
            $params['behaviour'] = $preferredbehaviour;
        }

        if (!is_null($maxmark)) {
            $params['maxmark'] = format_float($maxmark, -1);
        }

        if (!is_null($displayoptions)) {
            $params['correctness']     = $displayoptions->correctness;
            $params['marks']           = $displayoptions->marks;
            $params['markdp']          = $displayoptions->markdp;
            $params['feedback']        = (bool) $displayoptions->feedback;
            $params['generalfeedback'] = (bool) $displayoptions->generalfeedback;
            $params['rightanswer']     = (bool) $displayoptions->rightanswer;
            $params['history']         = (bool) $displayoptions->history;
        }

        if (!is_null($returnurl)) {
            $params['returnurl'] = $returnurl;
        }

        if ($variant) {
            $params['variant'] = $variant;
        }

        return new moodle_url('/question/bank/previewquestion/preview.php', $params);
    }

    /**
     * Popup params for the question preview.
     *
     * @return array that can be passed as $params to the {@see popup_action} constructor.
     */
    public static function question_preview_popup_params(): array {
        return [
                'height' => 600,
                'width' => 800,
        ];
    }

    /**
     * Get the extra elements for preview from qbank plugins.
     *
     * @param  question_definition $question question definition object
     * @param  int $courseid id of the course
     * @return array
     */
    public static function get_preview_extra_elements(question_definition $question, int $courseid): array {
        $plugins = get_plugin_list_with_function('qbank', 'preview_display');

        $comment = '';
        $extrahtml = [];
        foreach ($plugins as $componentname => $plugin) {
            $pluginhtml = component_callback($componentname, 'preview_display', [$question, $courseid]);
            if ($componentname === 'qbank_comment') {
                $comment = $pluginhtml;
                continue;
            }
            $extrahtml[] = $pluginhtml;
        }
        return [$comment, $extrahtml];
    }

    /**
     * Checks if question is the latest version.
     *
     * @param string $version Question version to check
     * @param string $questionbankentryid Entry to check against
     * @return bool
     */
    public static function is_latest(string $version, string $questionbankentryid): bool {
        global $DB;

        $sql = 'SELECT MAX(version) AS max
                  FROM {question_versions}
                 WHERE questionbankentryid = ?';
        $latestversion = $DB->get_record_sql($sql, [$questionbankentryid]);

        if (isset($latestversion->max)) {
            return ($version === $latestversion->max) ? true : false;
        }
        return false;
    }

    /**
     * Loads question version ids for current question.
     *
     * @param  string $questionbankentryid Question bank entry id
     * @return array  $questionids Array containing question id as key and version as value.
     */
    public static function load_versions(string $questionbankentryid): array {
        global $DB;

        $questionids = [];
        $sql = 'SELECT version, questionid
                  FROM {question_versions}
                 WHERE questionbankentryid = ?
              ORDER BY version';

        $versions = $DB->get_records_sql($sql, [$questionbankentryid]);
        foreach ($versions as $key => $version) {
            $questionids[$version->questionid] = $key;
        }
        return $questionids;
    }

    /**
     * Return the question ID from the array of id => version that corresponds to the requested version.
     *
     * If the requested version is question_preview_options::ALWAYS_LATEST, this will return the latest version.
     *
     * @param array $versions
     * @param int $restartversion
     * @return ?int
     */
    public static function get_restart_id(array $versions, int $restartversion): ?int {
        if ($restartversion === question_preview_options::ALWAYS_LATEST) {
            return array_key_last($versions);
        } else {
            return array_search($restartversion, $versions) ?: null;
        }
    }
}
