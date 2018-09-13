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
 * mod/hotpot/mediafilter/moodle/class.php
 *
 * @package   mod-hotpot
 * @copyright 2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// get the parent class (=hotpot_mediafilter)
require_once($CFG->dirroot.'/mod/hotpot/mediafilter/class.php');

/**
 * hotpot_mediafilter_moodle
 *
 * @copyright 2010 Gordon Bateson
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class hotpot_mediafilter_moodle extends hotpot_mediafilter {

    /*
     * mediaplugin_filter
     *
     * @param xxx $hotpot
     * @param xxx $text
     * @param xxx $options
     */
    function mediaplugin_filter($hotpot, $text, $options=array()) {
        global $CFG, $PAGE;
        static $eolas_fix_applied = 0;

        // insert media players using Moodle's standard mediaplugin filter
        $filter = new filter_mediaplugin($hotpot->context, array());
        $newtext = $filter->filter($text);

        if ($newtext==$text) {
            // do nothing
        } else if ($eolas_fix_applied==$hotpot->id) {
            // eolas_fix.js and ufo.js have already been added for this quiz
        } else {
            if ($eolas_fix_applied==0) {
                // 1st quiz - eolas_fix.js was added by filter/mediaplugin/filter.php
            } else {
                // 2nd (or later) quiz - e.g. we are being called by hotpot_cron()
                $PAGE->requires->js('/mod/hotpot/mediafilter/eolas_fix.js');
                //$newtext .= '<script defer="defer" src="'.$CFG->wwwroot.'/mod/hotpot/mediafilter/eolas_fix.js" type="text/javascript"></script>';
            }
            $PAGE->requires->js('/mod/hotpot/mediafilter/ufo.js', true);
            //$newtext .= '<script type="text/javascript" src="'.$CFG->wwwroot.'/mod/hotpot/mediafilter/ufo.js"></script>';
            $eolas_fix_applied = $hotpot->id;
        }

        return $newtext;
    }
}
