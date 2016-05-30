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
 * Form classes for editing badges
 *
 * @package    core
 * @subpackage badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/badgeslib.php');
require_once($CFG->libdir . '/filelib.php');

/**
 * Form to edit badge details.
 *
 */
class edit_details_form extends moodleform {

    /**
     * Defines the form
     */
    public function definition() {
        global $CFG;

        $mform = $this->_form;
        $badge = (isset($this->_customdata['badge'])) ? $this->_customdata['badge'] : false;
        $action = $this->_customdata['action'];

        $mform->addElement('header', 'badgedetails', get_string('badgedetails', 'badges'));
        $mform->addElement('text', 'name', get_string('name'), array('size' => '70'));
        // Using PARAM_FILE to avoid problems later when downloading badge files.
        $mform->setType('name', PARAM_FILE);
        $mform->addRule('name', null, 'required');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        $mform->addElement('textarea', 'description', get_string('description', 'badges'), 'wrap="virtual" rows="8" cols="70"');
        $mform->setType('description', PARAM_NOTAGS);
        $mform->addRule('description', null, 'required');

        $str = $action == 'new' ? get_string('badgeimage', 'badges') : get_string('newimage', 'badges');
        $imageoptions = array('maxbytes' => 262144, 'accepted_types' => array('web_image'));
        $mform->addElement('filepicker', 'image', $str, null, $imageoptions);

        if ($action == 'new') {
            $mform->addRule('image', null, 'required');
        } else {
            $currentimage = $mform->createElement('static', 'currentimage', get_string('currentimage', 'badges'));
            $mform->insertElementBefore($currentimage, 'image');
        }
        $mform->addHelpButton('image', 'badgeimage', 'badges');

        $mform->addElement('header', 'issuerdetails', get_string('issuerdetails', 'badges'));

        $mform->addElement('text', 'issuername', get_string('name'), array('size' => '70'));
        $mform->setType('issuername', PARAM_NOTAGS);
        $mform->addRule('issuername', null, 'required');
        if (isset($CFG->badges_defaultissuername)) {
            $mform->setDefault('issuername', $CFG->badges_defaultissuername);
        }
        $mform->addHelpButton('issuername', 'issuername', 'badges');

        $mform->addElement('text', 'issuercontact', get_string('contact', 'badges'), array('size' => '70'));
        if (isset($CFG->badges_defaultissuercontact)) {
            $mform->setDefault('issuercontact', $CFG->badges_defaultissuercontact);
        }
        $mform->setType('issuercontact', PARAM_RAW);
        $mform->addHelpButton('issuercontact', 'contact', 'badges');

        $mform->addElement('header', 'issuancedetails', get_string('issuancedetails', 'badges'));

        $issuancedetails = array();
        $issuancedetails[] =& $mform->createElement('radio', 'expiry', '', get_string('never', 'badges'), 0);
        $issuancedetails[] =& $mform->createElement('static', 'none_break', null, '<br/>');
        $issuancedetails[] =& $mform->createElement('radio', 'expiry', '', get_string('fixed', 'badges'), 1);
        $issuancedetails[] =& $mform->createElement('date_selector', 'expiredate', '');
        $issuancedetails[] =& $mform->createElement('static', 'expirydate_break', null, '<br/>');
        $issuancedetails[] =& $mform->createElement('radio', 'expiry', '', get_string('relative', 'badges'), 2);
        $issuancedetails[] =& $mform->createElement('duration', 'expireperiod', '', array('defaultunit' => 86400, 'optional' => false));
        $issuancedetails[] =& $mform->createElement('static', 'expiryperiods_break', null, get_string('after', 'badges'));

        $mform->addGroup($issuancedetails, 'expirydategr', get_string('expirydate', 'badges'), array(' '), false);
        $mform->addHelpButton('expirydategr', 'expirydate', 'badges');
        $mform->setDefault('expiry', 0);
        $mform->setDefault('expiredate', strtotime('+1 year'));
        $mform->disabledIf('expiredate[day]', 'expiry', 'neq', 1);
        $mform->disabledIf('expiredate[month]', 'expiry', 'neq', 1);
        $mform->disabledIf('expiredate[year]', 'expiry', 'neq', 1);
        $mform->disabledIf('expireperiod[number]', 'expiry', 'neq', 2);
        $mform->disabledIf('expireperiod[timeunit]', 'expiry', 'neq', 2);

        // Set issuer URL.
        // Have to parse URL because badge issuer origin cannot be a subfolder in wwwroot.
        $url = parse_url($CFG->wwwroot);
        $mform->addElement('hidden', 'issuerurl', $url['scheme'] . '://' . $url['host']);
        $mform->setType('issuerurl', PARAM_URL);

        $mform->addElement('hidden', 'action', $action);
        $mform->setType('action', PARAM_TEXT);

        if ($action == 'new') {
            $this->add_action_buttons(true, get_string('createbutton', 'badges'));
        } else {
            // Add hidden fields.
            $mform->addElement('hidden', 'id', $badge->id);
            $mform->setType('id', PARAM_INT);

            $this->add_action_buttons();
            $this->set_data($badge);

            // Freeze all elements if badge is active or locked.
            if ($badge->is_active() || $badge->is_locked()) {
                $mform->hardFreezeAllVisibleExcept(array());
            }
        }
    }

