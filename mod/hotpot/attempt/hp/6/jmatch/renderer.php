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
 * Output format: hp_6_jmatch
 *
 * @package   mod-hotpot
 * @copyright 2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// get parent class
require_once($CFG->dirroot.'/mod/hotpot/attempt/hp/6/renderer.php');

/**
 * mod_hotpot_attempt_hp_6_jmatch_renderer
 *
 * @copyright 2010 Gordon Bateson
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class mod_hotpot_attempt_hp_6_jmatch_renderer extends mod_hotpot_attempt_hp_6_renderer {
    public $icon = 'pix/f/jmt.gif';
    public $js_object_type = 'JMatch';

    public $templatefile = 'jmatch6.ht_';
    public $templatestrings = 'PreloadImageList|QsToShow|FixedArray|DragArray';

    public $l_items = array();
    public $r_items = array();

    // Glossary autolinking settings
    public $headcontent_strings = 'CorrectResponse|IncorrectResponse|YourScoreIs|F|D';
    public $headcontent_arrays = '';

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
        array_push($this->javascripts, 'mod/hotpot/attempt/hp/6/jmatch/jmatch.js');
    }

    /**
     * fix_headcontent
     */
    function fix_headcontent()   {
        $this->fix_headcontent_DragAndDrop();
    }

    /**
     * fix_bodycontent
     */
    function fix_bodycontent()  {
        // remove instructions if they are not required
        $search = '/\s*<div id="InstructionsDiv" class="StdDiv">\s*<div id="Instructions">\s*<\/div>\s*<\/div>/s';
        $this->bodycontent = preg_replace($search, '', $this->bodycontent, 1);

        parent::fix_bodycontent();
    }

    /**
     * fix_js_WriteToInstructions
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_WriteToInstructions(&$str, $start, $length)  {
        // remove this function from JMatch as it is not used
        $str = substr_replace($str, '', $start, $length);
    }

    /**
     * fix_bodycontent_DragAndDrop
     */
    function fix_bodycontent_DragAndDrop()  {
        $search = '/for \(var i=0; i<F\.length; i\+\+\)\{.*?\}/s';
        $replace = ''
            ."var myParentNode = null;\n"
            ."if (navigator.appName=='Microsoft Internet Explorer' && (document.documentMode==null || document.documentMode<8)) {\n"
            ."	// IE8+ (compatible mode) IE7, IE6, IE5 ...\n"
            ."} else {\n"
            ."	// Firefox, Safari, Opera, IE8+\n"
            ."	// prevent selection of parent node\n"
            ."	myParentNode = document.getElementById('$this->themecontainer');\n"
            ."	if (myParentNode==null) {\n"
            ."		var obj = document.getElementsByTagName('div');\n"
            ."		if (obj && obj.length) {\n"
            ."			myParentNode = obj[obj.length - 1].parentNode;\n"
            ."		}\n"
            ."	}\n"
            ."	if (myParentNode) {\n"
            ."		var css_prefix = new Array('webkit', 'khtml', 'moz', 'ms', 'o', '');\n"
            ."		for (var i=0; i<css_prefix.length; i++) {\n"
            ."			if (css_prefix[i]=='') {\n"
            ."				var userSelect = 'userSelect';\n"
            ."			} else {\n"
            ."				var userSelect = css_prefix[i] + 'UserSelect';\n"
            ."			}\n"
            ."			if (typeof(myParentNode.style[userSelect]) != 'undefined') {\n"
            ."				myParentNode.style[userSelect] = 'none';\n"
            ."				break;\n"
            ."			}\n"
            ."		}\n"
            ."		userSelect = null;\n"
            ."		css_prefix = null;\n"
            ."	}\n"
            ."}\n"
            ."for (var i=0; i<F.length; i++){\n"
            ."	if (myParentNode){\n"
            ."		var div = document.createElement('div');\n"
            ."		div.setAttribute('id', 'F' + i);\n"
            ."		div.setAttribute('class', 'CardStyle');\n"
            ."		myParentNode.appendChild(div);\n"
            ."	} else {\n"
            ."		document.write('".'<div id="'."F' + i + '".'" class="CardStyle"'."></div>');\n"
            ."	}\n"
            ."}"
        ;
        $this->bodycontent = preg_replace($search, $replace, $this->bodycontent, 1);

        $search = '/for \(var i=0; i<D\.length; i\+\+\)\{.*?\}/s';
        $replace = ''
            ."for (var i=0; i<D.length; i++){\n"
            ."	if (myParentNode){\n"
            ."		var div = document.createElement('div');\n"
            ."		div.setAttribute('id', 'D' + i);\n"
            ."		div.setAttribute('class', 'CardStyle');\n"
            ."		div = myParentNode.appendChild(div);\n"
            ."		HP_add_listener(div, 'mousedown', 'beginDrag(event, ' + i + ')');\n"
            ."	} else {\n"
            ."		document.write('".'<div id="'."D' + i + '".'" class="CardStyle" onmousedown="'."beginDrag(event, ' + i + ')".'"'."></div>');\n"
            ."	}\n"
            ."}\n"
            ."div = myParentNode = null;"
       ;
        $this->bodycontent = preg_replace($search, $replace, $this->bodycontent, 1);
    }

    /**
     * fix_js_StartUp_DragAndDrop
     *
     * @param xxx $substr (passed by reference)
     */
    function fix_js_StartUp_DragAndDrop(&$substr)  {

        // fix top and left of drag area
        $this->fix_js_StartUp_DragAndDrop_DragArea($substr);

        // stretch the canvas vertically down
        if ($pos = strrpos($substr, '}')) {
            $insert = ''
            ."	var b = 0;\n"
            ."	var objParentNode = null;\n"
            ."	if (window.F && window.D) {\n"
            ."		var obj = document.getElementById('F'+(F.length-1));\n"
            ."		if (obj) {\n"
            ."			b = Math.max(b, getOffset(obj, 'Bottom'));\n"
            ."			objParentNode = objParentNode || obj.parentNode;\n"
            ."		}\n"
            ."		var obj = document.getElementById('D'+(D.length-1));\n"
            ."		if (obj) {\n"
            ."			b = Math.max(b, getOffset(obj, 'Bottom'));\n"
            ."			objParentNode = objParentNode || obj.parentNode;\n"
            ."		}\n"
            ."	}\n"
            ."	if (b) {\n"
            ."		// stretch parentNodes down vertically, if necessary\n"
            ."		var canvas = document.getElementById('$this->themecontainer');\n"
            ."		while (objParentNode) {\n"
            ."			var more_height = Math.max(0, b - getOffset(objParentNode, 'Bottom'));\n"
            ."			if (more_height) {\n"
            ."				setOffset(objParentNode, 'Height', getOffset(objParentNode, 'Height') + more_height + 10);\n"
            ."			}\n"
            ."			if (canvas && objParentNode==canvas) {\n"
            ."				objParentNode = null;\n"
            ."			} else {\n"
            ."				objParentNode = objParentNode.parentNode;\n"
            ."			}\n"
            ."		}\n"
            ."	}\n"
            ;
            $substr = substr_replace($substr, $insert, $pos, 0);
        }
    }

    /**
     * get_js_functionnames
     *
     * @return xxx
     */
    function get_js_functionnames()  {
        // start list of function names
        $names = parent::get_js_functionnames();
        $names .= ($names ? ',' : '').'CardSetHTML,beginDrag,doDrag,endDrag,CheckAnswers';
        return $names;
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
     * get_beginDrag_target
     * for drag-and-drop JMatch and JMix
     *
     * @return string
     * @todo Finish documenting this function
     */
    public function get_beginDrag_target() {
        return 'DC[CurrDrag]';
    }

    /**
     * fix_js_StartUp_DragAndDrop_Flashcard
     *
     * @param xxx $substr (passed by reference)
     */
    function fix_js_StartUp_DragAndDrop_Flashcard(&$substr)  {

        // fix top and left of drag area
        $this->fix_js_StartUp_DragAndDrop_DragArea($substr);

        // stretch the canvas vertically down
        if ($pos = strrpos($substr, '}')) {
            $insert = ''
            ."	var canvas = document.getElementById('$this->themecontainer');\n"
            ."	if (canvas) {\n"
            ."		var b = 0;\n"
            ."		var tbody = document.getElementById('Questions');\n"
            ."		if (tbody) {\n"
            ."			var b = getOffset(tbody.parentNode, 'Bottom');\n"
            ."			if (b){\n"
            ."				setOffset(canvas, 'Bottom', b+4);\n"
            ."			}\n"
            ."		}\n"
            ."	}\n"
            ;
            $substr = substr_replace($substr, $insert, $pos, 0);
        }
    }

    /**
     * fix_mediafilter_onload_extra_Flashcard
     *
     * @return xxx
     */
    function fix_mediafilter_onload_extra_Flashcard()  {
        return ''
            ."\n"
            .'	// show first item'."\n"
            .'	if (window.ShowFirstItem){'."\n"
            .'		setTimeout("ShowItem()", 2000);'."\n"
            .'	}'."\n"
            ."\n"
            .parent::fix_mediafilter_onload_extra()
        ;
    }

    /**
     * fix_js_DeleteItem_Flashcard
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_DeleteItem_Flashcard(&$str, $start, $length)  {
        $event = $this->get_send_results_event();
        $substr = ''
            ."function DeleteItem(){\n"
            ."	var Qs = document.getElementById('Questions');\n"
            ."	if (Qs) {\n"
            ."		if (CurrItem) {\n"
            ."			var DelItem = CurrItem;\n"
            ."			Stage = 2;\n"
            ."			ShowItem();\n"
            ."			Qs.removeChild(DelItem);\n"
            ."		}\n"
            ."		var count = Qs.getElementsByTagName('tr').length\n"
            ."	} else {\n"
            ."		// no Questions - shouldn't happen !!\n"
            ."		var count = 0;\n"
            ."	}\n"
            ."	if (count==0){\n"
            ."		if (Qs) {\n"
            ."			var p = Qs.parentNode;\n"
            ."			p.parentNode.removeChild(p);\n"
            ."		}\n"
            ."		HP_send_results($event);\n"
            ."	}\n"
            ."}\n"
        ;
        $str = substr_replace($str, $substr, $start, $length);
    }

    /**
     * fix_js_ShowItem_Flashcard
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_ShowItem_Flashcard(&$str, $start, $length)  {

        $substr = substr($str, $start, $length);

        $event = $this->get_send_results_event();
        $search = '/(\s*)return;/';
        $replace = '$1'."HP_send_results($event);".'$0';
        $substr = preg_replace($search, $replace, $substr);

        if ($pos = strrpos($substr, '}')) {
            $append = "\n"
                ."	StretchCanvasToCoverContent();\n"
                ."	HP.onclickCheck(CurrItem);\n"
            ;
            $substr = substr_replace($substr, $append, $pos, 0);
        }

        $str = substr_replace($str, $substr, $start, $length);
    }

    /**
     * fix_title_rottmeier_JMemori
     */
    function fix_title_rottmeier_JMemori()  {
        // extract the current title
        $search = '/(<span class="ExerciseTitle">)\s*(.*?)\s*(<\/span>)/is';
        if (preg_match($search, $this->bodycontent, $matches)) {
            $title = $this->get_title();
            if ($this->hotpot->can_manage()) {
                $title .= $this->modedit_icon($this->hotpot);
            }
            $replace = $matches[1].$title.$matches[3];
            $this->bodycontent = str_replace($matches[0], $replace, $this->bodycontent);
        }
    }

    /**
     * fix_js_WriteFeedback_JMemori
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_WriteFeedback_JMemori(&$str, $start, $length)  {
        $substr = substr($str, $start, $length);

        // replace code for hiding elements
        $search = '/(\s*)if \(is\.ie\){.*?}.*?}.*?}/s';
        $replace = '$1'
            ."ShowElements(false, 'input');".'$1'
            ."ShowElements(false, 'select');".'$1'
            ."ShowElements(false, 'object');".'$1'
            ."ShowElements(true, 'object', 'FeedbackContent');".'$1'
            ."if (navigator.userAgent.indexOf('Chrome')>=0) {".'$1'
            ."	ShowElements(false, 'embed');".'$1'
            ."	ShowElements(true, 'embed', 'FeedbackContent');".'$1'
            ."}"
        ;
        $substr = preg_replace($search, $replace, $substr, 1);

        // add ShowElements() function
        $substr = ''
            ."function ShowElements(Show, TagName, ContainerToReverse){\n"
            ."	if (ContainerToReverse) {\n"
            ."		var TopNode = document.getElementById(ContainerToReverse);\n"
            ."	} else {\n"
            ."		var TopNode = null;\n"
            ."	}\n"
            ."	if (TopNode) {\n"
            ."		var Els = TopNode.getElementsByTagName(TagName);\n"
            ."	} else {\n"
            ."		var Els = document.getElementsByTagName(TagName);\n"
            ."	}\n"
            ."	if (Show) {\n"
            ."		var v = 'visible';\n"
            ."		var d = '';\n"
            ."	} else {\n"
            ."		var v = 'hidden';\n"
            ."		var d = 'none';\n"
            ."	}\n"
            ."	for (var i=0; i<Els.length; i++){\n"
            ."		if (TagName == 'embed' || TagName == 'object') {\n"
            ."			Els[i].style.visibility = v;\n"
            ."			if (is.mac && is.ns) {\n"
            ."				Els[i].style.display = d;\n"
            ."			}\n"
            ."		} else if (is.ie && is.v < 7) {\n"
            ."			Els[i].style.visibility = v;\n"
            ."		}\n"
            ."	}\n"
            ."}\n"
            .$substr
        ;

        $str = substr_replace($str, $substr, $start, $length);
    }

    /**
     * fix_js_HideFeedback_JMemori
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_HideFeedback_JMemori(&$str, $start, $length)  {
        $substr = substr($str, $start, $length);

        parent::fix_js_HideFeedback($substr, 0, strlen($substr));

        // replace code for showing elements
        $search = '/(\s*)if \(is\.ie\){.*?}.*?}.*?}/s';
        $replace = '$1'
            ."ShowElements(true, 'input');".'$1'
            ."ShowElements(true, 'select');".'$1'
            ."ShowElements(true, 'object');".'$1'
            ."ShowElements(false, 'object', 'FeedbackContent');".'$1'
            ."if (navigator.userAgent.indexOf('Chrome')>=0) {".'$1'
            ."	ShowElements(true, 'embed');".'$1'
            ."	ShowElements(false, 'embed', 'FeedbackContent');".'$1'
            ."}"
        ;
        $substr = preg_replace($search, $replace, $substr, 1);

        $str = substr_replace($str, $substr, $start, $length);
    }

    /**
     * fix_js_ShowSolution_JMemori
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_ShowSolution_JMemori(&$str, $start, $length)  {
        $substr = substr($str, $start, $length);

        $event = $this->get_send_results_event();
        $substr = str_replace('Finish()', "HP_send_results($event)", $substr);

        $str = substr_replace($str, $substr, $start, $length);
    }

    /**
     * fix_js_CheckPair_JMemori
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_CheckPair_JMemori(&$str, $start, $length)  {
        $substr = substr($str, $start, $length);

        // surround main body of function with if (id>=0) { ... }
        $search = '/(?<={)(.*)(?=if \(Pairs == F\.length\))/s';
        $replace = "\n\t".'if (id>=0) {$1}'."\n\t";
        $substr = preg_replace($search, $replace, $substr, 1);

        parent::fix_js_CheckAnswers($substr, 0, strlen($substr));
        $str = substr_replace($str, $substr, $start, $length);
    }
}
