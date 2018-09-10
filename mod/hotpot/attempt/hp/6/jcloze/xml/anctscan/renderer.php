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
 * Output format: hp_6_jcloze_xml_anctscan
 *
 * @package   mod-hotpot
 * @copyright 2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// get parent class
require_once($CFG->dirroot.'/mod/hotpot/attempt/hp/6/jcloze/xml/renderer.php');

/**
 * mod_hotpot_attempt_hp_6_jcloze_xml_anctscan_renderer
 *
 * @copyright 2010 Gordon Bateson
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class mod_hotpot_attempt_hp_6_jcloze_xml_anctscan_renderer extends mod_hotpot_attempt_hp_6_jcloze_xml_renderer {

    public $js_object_type = 'JCloze_ANCT_Scan';

    /**
     * init
     *
     * @param xxx $hotpot
     */
    function init($hotpot)  {
        parent::init($hotpot);

        // prepend templates for this output format
        array_unshift($this->templatesfolders, 'mod/hotpot/attempt/hp/6/jcloze/xml/anctscan/templates');

        // replace standard jcloze.js with dropdown.js
        $this->javascripts = preg_grep('/jcloze.js/', $this->javascripts, PREG_GREP_INVERT);
        array_push($this->javascripts, 'mod/hotpot/attempt/hp/6/jcloze/anctscan.js');
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
        $names .= ($names ? ',' : '').'CheckExStatus,DownTime,TimesUp';
        return $names;
    }

    /**
     * fix_js_CheckExStatus
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_CheckExStatus(&$str, $start, $length)  {
        $substr = substr($str, $start, $length);

        // add changes as per CheckAnswers in other type of HP quiz
        $this->fix_js_CheckAnswers($substr, 0, $length);

        $str = substr_replace($str, $substr, $start, $length);
    }

    /**
     * fix_js_DownTime
     *
     * @param xxx $str (passed by reference)
     * @param xxx $start
     * @param xxx $length
     */
    function fix_js_DownTime(&$str, $start, $length)  {
        $substr = substr($str, $start, $length);

        $substr = str_replace('TimesUp();', 'CheckExStatus(2);', $substr);

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
        $this->remove_js_function($str, $start, $length, 'TimesUp');
    }

    /**
     * get_stop_function_name
     *
     * @return xxx
     */
    function get_stop_function_name()  {
        return 'CheckExStatus';
    }

    /**
     * get_stop_function_search
     *
     * @return xxx
     */
    function get_stop_function_search()  {
        return '/\s*if \((ExStatus)\)({.*?)}/s';
    }

    /**
     * get_stop_function_intercept
     *
     * @return xxx
     */
    function get_stop_function_intercept()  {
        // do not add standard onclickCheck()
        return '';
    }

    /**
     * fix_bodycontent
     */
    function fix_bodycontent()  {
        $this->fix_bodycontent_rottmeier(true);
    }
}