    /**
     * Load in existing data as form defaults
     *
     * @param stdClass|array $default_values object or array of default values
     */
    public function set_data($badge) {
        $default_values = array();
        parent::set_data($badge);

        if (!empty($badge->expiredate)) {
            $default_values['expiry'] = 1;
            $default_values['expiredate'] = $badge->expiredate;
        } else if (!empty($badge->expireperiod)) {
            $default_values['expiry'] = 2;
            $default_values['expireperiod'] = $badge->expireperiod;
        }
        $default_values['currentimage'] = print_badge_image($badge, $badge->get_context(), 'large');

        parent::set_data($default_values);
    }

    /**
     * Validates form data
     */
    public function validation($data, $files) {
        global $DB;
        $errors = parent::validation($data, $files);

        if (!empty($data['issuercontact']) && !validate_email($data['issuercontact'])) {
            $errors['issuercontact'] = get_string('invalidemail');
        }

        if ($data['expiry'] == 2 && $data['expireperiod'] <= 0) {
            $errors['expirydategr'] = get_string('error:invalidexpireperiod', 'badges');
        }

        if ($data['expiry'] == 1 && $data['expiredate'] <= time()) {
            $errors['expirydategr'] = get_string('error:invalidexpiredate', 'badges');
        }

        // Check for duplicate badge names.
        if ($data['action'] == 'new') {
            $duplicate = $DB->record_exists_select('badge', 'name = :name AND status != :deleted',
                array('name' => $data['name'], 'deleted' => BADGE_STATUS_ARCHIVED));
        } else {
            $duplicate = $DB->record_exists_select('badge', 'name = :name AND id != :badgeid AND status != :deleted',
                array('name' => $data['name'], 'badgeid' => $data['id'], 'deleted' => BADGE_STATUS_ARCHIVED));
        }

        if ($duplicate) {
            $errors['name'] = get_string('error:duplicatename', 'badges');
        }

        return $errors;
    }
}

/**
 * Form to edit badge message.
 *
 */
class edit_message_form extends moodleform {
    public function definition() {
        global $CFG, $OUTPUT;

        $mform = $this->_form;
        $badge = $this->_customdata['badge'];
        $action = $this->_customdata['action'];
        $editoroptions = $this->_customdata['editoroptions'];

        // Add hidden fields.
        $mform->addElement('hidden', 'id', $badge->id);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'action', $action);
        $mform->setType('action', PARAM_TEXT);

        $mform->addElement('header', 'badgemessage', get_string('configuremessage', 'badges'));
        $mform->addHelpButton('badgemessage', 'variablesubstitution', 'badges');

        $mform->addElement('text', 'messagesubject', get_string('subject', 'badges'), array('size' => '70'));
        $mform->setType('messagesubject', PARAM_TEXT);
        $mform->addRule('messagesubject', null, 'required');
        $mform->addRule('messagesubject', get_string('maximumchars', '', 255), 'maxlength', 255);

        $mform->addElement('editor', 'message_editor', get_string('message', 'badges'), null, $editoroptions);
        $mform->setType('message_editor', PARAM_RAW);
        $mform->addRule('message_editor', null, 'required');

        $mform->addElement('advcheckbox', 'attachment', get_string('attachment', 'badges'), '', null, array(0, 1));
        $mform->addHelpButton('attachment', 'attachment', 'badges');
        if (empty($CFG->allowattachments)) {
            $mform->freeze('attachment');
        }

        $options = array(
                BADGE_MESSAGE_NEVER   => get_string('never'),
                BADGE_MESSAGE_ALWAYS  => get_string('notifyevery', 'badges'),
                BADGE_MESSAGE_DAILY   => get_string('notifydaily', 'badges'),
                BADGE_MESSAGE_WEEKLY  => get_string('notifyweekly', 'badges'),
                BADGE_MESSAGE_MONTHLY => get_string('notifymonthly', 'badges'),
                );
        $mform->addElement('select', 'notification', get_string('notification', 'badges'), $options);
        $mform->addHelpButton('notification', 'notification', 'badges');

        $this->add_action_buttons();
        $this->set_data($badge);
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        return $errors;
    }
}
