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
 * Block XP edit form.
 *
 * @package    block_xp
 * @copyright  2014 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Moodle expects a certain class to be found here, so we have to workaround it like this.
$class = \block_xp\di::get('block_edit_form_class');
if (!class_exists($class) || !is_subclass_of($class, 'block_edit_form')) {
    throw new coding_exception('Block edit form class does not pass validation, or does not exist.');
}
class_alias($class, 'block_xp_block_edit_form_class');

/**
 * Block XP edit form class.
 *
 * Some code checker maybe expecting to find the class, so here it is...
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_xp_edit_form extends block_xp_block_edit_form_class {
}
