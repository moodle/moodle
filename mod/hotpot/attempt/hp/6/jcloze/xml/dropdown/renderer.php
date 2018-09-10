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
 * Output format: hp_6_jcloze_xml_dropdown
 *
 * @package   mod-hotpot
 * @copyright 2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// get parent class
require_once($CFG->dirroot.'/mod/hotpot/attempt/hp/6/jcloze/xml/renderer.php');

/**
 * mod_hotpot_attempt_hp_6_jcloze_xml_dropdown_renderer
 *
 * @copyright 2010 Gordon Bateson
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class mod_hotpot_attempt_hp_6_jcloze_xml_dropdown_renderer extends mod_hotpot_attempt_hp_6_jcloze_xml_renderer {

    public $js_object_type = 'JClozeDropDown';

    /**
     * init
     *
     * @param xxx $hotpot
     */
    function init($hotpot)  {
        parent::init($hotpot);

        // prepend templates for this output format
        array_unshift($this->templatesfolders, 'mod/hotpot/attempt/hp/6/jcloze/xml/dropdown/templates');

        // replace standard jcloze.js with dropdown.js
        $this->javascripts = preg_grep('/jcloze.js/', $this->javascripts, PREG_GREP_INVERT);
        array_push($this->javascripts, 'mod/hotpot/attempt/hp/6/jcloze/dropdown.js');
    }

    /**
     * List of source types which this renderer can handle
     *
     * @return array of strings
     */
    static public function sourcetypes()  {
        return array('hp_6_jcloze_xml');
    }

    /**
     * get_js_functionnames
     *
     * @return xxx
     */
    function get_js_functionnames()  {
        // start list of function names
        $names = parent::get_js_functionnames();
        $names .= ($names ? ',' : '').'Show_Solution,Build_GapText';
        return $names;
    }

    /**
     * fix_headcontent
     */
    function fix_headcontent()  {
        $this->fix_headcontent_rottmeier('dropdown');
    }

    /**
     * fix_bodycontent
     */
    function fix_bodycontent()  {
        $this->fix_bodycontent_rottmeier(true);
    }

    /**
     * fix_js_Build_GapText
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     * @return xxx
     */
    function fix_js_Build_GapText(&$str, $start, $length) {
        $substr = substr($str, $start, $length);

        parent::fix_js_Build_GapText($substr, 0, strlen($substr));

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
}
