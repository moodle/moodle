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
//
/**
 * Defines global settings for the Message My Teacher block
 *
 * Allows selection of roles to be considered "Teachers", and thus displayed in the block
 *
 * @package    block_messageteacher
 * @author     Mark Johnson <mark.johnson@tauntons.ac.uk>
 * @copyright  2010 onwards Tauntons College, UK
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$settings->add(new admin_setting_pickroles('block_messageteacher/roles',
                                           get_string('teachersinclude', 'block_messageteacher'),
                                           get_string('rolesdesc', 'block_messageteacher'),
                                           array('moodle/legacy:teacher'),
                                           PARAM_TEXT));

$settings->add(new admin_setting_configcheckbox('block_messageteacher/groups',
                                           get_string('enablegroups', 'block_messageteacher'),
                                           get_string('groupsdesc', 'block_messageteacher'),
                                           0));

$settings->add(new admin_setting_configcheckbox('block_messageteacher/showuserpictures',
                                           get_string('showuserpictures', 'block_messageteacher'),
                                           get_string('showuserpicturesdesc', 'block_messageteacher'),
                                           0));

$settings->add(new admin_setting_configcheckbox('block_messageteacher/includecoursecat',
                                           get_string('includecoursecat', 'block_messageteacher'),
                                           get_string('includecoursecatdesc', 'block_messageteacher'),
                                           0));

$settings->add(new admin_setting_configcheckbox('block_messageteacher/appendurl',
                                           get_string('appendurl', 'block_messageteacher'),
                                           get_string('appendurldesc', 'block_messageteacher'),
                                           0));
