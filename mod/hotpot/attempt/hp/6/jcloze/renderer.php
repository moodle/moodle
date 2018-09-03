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
 * Output format: hp_6_jcloze
 *
 * @package   mod-hotpot
 * @copyright 2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/** Prevent direct access to this script */
defined('MOODLE_INTERNAL') || die();

/** Include required files */
require_once($CFG->dirroot.'/mod/hotpot/attempt/hp/6/renderer.php');

/**
 * mod_hotpot_attempt_hp_6_jcloze_renderer
 *
 * @copyright  2010 Gordon Bateson (gordon.bateson@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 2.0
 * @package    mod
 * @subpackage hotpot
 */
class mod_hotpot_attempt_hp_6_jcloze_renderer extends mod_hotpot_attempt_hp_6_renderer {
    public $icon = 'pix/f/jcl.gif';
    public $js_object_type = 'JCloze';

    public $templatefile = 'jcloze6.ht_';
    public $templatestrings = 'PreloadImageList';

    // Glossary autolinking settings
    public $headcontent_strings = 'Feedback|Correct|Incorrect|GiveHint|YourScoreIs|Guesses|(?:I\[\d+\]\[[12]\])';
    public $headcontent_arrays = '';

    /**
     * init
     *
     * @param xxx $hotpot
     */
    function init($hotpot)  {
        parent::init($hotpot);
        array_push($this->javascripts, 'mod/hotpot/attempt/hp/6/jcloze/jcloze.js');
    }

    /**
     * get_js_functionnames
     *
     * @return xxx
     */
    function get_js_functionnames()  {
        // start list of function names
        $names = parent::get_js_functionnames();
        $names .= ($names ? ',' : '').'TypeChars,ShowHint,ShowClue,CheckAnswers,CompileGuesses';
        return $names;
    }

    /**
     * fix_js_TypeChars_init
     *
     * @return xxx
     */
    function fix_js_TypeChars_init()  {
        return ''
            ."	var CurrGap = FindCurrent();\n"
            ."	if (CurrGap < 0){\n"
            ."		return;\n"
            ."	}\n"
            ."	var obj = document.getElementById('Gap' + CurrGap);\n"
        ;
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
                ."	var q = (window.Locked ? -1 : FindCurrent());\n"
                ."	if (q>=0 && GetHint(q)) HP.onclickHint(q);\n"
                ."\n"
                ."	// make sure required HTML element is present\n"
                ."	if (! document.getElementById('FeedbackDiv')) return;\n"
            ;
            $substr = substr_replace($substr, $insert, $pos+1, 0);
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

        // the argument for this function has different names in different output formats
        //   ItemNum : JCloze  + Rottmeier DropDown
        //   GapId   : Find It (a) + (b) + ANCT-Scan
        //   ClueNum : JCross (has its own fix function)
        // so it is safest to refer to it using "ShowClue.arguments[0]"

        // intercept Clues
        if ($pos = strpos($substr, '{')) {
            $insert = "\n"
                ."	// intercept this Clue\n"
                ."	if (!window.Locked) HP.onclickClue(ShowClue.arguments[0]);\n"
            ;
            $substr = substr_replace($substr, $insert, $pos+1, 0);
        }

        $str = substr_replace($str, $substr, $start, $length);
    }

    /**
     * fix_js_CompileGuesses
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_CompileGuesses(&$str, $start, $length)  {
        $this->remove_js_function($str, $start, $length, 'CompileGuesses');
    }

    /**
     * get_stop_function_name
     *
     * @return xxx
     */
    function get_stop_function_name()  {
        return 'CheckAnswers';
    }

    // the following functions are required by Michael Rottmeier output formats
    // they are put here so that they can be shared between the xml and html source files

    // ========
    // DropDown
    // ========

