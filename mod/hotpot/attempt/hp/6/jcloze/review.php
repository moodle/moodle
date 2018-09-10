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
 * Review results of an attempt at a HotPot quiz
 * Output format: hp_6_jcloze
 *
 * @package   mod-hotpot
 * @copyright 2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// get parent class
require_once($CFG->dirroot.'/mod/hotpot/attempt/hp/6/review.php');

/**
 * mod_hotpot_attempt_hp_6_jcloze_review
 *
 * @copyright 2010 Gordon Bateson
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class mod_hotpot_attempt_hp_6_jcloze_review extends mod_hotpot_attempt_hp_6_review {

    /**
     * attempt_fields
     *
     * @return xxx
     */
    static function attempt_fields()   {
        return array('attempt', 'score', 'status', 'duration', 'timemodified');
    }

    /**
     * response_num_fields
     *
     * @return xxx
     */
    static function response_num_fields()   {
        return array('score', 'hints', 'clues', 'checks');
    }
}
