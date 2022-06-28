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
 * Moodleoverflow external functions and service definitions.
 *
 * @package    mod_moodleoverflow
 * @category   external
 * @copyright  2017 Kennet Winter <k_wint10@uni-muenster.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

$functions = array(
    'mod_moodleoverflow_record_vote' => array(
        'classname'    => 'mod_moodleoverflow_external',
        'methodname'   => 'record_vote',
        'classpath'    => 'mod/moodleoverflow/externallib.php',
        'description'  => 'Records a vote and updates the reputation of a user',
        'type'         => 'write',
        'ajax'         => true,
        'capabilities' => 'mod/moodleoverflow:ratepost'
    )
);