    /**
     * fix_js_Show_Solution
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_Show_Solution(&$str, $start, $length) {
        $substr = substr($str, $start, $length);

        $substr = str_replace('Finish()', "HP_send_results(HP.EVENT_ABANDONED)", $substr);

        $str = substr_replace($str, $substr, $start, $length);
    }

    // ==============
    // FindIt (a + b)
    // ==============

    /**
     * fix_js_Markup_Text
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_Markup_Text(&$str, $start, $length) {
        // fix Markup_Text so it identifies individual incorrect words that were clicked
        $replace = ''
            ."function Markup_Text(Node){\n"
            ."	if (Node && Node.childNodes) {\n"
            ."		var x_max = Node.childNodes.length;\n"
            ."	} else {\n"
            ."		var x_max = 0;\n"
            ."	}\n"
            ."\n"
            ."	if (typeof(window.CurrentGapId)=='undefined') {\n"
            ."		window.CurrentGapId = -1;\n"
            ."	}\n"
            ."\n"
            ."	if (typeof(window.isNonStandardIE)=='undefined') {\n"
            ."		if (navigator.appName=='Microsoft Internet Explorer' && (document.documentMode==null || document.documentMode<8)) {\n"
            ."			// either IE8+ (in compatability mode) or IE7, IE6, IE5 ...\n"
            ."			window.isNonStandardIE = true;\n"
            ."		} else {\n"
            ."			// Firefox, Safari, Opera, IE8+\n"
            ."			window.isNonStandardIE = false;\n"
            ."		}\n"
            ."	}\n"
            ."\n"
            ."	for (var x=0; x<x_max; x++){\n"
            ."\n"
            ."		switch (Node.childNodes[x].nodeName.toLowerCase()){\n"
            ."\n"
            ."			case 'span' :\n"
            ."				if (Node.childNodes[x].id.substring(0,7)=='GapSpan') {\n"
            ."					window.CurrentGapId++;\n"
            ."				} else {\n"
            ."					Node.replaceChild(Markup_Text(Node.childNodes[x]), Node.childNodes[x]);\n"
            ."				}\n"
            ."				break;\n"
            ."\n"
            ."			case '#text' :\n"
            ."				var txt = Node.childNodes[x].nodeValue;\n"
            ."				if (typeof(txt)=='string' && txt.length){\n"
            ."\n"
            ."					var NewNode = document.createElement('span');\n"
            ."\n"
            ."					var space = ' \\t\\n\\r' + '!\"#$%&\\'()*+,-./:+<=>?@\\\\[\\\\]\\\\\\\\^_`{|}~';\n"
            ."					var match_space = new RegExp('[' + space + ']+');\n"
            ."					var match_chars = new RegExp('[^' + space + ']+');\n"
            ."\n"
            ."					var i_max = txt.length\n"
            ."					var i_chars = 0; // start of chars\n"
            ."					var i_space = 0; // start of space (and punctuation symbols)\n"
            ."\n"
            ."					while (i_chars<i_max && i_space<i_max) {\n"
            ."\n"
            ."						var m = match_space.exec(txt.substr(i_chars));\n"
            ."						if (m) {\n"
            ."							i_space = i_chars + m.index;\n"
            ."						} else {\n"
            ."							i_space = i_max;\n"
            ."						}\n"
            ."						if (i_space>i_chars) {\n"
            ."							// a word\n"
            ."							var SpanNode = document.createElement('span');\n"
            ."							var myClassName = 'GapSpan';\n"
            ."							var myFunction = 'CheckText(false,'+Math.max(0,CurrentGapId)+',this.childNodes[0].nodeValue)';\n"
            ."							if (window.isNonStandardIE) {\n"
            ."								SpanNode.setAttribute('className', myClassName);\n"
            ."								SpanNode.setAttribute('onclick', new Function(myFunction));\n"
            ."							} else {\n"
            ."								SpanNode.setAttribute('class', myClassName);\n"
            ."								SpanNode.setAttribute('onclick', myFunction);\n"
            ."							}\n"
            ."							SpanNode.appendChild(document.createTextNode(txt.substring(i_chars, i_space)));\n"
            ."							NewNode.appendChild(SpanNode);\n"
            ."						}\n"
            ."\n"
            ."						var m = match_chars.exec(txt.substr(i_space));\n"
            ."						if (m) {\n"
            ."							i_chars = i_space + m.index;\n"
            ."						} else {\n"
            ."							i_chars = i_max;\n"
            ."						}\n"
            ."						if (i_chars>i_space) {\n"
            ."							// some white space and/or puctuation\n"
            ."							NewNode.appendChild(document.createTextNode(txt.substring(i_space, i_chars)));\n"
            ."						}\n"
            ."					} // end while\n"
            ."\n"
            ."					// replace the old node with the new node\n"
            ."					Node.replaceChild(NewNode, Node.childNodes[x]);\n"
            ."\n"
            ."				} // end if txt\n"
            ."				break;\n"
            ."\n"
            ."			case 'param' :\n"
            ."				// IE chokes on this one;\n"
            ."				break;\n"
            ."\n"
            ."			default :\n"
            ."				// check childNodes\n"
            ."				Node.replaceChild(Markup_Text(Node.childNodes[x]), Node.childNodes[x]); break;\n"
            ."\n"
            ."		} // end switch\n"
            ."	} // end for\n"
            ."	return Node;\n"
            ."}"
        ;
        $str = substr_replace($str, $replace, $start, $length);
    }

    /**
     * fix_js_CheckText
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_CheckText(&$str, $start, $length)  {
        $substr = substr($str, $start, $length);

        // intercept Hints and add a 3rd parameter to the CheckText function
        if ($pos = strpos($substr, '{')) {
            $insert = ''
                ."function CheckText(GapState,GapId,GapValue){\n"
                ."	// intercept this Check\n"
                ."	HP.onclickCheck(GapState,GapId,GapValue);\n"
            ;
            $substr = substr_replace($substr, $insert, 0, $pos+1);
        }

        $str = substr_replace($str, $substr, $start, $length);
    }

    /**
     * fix_js_Build_GapText
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_Build_GapText(&$str, $start, $length)  {
        $substr = substr($str, $start, $length);

        //$substr = preg_replace('/TextBody = TextBody.parentNode;'.'\s*'.'if \(TextBody != null\)/s', 'if (TextBody)', $substr);
        $substr = str_replace('if (TextBody != null)', 'if (TextBody)', $substr);
        $substr = str_replace('TextBody', 'ClozeBody', $substr);

        // add "try {...} catch(err) {...}" around error-prone removeChild statement (in DropDown)
        $search = "/([\r\n]+[ \t]+)(GSpan\.removeChild\(GSpan\.getElementsByTagName\('input'\)\[0\]\);)/";
        // [1] : indent
        // [2] : javascript
        $replace = ''
            .'\\1'.'try {'."\n"
            .'\\1'.'	\\2'."\n"
            .'\\1'.'} catch(err) {'."\n"
            .'\\1'.'	//do nothing'."\n"
            .'\\1'.'}'
        ;
        $substr = preg_replace($search, $replace, $substr, 1);

        if ($endpos = strrpos($substr, '}')) {
            // optimize gap creation and unhide Cloze text when gaps have been set up
            $search = "var ClozeBody = document.getElementById('ClozeBody');";
            if ($pos = strrpos($substr, $search)) {
                // FindIt(a)+(b)
                $markup_text = ''
                    ."		for (var y=0; y<Cloze.childNodes.length; y++){\n"
                    ."			Cloze.replaceChild(Markup_Text(Cloze.childNodes[y]), Cloze.childNodes[y]);\n"
                    ."		}\n"
                ;
                $len = $endpos - $pos;
                $tab = '';
            } else {
                // DropDown
                $len = 0;
                $tab = "\t";
                $pos = $endpos;
                $markup_text = '';
            }
            $replace = $tab
                ."var Cloze = document.getElementById('Cloze');\n"
                ."	if (Cloze){\n"
                .$markup_text
                ."		Cloze.style.display = '';\n"
                ."	}\n"
            ;
            $substr = substr_replace($substr, $replace, $pos, $len);
        }

         if ($this->expand_CaseSensitive()) {
            $search = 'SelectorList = Shuffle(SelectorList);';
            $replace = 'SelectorList = AlphabeticalSort(SelectorList, x);';
            $substr = str_replace($search, $replace, $substr);
            $substr .= "\n"
                ."function AlphabeticalSort(SelectorList, x) {\n"
                ."	if (MakeIndividualDropdowns) {\n"
                ."		var y_max = I[x][1].length - 1;\n"
                ."	} else {\n"
                ."		var y_max = I.length - 1;\n"
                ."	}\n"
                ."	var sorted = false;\n"
                ."	while (! sorted) {\n"
                ."		sorted = true;\n"
                ."		for (var y=0; y<y_max; y++) {\n"
                ."			var y1 = SelectorList[y];\n"
                ."			var y2 = SelectorList[y + 1];\n"
                ."			if (MakeIndividualDropdowns) {\n"
                ."				var s1 = I[x][1][y1][0].toLowerCase();\n"
                ."				var s2 = I[x][1][y2][0].toLowerCase();\n"
                ."			} else {\n"
                ."				var s1 = I[y1][1][0][0].toLowerCase();\n"
                ."				var s2 = I[y2][1][0][0].toLowerCase();\n"
                ."			}\n"
                ."			if (s1 > s2) {\n"
                ."				sorted = false;\n"
                ."				SelectorList[y] = y2;\n"
                ."				SelectorList[y + 1] = y1;\n"
                ."			}\n"
                ."		}\n"
                ."	}\n"
                ."	return SelectorList;\n"
                ."}\n"
            ;
        }

        if ($this->expand_CaseSensitive()) {
            $search = 'SelectorList = Shuffle(SelectorList);';
            $replace = 'SelectorList = AlphabeticalSort(SelectorList, x);';
            $substr = str_replace($search, $replace, $substr);
            $substr .= "\n"
                ."function AlphabeticalSort(SelectorList, x) {\n"
                ."	if (MakeIndividualDropdowns) {\n"
                ."		var y_max = I[x][1].length - 1;\n"
                ."	} else {\n"
                ."		var y_max = I.length - 1;\n"
                ."	}\n"
                ."	var sorted = false;\n"
                ."	while (! sorted) {\n"
                ."		sorted = true;\n"
                ."		for (var y=0; y<y_max; y++) {\n"
                ."			var y1 = SelectorList[y];\n"
                ."			var y2 = SelectorList[y + 1];\n"
                ."			if (MakeIndividualDropdowns) {\n"
                ."				var s1 = I[x][1][y1][0].toLowerCase();\n"
                ."				var s2 = I[x][1][y2][0].toLowerCase();\n"
                ."			} else {\n"
                ."				var s1 = I[y1][1][0][0].toLowerCase();\n"
                ."				var s2 = I[y2][1][0][0].toLowerCase();\n"
                ."			}\n"
                ."			if (s1 > s2) {\n"
                ."				sorted = false;\n"
                ."				SelectorList[y] = y2;\n"
                ."				SelectorList[y + 1] = y1;\n"
                ."			}\n"
                ."		}\n"
                ."	}\n"
                ."	return SelectorList;\n"
                ."}\n"
            ;
        }

        $str = substr_replace($str, $substr, $start, $length);
    }

    /**
     * fix_js_ShowSolution
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_ShowSolution(&$str, $start, $length)  {
        $substr = substr($str, $start, $length);

        if ($pos = strrpos($substr, '}')) {
            $append = "\n"
                ."// send results after delay\n"
                ."	setTimeout('HP_send_results('+HP.EVENT_ABANDONED+')',SubmissionTimeout);\n"
                ."	return false;\n"
            ;
            $substr = substr_replace($substr, $append, $pos, 0);
        }

        $str = substr_replace($str, $substr, $start, $length);
    }

    /**
     * fix_js_Get_WrongGapContent
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     * @return xxx
     */
    function fix_js_Get_WrongGapContent(&$str, $start, $length)  {
        $substr = substr($str, $start, $length);
        if ($pos = strpos($substr, '{')) {
            $insert = "\n"
                ."	if (typeof(I[GapId][1][1])=='undefined') return '???';"
            ;
            $substr = substr_replace($substr, $insert, $pos+1, 0);
        }
        $str = substr_replace($str, $substr, $start, $length);
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

        $search = "/\s*document\.getElementById\('Timer'\)\.innerHTML = '([^']*)';/s";
        if (preg_match($search, $substr, $matches, PREG_OFFSET_CAPTURE)) {
            $msg = $matches[1][0];
            $substr = substr_replace($substr, '', $matches[0][1], strlen($matches[0][0]));
        } else {
            $msg = 'Your time is over!';
        }

        if ($pos = strrpos($substr, '}')) {
            $insert = ''
                ."	HP_send_results(HP.EVENT_TIMEDOUT);\n"
                ."	ShowMessage('$msg');\n"
            ;
            $substr = substr_replace($substr, $insert, $pos, 0);
        }

        $str = substr_replace($str, $substr, $start, $length);
    }

