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
 * Output format: hp_6_sequitur_xml
 *
 * @package   mod-hotpot
 * @copyright 2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// get parent class
require_once($CFG->dirroot.'/mod/hotpot/attempt/hp/6/sequitur/renderer.php');

/**
 * mod_hotpot_attempt_hp_6_sequitur_xml_renderer
 *
 * @copyright 2010 Gordon Bateson
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class mod_hotpot_attempt_hp_6_sequitur_xml_renderer extends mod_hotpot_attempt_hp_6_sequitur_renderer {

    /**
     * expand_JSSequitur6
     */
    function expand_JSSequitur6() {
        return $this->expand_template('sequitur6.js_');
    }

    /**
     * expand_NumberOfOptions
     *
     * @return xxx
     */
    function expand_NumberOfOptions()  {
        $tags = $this->hotpot->source->hbs_software.'-config-file,'.$this->hotpot->source->hbs_quiztype.',number-of-options';
        return $this->hotpot->source->xml_value_int($tags);
    }

    /**
     * expand_PartText
     *
     * @return xxx
     */
    function expand_PartText()  {
        $tags = $this->hotpot->source->hbs_software.'-config-file,'.$this->hotpot->source->hbs_quiztype.',show-part-text';
        return $this->hotpot->source->xml_value($tags);
    }

    /**
     * expand_Solution
     *
     * @return xxx
     */
    function expand_Solution()  {
        $tags = $this->hotpot->source->hbs_software.'-config-file,'.$this->hotpot->source->hbs_quiztype.',include-solution';
        return $this->hotpot->source->xml_value_int($tags);
    }

    /**
     * expand_SolutionCaption
     *
     * @return xxx
     */
    function expand_SolutionCaption() {
        $tags = $this->hotpot->source->hbs_software.'-config-file,global,solution-caption';
        return $this->hotpot->source->xml_value($tags);
    }

    /**
     * expand_Score
     *
     * @return xxx
     */
    function expand_Score()  {
        $tags = $this->hotpot->source->hbs_software.'-config-file,global,your-score-is';
        return $this->hotpot->source->xml_value_js($tags);
    }

    /**
     * expand_WholeText
     *
     * @return xxx
     */
    function expand_WholeText()  {
        $tags = $this->hotpot->source->hbs_software.'-config-file,'.$this->hotpot->source->hbs_quiztype.',show-whole-text';
        return $this->hotpot->source->xml_value($tags);
    }

    /**
     * expand_SegmentsArray
     *
     * @return xxx
     */
    function expand_SegmentsArray() {
        // we might have empty segments, so we need to first
        // find out how many segments there are and then go
        // through them all, ignoring the empty ones

        $i_max = 0;
        if ($segments = $this->hotpot->source->xml_value('data,segments')) {
            if (isset($segments['segment'])) {
                $i_max = count($segments['segment']);
            }
        }
        unset($segments);

        $str = '';
        $tags = 'data,segments,segment';

        $i =0 ;
        $ii =0 ;
        while ($i<$i_max) {
            if ($segment = $this->hotpot->source->xml_value_js($tags, "[$i]['#']")) {
                $str .= "Segments[$ii]='$segment';\n";
                $ii++;
            }
            $i++;
        }

        return $str;
    }

    /**
     * expand_StyleSheet
     *
     * @return xxx
     */
    function expand_StyleSheet()  {
        return $this->expand_template('tt3.cs_');
    }
}
