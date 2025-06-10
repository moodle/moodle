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

namespace mod_questionnaire\output;

use mod_questionnaire\responsetype\response\response;

/**
 * Mobile output class for mod_questionnaire.
 *
 * @package    mod_questionnaire
 * @copyright  2016 Mike Churchward (mike.churchward@poetgroup.org)
 * @author     Mike Churchward
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mobile {

    /**
     * Returns the initial page when viewing the activity for the mobile app.
     *
     * @param  array $args Arguments from tool_mobile_get_content WS
     * @return array HTML, javascript and other data
     */
    public static function mobile_view_activity($args) {
        global $OUTPUT, $USER, $CFG, $DB;
        require_once($CFG->dirroot.'/mod/questionnaire/questionnaire.class.php');

        $args = (object) $args;

        $versionname = $args->appversioncode >= 44000 ? 'latest' : 'ionic5';
        $cmid = $args->cmid;
        $rid = isset($args->rid) ? $args->rid : 0;
        $action = isset($args->action) ? $args->action : 'index';
        $pagenum = (isset($args->pagenum) && !empty($args->pagenum)) ? intval($args->pagenum) : 1;
        $userid = isset($args->userid) ? $args->userid : $USER->id;
        $submit = isset($args->submit) ? $args->submit : false;
        $completed = isset($args->completed) ? $args->completed : false;

        list($cm, $course, $questionnaire) = questionnaire_get_standard_page_items($cmid);
        $questionnaire = new \questionnaire($course, $cm, 0, $questionnaire);

        $data = [];
        $data['cmid'] = $cmid;
        $data['userid'] = $userid;
        $data['intro'] = $questionnaire->intro;
        $data['autonumquestions'] = $questionnaire->autonum;
        $data['id'] = $questionnaire->id;
        $data['rid'] = $rid;
        $data['surveyid'] = $questionnaire->survey->id;
        $data['pagenum'] = $pagenum;
        $data['prevpage'] = 0;
        $data['nextpage'] = 0;

        // Capabilities check.
        $context = \context_module::instance($cmid);
        self::require_capability($cm, $context, 'mod/questionnaire:view');

        // Any notifications will be displayed on top of main page, and prevent questionnaire from being completed. This also checks
        // appropriate capabilities.
        $data['notifications'] = $questionnaire->user_access_messages($userid);
        $responses = [];
        $result = '';

        $data['emptypage'] = 1;
        $template = "mod_questionnaire/local/mobile/$versionname/main_index_page";

        switch ($action) {
            case 'index':
                self::add_index_data($questionnaire, $data, $userid);
                $template = "mod_questionnaire/local/mobile/$versionname/main_index_page";
                break;

            case 'submit':
            case 'nextpage':
            case 'previouspage':
                if (!$data['notifications']) {
                    $result = $questionnaire->save_mobile_data($userid, $pagenum, $completed, $rid, $submit, $action, (array)$args);
                }

            case 'respond':
            case 'resume':
                // Completing a questionnaire.
                if (!$data['notifications']) {
                    if ($questionnaire->user_has_saved_response($userid)) {
                        if (empty($rid)) {
                            $rid = $questionnaire->get_latest_responseid($userid);
                        }
                        $questionnaire->add_response($rid);
                        $data['rid'] = $rid;
                    }
                    $response = (isset($questionnaire->responses) && !empty($questionnaire->responses)) ?
                        end($questionnaire->responses) : \mod_questionnaire\responsetype\response\response::create_from_data([]);
                    $response->sec = $pagenum;
                    if (isset($result['warnings'])) {
                        if ($action == 'submit') {
                            $response = $result['response'];
                        }
                        $data['notifications'] = $result['warnings'];
                    } else if ($action == 'nextpage') {
                        $pageresult = $result['nextpagenum'];
                        if ($pageresult === false) {
                            $pagenum = count($questionnaire->questionsbysec);
                        } else if (is_string($pageresult)) {
                            $data['notifications'] .= !empty($data['notifications']) ? "\n<br />$pageresult" : $pageresult;
                        } else {
                            $pagenum = $pageresult;
                        }
                    } else if ($action == 'previouspage') {
                        $prevpage = $result['nextpagenum'];
                        if ($prevpage === false) {
                            $pagenum = 1;
                        } else {
                            $pagenum = $prevpage;
                        }
                    } else if ($action == 'submit') {
                        self::add_index_data($questionnaire, $data, $userid);
                        $data['action'] = 'index';
                        $template = "mod_questionnaire/local/mobile/$versionname/main_index_page";
                        break;
                    }
                    $pagequestiondata = self::add_pagequestion_data($questionnaire, $pagenum, $response);
                    $data['pagequestions'] = $pagequestiondata['pagequestions'];
                    $responses = $pagequestiondata['responses'];
                    $numpages = count($questionnaire->questionsbysec);
                    // Set some variables we are going to be using.
                    if (!empty($questionnaire->questionsbysec) && ($numpages > 1)) {
                        if ($pagenum > 1) {
                            $data['prevpage'] = true;
                        }
                        if ($pagenum < $numpages) {
                            $data['nextpage'] = true;
                        }
                    }
                    $data['pagenum'] = $pagenum;
                    $data['completed'] = 0;
                    $data['emptypage'] = 0;
                    $template = "mod_questionnaire/local/mobile/$versionname/view_activity_page";
                }
                break;

            case 'review':
                // If reviewing a submission.
                if ($questionnaire->capabilities->readownresponses && isset($args->submissionid) && !empty($args->submissionid)) {
                    $questionnaire->add_response($args->submissionid);
                    $response = $questionnaire->responses[$args->submissionid];
                    $qnum = 1;
                    $pagequestions = [];
                    foreach ($questionnaire->questions as $question) {
                        if ($question->supports_mobile()) {
                            $pagequestions[] = $question->mobile_question_display($qnum, $questionnaire->autonum);
                            $responses = array_merge($responses, $question->get_mobile_response_data($response));
                            if ($question->is_numbered()) {
                                $qnum++;
                            }
                        }
                    }
                    $data['prevpage'] = 0;
                    $data['nextpage'] = 0;
                    $data['pagequestions'] = $pagequestions;
                    $data['completed'] = 1;
                    $data['emptypage'] = 0;
                    $template = "mod_questionnaire/local/mobile/$versionname/view_activity_page";
                }
                break;
        }

        $data['hasmorepages'] = $data['prevpage'] || $data['nextpage'];

        $return = [
            'templates' => [
                [
                    'id' => 'main',
                    'html' => $OUTPUT->render_from_template($template, $data)
                ],
            ],
            'javascript' => file_get_contents($CFG->dirroot . '/mod/questionnaire/appjs/uncheckother.js'),
            'otherdata' => $responses,
            'files' => null
        ];
        return $return;
    }

    /**
     * Confirms the user is logged in and has the specified capability.
     *
     * @param \stdClass $cm
     * @param \context $context
     * @param string $cap
     */
    protected static function require_capability(\stdClass $cm, \context $context, string $cap) {
        require_login($cm->course, false, $cm, true, true);
        require_capability($cap, $context);
    }

    /**
     * Add the submissions.
     * @param \questionnaire $questionnaire
     * @param array $data
     * @param int $userid
     */
    protected static function add_index_data($questionnaire, &$data, $userid) {
        // List any existing submissions, if user is allowed to review them.
        if ($questionnaire->capabilities->readownresponses) {
            $questionnaire->add_user_responses();
            $submissions = [];
            foreach ($questionnaire->responses as $response) {
                $submissions[] = ['submissiondate' => userdate($response->submitted), 'submissionid' => $response->id];
            }
            if (!empty($submissions)) {
                $data['submissions'] = $submissions;
            } else {
                $data['emptypage'] = 1;
            }
            if ($questionnaire->user_has_saved_response($userid)) {
                $data['resume'] = 1;
            }
            $data['emptypage'] = 0;
        }
    }

    /**
     * Ass the questions for the page.
     * @param \questionnaire $questionnaire
     * @param int $pagenum
     * @param response $response
     * @return array
     */
    protected static function add_pagequestion_data($questionnaire, $pagenum, $response=null) {
        $qnum = 1;
        $pagequestions = [];
        $responses = [];

        // Find out what question number we are on $i New fix for question numbering.
        $i = 0;
        if ($pagenum > 1) {
            for ($j = 2; $j <= $pagenum; $j++) {
                foreach ($questionnaire->questionsbysec[$j - 1] as $questionid) {
                    if ($questionnaire->questions[$questionid]->type_id < QUESPAGEBREAK) {
                        $i++;
                    }
                }
            }
        }
        $qnum = $i + 1;

        foreach ($questionnaire->questionsbysec[$pagenum] as $questionid) {
            $question = $questionnaire->questions[$questionid];
            if ($question->supports_mobile()) {
                $pagequestions[] = $question->mobile_question_display($qnum, $questionnaire->autonum);
                $mobileotherdata = $question->mobile_otherdata();
                if (!empty($mobileotherdata)) {
                    $responses = array_merge($responses, $mobileotherdata);
                }
                if (($response !== null) && isset($response->answers[$questionid])) {
                    $responses = array_merge($responses, $question->get_mobile_response_data($response));
                }
                if ($question->is_numbered()) {
                    $qnum++;
                }
            }
        }

        return ['pagequestions' => $pagequestions, 'responses' => $responses];
    }
}
