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
 * Block MHAAIRS Improved
 *
 * @package    block_mhaairs
 * @copyright  2013-2014 Moodlerooms inc.
 * @author     Teresa Hardy <thardy@moodlerooms.com>
 * @author     Darko Miletic <dmiletic@moodlerooms.xom>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$capabilities = array(
    'block/mhaairs:addinstance' => array(
        'riskbitmask' => RISK_SPAM | RISK_XSS,
        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'coursecreator' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        ),
        'clonepermissionsfrom' => 'moodle/site:manageblocks'
    ),

    'block/mhaairs:viewadmindoc' => array(
            'captype' => 'read',
            'contextlevel' => CONTEXT_COURSE,
            'legacy' => array(
                    'manager' => CAP_ALLOW
            )
    ),

    'block/mhaairs:viewteacherdoc' => array(
            'captype' => 'read',
            'contextlevel' => CONTEXT_COURSE,
            'legacy' => array(
                    'teacher' => CAP_ALLOW,
                    'editingteacher' => CAP_ALLOW,
                    'coursecreator' => CAP_ALLOW,
                    'manager' => CAP_ALLOW
            )
     ),

);
