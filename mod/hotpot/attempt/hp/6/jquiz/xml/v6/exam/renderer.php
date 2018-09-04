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
 * Output format: hp_6_jquiz_xml_v6_exam
 *
 * @package   mod-hotpot
 * @copyright 2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// get parent class
require_once($CFG->dirroot.'/mod/hotpot/attempt/hp/6/jquiz/xml/v6/renderer.php');

/**
 * mod_hotpot_attempt_hp_6_jquiz_xml_v6_exam_renderer
 *
 * @copyright 2010 Gordon Bateson
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class mod_hotpot_attempt_hp_6_jquiz_xml_v6_exam_renderer extends mod_hotpot_attempt_hp_6_jquiz_xml_v6_renderer {

    // to faciliate autoadvance to next question, pass QNum to ShowMessage()
    public $searchShowMessage = '/(?<=ShowMessage)\(([^)]*)\)/';
    public $replaceShowMessage = '($1, QNum)';

    // to facilitate autoplay of media, call PlaySound() every time we advance to a new question
    public $searchSetQNumReadout = '/(\s*)SetQNumReadout\(\);/';
    public $replaceSetQNumReadout = '$1PlaySound(CurrQNum,0);$0';

    /**
     * List of source types which this renderer can handle
     *
     * @return array of strings
     */
    static public function sourcetypes()  {
        return array('hp_6_jquiz_xml');
    }

    /**
     * get_js_functionnames
     *
     * @return xxx
     */
    function get_js_functionnames()  {
        // start list of function names
        $names = parent::get_js_functionnames();
        $names .= ($names ? ',' : '').'CompleteEmptyFeedback';
        return $names;
    }

    /**
     * fix_js_StartUp
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_StartUp(&$str, $start, $length)  {
        $substr = substr($str, $start, $length);
        $search = '/(\s*)strInstructions = [^;]*;/';
        $replace = '$1'
            ."var obj = document.getElementById('Instructions');".'$1'
            ."if (obj==null || obj.innerHTML=='') {".'$1'
            ."	var obj = document.getElementById('InstructionsDiv');".'$1'
            ."	if (obj) {".'$1'
            ."		obj.style.display = 'none';".'$1'
            ."	}".'$1'
            ."}"
        ;
        $substr = preg_replace($search, $replace, $substr, 1);
        parent::fix_js_StartUp($substr, 0, strlen($substr));

        // append the PlaySound() and StopSound() functions
        if ($pos = strrpos($substr, '}')) {
            $append = "\n"
                ."function PlaySound(i, count) {\n"
                .'	// li (id="Q_99") -> p (class="questionText") -> span (class="mediaplugin_mp3") -> object'."\n"
                ."	var li = QArray[i];\n"
                ."	try {\n"
                ."		var SoundLoaded = li.childNodes[0].childNodes[0].childNodes[0].isSoundLoadedFromJS();\n"
                ."	} catch (err) {\n"
                ."		var SoundLoaded = false;\n"
                ."	}\n"
                ."	if (SoundLoaded) {\n"
                ."		try {\n"
                ."			li.childNodes[0].childNodes[0].childNodes[0].playSoundFromJS();\n"
                ."			var SoundPlayed = true;\n"
                ."		} catch (err) {\n"
                ."			var SoundPlayed = false;\n"
                ."		}\n"
                ."	}\n"
                ."	if (SoundLoaded && SoundPlayed) {\n"
                ."		// sound was successfully played\n"
                ."	} else {\n"
                ."		// sound could not be loaded or played\n"
                ."		if (count<=100) {\n"
                ."			// try again in 1/10th of a second\n"
                ."			setTimeout('PlaySound('+i+','+(count+1)+')', 100);\n"
                ."		}\n"
                ."	}\n"
                ."}\n"
                ."function StopSound(i) {\n"
                .'	// li (id="Q_99") -> p (class="questionText") -> span (class="mediaplugin_mp3") -> object'."\n"
                ."	var li = QArray[i];\n"
                ."	try {\n"
                ."		var SoundLoaded = li.childNodes[0].childNodes[0].childNodes[0].isSoundLoadedFromJS();\n"
                ."	} catch (err) {\n"
                ."		var SoundLoaded = false;\n"
                ."	}\n"
                ."	if (SoundLoaded) {\n"
                ."		try {\n"
                ."			li.childNodes[0].childNodes[0].childNodes[0].stopSoundFromJS();\n"
                ."			var SoundStopped = true;\n"
                ."		} catch (err) {\n"
                ."			var SoundStopped = false;\n"
                ."		}\n"
                ."	}\n"
                ."}"
            ;
            $substr = substr_replace($substr, $append, $pos+1, 0);
        }
        $str = substr_replace($str, $substr, $start, $length);
    }

    /**
     * fix_js_ShowMessage
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     * @return xxx
     */
    function fix_js_ShowMessage(&$str, $start, $length)  {
        $substr = substr($str, $start, $length);

        // do standard fix for this function
        parent::fix_js_ShowMessage($substr, 0, strlen($substr));

        // add extra argument, QNum, to this function
        $substr = preg_replace($this->searchShowMessage, $this->replaceShowMessage, $substr, 1);

        if ($pos = strpos($substr, '{')) {
            $insert = "\n"
                ."	if (typeof(QNum)!='undefined' && State[QNum] && State[QNum][0]>=0) {\n"
                ."		// this question is finished\n"
                ."		StopSound(CurrQNum);\n"
                ."		if (ShowingAllQuestions) {\n"
                ."			CurrQNum = QNum;\n"
                ."		} else {\n"
                ."			// move to next question, if there is one\n"
                ."			var i_max = QArray.length;\n"
                ."			for (var i=1; i<i_max; i++) {\n"
                ."				// calculate the next index for QArray\n"
                ."				var next_i = (i + CurrQNum) % i_max;\n"
                ."				if (QArray[next_i] && QArray[next_i].id) {\n"
                ."					var matches = QArray[next_i].id.match(new RegExp('\\\\d+$'));\n"
                ."					if (matches) {\n"
                ."						var next_q = parseInt(matches[0]);\n"
                ."						if (State[next_q] && State[next_q][0]<0) {\n"
                ."							// change to unanswered question\n"
                ."							ChangeQ(next_i - CurrQNum);\n"
                ."							break;\n"
                ."						}\n"
                ."					}\n"
                ."				}\n"
                ."			}\n"
                ."		}\n"
                ."	}\n"
                ."	// only show feedback if quiz is finished\n"
                ."	if (! Finished) return false;"
            ;
            $substr = substr_replace($substr, $insert, $pos+1, 0);
        }
        $str = substr_replace($str, $substr, $start, $length);
    }

    /**
     * fix_js_WriteToInstructions
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_WriteToInstructions(&$str, $start, $length)  {
        $substr = substr($str, $start, $length);

        $search = "/(\s*)document\.getElementById\('InstructionsDiv'\)\.innerHTML = Feedback;/";
        $replace = '$1'
            ."var AllDone = true;".'$1'
            ."for (var QNum=0; QNum<State.length; QNum++){".'$1'
            ."	if (State[QNum]){".'$1'
            ."		if (State[QNum][0] < 0){".'$1'
            ."			AllDone = false;".'$1'
            ."		}".'$1'
            ."	}".'$1'
            ."}".'$1'
            ."if (AllDone) {".'$1'
            ."	var obj = document.getElementById('InstructionsDiv');".'$1'
            ."	if (obj) {".'$1'
            ."		obj.innerHTML = Feedback;".'$1'
            ."		obj.style.display = '';".'$1'
            ."	}".'$1'
            ."	Finished = true;".'$1'
            ."	ShowMessage(Feedback);".'$1'
            ."}"
        ;
        $substr = preg_replace($search, $replace, $substr, 1);

        parent::fix_js_WriteToInstructions($substr, 0, strlen($substr));
        $str = substr_replace($str, $substr, $start, $length);
    }

    /**
     * fix_js_CompleteEmptyFeedback
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_CompleteEmptyFeedback(&$str, $start, $length)  {
        $substr = substr($str, $start, $length);

        // display only correct feedback
        $substr = str_replace('DefaultWrong', 'DefaultRight', $substr);

        // set all answers to be treated as correct
        $search = '/(\s*)if \(I\[QNum\]\[3\]\[ANum\]\[1\]\.length < 1\)\{/';
        $replace = '$1'.'I[QNum][3][ANum][2] = 1;'.'$0';
        $substr = preg_replace($search, $replace, $substr, 1);

        $str = substr_replace($str, $substr, $start, $length);
    }

    // utility function to search and replace and, if required, call the fix_js_xxx() method of the parent class

    /**
     * fix_js_search_replace
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     * @param xxx $thisfunction
     * @param xxx $parentfunction (optional, default='')
     */
    function fix_js_search_replace(&$str, $start, $length, $thisfunction, $parentfunction='') {
        $substr = substr($str, $start, $length);

        $search = 'search'.$thisfunction;
        $replace = 'replace'.$thisfunction;
        $substr = preg_replace($this->$search, $this->$replace, $substr);

        if ($parentfunction) {
            $parentmethod = 'fix_js_'.$parentfunction;
            parent::$parentmethod($substr, 0, strlen($substr));
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
        $this->fix_js_search_replace($str, $start, $length, 'SetQNumReadout', 'SetUpQuestions');
    }

    /**
     * fix_js_ChangeQ
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_ChangeQ(&$str, $start, $length)  {
        $this->fix_js_search_replace($str, $start, $length, 'SetQNumReadout', 'ChangeQ');
    }

    /**
     * fix_js_ShowAnswers
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_ShowAnswers(&$str, $start, $length)  {
        $this->fix_js_search_replace($str, $start, $length, 'ShowMessage');
    }

    /**
     * fix_js_ShowHint
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_ShowHint(&$str, $start, $length)  {
        $this->fix_js_search_replace($str, $start, $length, 'ShowMessage');
    }

    /**
     * fix_js_CheckMCAnswer
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_CheckMCAnswer(&$str, $start, $length)  {
        $this->fix_js_search_replace($str, $start, $length, 'ShowMessage', 'CheckMCAnswer');
    }

    /**
     * fix_js_CheckMultiSelAnswer
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_CheckMultiSelAnswer(&$str, $start, $length)  {
        $this->fix_js_search_replace($str, $start, $length, 'ShowMessage', 'CheckMultiSelAnswer');
    }

    /**
     * fix_js_CheckShortAnswer
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_CheckShortAnswer(&$str, $start, $length)  {
        $this->fix_js_search_replace($str, $start, $length, 'ShowMessage', 'CheckShortAnswer');
    }
}
