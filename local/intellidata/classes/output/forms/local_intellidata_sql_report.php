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
 * Sql report form.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2022
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_intellidata\output\forms;
defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/lib/formslib.php');

/**
 * Sql report form.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2022
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_intellidata_sql_report extends \moodleform {

    /**
     * Form definition.
     *
     * @return void
     * @throws \coding_exception
     */
    public function definition() {
        $mform = $this->_form;
        $data = $this->_customdata['data'];

        $mform->addElement('header', 'general', get_string('sql_report', 'local_intellidata'));

        $mform->addElement('text', 'name', get_string('sql_report_name', 'local_intellidata'));
        $mform->setType('name', PARAM_RAW);

        $mform->addElement('textarea', 'sqlcode', get_string('sql_report_code', 'local_intellidata'), [
            'readonly' => true, 'rows' => 16, 'cols' => 80,
        ]);
        $mform->setType('sqlcode', PARAM_RAW);

        $options = [
            get_string('sql_report_inactive', 'local_intellidata'),
            get_string('sql_report_active', 'local_intellidata'),
        ];
        $mform->addElement('select', 'status', get_string('sql_report_status', 'local_intellidata'), $options);

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'debug');
        $mform->setType('debug', PARAM_INT);

        $this->add_action_buttons();
        $this->set_data($data);
    }
}
