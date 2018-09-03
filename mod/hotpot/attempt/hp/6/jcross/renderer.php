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
 * Output format: hp_6_jcross
 *
 * @package    mod
 * @subpackage hotpot
 * @copyright  2010 Gordon Bateson (gordon.bateson@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 2.0
 */

/** Prevent direct access to this script */
defined('MOODLE_INTERNAL') || die();

/** Include required files */
require_once($CFG->dirroot.'/mod/hotpot/attempt/hp/6/renderer.php');

/**
 * mod_hotpot_attempt_hp_6_jcross_renderer
 *
 * @copyright  2010 Gordon Bateson (gordon.bateson@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 2.0
 * @package    mod
 * @subpackage hotpot
 */
class mod_hotpot_attempt_hp_6_jcross_renderer extends mod_hotpot_attempt_hp_6_renderer {
    public $icon = 'pix/f/jcw.gif';
    public $js_object_type = 'JCross';

    public $templatefile = 'jcross6.ht_';
    public $templatestrings = 'PreloadImageList|ShowHideClueList';

    // Glossary autolinking settings
    public $headcontent_strings = 'Feedback|AcrossCaption|DownCaption|Correct|Incorrect|GiveHint|YourScoreIs';
    public $headcontent_arrays = '';

    public $response_text_fields = array(
        'correct', 'wrong' // remove: ignored
    );

    public $response_num_fields = array(
        'score', 'hints', 'clues', 'checks' // remove: weighting
    );

    /**
     * init
     *
     * @param xxx $hotpot
     */
    function init($hotpot)  {
        parent::init($hotpot);
        array_push($this->javascripts, 'mod/hotpot/attempt/hp/6/jcross/jcross.js');
    }

    /**
     * filter_text_headcontent_search_array
     *
     * @return xxx
     */
    function filter_text_headcontent_search_array()  {
        return ''; // disable search for array names
    }

    /**
     * fix_headcontent
     */
    function fix_headcontent()  {
        // switch off auto complete on answer text boxes
        $search = '/(?<=<form method="post" action="" onsubmit="return false;")(?=>)/';
        $replace = ' autocomplete="off"';
        $this->headcontent = preg_replace($search, $replace, $this->headcontent, 1);

        parent::fix_headcontent();
    }

    /**
     * fix_bodycontent
     *
     * @return xxx
     */
    function fix_bodycontent()  {
        // we must add a false return value to clue links in order not to trigger the onbeforeunload event handler
        $search = '/(?<='.'<a href="javascript:void\(0\);" class="GridNum" onclick="'.')'.'ShowClue\([^)]*\)'.'(?='.'">'.')/';
        $replace = '$0; return false;';
        $this->bodycontent = preg_replace($search, $replace, $this->bodycontent);

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
        $names .= ($names ? ',' : '').'TypeChars,ShowHint,ShowClue,CheckAnswers';
        return $names;
    }

    /**
     * fix_js_Finish
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_Finish(&$str, $start, $length)  {
        $name = 'Finish';

        // remove the first occurrence of this function
        $this->remove_js_function($str, $start, $length, $name);

        // the JCross template file, jcross6.js_, contains an duplicate version
        // of the Finish() function, so for completeness we remove that as well

        list($start, $finish) = $this->locate_js_block('function', $name, $str);
        if ($finish) {
            // remove the second occurrence of this function
            $this->remove_js_function($str, $start, ($finish - $start), $name);
        }

        // remove all delayed calls to this function
        // don't put this into hp/6/class.php because it breaks JQuiz !!
        //$search = "/\s*"."setTimeout\('$name\([^)]*\)', .*?\);/s";
        //$str = preg_replace($search, '', $str);
    }

    /**
     * fix_js_TypeChars_init
     *
     * @return xxx
     */
    function fix_js_TypeChars_init()  {
        return ''
            ."	if (CurrentBox && (CurrentBox.parentNode==null || CurrentBox.parentNode.parentNode==null)) {\n"
            ."		CurrentBox = null;\n"
            ."	}\n"
            ."	if (CurrentBox==null) {\n"
            ."		var ClueEntry = document.getElementById('ClueEntry');\n"
            ."		if (ClueEntry) {\n"
            ."			var InputTags = ClueEntry.getElementsByTagName('input');\n"
            ."			if (InputTags && InputTags.length) {\n"
            ."				CurrentBox = InputTags[0];\n"
            ."			}\n"
            ."			InputTags = null;\n"
            ."		}\n"
            ."		ClueEntry = null;\n"
            ."	}\n"
        ;
    }

    /**
     * fix_js_TypeChars_obj
     *
     * @return xxx
     */
    function fix_js_TypeChars_obj()  {
        return 'CurrentBox';
    }

    /**
     * fix_js_ShowHint
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_ShowHint(&$str, $start, $length)  {
        $substr = substr($str, $start, $length);

        // intercept Hints
        if ($pos = strrpos($substr, '}')) {
            $append = "\n"
                ."	if (OutString.length) {\n"
                ."		// intercept this Hint\n"
                ."		HP.onclickHint(HP.getQuestionName(ClueNum, (Across ? 'A' : 'D')));\n"
                ."	}\n"
            ;
            $substr = substr_replace($substr, $append, $pos, 0);
        }

        $str = substr_replace($str, $substr, $start, $length);
    }

    /**
     * fix_js_ShowClue
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_ShowClue(&$str, $start, $length)  {
        $substr = substr($str, $start, $length);

        // intercept Clues
        if ($pos = strpos($substr, '{')) {
            $insert = "\n"
                ."	// intercept this Clue\n"
                ."	if(document.getElementById('Clue_A_' + ClueNum)) {\n"
                ."		HP.onclickClue(HP.getQuestionName(ClueNum, 'A'));\n"
                ."	}\n"
                ."	if(document.getElementById('Clue_D_' + ClueNum)) {\n"
                ."		HP.onclickClue(HP.getQuestionName(ClueNum, 'D'));\n"
                ."	}\n"
            ;
            $substr = substr_replace($substr, $insert, $pos+1, 0);
        }

        // stretch the canvas vertically down to cover the content, if any
        if ($pos = strrpos($substr, '}')) {
            $substr = substr_replace($substr, '	StretchCanvasToCoverContent(true);'."\n", $pos, 0);
        }

        $str = substr_replace($str, $substr, $start, $length);
    }

    /**
     * get_stop_function_name
     *
     * @return xxx
     */
    function get_stop_function_name()  {
        return 'CheckAnswers';
    }

    /**
     * return JS-safe version of expand_EnterCaption()
     *
     * @return xxx
     */
    function expand_EnterCaption()  {
        $caption = parent::expand_EnterCaption();
        return $this->hotpot->source->js_value_safe($caption, true);
    }

    /**
     * return JS-safe version of expand_HintCaption()
     *
     * @return xxx
     */
    function expand_HintCaption()  {
        $caption = parent::expand_HintCaption();
        return $this->hotpot->source->js_value_safe($caption, true);
    }
}
