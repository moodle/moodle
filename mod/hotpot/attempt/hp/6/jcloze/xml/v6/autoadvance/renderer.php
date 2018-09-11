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
 * Output format: hp_6_jcloze_xml_v6_autoadvance
 *
 * @package   mod-hotpot
 * @copyright 2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// get parent class
require_once($CFG->dirroot.'/mod/hotpot/attempt/hp/6/jcloze/xml/v6/renderer.php');

/**
 * mod_hotpot_attempt_hp_6_jcloze_xml_v6_autoadvance_renderer
 *
 * @copyright 2010 Gordon Bateson
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class mod_hotpot_attempt_hp_6_jcloze_xml_v6_autoadvance_renderer extends mod_hotpot_attempt_hp_6_jcloze_xml_v6_renderer {

    /**
     * get_js_functionnames
     *
     * @return xxx
     */
    function get_js_functionnames() {
        // start list of function names
        $names = parent::get_js_functionnames();
        $names .= ($names ? ',' : '').'CheckAnswer';
        return $names;
    }

    /**
     * fix_js_StartUp
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     * @return xxx
     */
    function fix_js_StartUp(&$str, $start, $length) {
        global $CFG;

        $substr = substr($str, $start, $length);

        $append = '';
        if ($pos = strrpos($substr, '}')) {

            if ($this->use_DropDownList()) {
                $gaptype = 'select';
            } else {
                $gaptype = 'input';
            }

            $insert = "\n"
                ."	var ClozeBody = null;\n"
                ."	window.CurrentListItem = 0;\n"
                ."	window.ListItems = new Array();\n"
                ."	var div = document.getElementsByTagName('div');\n"
                ."	if (div) {\n"
                ."		var d_max = div.length;\n"
                ."		for (var d=0; d<d_max; d++) {\n"
                ."			if (div[d].className=='ClozeBody') {\n"
                ."				ListItems = div[d].getElementsByTagName('li');\n"
                ."				ClozeBody = div[d];\n"
                ."				break;\n"
                ."			}\n"
                ."		}\n"
                ."	}\n"
                ."	div = null;\n"
                ."	var i_max = ListItems.length;\n"
                ."	var gapid = new RegExp('^Gap[0-9]+\$');\n"
                ."	for (var i=0; i<i_max; i++) {\n"
                ."		ListItems[i].id = 'Q_' + i;\n"
                ."		if (i==CurrentListItem) {\n"
                ."			ListItems[i].style.display = '';\n"
                ."		} else {\n"
                ."			ListItems[i].style.display = 'none';\n"
                ."		}\n"
                ."		ListItems[i].gaps = new Array();\n"
                ."		var gap = ListItems[i].getElementsByTagName('$gaptype');\n"
                ."		if (gap) {\n"
                ."			var g_max = gap.length;\n"
                ."			for (var g=0; g<g_max; g++) {\n"
                ."				if (gapid.test(gap[g].id)) {\n"
                ."					ListItems[i].gaps.push(gap[g]);\n"
                ."				}\n"
                ."			}\n"
                ."			ListItems[i].score = -1;\n"
                ."			ListItems[i].AnsweredCorrectly = false;\n"
                ."		} else {\n"
                ."			ListItems[i].score = 0;\n"
                ."			ListItems[i].AnsweredCorrectly = true;\n"
                ."		}\n"
                ."		gap = null;\n"
                ."	}\n"
                ."	gapid = null;\n"
            ;

            $dots = 'squares'; // default
            if ($param = clean_param($this->expand_UserDefined1(), PARAM_ALPHANUM)) {
                if (is_dir($CFG->dirroot."/mod/hotpot/pix/autoadvance/$param")) {
                    $dots = $param;
                }
            }

            if ($dots) {
                $insert .= ''
                    ."	if (ClozeBody) {\n"
                    ."		var ProgressBar = document.createElement('div');\n"
                    ."		ProgressBar.setAttribute('id', 'ProgressBar');\n"
                    ."		ProgressBar.setAttribute(AA_className(), 'ProgressBar');\n"
                    ."\n"
                    ."		// add feedback boxes and progess dots for each question\n"
                    ."		for (var i=0; i<ListItems.length; i++){\n"
                    ."\n"
                    ."			if (ProgressBar.childNodes.length) {\n"
                    ."				// add arrow between progress dots\n"
                    ."				ProgressBar.appendChild(document.createTextNode(' '));\n"
                    ."				ProgressBar.appendChild(AA_ProgressArrow());\n"
                    ."				ProgressBar.appendChild(document.createTextNode(' '));\n"
                    ."			}\n"
                    ."			ProgressBar.appendChild(AA_ProgressDot(i));\n"
                    ."\n"
                    ."			// AA_Add_FeedbackBox(i);\n"
                    ."		}\n"
                    ."		ClozeBody.parentNode.insertBefore(ProgressBar, ClozeBody);\n"
                    ."		AA_SetProgressBar();\n"
                    ."	}\n"
                ;
                $append = "\n"
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
                    ."	// i is either an index on ListItems \n"
                    ."	// or a string to be used as an id for an HTML element\n"
                    ."	if (typeof(i)=='string') {\n"
                    ."		var id = i;\n"
                    ."		var add_link = false;\n"
                    ."	} else if (ListItems[i]) {\n"
                    ."		var id = ListItems[i].id;\n"
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
                    ."		var fn = 'AA_ChangeListItem('+i+');return false;';\n"
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
                    ."function AA_SetProgressDot(i, next_i) {\n"
                    ."	var img = document.getElementById('Q_'+i+'_ProgressDotImg');\n"
                    ."	if (! img) {\n"
                    ."		return;\n"
                    ."	}\n"
                    ."	var src = '';\n"
                    ."	if (ListItems[i].score >= 0) {\n"
                    ."		var score = Math.max(0, ListItems[i].score);\n"
                    ."		if (score >= 99) {\n"
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
                    ."		if (typeof(next_i)=='number' && i==next_i) {\n"
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
                    ."function AA_ChangeListItem(i) {\n"
                    ."	ListItems[CurrentListItem].style.display = 'none';\n"
                    ."	var obj = ListItems[i].parentNode;\n"
                    ."	while (obj) {\n"
                    ."		if (obj.tagName=='OL') {\n"
                    ."			obj.start = (i+1);\n"
                    ."			obj = null;\n"
                    ."		} else {\n"
                    ."			// workaround for IE7\n"
                    ."			obj = obj.parentNode;\n"
                    ."		}\n"
                    ."	}\n"
                    ."	ListItems[i].style.display = '';\n"
                    ."	AA_SetProgressBar(i);\n"
                    ."	CurrentListItem = i;\n"
                    ."}\n"
                    ."function AA_SetProgressBar(next_i) {\n"
                    ."	if (typeof(next_i)=='undefined') {\n"
                    ."		next_i = CurrentListItem;\n"
                    ."	}\n"
                    ."	for (var i=0; i<ListItems.length; i++) {\n"
                    ."		AA_SetProgressDot(i, next_i);\n"
                    ."	}\n"
                    ."}\n"
                ;
            }

            $insert .= "	ClozeBody = null;\n";
            $substr = substr_replace($substr, $insert, $pos, 0);
        }

        parent::fix_js_StartUp($substr, 0, strlen($substr));
        $substr .= $append;

        $str = substr_replace($str, $substr, $start, $length);
    }

    /**
     * fix_js_CheckAnswer
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_CheckAnswer(&$str, $start, $length) {
        $substr = substr($str, $start, $length);

        // make sure we trim answer as  well as response when checking for correctness
        $search = '/(?<=TrimString\(UpperGuess\) == )(UpperAnswer)/';
        $substr = preg_replace($search, 'TrimString($1)', $substr);

        if ($this->use_DropDownList()) {
            // only treat 1st possible answer as correct
            $substr = str_replace('I[GapNum][1].length', '1', $substr);
        }

        $str = substr_replace($str, $substr, $start, $length);
    }

    /**
     * fix_js_CheckAnswers
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_CheckAnswers(&$str, $start, $length) {
        $substr = substr($str, $start, $length);

        $search = '/for \(var i \= 0; i<I\.length; i\+\+\)\{(.*?)(?=var TotalScore = 0;)/s';
        $replace = "\n"
            ."	var clues = new Array();\n"
            ."	var li = ListItems[CurrentListItem];\n"
            ."	if (li.AnsweredCorrectly==false) {\n"
            ."\n"
            ."		var gapid = new RegExp('^Gap([0-9]+)\$');\n"
            ."		var ListItemScore = 0;\n"
            ."\n"
            ."		var g_max = li.gaps.length;\n"
            ."		for (var g=0; g<g_max; g++) {\n"
            ."\n"
            ."			var m = li.gaps[g].id.match(gapid);\n"
            ."			if (! m) {\n"
            ."				continue;\n"
            ."			}\n"
            ."\n"
            ."			var i = parseInt(m[1]);\n"
            ."			if (! State[i]) {\n"
            ."				continue;\n"
            ."			}\n"
            ."\n"
            ."			if (State[i].AnsweredCorrectly) {\n"
            ."				ListItemScore += State[i].ItemScore;\n"
            ."			} else {\n"
            ."				var GapValue = GetGapValue(i);\n"
            ."				if (typeof(GapValue)=='string' && GapValue=='') {\n"
            ."					// not answered yet\n"
            ."					AllCorrect = false;\n"
            ."				} else if (CheckAnswer(i, true) > -1) {\n"
            ."					// correct answer\n"
            ."					var TotalChars = GapValue.length;\n"
            ."					State[i].ItemScore = (TotalChars-State[i].HintsAndChecks)/TotalChars;\n"
            ."					if (State[i].ClueGiven){\n"
            ."						State[i].ItemScore /= 2;\n"
            ."					}\n"
            ."					if (State[i].ItemScore < 0){\n"
            ."						State[i].ItemScore = 0;\n"
            ."					}\n"
            ."					State[i].AnsweredCorrectly = true;\n"
            ."					SetCorrectAnswer(i, GapValue);\n"
            ."					ListItemScore += State[i].ItemScore;\n"
            ."				} else {\n"
            ."					// wrong answer\n"
            ."					var clue = I[i][2];\n"
            ."					if (clue) {\n"
            ."						var c_max = clues.length;\n"
            ."						for (var c=0; c<c_max; c++) {\n"
            ."							if (clues[c]==clue) {\n"
            ."								break;\n"
            ."							}\n"
            ."						}\n"
            ."						if (c==c_max) {\n"
            ."							clues[c] = clue;\n"
            ."						}\n"
            ."						State[i].ClueGiven = true;\n"
            ."					}\n"
            ."					AllCorrect = false;\n"
            ."				}\n"
            ."			}\n"
            ."		}\n"
            ."		li.AnsweredCorrectly = AllCorrect;\n"
            ."		if (li.AnsweredCorrectly) {\n"
            ."			li.score = Math.round(100 * (ListItemScore / g_max));\n"
            ."			var next_i = CurrentListItem;\n"
            ."			var i_max = ListItems.length;\n"
            ."			for (var i=0; i<i_max; i++) {\n"
            ."				var next_i = (CurrentListItem + i + 1) % i_max;\n"
            ."				if (ListItems[next_i].AnsweredCorrectly==false) {\n"
            ."					break;\n"
            ."				}\n"
            ."			}\n"
            ."			if (next_i==CurrentListItem) {\n"
            ."				AA_SetProgressBar(next_i);\n"
            ."			} else {\n"
            ."				AA_ChangeListItem(next_i);\n"
            ."			}\n"
            ."		}\n"
            ."	}\n"
            ."	li = null;\n"
            ."	clues = clues.join('\\n\\n');\n"
            .'	'
        ;
        $substr = preg_replace($search, $replace, $substr, 1);

        $search = '		TotalScore += State[i].ItemScore;';
        if ($pos = strpos($substr, $search)) {
            $insert = ''
                ."		if (State[i].AnsweredCorrectly==false) {\n"
                ."			AllCorrect = false;\n"
                ."		}\n"
            ;
            $substr = substr_replace($substr, $insert, $pos, 0);
        }

        $search = 'Output += Incorrect';
        if ($pos = strpos($substr, $search)) {
            $insert = 'Output += (clues ? clues : Incorrect)';
            $substr = substr_replace($substr, $insert, $pos, strlen($search));
        }

        $search = 'ShowMessage(Output)';
        if ($pos = strpos($substr, $search)) {
            $insert = 'if (clues || AllCorrect) ';
            $substr = substr_replace($substr, $insert, $pos, 0);
        }

        $search = "setTimeout('WriteToInstructions(Output)', 50);";
        if ($pos = strpos($substr, $search)) {
            $substr = substr_replace($substr, '', $pos, strlen($search));
        }

        parent::fix_js_CheckAnswers($substr, 0, strlen($substr));
        $str = substr_replace($str, $substr, $start, $length);
    }

    /**
     * expand_ClozeBody
     *
     * @return xxx
     */
    function expand_ClozeBody() {
        $str = '';

        $wordlist = $this->setup_wordlist();

        // cache clues flag and caption
        $includeclues = $this->expand_Clues();
        $cluecaption = $this->expand_ClueCaption();

        // detect if cloze starts with gap
        if (strpos($this->hotpot->source->filecontents, '<gap-fill><question-record>')) {
            $startwithgap = true;
        } else {
            $startwithgap = false;
        }

        // initialize loop values
        $i = 0;
        $tags = 'data,gap-fill';
        $open_text = "$tags,open-text";
        $question_record = "$tags,question-record";

        // loop through text and gaps
        $looping = true;
        while ($looping) {
            $item = "[$i]['#']";
            $text = $this->hotpot->source->xml_value($open_text, $item);
            $gap = '';
            if ($this->hotpot->source->xml_value($question_record, $item)) {
                $gap .= '<span class="GapSpan" id="GapSpan'.$i.'">';
                if (is_array($wordlist)) {
                    $gap .= '<select id="Gap'.$i.'"><option value=""></option>'.$wordlist[$i].'</select>';
                } else if ($wordlist) {
                    $gap .= '<select id="Gap'.$i.'"><option value=""></option>'.$wordlist.'</select>';
                } else {
                    // minimum gap size
                    if (! $gapsize = $this->hotpot->source->xml_value_int($this->hotpot->source->hbs_software.'-config-file,'.$this->hotpot->source->hbs_quiztype.',minimum-gap-size')) {
                        $gapsize = 6;
                    }

                    // increase gap size to length of longest answer for this gap
                    $a = 0;
                    while (($answer=$item."['answer'][$a]['#']") && $this->hotpot->source->xml_value($question_record, $answer)) {
                        $answertext = $this->hotpot->source->xml_value($question_record,  $answer."['text'][0]['#']");
                        $answertext = preg_replace('/&[#a-zA-Z0-9]+;/', 'x', $answertext);
                        $gapsize = max($gapsize, strlen($answertext));
                        $a++;
                    }

                    $gap .= '<input type="text" id="Gap'.$i.'" onfocus="TrackFocus('.$i.')" onblur="LeaveGap()" class="GapBox" size="'.$gapsize.'"></input>';
                }
                if ($includeclues) {
                    $clue = $this->hotpot->source->xml_value($question_record, $item."['clue'][0]['#']");
                    if (strlen($clue)) {
                        $gap .= '<button style="line-height: 1.0" class="FuncButton" onfocus="FuncBtnOver(this)" onmouseover="FuncBtnOver(this)" onblur="FuncBtnOut(this)" onmouseout="FuncBtnOut(this)" onmousedown="FuncBtnDown(this)" onmouseup="FuncBtnOut(this)" onclick="ShowClue('.$i.')">'.$cluecaption.'</button>';
                    }
                }
                $gap .= '</span>';
            }
            if (strlen($text) || strlen($gap)) {
                if ($startwithgap) {
                    $str .= $gap.$text;
                } else {
                    $str .= $text.$gap;
                }
                $i++;
            } else {
                // no text or gap, so force end of loop
                $looping = false;
            }
        }
        if ($i==0) {
            // oops, no gaps found!
            return $this->hotpot->source->xml_value($tags);
        } else {
            return $str;
        }
    }

    /**
     * setup_wordlist
     *
     * @return xxx
     */
    function setup_wordlist() {

        // get drop down list of words
        $words = array();
        $wordlists = array();
        $singlewordlist = true;

        if ($this->use_DropDownList()) {
            $q = 0;
            $tags = 'data,gap-fill,question-record';
            while (($question="[$q]['#']") && $this->hotpot->source->xml_value($tags, $question)) {
                $a = 0;
                $aa = 0;
                while (($answer=$question."['answer'][$a]['#']") && $this->hotpot->source->xml_value($tags, $answer)) {
                    $text = $this->hotpot->source->xml_value($tags,  $answer."['text'][0]['#']");
                    if (strlen($text)) {
                        $wordlists[$q][$aa] = $text;
                        $words[] = $text;
                        $aa++;
                    }
                    $a++;
                }
                if ($aa) {
                    $wordlists[$q] = array_unique($wordlists[$q]);
                    sort($wordlists[$q]);

                    $wordlist = '';
                    foreach ($wordlists[$q] as $word) {
                        $wordlist .= '<option value="'.$word.'">'.$word.'</option>';
                    }
                    $wordlists[$q] = $wordlist;

                    if ($aa >= 2) {
                        $singlewordlist = false;
                    }
                }
                $q++;
            }

            $words = array_unique($words);
            sort($words);
        }

        if ($singlewordlist) {
            $wordlist = '';
            foreach ($words as $word) {
                $wordlist .= '<option value="'.$word.'">'.$word.'</option>';
            }
            return $wordlist;
        } else {
            return $wordlists;
        }
    }
}
