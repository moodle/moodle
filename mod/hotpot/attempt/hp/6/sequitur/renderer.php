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
 * Render an attempt at a HotPot quiz
 * Output format: hp_6_sequitur
 *
 * @package   mod-hotpot
 * @copyright 2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// get parent class
require_once($CFG->dirroot.'/mod/hotpot/attempt/hp/6/renderer.php');

/**
 * mod_hotpot_attempt_hp_6_sequitur_renderer
 *
 * @copyright 2010 Gordon Bateson
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class mod_hotpot_attempt_hp_6_sequitur_renderer extends mod_hotpot_attempt_hp_6_renderer {
    public $js_object_type = 'Sequitur';

    public $templatefile = 'sequitur6.ht_';
    public $templatestrings = 'PreloadImageList|SegmentsArray';

    // Glossary autolinking settings
    public $headcontent_strings = 'CorrectIndicator|IncorrectIndicator|YourScoreIs|strTimesUp';
    public $headcontent_arrays = 'Segments';

    // TexToys do not have a SubmissionTimeout variable
    public $hasSubmissionTimeout = false;

    public $response_text_fields = array(
        'correct', 'wrong' // remove: ignored
    );

    public $response_num_fields = array(
        'checks' // remove: score, weighting, hints, clues
    );

    /**
     * init
     *
     * @param xxx $hotpot
     */
    function init($hotpot)  {
        parent::init($hotpot);
        array_push($this->javascripts, 'mod/hotpot/attempt/hp/6/sequitur/sequitur.js');
    }

    /**
     * get_js_functionnames
     *
     * @return xxx
     */
    function get_js_functionnames()  {
        // start list of function names
        $names = parent::get_js_functionnames();
        $names .= ($names ? ',' : '').'CheckAnswer,TimesUp';
        return $names;
    }

    /**
     * fix_js_TimesUp
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_TimesUp(&$str, $start, $length)  {
        $substr = substr($str, $start, $length);

        if ($pos = strpos($substr, '	ShowMessage')) {
            $insert = ''
                ."	Finished = true;\n"
                ."	HP_send_results(HP.EVENT_TIMEDOUT);\n"
            ;
            $substr = substr_replace($substr, $insert, $pos, 0);
        }

        $str = substr_replace($str, $substr, $start, $length);
    }

    /**
     * fix_js_CalculateScore
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_CalculateScore(&$str, $start, $length) {
        // original function was simply this:
        // return Math.floor(100*ScoredPoints/TotalPoints);
        $substr = ''
            ."function CalculateScore(){\n"
            ."	if (typeof(window.TotalPointsAvailable)=='undefined') {\n"
            ."\n"
            ."		// initialize TotalPointsAvailable\n"
            ."		window.TotalPointsAvailable = 0;\n"
            ."\n"
            ."		// add points for questions with complete number of distractors\n"
            ."		TotalPointsAvailable += (TotalSegments - NumberOfOptions) * (NumberOfOptions - 1);\n"
            ."\n"
            ."		// add points for questions with less than the total number of distractors\n"
            ."		TotalPointsAvailable += (NumberOfOptions - 1) * NumberOfOptions / 2;\n"
            ."	}\n"
            ."\n"
            ."	if (TotalPointsAvailable==0) {\n"
            ."		return 0;\n"
            ."	} else {\n"
            ."		return Math.floor(100*ScoredPoints/TotalPointsAvailable);\n"
            ."	}\n"
            ."}"
        ;
        $str = substr_replace($str, $substr, $start, $length);
    }


    /**
     * fix_js_CheckAnswer
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_CheckAnswer(&$str, $start, $length)  {
        $substr = substr($str, $start, $length);

        // add extra argument to this function, so it can be called from stop button
        if ($pos = strpos($substr, ')')) {
            $substr = substr_replace($substr, ', ForceQuizEvent', $pos, 0);
        }

        // allow for Btn being null (as it is when called from stop button)
        if ($pos = strpos($substr, 'Btn.innerHTML == IncorrectIndicator')) {
            $substr = substr_replace($substr, 'Btn && ', $pos, 0);
        }
        $search = 'else{';
        if ($pos = strrpos($substr, $search)) {
            $substr = substr_replace($substr, 'else if (Btn){', $pos, strlen($search));
        }

        // intercept checks
        if ($pos = strpos($substr, '{')) {
            $insert = "\n"
                ."	if (CurrentNumber!=TotalSegments && !AllDone && Btn && Btn.innerHTML!=IncorrectIndicator){\n"
                ."		HP.onclickCheck(Chosen);\n"
                ."	}"
            ;
            $substr = substr_replace($substr, $insert, $pos+1, 0);
        }

        // set quiz status
        if ($pos = strpos($substr, 'if (CurrentCorrect == Chosen)')) {
            $event = $this->get_send_results_event();
            $insert = ''
                ."if (CurrentCorrect==Chosen && CurrentNumber>=(TotalSegments-2)){\n"
                ."		var QuizEvent = $event;\n" // COMPLETED or SETVALUES
                ."	} else if (ForceQuizEvent){\n"
                ."		var QuizEvent = ForceQuizEvent;\n"
                ."	} else if (TimeOver){\n"
                ."		var QuizEvent = HP.EVENT_TIMEDOUT;\n"
                ."	} else {\n"
                ."		var QuizEvent = HP.EVENT_CHECK;\n"
                ."	}\n"
                ."	"
            ;
            $substr = substr_replace($substr, $insert, $pos, 0);
        }

        // send results to Moodle, if necessary
        if ($pos = strrpos($substr, '}')) {
            $insert = "\n"
                ."	if (HP.end_of_quiz(QuizEvent)) {\n"
                ."		TimeOver = true;\n"
                ."		Locked = true;\n"
                ."		Finished = true;\n"
                ."	}\n"
                ."	if (Finished || HP.sendallclicks){\n"
                ."		if (QuizEvent==HP.EVENT_COMPLETED){\n"
                ."			// send results after delay (quiz completed as expected)\n"
                ."			setTimeout('HP_send_results('+QuizEvent+')', SubmissionTimeout);\n"
                ."		} else {\n"
                ."			// send results immediately (quiz finished unexpectedly)\n"
                ."			HP_send_results(QuizEvent);\n"
                ."		}\n"
                ."	}\n"
            ;
            $substr = substr_replace($substr, $insert, $pos, 0);
        }

        $str = substr_replace($str, $substr, $start, $length);
    }

    /**
     * get_stop_function_name
     *
     * @return xxx
     */
    function get_stop_function_name()  {
        return 'CheckAnswer';
    }

    /**
     * get_stop_function_args
     *
     * @return xxx
     */
    function get_stop_function_args()  {
        return '0,null,HP.EVENT_ABANDONED';
    }
}