    // ==========
    // FindIt (a)
    // ==========

    /**
     * fix_js_CorrectChoice
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_CorrectChoice(&$str, $start, $length) {
        $substr = substr($str, $start, $length);

        // make sure GapId is valid
        $search = '/'.'(\s*)(GapList[^;]*;)(.*)(Show_GapSolution[^;]*;)/s';
        $replace = '$1'
            ."if (typeof(GapId)=='number' && GapList[GapId]){".'$1'
            .'	$2$3'
            .'	$4$1'
            .'}'
        ;
        $substr = preg_replace($search, $replace, $substr, 1);

        // add changes as per CheckAnswers in other type of HP quiz
        $this->fix_js_CheckAnswers($substr, 0, strlen($substr));

        $str = substr_replace($str, $substr, $start, $length);
    }

    // ==========
    // JGloss
    // ==========

    /**
     * fix_js_Show_GlossContent
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_Show_GlossContent(&$str, $start, $length) {
        $substr = substr($str, $start, $length);

        // intercept onmouseover and store as a "Check" on this item
        if ($pos = strpos($substr, '{')) {
            $insert = "\n"
                ."	// intercept this Check\n"
                ."	HP.onclickCheck(id);"
            ;
            $substr = substr_replace($substr, $insert, $pos+1, 0);
        }

        //$substr = str_replace('C.IE', 'C.ie', $substr);
        $substr = $this->fix_js_if_then_else($substr);
        $substr = $this->fix_js_clientXY($substr, 'Pos.X', 'Evt');

        // remove the following lines
        $search = array('/var Pos = .*;/',
                        '/Pos\\.(X|Y) = .*;/',
                        '/var VertPos = .*;/',
                        '/FDiv.style.(top|left) = .*;/',
                        '/FDiv.style.(top|left) = .*;/',
                        '/if \\(parseInt\\(FDiv\\.style\\.left\\) < 5\\)\\{/',
                        "/document.getElementById\\('FeedbackOKButton'\\)\\.style\\.display = .*;/"
        );
        $substr = preg_replace($search, '', $substr);

        // improve readability and robustness of code to update feedback element
        $search = "\tdocument.getElementById('FeedbackContent').innerHTML = '<p>' + I[id][2] + '</p>';";
        if ($pos = strpos($substr, $search)) {
            $insert = "\tvar FContent = document.getElementById('FeedbackContent');\n".
                      "\tif (FContent) {\n".
                      "\t\tFContent.innerHTML = '<p>' + I[id][2] + '</p>';\n".
                      "\t}\n".
                      "\tvar FButton = document.getElementById('FeedbackOKButton');\n".
                      "\tif (FButton) {\n".
                      "\t\tFButton.style.display = 'none';\n".
                      "\t}\n";
            $substr = substr_replace($substr, $insert, $pos, strlen($search));
        }

        // we must position the feedback AFTER making it visible
        // also use setOffset(), instead of FDiv.style.left/top
        $search = "FDiv.style.display = 'block';";
        if ($pos = strpos($substr, $search)) {
            $insert = "\n".
                      "\tx = Math.min(x + 10, getOffset(FDiv.offsetParent, 'Right'));\n".
                      "\ty = Math.min(y - 10, getOffset(FDiv.offsetParent, 'Bottom'));\n".
                      "\tx = Math.max(x - getOffset(FDiv, 'Width'), getOffset(FDiv.offsetParent, 'Left'));\n".
                      "\ty = Math.max(y - getOffset(FDiv, 'Height'), getOffset(FDiv.offsetParent, 'Top'));\n".
                      "\tsetOffset(FDiv, 'Left', x);\n".
                      "\tsetOffset(FDiv, 'Top',  y);\n".
                      "\tFContent = null;\n".
                      "\tFButton = null;\n".
                      "\tFDiv = null;\n";
            $substr = substr_replace($substr, $insert, $pos + strlen($search), 0);
        }

        $str = substr_replace($str, $substr, $start, $length);
    }

    /**
     * fix_js_Add_GlossFunctionality
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_Add_GlossFunctionality(&$str, $start, $length)  {
        $substr = substr($str, $start, $length);

        $search = '/onmousedown="[^"]*" onmouseup="[^"]*"/';
        $replace = 'onclick = "return false"';
        $substr = preg_replace($search, $replace, $substr);

        $str = substr_replace($str, $substr, $start, $length);
    }

    /**
     * fix_js_ShowElements
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_ShowElements(&$str, $start, $length)  {
        $substr = substr($str, $start, $length);

        // remove HP's fix for alleged bug on FF (Mac)
        // HP's fix makes the screen flicker as the objects' style display is toggled
        // and tests on FF 2 and 3 show that the alleged bug is no longer an issue
        $search = "/\s*if \(C.mac && C.gecko\) \{Els\[i\]\.style\.display = '[^']*';\}/s";
        $substr = preg_replace($search, '', $substr);

        $str = substr_replace($str, $substr, $start, $length);
    }
}
