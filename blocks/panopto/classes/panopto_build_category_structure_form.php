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

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * The form wrapper used to build the Moodle category structure on Panopto
 *
 * @package block_panopto
 * @copyright  Panopto 2020
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class panopto_build_category_structure_form extends moodleform {

    /**
     * @var string $title
     */
    protected $title = '';

    /**
     * @var string $description
     */
    protected $description = '';

    /**
     * Defines a Panopto build category structure form
     */
    public function definition() {

        global $DB;
        global $aserverarray;

        $mform = & $this->_form;

        $serverselect = $mform->addElement('select', 'servers', get_string('select_server', 'block_panopto'), $aserverarray);
        $mform->addHelpButton('servers', 'select_server', 'block_panopto');

        $this->add_action_buttons(true, get_string('begin_building_category_structure', 'block_panopto'));
    }
}
