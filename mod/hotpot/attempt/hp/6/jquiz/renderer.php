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
 * Output format: hp_6_jquiz
 *
 * @package   mod-hotpot
 * @copyright 2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// get parent class
require_once($CFG->dirroot.'/mod/hotpot/attempt/hp/6/renderer.php');

/**
 * mod_hotpot_attempt_hp_6_jquiz_renderer
 *
 * @copyright 2010 Gordon Bateson
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class mod_hotpot_attempt_hp_6_jquiz_renderer extends mod_hotpot_attempt_hp_6_renderer {
    public $icon = 'pix/f/jqz.gif';
    public $js_object_type = 'JQuiz';

    public $templatefile = 'jquiz6.ht_';
    public $templatestrings = 'PreloadImageList|QsToShow';

    // Glossary autolinking settings
    public $headcontent_strings = 'CorrectIndicator|IncorrectIndicator|YourScoreIs|CorrectFirstTime|DefaultRight|DefaultWrong|ShowAllQuestionsCaption|ShowOneByOneCaption|I';
    public $headcontent_arrays = 'I';

    public $response_num_fields = array(
        'score', 'weighting', 'hints', 'checks' // remove: clues
    );

    /**
     * init
     *
     * @param xxx $hotpot
     */
    function init($hotpot)  {
        parent::init($hotpot);
        array_push($this->javascripts, 'mod/hotpot/attempt/hp/6/jquiz/jquiz.js');
    }

    /**
     * fix_bodycontent
     */
    function fix_bodycontent()  {
        // switch off auto complete on short answer text boxes
        $search = '/<div class="ShortAnswer"[^>]*><form([^>]*)>/';
        if (preg_match_all($search, $this->bodycontent, $matches, PREG_OFFSET_CAPTURE)) {
            $i_max = count($matches[0]) - 1;
            for ($i=$i_max; $i>=0; $i--) {
                list($match, $start) = $matches[1][$i];
                if (strpos($match, 'autocomplete')===false) {
                    $start += strlen($match);
                    $this->bodycontent = substr_replace($this->bodycontent, ' autocomplete="off"', $start, 0);
                }
            }
        }
        parent::fix_bodycontent();
    }

    /**
     * fix_headcontent
     */
    function fix_headcontent() {
        if ($pos = strrpos($this->headcontent, '</style>')) {
            $insert = ''
                .'#'.$this->themecontainer.' ol.QuizQuestions{'."\n"
                .'	margin-bottom: 0px;'."\n"
                .'}'."\n"
                .'#'.$this->themecontainer.' li.QuizQuestion{'."\n"
                .'	overflow: auto;'."\n"
                .'}'."\n"
            ;
            $this->headcontent = substr_replace($this->headcontent, $insert, $pos, 0);
        }
        parent::fix_headcontent();
    }

    /**
     * get_js_functionnames
     *
     * @return xxx
     */
    function get_js_functionnames()  {
        // start list of function names
        $names = parent::get_js_functionnames();
        $names .= ($names ? ',' : '').'TypeChars,ShowHint,ShowAnswers,ChangeQ,ShowHideQuestions,CheckMCAnswer,CheckMultiSelAnswer,CheckShortAnswer,CheckFinished,SwitchHybridDisplay,SetUpQuestions,CalculateOverallScore';
        return $names;
    }

    /**
     * fix_js_TypeChars_obj
     *
     * @return xxx
     */
    function fix_js_TypeChars_obj()  {
        return 'CurrBox';
    }

    /**
     * fix_js_ChangeQ
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_ChangeQ(&$str, $start, $length)  {
        $substr = substr($str, $start, $length);

        // stretch the canvas vertically down to cover the reading, if any
        if ($pos = strrpos($substr, '}')) {
            $substr = substr_replace($substr, '	StretchCanvasToCoverContent(true);'."\n", $pos, 0);
        }

        $str = substr_replace($str, $substr, $start, $length);
    }

    /**
     * fix_js_ShowHideQuestions
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_ShowHideQuestions(&$str, $start, $length)  {
        $substr = substr($str, $start, $length);

        // hide/show bottom border of questions (override class="QuizQuestion")
        $n = "\n\t\t\t\t";
        if ($pos = strpos($substr, "QArray[i].style.display = '';")) {
            $substr = substr_replace($substr, 'if ((i+1)<QArray.length){'.$n."\t"."QArray[i].style.borderWidth = '';".$n.'}'.$n, $pos, 0);
        }
        if ($pos = strpos($substr, "if (i != CurrQNum){")) {
            $substr = substr_replace($substr, "QArray[i].style.borderWidth = '0px';".$n, $pos, 0);
        }

        // stretch the canvas vertically down to cover the reading, if any
        if ($pos = strrpos($substr, '}')) {
            $substr = substr_replace($substr, '	StretchCanvasToCoverContent(true);'."\n", $pos, 0);
        }

        $str = substr_replace($str, $substr, $start, $length);
    }

    /**
     * fix_js_SetUpQuestions
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_SetUpQuestions(&$str, $start, $length)  {
        $substr = substr($str, $start, $length);

        // catch errors due to invalid XHTML syntax (e.g. unclosed <font> tags)
        $search  = "/\s+while \(Qs\.getElementsByTagName\('li'\)\.length > 0\)\{.*?\}/s";
        $replace = "\n"
            ."	while (Qs.firstChild) {\n"
            ."		var Q = Qs.removeChild(Qs.firstChild);\n"
            ."		if (Q.nodeType==1) {\n"
            ."			if (Q.tagName=='LI') {\n"
            ."				QList.push(Q);\n"
            ."			} else {\n"
            ."				alert('Sorry, SetUpQuestions() failed.\\n\\n'+'Perhaps there are some invalid\\n'+'HTML tags in question ' + QList.length + ' ?');\n"
            ."			}\n"
            ."		}\n"
            ."	}\n"
        ;
        $substr = preg_replace($search, $replace, $substr, 1);

        // hide bottom border of question (override class="QuizQuestion")
        if ($pos = strpos($substr, "Qs.appendChild(QList[i]);")) {
            $substr = substr_replace($substr, "QList[i].style.borderWidth = '0px';"."\n\t\t", $pos, 0);
        }

        $str = substr_replace($str, $substr, $start, $length);
    }

    /**
     * fix_js_SwitchHybridDisplay
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_SwitchHybridDisplay(&$str, $start, $length)  {
        $substr = substr($str, $start, $length);

        // stretch the canvas vertically down to cover the reading, if any
        if ($pos = strrpos($substr, '}')) {
            $substr = substr_replace($substr, '	StretchCanvasToCoverContent(true);'."\n", $pos, 0);
        }

        $str = substr_replace($str, $substr, $start, $length);
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
        if ($pos = strpos($substr, '{')) {
            $insert = "\n"
                ."	// intercept this Hint\n"
                ."	HP.onclickHint(QNum);\n"
            ;
            $substr = substr_replace($substr, $insert, $pos+1, 0);
        }

        $str = substr_replace($str, $substr, $start, $length);
    }

    /**
     * fix_js_ShowAnswers
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_ShowAnswers(&$str, $start, $length)  {
        $substr = substr($str, $start, $length);

        // intercept Clues
        if ($pos = strpos($substr, '{')) {
            $insert = "\n"
                ."	// intercept this Clue\n"
                ."	if (State[QNum][0]<1) HP.onclickClue(QNum);\n"
            ;
            $substr = substr_replace($substr, $insert, $pos+1, 0);
        }

        $str = substr_replace($str, $substr, $start, $length);
    }

    /**
     * fix_js_CheckMCAnswer
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_CheckMCAnswer(&$str, $start, $length)  {
        $substr = substr($str, $start, $length);

        // intercept Check
        if ($pos = strpos($substr, '{')) {
            $insert = "\n"
                ."	// intercept this Check\n"
                ."	if(!Finished && State[QNum].length && State[QNum][0]<0) {\n"
                ."		var args = new Array(QNum, I[QNum][3][ANum][0]);\n"
                ."		HP.onclickCheck(args);\n"
                ."	}\n"
            ;
            $substr = substr_replace($substr, $insert, $pos+1, 0);
        }

        $str = substr_replace($str, $substr, $start, $length);
    }

    /**
     * fix_js_CheckShortAnswer
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_CheckShortAnswer(&$str, $start, $length)  {
        $substr = substr($str, $start, $length);

        // intercept Check
        if ($pos = strpos($substr, '{')) {
            $insert = "\n"
                ."	// intercept this Check\n"
                ."	if(!Finished && State[QNum].length && State[QNum][0]<0) {\n"
                ."		var obj = document.getElementById('Q_'+QNum+'_Guess');\n"
                ."		var args = new Array(QNum, obj.value);\n"
                ."		HP.onclickCheck(args);\n"
                ."	}\n"
            ;
            $substr = substr_replace($substr, $insert, $pos+1, 0);
        }

        $str = substr_replace($str, $substr, $start, $length);
    }

    /**
     * fix_js_CheckMultiSelAnswer
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_CheckMultiSelAnswer(&$str, $start, $length)  {
        $substr = substr($str, $start, $length);

        // intercept Check
        if ($pos = strpos($substr, '{')) {
            $insert = "\n"
                ."	// intercept this Check\n"
                ."	if(!Finished && State[QNum].length && State[QNum][0]<0) {\n"
                ."		var g='';\n"
                ."		for (var ANum=0; ANum<I[QNum][3].length; ANum++){\n"
                ."			var obj = document.getElementById('Q_'+QNum+'_'+ANum+'_Chk');\n"
                ."			if (obj.checked) g += (g ? '+' : '') + I[QNum][3][ANum][0];\n"
                ."		}\n"
                ."		var args = new Array(QNum, g);\n"
                ."		HP.onclickCheck(args);\n"
                ."	}\n"
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
        $substr = substr($str, $start, $length);

        // remove creation of HotPotNet results XML and call to Finish()
		$search = '/\s*'.'Detail = [^;]*?;'.'.*?'.'setTimeout[^;]*;'.'/s';
        $substr = preg_replace($search, '', $substr, 1);

        // add other changes as per CheckAnswers in other type of HP quiz
        $this->fix_js_CheckAnswers($substr, 0, strlen($substr));

        // use "HP.setScoreAndPenalties(true)" instead of "CalculateOverallScore()"
        // because it calculates the score for the whole quiz,
        // not just for questions attempted so far
        // this is particular important when someone hits the "STOP" button
        $search = 'CalculateOverallScore();';
        $substr = str_replace($search, 'HP.setScoreAndPenalties(true);', $substr);

        $str = substr_replace($str, $substr, $start, $length);
    }

    /**
     * fix_js_CalculateOverallScore
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_CalculateOverallScore(&$str, $start, $length)  {
        $substr = substr($str, $start, $length);

        $substr = preg_replace('/(\s*)var TotalScore = 0;/s', '$0$1'.'var TotalCount = 0;', $substr, 1);
        $substr = preg_replace('/(\s*)TotalScore \+= [^;]*;/s', '$0$1'.'TotalCount ++;', $substr, 1);
        $substr = preg_replace('/(\s*)\}\s*else\s*\{/s', '$1} else if (TotalCount==0) {$1'."\t".'Score = 0;'.'$0', $substr, 1);

        $str = substr_replace($str, $substr, $start, $length);
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
     * get_stop_function_intercept
     *
     * @return xxx
     */
    function get_stop_function_intercept()  {
        // intercept is not required in the giveup function of JQuiz
        // because the checks are intercepted by CheckFinished (see above)
        return '';
    }

    /**
     * get_stop_function_search
     *
     * @return xxx
     */
    function get_stop_function_search()  {
        return '/\s*if \((AllDone) == true\)({.*?WriteToInstructions[^;]*;).*?\w+ = true;\s*}\s*/s';
    }
}
