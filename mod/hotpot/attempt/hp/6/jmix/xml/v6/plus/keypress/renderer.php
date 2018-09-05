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
 * Output format: hp_6_jmix_xml_v6_plus_keypress
 *
 * @package   mod-hotpot
 * @copyright 2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// get parent class
require_once($CFG->dirroot.'/mod/hotpot/attempt/hp/6/jmix/xml/v6/plus/renderer.php');

/**
 * mod_hotpot_attempt_hp_6_jmix_xml_v6_plus_keypress_renderer
 *
 * @copyright 2010 Gordon Bateson
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class mod_hotpot_attempt_hp_6_jmix_xml_v6_plus_keypress_renderer extends mod_hotpot_attempt_hp_6_jmix_xml_v6_plus_renderer {

    /**
     * fix_bodycontent
     *
     * @return xxx
     */
    function fix_bodycontent() {
        parent::fix_bodycontent_DragAndDrop();

        $search = 'onclick="location.reload()"';
        $replace = 'onclick="hotpot_jmix_restart()"';
        $this->bodycontent = str_replace($search, $replace, $this->bodycontent);

        $this->bodycontent .= "\n"
            .'<script type="text/javascript">'."\n"
            ."//<![CDATA[\n"

            ."function GetLastCard(){\n"
            ."	var LastLine = -1;\n"
            ."	var Lines = new Array();\n"
            ."	for (var Lines_i=0; Lines_i<L.length; Lines_i++){\n"
            ."		var LineT = L[Lines_i].GetT() - 4;\n"
            ."		var LineB = L[Lines_i].GetB() + 4;\n"
            ."		for (var Cards_i=0; Cards_i<Cds.length; Cards_i++){\n"
            ."			var CardT = Cds[Cards_i].GetT();\n"
            ."			var CardB = Cds[Cards_i].GetB();\n"
            ."			if (CardT >= LineT && CardB <= LineB) {\n"
            ."				if (Lines_i > LastLine) {\n"
            ."					Lines[Lines_i] = new Array();\n"
            ."					LastLine = Lines_i;\n"
            ."				}\n"
            ."				Lines[Lines_i][Lines[Lines_i].length] = Cds[Cards_i];\n"
            ."			}\n"
            ."		}\n"
            ."	}\n"
            ."	var LastCard = null;\n"
            ."	if (LastLine >= 0) {\n"
            ."		for (var i=0; i<Lines[LastLine].length; i++){\n"
            ."			if (LastCard==null || LastCard.GetL() < Lines[LastLine][i].GetL()) {\n"
            ."				LastCard = Lines[LastLine][i];\n"
            ."			}\n"
            ."		}\n"
            ."	}\n"
            ."	return LastCard;\n"
            ."}\n"

            ."function GetIndexByProperty(array, property, value, i_min) {\n"
            ."	if (typeof(i_min)=='undefined') {\n"
            ."		i_min = 0;\n"
            ."	} else {\n"
            ."		i_min = i_min + 1;\n"
            ."	}\n"
            ."	var i_max = array.length;\n"
            ."	for (var i=i_min; i<i_max; i++) {\n"
            ."		if (typeof(array[i][property])=='string' && typeof(value)=='string') {\n"
            ."			var len = Math.min(value.length, array[i][property].length);\n"
            ."			if (len && value.substr(0, len)==array[i][property].substr(0, len)) {\n"
            ."				return i;\n"
            ."			}\n"
            ."		} else if (array[i][property]==value) {\n"
            ."			return i;\n"
            ."		}\n"
            ."	}\n"
            ."	return i_max;\n"
            ."}\n"

            ."function hotpot_jmix_get_unicode(e) {\n"
            ."	if (typeof(e)=='undefined' && window.event) {\n"
            ."		e = window.event;\n"
            ."	}\n"
            ."	var unicode = 0;\n"
            ."	if (e) {\n"
            ."		if (e.altKey || e.ctrlKey) {\n"
            ."			unicode = 0;\n"
            ."		} else if (e.charCode) {\n"
            ."			unicode = e.charCode;\n"
            ."		} else if (e.keyCode) {\n"
            ."			unicode = e.keyCode;\n"
            ."		}\n"
            ."	}\n"
            ."	return unicode;\n"
            ."}\n"

            ."function hotpot_jmix_onkeypress(e) {\n"
            ."	var unicode = hotpot_jmix_get_unicode(e);\n"
            ."	if (unicode) {\n"
            ."		var char = String.fromCharCode(unicode);\n"
            ."		var LastLine_Bottom = L[L.length - 1].GetB();\n"

            ."		var Segment_i = GetIndexByProperty(Segments, 0, char);\n"
            ."		var ThisCard_i = GetIndexByProperty(Cds, 'index', Segment_i);\n"

            ."		var found = false;\n"
            ."		while (true) {\n"
            ."			if (Segment_i >= Segments.length) {\n"
            ."				break;\n"
            ."			}\n"
            ."			if (ThisCard_i >= Cds.length) {\n"
            ."				break;\n"
            ."			}\n"
            ."			if (Cds[ThisCard_i].GetT() > LastLine_Bottom) {\n"
            ."				found = true;\n"
            ."				break;\n"
            ."			}\n"
            ."			Segment_i = GetIndexByProperty(Segments, 0, char, Segment_i);\n"
            ."			ThisCard_i = GetIndexByProperty(Cds, 'index', Segment_i);\n"
            ."		}\n"

            ."		if (found) {\n"
            ."			var LastCard = GetLastCard();\n"
            ."			if (LastCard) {\n"
            ."				var l = LastCard.GetR() + 4;\n"
            ."				if (window.hotpot_jmix_whitespace) {\n"
            ."					l += LastCard.GetW();\n"
            ."				}\n"
            ."				var t = LastCard.GetT();\n"
            ."			} else {\n"
            ."				var l = Cds[0].HomeL;\n"
            ."				var t = L[0].GetT();\n"
            ."			}\n"
            ."			Cds[ThisCard_i].SetL(l);\n"
            ."			Cds[ThisCard_i].SetT(t);\n"
            ."			window.hotpot_jmix_whitespace = false;\n"
            ."			window.hotpot_jmix_checkresults = true;\n"
            ."			// adjust css top of ThisCard in standard HP way (required for Mac FF)\n"
            ."			CurrDrag = ThisCard_i;"
            ."			onEndDrag();"
            ."			CurrDrag = -1;"
            ."			// all done\n"
            ."			return false;\n"
            ."		}\n"

            ."		// 10=Enter, 13=Return\n"
            ."		if (unicode==10 || unicode==13) {\n"
            ."			window.hotpot_jmix_whitespace = false;\n"
            ."			if (window.hotpot_jmix_checkresults) {\n"
            ."				CheckResults(0);\n"
            ."				window.hotpot_jmix_checkresults = false;\n"
            ."				return false;\n"
            ."			}\n"
            ."		}\n"

            ."		// 32=Space\n"
            ."		if (unicode==32) {\n"
            ."			window.hotpot_jmix_whitespace = true;\n"
            ."			window.hotpot_jmix_checkresults = true;\n"
            ."			return false;\n"
            ."		}\n"

            ."	}\n"
            ."	return true;\n"
            ."}\n"
            ."document.onkeypress = hotpot_jmix_onkeypress;\n"

            ."function hotpot_jmix_onkeydown(e) {\n"
            ."	var unicode = hotpot_jmix_get_unicode(e);\n"
            ."	if (unicode) {\n"
            ."		// 8=Backspace, 46=Delete\n"
            ."		if (unicode==8 || unicode==46) {\n"
            ."			var LastCard = GetLastCard();\n"
            ."			if (LastCard) {\n"
            ."				LastCard.GoHome();\n"
            ."			}\n"
            ."			window.hotpot_jmix_whitespace = false;\n"
            ."			window.hotpot_jmix_checkresults = true;\n"
            ."			return false;\n"
            ."		}\n"
            ."		// 27=Esc\n"
            ."		if (unicode==27) {\n"
            ."			hotpot_jmix_restart();\n"
            ."			return false;\n"
            ."		}\n"
            ."	}\n"
            ."	return true;\n"
            ."}\n"
            ."document.onkeydown = hotpot_jmix_onkeydown;\n"

            ."function hotpot_jmix_restart() {\n"
            ."	for (var i=0; i<Cds.length; i++){\n"
            ."		Cds[i].GoHome();\n"
            ."	}\n"
            ."	window.hotpot_jmix_whitespace = false;\n"
            ."	window.hotpot_jmix_checkresults = true;\n"
            ."	return true;\n"
            ."}\n"

            ."//]]>\n"
            .'</script>'
        ;
    }
}
