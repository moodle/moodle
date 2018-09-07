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
 * Output format: hp_6_jquiz_xml_v6_autoadvance
 *
 * @package   mod-hotpot
 * @copyright 2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// get parent class
require_once($CFG->dirroot.'/mod/hotpot/attempt/hp/6/jquiz/xml/v6/renderer.php');

/**
 * mod_hotpot_attempt_hp_6_jquiz_xml_v6_autoadvance_renderer
 *
 * @copyright 2010 Gordon Bateson
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class mod_hotpot_attempt_hp_6_jquiz_xml_v6_autoadvance_renderer extends mod_hotpot_attempt_hp_6_jquiz_xml_v6_renderer {

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
    function get_js_functionnames() {
        // start list of function names
        $names = parent::get_js_functionnames();
        $names .= ($names ? ',' : '').'CompleteEmptyFeedback,ReplaceGuessBox,SetFocusToTextbox';
        return $names;
    }

    /**
     * fix_js_WriteToInstructions
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_WriteToInstructions(&$str, $start, $length) {
        $substr = substr($str, $start, $length);

        $search = "/(\s*)document\.getElementById\('InstructionsDiv'\)\.innerHTML = Feedback;/";
        $replace = "\\1"
            ."var AllDone = true;\\1"
            ."for (var QNum=0; QNum<State.length; QNum++){\\1"
            ."	if (State[QNum]){\\1"
            ."		if (State[QNum][0] < 0){\\1"
            ."			AllDone = false;\\1"
            ."		}\\1"
            ."	}\\1"
            ."}\\1"
            ."if (AllDone) {\\1"
            ."	var obj = document.getElementById('InstructionsDiv');\\1"
            ."	if (obj) {\\1"
            ."		obj.innerHTML = Feedback;\\1"
            ."		obj.style.display = '';\\1"
            ."	}\\1"
            ."	var obj = document.getElementById('FeedbackDiv');\\1"
            ."	if (obj && obj.style.display=='block') {\\1"
            ."		var obj = document.getElementById('FeedbackContent');\\1"
            ."		if (obj) {\\1"
            ."			Feedback = obj.innerHTML + Feedback;;\\1"
            ."		}\\1"
            ."	}\\1"
            ."	Finished = true;\\1"
            ."	ShowMessage(Feedback);\\1"
            ."}"
        ;
        $substr = preg_replace($search, $replace, $substr, 1);

        parent::fix_js_WriteToInstructions($substr, 0, strlen($substr));
        $str = substr_replace($str, $substr, $start, $length);
    }

    /**
     * fix_js_StartUp
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_StartUp(&$str, $start, $length) {
        $substr = substr($str, $start, $length);

        // hide instructions, if they are not required
        $search = '/(\s*)strInstructions = document.getElementById[^;]*;/s';
        $replace = ''
            .'\\1'."var obj = document.getElementById('Instructions');"
            .'\\1'."if (obj==null || obj.innerHTML=='') {"
            .'\\1'."	var obj = document.getElementById('InstructionsDiv');"
            .'\\1'."	if (obj) {"
            .'\\1'."		obj.style.display = 'none';"
            .'\\1'."	}"
            .'\\1'."}"
        ;
        //$substr = preg_replace($search, $replace, $substr, 1);

        $insert = '';
        if ($this->expand_UserDefined1()) {
            $insert .= "	AA_SetProgressBar();\n";
        }
        if ($this->expand_UserDefined2()) {
            $insert .= "	setTimeout('AA_PlaySound(0,0)', 500);";
        }
        if ($insert) {
            $pos = strrpos($substr, '}');
            $substr = substr_replace($substr, $insert, $pos, 0);
        }

        // call the fix_js_StartUp() method on the parent object
        parent::fix_js_StartUp($substr, 0, strlen($substr));

        $str = substr_replace($str, $substr, $start, $length);
    }

    /**
     * fix_js_SetUpQuestions
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     * @return xxx
     */
    function fix_js_SetUpQuestions(&$str, $start, $length) {
        global $CFG;
        $substr = substr($str, $start, $length);

        parent::fix_js_SetUpQuestions($substr, 0, strlen($substr));

        $dots = 'squares'; // default
        if ($param = clean_param($this->expand_UserDefined1(), PARAM_ALPHANUM)) {
            if (is_dir($CFG->dirroot."/mod/hotpot/pix/autoadvance/$param")) {
                $dots = $param;
            }
        }

        // assume there are no jmix questions
        $jmix = true;

        $search = 'Qs.appendChild(QList[i]);';
        if ($pos = strpos($substr, $search)) {
            $insert = "\n"
                ."		SetUpJMixQuestion(i);\n"
            ;
            $substr = substr_replace($substr, $insert, $pos + strlen($search), 0);
        }

        // add progress bar
        if ($dots) {
            $search = '/\s*'.'SetQNumReadout\(\);/';
            $replace = "\n"
                ."	var ProgressBar = document.createElement('div');\n"
                ."	ProgressBar.setAttribute('id', 'ProgressBar');\n"
                ."	ProgressBar.setAttribute(AA_className(), 'ProgressBar');\n"
                ."\n"
                ."	// add feedback boxes and progess dots for each question\n"
                ."	for (var i=0; i<QArray.length; i++){\n"
                ."\n"
                .'		// remove bottom border (override class="QuizQuestion")'."\n"
                ."		if (QArray[i]) {\n"
                ."			QArray[i].style.borderWidth = '0px';\n"
                ."		}\n"
                ."\n"
                ."		if (ProgressBar.childNodes.length) {\n"
                ."			// add arrow between progress dots\n"
                ."			ProgressBar.appendChild(document.createTextNode(' '));\n"
                ."			ProgressBar.appendChild(AA_ProgressArrow());\n"
                ."			ProgressBar.appendChild(document.createTextNode(' '));\n"
                ."		}\n"
                ."		ProgressBar.appendChild(AA_ProgressDot(i));\n"
                ."\n"
                ."		// AA_Add_FeedbackBox(i);\n"
                ."	}\n"
                ."	var OneByOneReadout = document.getElementById('OneByOneReadout');\n"
                ."	if (OneByOneReadout) {\n"
                ."		OneByOneReadout.parentNode.insertBefore(ProgressBar, OneByOneReadout);\n"
                ."		OneByOneReadout.parentNode.removeChild(OneByOneReadout);\n"
                ."	}\n"
                ."	OneByOneReadout = null;\n"
                ."	// hide the div containing ShowMethodButton, PrevQButton and NextQButton\n"
                ."	var btn = document.getElementById('ShowMethodButton');\n"
                ."	if (btn) {\n"
                ."		btn.parentNode.style.display = 'none';\n"
                ."	}\n"
                ."	// activate first Progress dot\n"
                ."	AA_SetProgressDot(0, 0);\n"
                ."\t"
            ;
            $substr = preg_replace($search, $replace, $substr, 1);
        }

        if ($jmix || $dots) {
            $substr .= "\n"
                ."function AA_isNonStandardIE() {\n"
                ."	if (typeof(window.isNonStandardIE)=='undefined') {\n"
                ."		if (navigator.appName=='Microsoft Internet Explorer' && (document.documentMode==null || document.documentMode<8)) {\n"
                ."			// either IE8+ (in compatability mode) or IE7, IE6, IE5 ...\n"
                ."			window.isNonStandardIE = true;\n"
                ."		} else {\n"
                ."			// Firefox, Safari, Opera, IE8+\n"
                ."			window.isNonStandardIE = false;\n"
                ."		}\n"
                ."	}\n"
                ."	return window.isNonStandardIE;\n"
                ."}\n"
                ."function AA_className() {\n"
                ."	if (AA_isNonStandardIE()){\n"
                ."		return 'className';\n"
                ."	} else {\n"
                ."		return 'class';\n"
                ."	}\n"
                ."}\n"
                ."function AA_onclickAttribute(fn) {\n"
                ."	if (AA_isNonStandardIE()){\n"
                ."		return new Function(fn);\n"
                ."	} else {\n"
                ."		return fn; // just return the string\n"
                ."	}\n"
                ."}\n"
            ;
        }

        if ($dots) {
            // add functions required for progress bar
            $substr .= "\n"
                ."function AA_images() {\n"
                ."	return 'pix/autoadvance/$dots';\n"
                ."}\n"
                ."function AA_ProgressArrow() {\n"
                ."	var img = document.createElement('img');\n"
                ."	var src = 'ProgressDotArrow.gif';\n"
                ."	img.setAttribute('src', AA_images() + '/' + src);\n"
                ."	img.setAttribute('alt', src);\n"
                ."	img.setAttribute('title', src);\n"
                ."	//img.setAttribute('height', 18);\n"
                ."	//img.setAttribute('width', 18);\n"
                ."	img.setAttribute(AA_className(), 'ProgressDotArrow');\n"
                ."	return img;\n"
                ."}\n"
                ."function AA_ProgressDot(i) {\n"
                ."	// i is either an index on QArray \n"
                ."	// or a string to be used as an id for an HTML element\n"
                ."	if (typeof(i)=='string') {\n"
                ."		var id = i;\n"
                ."		var add_link = false;\n"
                ."	} else if (QArray[i]) {\n"
                ."		var id = QArray[i].id;\n"
                ."		var add_link = true;\n"
                ."	} else {\n"
                ."		return false;\n"
                ."	}\n"
                ."	// id should now be: 'Q_' + q ...\n"
                ."	// where q is an index on the State array\n"
                ."	var src = 'ProgressDotEmpty.gif';\n"
                ."	var img = document.createElement('img');\n"
                ."	img.setAttribute('id', id + '_ProgressDotImg');\n"
                ."	img.setAttribute('src', AA_images() + '/' + src);\n"
                ."	img.setAttribute('alt', src);\n"
                ."	img.setAttribute('title', src);\n"
                ."	//img.setAttribute('height', 18);\n"
                ."	//img.setAttribute('width', 18);\n"
                ."	img.setAttribute(AA_className(), 'ProgressDotEmpty');\n"
                ."	if (add_link) {\n"
                ."		var link = document.createElement('a');\n"
                ."		link.setAttribute('id', id + '_ProgressDotLink');\n"
                ."		link.setAttribute(AA_className(), 'ProgressDotLink');\n"
                ."		link.setAttribute('title', 'go to question '+(i+1));\n"
                ."		var fn = 'ChangeQ('+i+'-CurrQNum);return false;';\n"
                ."		link.setAttribute('onclick', AA_onclickAttribute(fn));\n"
                ."		link.appendChild(img);\n"
                ."	}\n"
                ."	var span = document.createElement('span');\n"
                ."	span.setAttribute('id', id + '_ProgressDot');\n"
                ."	span.setAttribute(AA_className(), 'ProgressDot');\n"
                ."	if (add_link) {\n"
                ."		span.appendChild(link);\n"
                ."	} else {\n"
                ."		span.appendChild(img);\n"
                ."	}\n"
                ."	return span;\n"
                ."}\n"
                ."function AA_JQuiz_GetQ(i) {\n"
               ."	if (! QArray[i]) {\n"
                ."		return -1;\n"
                ."	}\n"
                ."	if (! QArray[i].id) {\n"
                ."		return -1;\n"
                ."	}\n"
                ."	var matches = QArray[i].id.match(new RegExp('\\\\d+$'));\n"
                ."	if (! matches) {\n"
                ."		return -1;\n"
                ."	}\n"
                ."	var q = matches[0];\n"
                ."	if (! State[q]) {\n"
                ."		return -1;\n"
                ."	}\n"
                ."	return parseInt(q);\n"
                ."}\n"
                ."function AA_SetProgressDot(q, next_q) {\n"
                ."	var img = document.getElementById('Q_'+q+'_ProgressDotImg');\n"
                ."	if (! img) {\n"
                ."		return;\n"
                ."	}\n"
                ."	var src = '';\n"
                ."	// State[q][0] : the score (as a decimal fraction of 1)  for this question (initially -1)\n"
                ."	// State[q][2] : no of checks for this question (initially 0)\n"
                ."	if (State[q] && State[q][0]>=0) {\n"
                ."		var score = Math.max(0, I[q][0] * State[q][0]);\n"
                ."		// Note that if there are only two options on a multiple-choice question, then \n"
                ."		// even if the wrong answer is chosen, the question will be considered finished\n"
                ."		if (neutralProgressBar) {\n"
                ."			src = 'ProgressDotCorrect00Plus'+'.gif';\n"
                ."		} else if (score >= 99) {\n"
                ."			src = 'ProgressDotCorrect99Plus'+'.gif';\n"
                ."		} else if (score >= 80) {\n"
                ."			src = 'ProgressDotCorrect80Plus'+'.gif';\n"
                ."		} else if (score >= 60) {\n"
                ."			src = 'ProgressDotCorrect60Plus'+'.gif';\n"
                ."		} else if (score >= 40) {\n"
                ."			src = 'ProgressDotCorrect40Plus'+'.gif';\n"
                ."		} else if (score >= 20) {\n"
                ."			src = 'ProgressDotCorrect20Plus'+'.gif';\n"
                ."		} else if (score >= 0) {\n"
                ."			src = 'ProgressDotCorrect00Plus'+'.gif';\n"
                ."		} else {\n"
                ."			// this question has negative score, which means it has not yet been correctly answered\n"
                ."			src = 'ProgressDotWrong'+'.gif';\n"
                ."		}\n"
                ."	} else {\n"
                ."		// this question has not been completed\n"
                ."		if (typeof(next_q)=='number' && q==next_q) {\n"
                ."			// this question will be attempted next\n"
                ."			src = 'ProgressDotCurrent'+'.gif';\n"
                ."		} else {\n"
                ."			src = 'ProgressDotEmpty'+'.gif';\n"
                ."		}\n"
                ."	}\n"
                ."	var full_src = AA_images() + '/' + src;\n"
                ."	if (img.src != full_src) {\n"
                ."		img.setAttribute('src', full_src);\n"
                ."	}\n"
                ."}\n"
                ."function AA_SetProgressBar(next_q) {\n"
                ."	// next_q is an index on State array\n"
                ."	// CurrQNum is an index on QArray\n"
                ."	if (typeof(next_q)=='undefined') {\n"
                ."		next_q = AA_JQuiz_GetQ(window.CurrQNum || 0);\n"
                ."	}\n"
                ."	for (var i=0; i<QArray.length; i++) {\n"
                ."		var q = AA_JQuiz_GetQ(i);\n"
                ."		if (q>=0) {\n"
                ."			AA_SetProgressDot(q, next_q);\n"
                ."		}\n"
                ."	}\n"
                ."}\n"
            ;
        }

        // append the PlaySound() and StopSound() functions
        if ($this->expand_UserDefined2()) {
            $substr .= "\n"
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
        }

        // append the SetUpJMixQuestion() and MoveJumbledItem() functions
        if ($jmix) {
            $substr .= "\n"
                ."function SetUpJMixQuestion(q) {\n"
                ."	var JumbledItems = document.getElementById('Q_'+q+'_JumbledItems');\n"
                ."	if (JumbledItems==null) {\n"
                ."		return;\n"
                ."	}\n"
                ."	var spans = new Array();\n"
                ."	var i_max = JumbledItems.getElementsByTagName('span').length;\n"
                ."	for (var i=i_max; i>0; i--) {\n"
                ."		spans.push(JumbledItems.removeChild(JumbledItems.getElementsByTagName('span')[i-1]));\n"
                ."	}\n"
                ."	var i_max = JumbledItems.childNodes.length;\n"
                ."	for (var i=i_max; i>0; i--) {\n"
                ."		JumbledItems.removeChild(JumbledItems.childNodes[i-1]);\n"
                ."	}\n"
                ."	spans = Shuffle(spans);\n"
                ."	var i_max = spans.length\n"
                ."	for (var i=0; i<i_max; i++) {\n"
                ."		if (i) {\n"
                ."			JumbledItems.appendChild(document.createTextNode(' '));\n"
                ."		}\n"
                ."		JumbledItems.appendChild(spans[i]);\n"
                ."	}\n"
                ."	spans = null;\n"
                ."	var obj = document.getElementById('Q_'+q+'_Guess');\n"
                ."	if (obj) {\n"
                ."		obj.style.display = 'none';\n"
                ."		var DropArea = document.createElement('span');\n"
                ."		DropArea.setAttribute('id', 'Q_'+q+'_DropArea');\n"
                ."		DropArea.setAttribute(AA_className(), 'DropArea');\n"
                ."		obj.parentNode.insertBefore(DropArea, obj);\n"
                ."	}\n"
                ."}\n"
                ."function MoveJumbledItem(obj) {\n"
                ."	var m = obj.id.match(RegExp('Q_([0-9]+)_[a-zA-Z]+_[0-9]+'));\n"
                ."	if (m && m[0]) {\n"
                ."		var JumbledItems = document.getElementById('Q_'+m[1]+'_JumbledItems');\n"
                ."		var DropArea = document.getElementById('Q_'+m[1]+'_DropArea');\n"
                ."		var Guess = document.getElementById('Q_'+m[1]+'_Guess');\n"
                ."	} else {\n"
                ."		var JumbledItems = null;\n"
                ."		var DropArea = null;\n"
                ."		var Guess = null;\n"
                ."	}\n"
                ."	if (JumbledItems && DropArea && Guess) {\n"
                ."		if (obj.parentNode == JumbledItems) {\n"
                ."			if (obj.previousSibling) {\n"
                ."				JumbledItems.removeChild(obj.previousSibling);\n"
                ."			}\n"
                ."			if (DropArea.childNodes.length) {\n"
                ."				DropArea.appendChild(document.createTextNode(' '));\n"
                ."			}\n"
                ."			DropArea.appendChild(JumbledItems.removeChild(obj));\n"
                ."		} else {\n"
                ."			if (obj.previousSibling) {\n"
                ."				DropArea.removeChild(obj.previousSibling);\n"
                ."			}\n"
                ."			if (JumbledItems.childNodes.length) {\n"
                ."				JumbledItems.appendChild(document.createTextNode(' '));\n"
                ."			}\n"
                ."			JumbledItems.appendChild(DropArea.removeChild(obj));\n"
                ."		}\n"
                ."		Guess.value = '';\n"
                ."		var i_max = DropArea.getElementsByTagName('span').length;\n"
                ."		for (var i=0; i<i_max; i++) {\n"
                ."			if (i) {\n"
                ."				Guess.value += ' ';\n"
                ."			}\n"
                ."			Guess.value += DropArea.getElementsByTagName('span')[i].innerHTML;\n"
                ."		}\n"
                ."	}\n"
                ."}"
            ;
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
    function fix_js_ShowMessage(&$str, $start, $length) {
        $substr = substr($str, $start, $length);

        // do standard fix for this function
        parent::fix_js_ShowMessage($substr, 0, strlen($substr));

        // add extra argument, QNum, to this function
        $substr = preg_replace('/(?<=ShowMessage)\('.'(.*)'.'\)/', '(\\1, QNum)', $substr, 1);

        if ($this->expand_UserDefined2()) {
            $StopSound = "\t\t\t".'StopSound(CurrQNum);'."\n";
        } else {
            $StopSound = '';
        }
        if ($pos = strpos($substr, '{')) {
            $insert = "\n"
                ."	if (typeof(QNum)!='undefined' && State[QNum] && State[QNum][0]>=0) {\n"
                ."		// this question is finished\n"
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
                ."		var q = AA_JQuiz_GetQ(CurrQNum);\n"
                ."		if (q==QNum) {\n"
                ."			// this was the last question\n"
                ."			AA_SetProgressDot(q, q);\n"
                ."		}\n"
                ."	}\n"
                ."	// clear and hide feedback, if necessary\n"
                ."	if (! Feedback) {\n"
                ."		var obj = document.getElementById('FeedbackContent')\n"
                ."		if (obj) {\n"
                ."			obj.innerHTML = '';\n"
                ."		}\n"
                ."		var obj = document.getElementById('FeedbackDiv')\n"
                ."		if (obj) {\n"
                ."			obj.style.display = 'none';\n"
                ."		}\n"
                ."		obj = null;\n"
                ."		return false;\n"
                ."	}"
            ;
            $substr = substr_replace($substr, $insert, $pos+1, 0);
        }
        $str = substr_replace($str, $substr, $start, $length);
    }

    /**
     * fix_js_CompleteEmptyFeedback
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_CompleteEmptyFeedback(&$str, $start, $length) {
        $substr = substr($str, $start, $length);

        // set empty feedback to blank string
        $substr = str_replace('DefaultWrong', "''", $substr);
        $substr = str_replace('DefaultRight', "''", $substr);

        $str = substr_replace($str, $substr, $start, $length);
    }

    /**
     * fix_js_ChangeQ
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_ChangeQ(&$str, $start, $length) {
        $substr = substr($str, $start, $length);

        $search = '/(\s*)SetQNumReadout\('.'(.*?)'.'\);/s';
        $replace = ''
            .'\\1'.'var q = AA_JQuiz_GetQ(CurrQNum - ChangeBy);'
            .'\\1'.'AA_SetProgressDot(q);'
            .'\\1'.'var q = AA_JQuiz_GetQ(CurrQNum);'
            .'\\1'.'AA_SetProgressDot(q, q);'
            .'\\1'.'StretchCanvasToCoverContent(true);'
        ;
        $substr = preg_replace($search, $replace, $substr);

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
     * @param xxx $search_insert (optional, default=null)
     */
    function fix_js_search_replace(&$str, $start, $length, $thisfunction, $parentfunction='', $search_insert=null){
        $substr = substr($str, $start, $length);

        if ($search_insert) {
            foreach ($search_insert as $search => $insert) {
                if ($pos = strpos($substr, $search)) {
                    $substr = substr_replace($substr, $insert, $pos, 0);
                }
            }
        }

        $search = '/(?<='.$thisfunction.')\('.'([^)]*)'.'\)/';
        $replace = '(\\1, QNum)';
        $substr = preg_replace($search, $replace, $substr);

        if ($parentfunction) {
            $parentmethod = 'fix_js_'.$parentfunction;
            parent::$parentmethod($substr, 0, strlen($substr));
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
    function fix_js_ShowAnswers(&$str, $start, $length) {
        $this->fix_js_search_replace($str, $start, $length, 'ShowMessage');
    }

    /**
     * fix_js_ShowHint
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_ShowHint(&$str, $start, $length) {
        $this->fix_js_search_replace($str, $start, $length, 'ShowMessage');
    }

    /**
     * fix_js_CheckMCAnswer
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_CheckMCAnswer(&$str, $start, $length) {
        $search = 'if (I[QNum][3][ANum][2] < 1){';
        $insert = 'if (I[QNum][3][ANum][2] < 1 && ( maximumTriesPerQuestion && State[QNum][2] >= maximumTriesPerQuestion)){'."\n"
            ."		Btn.innerHTML = IncorrectIndicator;\n"
            .$this->js_to_display_feedback('CalculateMCQuestionScore')
            ."		for (var i=0; i<I[QNum][3].length; i++) {\n"
            ."			if (i==ANum) {\n"
            ."				continue;\n"
            ."			}\n"
            ."			var obj = document.getElementById('Q_'+QNum+'_'+i+'_Btn');\n"
            ."			if (obj) {\n"
            ."				obj.parentNode.removeChild(obj);\n"
            ."				obj = null;\n"
            ."			}\n"
            ."		}\n"
            ."	} else "
        ;
        $this->fix_js_search_replace($str, $start, $length, 'ShowMessage', 'CheckMCAnswer', array($search => $insert));
    }

    /**
     * fix_js_CheckMultiSelAnswer
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_CheckMultiSelAnswer(&$str, $start, $length) {
        $search = 'if (Matches == I[QNum][3].length){';
        $insert = 'if (Matches < I[QNum][3].length && ( maximumTriesPerQuestion && State[QNum][2] >= maximumTriesPerQuestion)){'."\n"
            .$this->js_to_display_feedback('CalculateMultiSelQuestionScore')
            ."		for (var i=0; i<I[QNum][3].length; i++) {\n"
            ."			var Chk = document.getElementById('Q_'+QNum+'_'+i+'_Chk');\n"
            ."			if (Chk) {\n"
            ."				Chk.disabled = true;\n"
            ."			}\n"
            ."			Chk = null;\n"
            ."		}\n"
            ."		var Btn = document.getElementById('Q_'+QNum).getElementsByTagName('button');\n"
            ."		if (Btn) {\n"
            ."			Btn[0].parentNode.removeChild(Btn[0]);\n"
            ."		}\n"
            ."		Btn = null;\n"
            ."	} else \n"
        ;
        $search_insert = array(
            'Feedback = Matches'=>'if (Feedback) ',
            $search => $insert
        );
        $this->fix_js_search_replace($str, $start, $length, 'ShowMessage', 'CheckMultiSelAnswer', $search_insert);
    }

    /**
     * fix_js_CheckShortAnswer
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_CheckShortAnswer(&$str, $start, $length) {
        $search_insert = array();

        $search = 'if (CA.CompleteMatch == true){';
        $search_insert[$search] = ''
            ."if ((CA.CompleteMatch==false || CA.Score < 100) && ( maximumTriesPerQuestion && State[QNum][2] >= maximumTriesPerQuestion)){\n"
            .$this->js_to_display_feedback('CalculateShortAnsQuestionScore')
            ."		ShowMessage(CA.Feedback);\n"
            ."		ReplaceGuessBox(QNum, G);\n"
            ."		CheckFinished();\n"
            ."		return;\n"
            ."	}\n"
            ."	"
        ;

        $search = '	if (CA.Feedback.length < 1){CA.Feedback = DefaultWrong;}';
        $search_insert[$search] = ''
            ."	if (document.getElementById('Q_'+QNum+'_JumbledItems')) {\n"
            ."		CA.IncorrectFeedback = new Array()\n"
            ."		var i_max = CA.Answers.length;\n"
            ."		for (var i=0; i<i_max; i++) {\n"
            ."			if (CA.Answers[i].PercentCorrect) {\n"
            ."				continue;\n"
            ."			}\n"
            ."			if (CA.Answers[i].Feedback=='') {\n"
            ."				continue;\n"
            ."			}\n"
            ."			if (CA.Answers[i].Answer==G) {\n"
            ."				continue;\n"
            ."			}\n"
            ."			var ii_max = CA.IncorrectFeedback.length;\n"
            ."			for (var ii=0; ii<ii_max; ii++) {\n"
            ."				if (CA.IncorrectFeedback[ii]==CA.Answers[i].Feedback) {\n"
            ."					break;\n"
            ."				}\n"
            ."			}\n"
            ."			if (ii==ii_max) {\n"
            ."				CA.IncorrectFeedback[ii] = CA.Answers[i].Feedback;\n"
            ."			}\n"
            ."		}\n"
            ."		if (CA.IncorrectFeedback.length) {\n"
            ."			if (CA.Feedback) {\n"
            ."				CA.Feedback += '<br /><br />';\n"
            ."			}\n"
            ."			CA.Feedback += CA.IncorrectFeedback.join('<br /><br />');\n"
            ."		}\n"
            ."	}\n"
        ;

        $this->fix_js_search_replace($str, $start, $length, 'ShowMessage', 'CheckShortAnswer', $search_insert);
    }

    /**
     * fix_js_ShowHideQuestions
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_ShowHideQuestions(&$str, $start, $length) {
        $substr = substr($str, $start, $length);

        $search = "/\s*document\.getElementById\('OneByOneReadout'\)\.style.display = '[^']*';/s";
        $substr = preg_replace($search, '', $substr);

        // do standard fix for this function
        parent::fix_js_ShowHideQuestions($substr, 0, strlen($substr));
    }

    /**
     * js_to_display_feedback
     *
     * @param xxx $functionname
     * @return xxx
     */
    function js_to_display_feedback($functionname) {
        return ''
            ."		State[QNum][0] = 0;\n"
            ."		$functionname(QNum);\n"
            ."		var QsDone = CheckQuestionsCompleted();\n"
            ."		if (ContinuousScoring){\n"
            ."			CalculateOverallScore();\n"
            ."			QsDone = YourScoreIs + ' ' + Score + '%.' + '<br />' + QsDone;\n"
            ."			Feedback += (Feedback=='' ? '' : '<br />') + QsDone;\n"
            ."		}\n"
            ."		WriteToInstructions(QsDone);\n"
        ;
    }

    /**
     * fix_js_ReplaceGuessBox
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_ReplaceGuessBox(&$str, $start, $length) {
        $substr = substr($str, $start, $length);

        if ($pos = strpos($substr, '{')) {
            $insert = "\n"
            ."	var obj = document.getElementById('Q_' + QNum + '_JumbledItemsPrefix');\n"
            ."	if (obj) {\n"
            ."		Ans = obj.innerHTML + ' ' + Ans;\n"
            ."	}\n"
            ."	var obj = document.getElementById('Q_' + QNum + '_JumbledItemsSuffix');\n"
            ."	if (obj) {\n"
            ."		Ans += ' ' + obj.innerHTML;\n"
            ."	}\n"
            ;
            $substr = substr_replace($substr, $insert, $pos + 1, 0);
        }

        $str = substr_replace($str, $substr, $start, $length);
    }

    /**
     * fix_js_SetFocusToTextbox
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_SetFocusToTextbox(&$str, $start, $length) {
        $substr = ''
            ."function SetFocusToTextbox(){\n"
            ."	var obj = QArray[CurrQNum].getElementsByTagName('input') || QArray[CurrQNum].getElementsByTagName('textarea');\n"
            ."	if (obj==null || obj.length==0 || obj[0].style.display=='none') {\n"
            ."		var keypad_display = 'none';\n"
            ."	} else {\n"
            ."		if (obj[0].focus && obj[0].disabled==false) {\n"
            ."			obj[0].focus();\n"
            ."		}\n"
            ."		var keypad_display = 'block';\n"
            ."	}\n"
            ."	var obj = document.getElementById('CharacterKeypad');\n"
            ."	if (obj) {\n"
            ."		obj.style.display = keypad_display;\n"
            ."	}\n"
            ."}\n"
        ;
        $str = substr_replace($str, $substr, $start, $length);
    }

    /**
     * fix_js_CheckFinished
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_CheckFinished(&$str, $start, $length) {
        $substr = substr($str, $start, $length);

        $search = '	var AllDone = true;';
        if ($pos = strpos($substr, $search)) {
            $insert = ''
                ."	var CountWrong = 0;\n"
                ."	var TotalWeighting = 0;\n"
                ."	var BestPossibleScore = 0;\n"
            ;
            $substr = substr_replace($substr, $insert, $pos, 0);
        }

        $search = '			if (State[QNum][0] < 0){';
        if ($pos = strpos($substr, $search)) {
            $insert = ''
                ."			if (State[QNum][2] && State[QNum][0] <= 0){\n"
                ."				CountWrong++;\n"
                ."			}\n"
                ."			TotalWeighting += I[QNum][0]\n"
                ."			if (State[QNum][0] >= 0){\n"
                ."				BestPossibleScore += (I[QNum][0] * State[QNum][0]);\n"
                ."			} else {\n"
                ."				BestPossibleScore += I[QNum][0];\n"
                ."			}\n"
            ;
            $substr = substr_replace($substr, $insert, $pos, 0);
        }

        $search = '	if (AllDone == true){';
        if ($pos = strpos($substr, $search)) {
            $insert = ''
                ."	if (typeof(ForceQuizEvent)=='undefined') {\n"
                ."		if (maximumWrong && CountWrong > maximumWrong) {\n"
                ."			ForceQuizEvent = 3;\n"
                ."		}\n"
                ."		if (TotalWeighting > 0) {\n"
                ."			BestPossibleScore = Math.floor((BestPossibleScore/TotalWeighting)*100);\n"
                ."		} else {\n"
                ."			BestPossibleScore = 100;\n"
                ."		}\n"
                ."		if (minimumScore && BestPossibleScore < minimumScore) {\n"
                ."			ForceQuizEvent = 3;\n"
                ."		}\n"
                ."	}\n"
            ;
            $substr = substr_replace($substr, $insert, $pos, 0);
        }

        parent::fix_js_CheckFinished($substr, 0, strlen($substr));

        $str = substr_replace($str, $substr, $start, $length);
    }

    /**
     * expand_HeaderCode
     *
     * @return xxx
     */
    function expand_HeaderCode() {
        $vars = array(
            'minimumScore' => 0,
            'maximumWrong' => 0,
            'maximumTriesPerQuestion' => 0,
            'neutralProgressBar' => 0
        );
        $headercode = parent::expand_HeaderCode();
        if ($headercode = $this->hotpot->source->single_line($headercode)) {
            $search = '/\s*(?:var\s+)?(\w+)\s*=\s*(\d+)(?:\s*;)?/s';
            if (preg_match_all($search, $headercode, $matches, PREG_OFFSET_CAPTURE)) {
                $i_max = count($matches[0]) - 1;
                for ($i=$i_max; $i>=0; $i--) {
                    list($match, $start) = $matches[0][$i];
                    if (array_key_exists($matches[1][$i][0], $vars)) {
                        $vars[$matches[1][$i][0]] = intval($matches[2][$i][0]);
                        $headercode = substr_replace($headercode, '', $start, strlen($match));
                    }
                }
            }
        }
        $search = array(
            '/(?:\s*\/\/?)\s*<!\[CDATA(?:\s*\/\/)?\s*\]\]>/is',
            '/(?:\s*\/\/)?\s*<!--(?:\s*\/\/)?\s*-->/s',
            '/\s*<script type="text\/javascript">\s*<\/script>/is',
        );
        $headercode = preg_replace($search, '', $headercode);
        if ($headercode = trim($headercode)) {
            $headercode .= "\n";
        }
        $headercode .= '<script type="text/javascript">'."\n".'//<![CDATA['."\n\n";
        foreach ($vars as $name => $value) {
            $headercode .= "    var $name = $value;\n";
        }
        $headercode .= '//]]>'."\n\n".'</script>'."\n";

        $headercode = ''
            .'<style type="text/css">'."\n"
            .'li.QuizQuestion div.JumbledItems {'."\n"
            .'	margin-bottom: 1.0em;'."\n"
            .'	padding-right: 8px;'."\n"
            .'}'."\n"
            .'li.QuizQuestion div.JumbledItems span.JumbledItem {'."\n"
            .'	padding: 2px 6px;'."\n"
            .'	margin: 0px 8px;'."\n"
            .'	border: 2px solid #ff9999;'."\n"
            .'	background-color: #fff9f9;'."\n"
            .'}'."\n"
            .'li.QuizQuestion span.JumbledItemsPrefix {'."\n"
            .'	font-size: 1.1em;'."\n"
            .'}'."\n"
            .'li.QuizQuestion span.DropArea {'."\n"
            .'	padding: 2px 8px;'."\n"
            .'	border: 2px solid #99ff99;'."\n"
            .'	background-color: #f9fff9;'."\n"
            .'}'."\n"
            .'li.QuizQuestion span.DropArea span.JumbledItem {'."\n"
            .'	padding: 0px 4px;'."\n"
            .'}'."\n"
            .'li.QuizQuestion span.JumbledItemsSuffix {'."\n"
            .'	font-size: 1.1em;'."\n"
            .'}'."\n"
            .'</style>'."\n"
            .$headercode
        ;

        return $headercode;
    }

    /**
     * expand_ItemArray_answertext
     *
     * @param xxx $tags
     * @param xxx $answer
     * @param xxx $a
     * @return xxx
     */
    function expand_ItemArray_answertext($tags,  $answer, $a) {
        $text = $this->hotpot->source->xml_value($tags, $answer."['text'][0]['#']", '', false);

        $lines = preg_split('/\s*[\r\n]+\s*/', $text);
        if ($a==0 && $lines[0]=='JMIX') {
            $text = '';
            $jmix = true;
            array_shift($lines);
            foreach ($lines as $line) {
                if (substr($line, 0, 1)=='{' && substr($line, -1)=='}') {
                    continue;
                }
                $text .= ($text=='' ? '' : ' ').$line;
            }
        }

        return $this->hotpot->source->js_value_safe($text, true);
    }

    /**
     * expand_jquiz_textbox_details
     *
     * @param xxx $tags
     * @param xxx $answers
     * @param xxx $q
     * @param xxx $defaultsize (optional, default=9)
     * @return xxx
     */
    function expand_jquiz_textbox_details($tags, $answers, $q, $defaultsize=9) {
        $prefix = '';
        $suffix = '';
        $size = $defaultsize;

        $a = 0;
        $jmix = false;
        $items = array();
        while (($answer = $answers."['answer'][$a]['#']") && $this->hotpot->source->xml_value($tags, $answer)) {
            $text = $this->hotpot->source->xml_value($tags, $answer."['text'][0]['#']", '', false);
            $lines = preg_split('/\s*[\r\n]+\s*/', $text);
            $lines = array_filter($lines);
            if ($a==0 && $lines[0]=='JMIX') {
                $jmix = true;
                array_shift($lines);
            }
            if ($jmix) {
                $text = '';
                foreach ($lines as $line) {
                    if (substr($line, 0, 1)=='{' && substr($line, -1)=='}') {
                        if ($text=='') {
                            $prefix .= substr($line, 1, -1).' ';
                        } else {
                            $suffix .= ' '.substr($line, 1, -1);
                        }
                    } else {
                        $text .= ($text=='' ? '' : ' ').$line;
                        $items[] = $line;
                    }
                }
            }
            $text = preg_replace('/&[#a-zA-Z0-9]+;/', 'x', $text);
            $size = max($size, strlen($text));
            $a++;
        }

        if ($jmix) {

            // add spans to $prefix and $suffix
            if ($prefix) {
                $prefix = '<span id="Q_'.$q.'_JumbledItemsPrefix" class="JumbledItemsPrefix">'.$prefix.'</span>';
            }
            if ($suffix) {
                $suffix = '<span id="Q_'.$q.'_JumbledItemsSuffix" class="JumbledItemsSuffix">'.$suffix.'</span>';
            }

            // create array of random indexes
            $index = range(1, count($items));
            shuffle($index);

            $cues = '';
            foreach ($items as $i => $item) {
                if ($i) {
                    $cues .= ' ';
                }
                $cues .= '<span class="JumbledItem" id="Q_'.$q.'_JumbledItem_'.$index[$i].'" onclick="MoveJumbledItem(this);">'.$item.'</span>';
            }

            // add Jumbled items div
            if ($cues) {
                $prefix = '<div class="JumbledItems" id="Q_'.$q.'_JumbledItems">'.$cues.'</div>'.$prefix;
            }
        }

        return array($prefix, $suffix, $size);
    }
}
