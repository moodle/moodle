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

use local_intellidata\helpers\TrackingHelper;
use local_intellidata\persistent\datatypeconfig;

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
class local_intellidata_edit_config extends \moodleform {

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

        if (isset($this->_customdata['is_required']) && $this->_customdata['is_required']) {
            $this->required_form();
        } else {
            $this->optional_form();
        }

        $mform->addElement('hidden', 'datatype');
        $mform->setType('datatype', PARAM_ALPHA);

        $mform->addElement('hidden', 'tableindex');
        $mform->setType('tableindex', PARAM_TEXT);

        $this->add_action_buttons();
        $this->set_data($data);
    }

    /**
     * Form for required datatype.
     *
     * @return void
     * @throws \coding_exception
     */
    protected function required_form() {
        $mform = $this->_form;
        $config = $this->_customdata['config'];

        $options = [
            datatypeconfig::TABLETYPE_REQUIRED => get_string('required', 'local_intellidata'),
            datatypeconfig::TABLETYPE_OPTIONAL => get_string('optional', 'local_intellidata'),
        ];
        $mform->addElement('select', 'tabletype', get_string('tabletype', 'local_intellidata'), $options);
        $mform->setType('tabletype', PARAM_INT);

        if (isset($config->canbedisabled)) {
            $mform->addElement('advcheckbox', 'enableexport', get_string('enableexport', 'local_intellidata'));
            $mform->setType('enableexport', PARAM_INT);
            $mform->disabledIf('enableexport', 'status', 'neq', datatypeconfig::STATUS_ENABLED);
        }
    }

    /**
     * Form for optional datatype.
     *
     * @return void
     * @throws \coding_exception
     * @throws \dml_exception
     */
    protected function optional_form() {
        $mform = $this->_form;
        $config = $this->_customdata['config'];

        $options = [
            datatypeconfig::STATUS_ENABLED => get_string('enabled', 'local_intellidata'),
            datatypeconfig::STATUS_DISABLED => get_string('disabled', 'local_intellidata'),
        ];
        $mform->addElement('select', 'status', get_string('status', 'local_intellidata'), $options);
        $mform->setType('status', PARAM_INT);

        $mform->addElement('advcheckbox', 'enableexport', get_string('enableexport', 'local_intellidata'));
        $mform->setType('enableexport', PARAM_INT);
        $mform->disabledIf('enableexport', 'status', 'neq', datatypeconfig::STATUS_ENABLED);

        if (!TrackingHelper::new_tracking_enabled()) {
            $mform->addElement('select', 'timemodified_field',
                get_string('timemodified_field', 'local_intellidata'), ['' => ''] + $config->timemodifiedfields);
            $mform->setType('timemodified_field', PARAM_ALPHANUMEXT);

            if (!empty($config->observer)) {
                $mform->addElement('advcheckbox', 'events_tracking', get_string('events_tracking', 'local_intellidata'));
                $mform->setType('events_tracking', PARAM_INT);
            }

            $mform->addElement('advcheckbox', 'filterbyid', get_string('filterbyid', 'local_intellidata'));
            $mform->setType('filterbyid', PARAM_INT);
            $mform->disabledIf('filterbyid', 'timemodified_field', 'neq', '');
        } else {
            $mform->addElement('hidden', 'filterbyid');
            $mform->setType('filterbyid', PARAM_INT);

            $mform->addElement('hidden', 'timemodified_field');
            $mform->setType('timemodified_field', PARAM_ALPHANUMEXT);
        }

        $mform->addElement('advcheckbox', 'rewritable', get_string('rewritable', 'local_intellidata'));
        $mform->setType('rewritable', PARAM_INT);
        $mform->disabledIf('rewritable', 'timemodified_field', 'neq', '');
        $mform->disabledIf('rewritable', 'filterbyid', 'checked');
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
        $buttonarray[] = &$mform->createElement('submit', 'reset', get_string('resettodefault', 'local_intellidata'));
        $buttonarray[] = &$mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', [' '], false);
        $mform->closeHeaderBefore('buttonar');
    }
}
