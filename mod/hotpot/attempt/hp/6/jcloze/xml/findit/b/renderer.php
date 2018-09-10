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
 * Output format: hp_6_jcloze_xml_findit_b
 *
 * @package   mod-hotpot
 * @copyright 2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// get parent class
require_once($CFG->dirroot.'/mod/hotpot/attempt/hp/6/jcloze/xml/findit/renderer.php');

/**
 * mod_hotpot_attempt_hp_6_jcloze_xml_findit_b_renderer
 *
 * @copyright 2010 Gordon Bateson
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class mod_hotpot_attempt_hp_6_jcloze_xml_findit_b_renderer extends mod_hotpot_attempt_hp_6_jcloze_xml_findit_renderer {


    public $js_object_type = 'JClozeFindItB';

    /**
     * init
     *
     * @param xxx $hotpot
     */
    function init($hotpot)  {
        parent::init($hotpot);

        // prepend templates for this output format
        array_unshift($this->templatesfolders, 'mod/hotpot/attempt/hp/6/jcloze/xml/findit/b/templates');

        // replace standard jcloze.js with findit.b.js
        $this->javascripts = preg_grep('/jcloze.js/', $this->javascripts, PREG_GREP_INVERT);
        array_push($this->javascripts, 'mod/hotpot/attempt/hp/6/jcloze/findit.b.js');
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
     * fix_js_CheckAnswers
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_CheckAnswers(&$str, $start, $length)  {
        $substr = substr($str, $start, $length);

        // do several search and replace actions at once
        $search = array(
            '/if \(NumOfVisibleGaps < 1\)\{return;\}/',
            "/(\s+)Output = '';/s",
            '/Output \+= MissingMistakes \+ Get_NumMissingErr\(\);/',
            '/CalculateScore\(\);/' // last occurrence
        );
        $replace = array(
            'if (NumOfVisibleGaps){',
            '$1}$0',
            'if (NumOfVisibleGaps) $0',
            'if (NumOfVisibleGaps) $0'
        );
        $substr = preg_replace($search, $replace, $substr, 1);

        parent::fix_js_CheckAnswers($substr, 0, strlen($substr));

        $str = substr_replace($str, $substr, $start, $length);
    }
}
