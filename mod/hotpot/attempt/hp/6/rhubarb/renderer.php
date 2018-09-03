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
 * Output format: hp_6_rhubarb
 *
 * @package   mod-hotpot
 * @copyright 2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// get parent class
require_once($CFG->dirroot.'/mod/hotpot/attempt/hp/6/renderer.php');

/**
 * mod_hotpot_attempt_hp_6_rhubarb_renderer
 *
 * @copyright 2010 Gordon Bateson
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class mod_hotpot_attempt_hp_6_rhubarb_renderer extends mod_hotpot_attempt_hp_6_renderer {
    public $js_object_type = 'Rhubarb';

    public $templatefile = 'rhubarb6.ht_';
    public $templatestrings = 'PreloadImageList|FreeWordsArray|WordsArray';

    // Glossary autolinking settings
    public $headcontent_strings = 'strFinished|YourScoreIs|strTimesUp';
    public $headcontent_arrays = 'Words';

    // TexToys do not have a SubmissionTimeout variable
    public $hasSubmissionTimeout = false;

    /**
     * init
     *
     * @param xxx $hotpot
     */
    function init($hotpot)  {
        parent::init($hotpot);
        array_push($this->javascripts, 'mod/hotpot/attempt/hp/6/rhubarb/rhubarb.js');
    }

    /**
     * fix_bodycontent
     */
    function fix_bodycontent()  {
        // switch off auto complete on Rhubarb text boxes
        $search = '/<form id="Rhubarb"([^>]*)>/';
        if (preg_match($search, $this->bodycontent, $matches, PREG_OFFSET_CAPTURE)) {
            $match = $matches[1][0];
            $start = $matches[1][1];
            if (strpos($match, 'autocomplete')===false) {
                $this->bodycontent = substr_replace($this->bodycontent, $match.' autocomplete="off"', $start, strlen($match));
            }
        }
        parent::fix_bodycontent();
    }

    /**
     * get_js_functionnames
     *
     * @return xxx
     */
    function get_js_functionnames()  {
        // start list of function names
        $names = parent::get_js_functionnames();
        $names .= ($names ? ',' : '').'TypeChars,Hint,CheckWord,CheckFinished,TimesUp';
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
     * fix_js_TypeChars_init
     *
     * @return xxx
     */
    function fix_js_TypeChars_init()  {
        return "	var obj = document.getElementById('Guess');\n";
    }

    /**
     * fix_js_TypeChars_obj
     *
     * @return xxx
     */
    function fix_js_TypeChars_obj()  {
        return 'obj';
    }

    /**
     * fix_js_Hint
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_Hint(&$str, $start, $length)  {
        $substr = substr($str, $start, $length);

        // intercept Hints
        if ($pos = strpos($substr, '{')) {
            $insert = "\n"
                ."	if (! AllDone) {\n"
                ."		// intercept this Hint\n"
                ."		HP.onclickHint(0);\n"
                ."	}\n"
            ;
            $substr = substr_replace($substr, $insert, $pos+1, 0);
        }

        $str = substr_replace($str, $substr, $start, $length);
    }

    /**
     * fix_js_CheckWord
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_CheckWord(&$str, $start, $length)  {
        $substr = substr($str, $start, $length);

        // intercept Hints
        if ($pos = strpos($substr, '{')) {
            $insert = "\n"
                ."	if (! AllDone && InputWord.length) {\n"
                ."		// intercept this Check\n"
                ."		HP.onclickCheck(InputWord);\n"
                ."	}"
            ;
            $substr = substr_replace($substr, $insert, $pos+1, 0);
        }

        $str = substr_replace($str, $substr, $start, $length);
    }

    /**
     * fix_js_CheckFinished
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_CheckFinished(&$str, $start, $length)  {
        parent::fix_js_CheckAnswers($str, $start, $length);
    }

    /**
     * get_stop_function_intercept
     *
     * @return xxx
     */
    function get_stop_function_intercept()  {
        // intercept is not required in the giveup function of Rhubarb
        // because the checks are intercepted by CheckFinished (see above)
        return '';
    }

    /**
     * get_stop_function_name
     *
     * @return xxx
     */
    function get_stop_function_name()  {
        return 'CheckFinished';
    }

    /**
     * get_stop_function_args
     *
     * @return xxx
     */
    function get_stop_function_args()  {
        return 'HP.EVENT_ABANDONED';
    }

    /**
     * get_stop_function_search
     *
     * @return xxx
     */
    function get_stop_function_search()  {
        return '/\s*if \((Done) == true\)(\{.*?)\}/s';
    }
}
