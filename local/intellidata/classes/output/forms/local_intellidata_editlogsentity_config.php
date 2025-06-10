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
 * Edit config form.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2022
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_intellidata\output\forms;

use local_intellidata\persistent\datatypeconfig;
use local_intellidata\repositories\logs_tables_repository;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/lib/formslib.php');

/**
 * Edit config form.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2022
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_intellidata_editlogsentity_config extends \moodleform {

    /**
     * Form definition.
     *
     * @return void
     * @throws \coding_exception
     */
    public function definition() {
        $mform = $this->_form;
        $data = $this->_customdata['data'];
        $exportlog = $this->_customdata['exportlog'];

        $data->enableexport = (!empty($exportlog)) ? 1 : 0;

        if (!empty($data->id)) {
            $mform->addElement('hidden', 'datatype');
        } else {
            $mform->addElement('text', 'datatype', get_string('datatype', 'local_intellidata'));
            $mform->addRule('datatype', get_string('required'), 'required', null, 'client');
        }
        $mform->setType('datatype', PARAM_ALPHANUMEXT);

        $options = [
            datatypeconfig::STATUS_ENABLED => get_string('enabled', 'local_intellidata'),
            datatypeconfig::STATUS_DISABLED => get_string('disabled', 'local_intellidata'),
        ];
        $mform->addElement('select', 'status', get_string('status', 'local_intellidata'), $options);
        $mform->setType('status', PARAM_INT);

        $mform->addElement('advcheckbox', 'enableexport', get_string('enableexport', 'local_intellidata'));
        $mform->setType('enableexport', PARAM_INT);
        $mform->disabledIf('enableexport', 'status', 'neq', datatypeconfig::STATUS_ENABLED);

        foreach (logs_tables_repository::get_logtable_fields() as $fieldname) {
            $mform->addElement('text', 'params[' . $fieldname . ']', $fieldname);
            $mform->setType('params[' . $fieldname . ']', PARAM_TEXT);
        }

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $this->add_action_buttons();
        $this->set_data($data);
    }

    /**
     * Add action buttons.
     *
     * @param bool $cancel
     * @param null $submitlabel
     * @throws \coding_exception
     */
    public function add_action_buttons($cancel = true, $submitlabel = null) {
        if (is_null($submitlabel)) {
            $submitlabel = get_string('savechanges');
        }
        $mform =& $this->_form;

        // When two elements we need a group.
        $buttonarray = [];
        $buttonarray[] = &$mform->createElement('submit', 'submitbutton', $submitlabel);
        $buttonarray[] = &$mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', [' '], false);
        $mform->closeHeaderBefore('buttonar');
    }

    /**
     * Form validation rules.
     *
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // Validate datatype name.
        if (empty($data['id'])) {
            $datatype = strtolower($data['datatype']);

            if (datatypeconfig::record_exists_select('datatype = :datatype', ['datatype' => $datatype])) {
                $errors['datatype'] = get_string('datatypealreadyexists', 'local_intellidata');
            }
        }

        // Validate params.
        $empty = true;
        foreach ($data['params'] as $val) {
            if (!empty($val)) {
                $empty = false;
                break;
            }
        }
        if ($empty) {
            foreach (logs_tables_repository::get_logtable_fields() as $fieldname) {
                $errors['params[' . $fieldname . ']'] = get_string('paramsshouldbespecified', 'local_intellidata');
            }
        }

        return $errors;
    }
}
