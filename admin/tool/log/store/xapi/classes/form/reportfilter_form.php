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

namespace logstore_xapi\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

/**
 * The filter form for the xAPI admin reports
 *
 * @package logstore_xapi
 * @copyright 2020 Learning Pool Ltd <https://learningpool.com/>
 * @author Stephen O'Hara <stephen.ohara@learningpool.com>
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_logstore_xapi_reportfilter_form extends \moodleform {

    /**
     * Form definition.
     */
    public function definition() {
        $mform = $this->_form;

        // Set filters contents and set defaults.
        $reportid = $this->_customdata['reportid'];
        $eventnames = $this->_customdata['eventnames'];

        // Check permissions.
        $systemcontext = \context_system::instance();
        $canmanage = false;

        switch ($reportid) {

            case XAPI_REPORT_ID_ERROR:
                $errortypes = $this->_customdata['errortypes'];
                $responses = $this->_customdata['responses'];

                if (has_capability('logstore/xapi:manageerrors', $systemcontext)) {
                    $canmanage = true;
                }
                break;

            case XAPI_REPORT_ID_HISTORIC:
                $eventcontexts = $this->_customdata['eventcontexts'];

                if (has_capability('logstore/xapi:managehistoric', $systemcontext)) {
                    $canmanage = true;
                }
                break;

            default:
                break;
        }

        $mform->addElement('hidden', 'run', true);
        $mform->setType('run', PARAM_BOOL);

        $mform->addElement('hidden', 'resend');
        $mform->setType('resend', PARAM_BOOL);
        $mform->setDefault('resend', $this->_customdata['defaults']['resend']);

        if ($reportid == XAPI_REPORT_ID_ERROR) {
            $mform->addElement('select', 'errortype', get_string('errortype', 'logstore_xapi'), $errortypes);

            if (!empty($this->_customdata['defaults']['errortype'])) {
                $mform->setDefault('errortype', $this->_customdata['defaults']['errortype']);
            }
        }

        $mform->addElement('select', 'eventnames', get_string('eventname', 'logstore_xapi'), $eventnames);
        $mform->getElement('eventnames')->setMultiple(true);
        if (!empty($this->_customdata['defaults']['eventnames'])) {
            $mform->getElement('eventnames')->setSelected($this->_customdata['defaults']['eventnames']);
        }

        switch ($reportid) {

            case XAPI_REPORT_ID_ERROR:
                $mform->addElement('select', 'response', get_string('response', 'logstore_xapi'), $responses);

                if (!empty($this->_customdata['defaults']['response'])) {
                    $mform->setDefault('response', $this->_customdata['defaults']['response']);
                }
                break;

            case XAPI_REPORT_ID_HISTORIC:
                $mform->addElement('text', 'userfullname', get_string('user', 'logstore_xapi'));
                $mform->setType('userfullname', PARAM_RAW);
                $mform->addHelpButton('userfullname', 'user', 'logstore_xapi');
                if (!empty($this->_customdata['defaults']['userfullname'])) {
                    $mform->setDefault('userfullname', $this->_customdata['defaults']['userfullname']);
                }

                $mform->addElement('select', 'eventcontext', get_string('eventcontext', 'logstore_xapi'), $eventcontexts);
                if (!empty($this->_customdata['defaults']['eventcontext'])) {
                    $mform->setDefault('eventcontext', $this->_customdata['defaults']['eventcontext']);
                }
                break;

            default:
                break;
        }

        $mform->addElement('date_selector', 'datefrom', get_string('from'), ['optional' => true]);
        if (!empty($this->_customdata['defaults']['datefrom'])) {
            $mform->setDefault('datefrom', $this->_customdata['defaults']['datefrom']);
        }
        $mform->addElement('date_selector', 'dateto', get_string('to'), ['optional' => true]);
        if (!empty($this->_customdata['defaults']['dateto'])) {
            $mform->setDefault('dateto', $this->_customdata['defaults']['dateto']);
        }

        $this->add_action_buttons(false, get_string('search'));

        if ($canmanage) {
            $mform->addElement('button', 'resendselected', '', ['disabled' => true, 'class' => 'disabled']);
        }
    }

    /**
     * Form validation
     *
     * @param array $data data from the form.
     * @param array $files files uploaded.
     *
     * @return array of errors.
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if (!empty($data['datefrom']) && !empty($data['dateto'])) {
            if ($data['datefrom'] > $data['dateto']) {
                $errors['dateto'] = get_string('datetovalidation', 'logstore_xapi');
            }
        }

        return $errors;
    }
}
