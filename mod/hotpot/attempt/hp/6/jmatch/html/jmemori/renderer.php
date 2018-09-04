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
 * Output format: hp_6_jmatch_html_jmemori
 *
 * @package   mod-hotpot
 * @copyright 2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// get parent class
require_once($CFG->dirroot.'/mod/hotpot/attempt/hp/6/jmatch/html/renderer.php');

/**
 * mod_hotpot_attempt_hp_6_jmatch_html_jmemori_renderer
 *
 * @copyright 2010 Gordon Bateson
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class mod_hotpot_attempt_hp_6_jmatch_html_jmemori_renderer extends mod_hotpot_attempt_hp_6_jmatch_html_renderer {

    public $js_object_type = 'JMemori';

    /**
     * init
     *
     * @param xxx $hotpot
     */
    function init($hotpot)  {
        parent::init($hotpot);
        // replace standard jcloze.js with jmemori.js
        $this->javascripts = preg_grep('/jmatch.js/', $this->javascripts, PREG_GREP_INVERT);
        array_push($this->javascripts, 'mod/hotpot/attempt/hp/6/jmatch/jmemori.js');
    }

    /**
     * List of source types which this renderer can handle
     *
     * @return array of strings
     */
    static public function sourcetypes()  {
        return array('hp_6_jmatch_html_jmemori');
    }

    /**
     * fix_headcontent
     */
    function fix_headcontent()  {
        $this->fix_headcontent_rottmeier('jmemori');
    }

    /**
     * fix_bodycontent
     */
    function fix_bodycontent()  {
        $this->fix_bodycontent_rottmeier();
        parent::fix_bodycontent();
    }

    /**
     * fix_title
     */
    function fix_title()  {
        $this->fix_title_rottmeier_JMemori();
    }

    /**
     * get_js_functionnames
     *
     * @return xxx
     */
    function get_js_functionnames()  {
        // start list of function names
        $names = parent::get_js_functionnames();
        $names .= ($names ? ',' : '').'ShowSolution,CheckPair,WriteFeedback';
        return $names;
    }

    /**
     * fix_js_WriteFeedback
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_WriteFeedback(&$str, $start, $length)  {
        $this->fix_js_WriteFeedback_JMemori($str, $start, $length);
    }

    /**
     * fix_js_HideFeedback
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_HideFeedback(&$str, $start, $length)  {
        $this->fix_js_HideFeedback_JMemori($str, $start, $length);
    }

    /**
     * fix_js_ShowSolution
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_ShowSolution(&$str, $start, $length)  {
        $this->fix_js_ShowSolution_JMemori($str, $start, $length);
    }

    /**
     * fix_js_CheckPair
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_CheckPair(&$str, $start, $length)  {
        $this->fix_js_CheckPair_JMemori($str, $start, $length);
    }

    /**
     * get_stop_function_name
     *
     * @return xxx
     */
    function get_stop_function_name()  {
        return 'CheckPair';
    }

    /**
     * get_stop_function_search
     *
     * @return xxx
     */
    function get_stop_function_search()  {
        return '/\s*if \((Pairs == F\.length)\)({.*?)setTimeout.*?}/s';
    }

    /**
     * get_stop_function_args
     *
     * @return xxx
     */
    function get_stop_function_args()  {
        // the arguments required by CheckPair
        return '-1,'.hotpot::STATUS_ABANDONED;
    }

    /**
     * get_stop_function_intercept
     *
     * @return xxx
     */
    function get_stop_function_intercept()  {
        return "\n"
            ."	// intercept this Check\n"
            ."	if (id>=0) HP.onclickCheck(id);\n"
        ;
    }
}
