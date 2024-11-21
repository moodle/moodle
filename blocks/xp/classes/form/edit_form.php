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
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\form;
defined('MOODLE_INTERNAL') || die();

use block_edit_form;
use block_xp\di;

// Workaround code that would have been written in a way that does not load the form.
require_once($CFG->dirroot . '/blocks/edit_form.php');

/**
 * Block XP edit form class.
 *
 * @package    block_xp
 * @copyright  2014 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class edit_form extends block_edit_form {

    /**
     * Form definition.
     *
     * @param moodleform $mform Moodle form.
     * @return void
     */
    protected function specific_definition($mform) {
        $output = di::get('renderer');
        $mform->addElement('html', $output->notification_without_close(
            get_string('blockappearancemovedtopluginsettings', 'block_xp'), 'info'));
    }

}
