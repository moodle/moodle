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

namespace core_badges\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/badgeslib.php');
require_once($CFG->libdir . '/filelib.php');

use moodleform;

/**
 * Form to edit badge details.
 *
 */
class badge extends moodleform {

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
        // When downloading badge, it will be necessary to clean the name as PARAM_FILE.
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        $mform->addElement('text', 'version', get_string('version', 'badges'), array('size' => '70'));
        $mform->setType('version', PARAM_TEXT);
        $mform->addHelpButton('version', 'version', 'badges');

        $languages = get_string_manager()->get_list_of_languages();
        $mform->addElement('select', 'language', get_string('language'), $languages);
        $mform->addHelpButton('language', 'language', 'badges');

        $mform->addElement('textarea', 'description', get_string('description', 'badges'), 'wrap="virtual" rows="8" cols="70"');
        $mform->setType('description', PARAM_NOTAGS);
        $mform->addRule('description', null, 'required');

        $str = $action == 'new' ? get_string('badgeimage', 'badges') : get_string('newimage', 'badges');
        $imageoptions = array('maxbytes' => 262144, 'accepted_types' => array('optimised_image'));
        $mform->addElement('filepicker', 'image', $str, null, $imageoptions);

        if ($action == 'new') {
            $mform->addRule('image', null, 'required');
        } else {
            $currentimage = $mform->createElement('static', 'currentimage', get_string('currentimage', 'badges'));
            $mform->insertElementBefore($currentimage, 'image');
        }
        $mform->addHelpButton('image', 'badgeimage', 'badges');
        $mform->addElement('text', 'imageauthorname', get_string('imageauthorname', 'badges'), array('size' => '70'));
        $mform->setType('imageauthorname', PARAM_TEXT);
        $mform->addHelpButton('imageauthorname', 'imageauthorname', 'badges');
        $mform->addElement('text', 'imageauthoremail', get_string('imageauthoremail', 'badges'), array('size' => '70'));
        $mform->setType('imageauthoremail', PARAM_TEXT);
        $mform->addHelpButton('imageauthoremail', 'imageauthoremail', 'badges');
        $mform->addElement('text', 'imageauthorurl', get_string('imageauthorurl', 'badges'), array('size' => '70'));
        $mform->setType('imageauthorurl', PARAM_URL);
        $mform->addHelpButton('imageauthorurl', 'imageauthorurl', 'badges');
        $mform->addElement('text', 'imagecaption', get_string('imagecaption', 'badges'), array('size' => '70'));
        $mform->setType('imagecaption', PARAM_TEXT);
        $mform->addHelpButton('imagecaption', 'imagecaption', 'badges');

        $mform->addElement('header', 'issuerdetails', get_string('issuerdetails', 'badges'));

        if (badges_open_badges_backpack_api() != OPEN_BADGES_V2) {
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
            // Set issuer URL.
            // Have to parse URL because badge issuer origin cannot be a subfolder in wwwroot.
            $url = parse_url($CFG->wwwroot);
            $mform->addElement('hidden', 'issuerurl', $url['scheme'] . '://' . $url['host']);
            $mform->setType('issuerurl', PARAM_URL);

        } else {
            $name = $CFG->badges_defaultissuername;
            $mform->addElement('static', 'issuernamelabel', get_string('name'), $name);
            $mform->addElement('hidden', 'issuername', $name);
            $mform->setType('issuername', PARAM_NOTAGS);

            $contact = $CFG->badges_defaultissuercontact;
            $mform->addElement('static', 'issuercontactlabel', get_string('contact', 'badges'), $contact);
            $mform->addElement('hidden', 'issuercontact', $contact);
            $mform->setType('issuercontact', PARAM_RAW);

            $url = parse_url($CFG->wwwroot);
            $mform->addElement('hidden', 'issuerurl', $url['scheme'] . '://' . $url['host']);
            $mform->setType('issuerurl', PARAM_URL);
        }

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

        $mform->addElement('hidden', 'action', $action);
        $mform->setType('action', PARAM_TEXT);

        if ($action == 'new') {
            // Try to set default badge language to that of current language, or it's parent.
            $language = current_language();
            if (isset($languages[$language])) {
                $defaultlanguage = $language;
            } else {
                // Calling get_parent_language returns an empty string instead of 'en'.
                $defaultlanguage = get_parent_language($language) ?: 'en';
            }

            $mform->setDefault('language', $defaultlanguage);
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

        if (badges_open_badges_backpack_api() != OPEN_BADGES_V2) {
            if (!empty($data['issuercontact']) && !validate_email($data['issuercontact'])) {
                $errors['issuercontact'] = get_string('invalidemail');
            }
        }

        if ($data['expiry'] == 2 && $data['expireperiod'] <= 0) {
            $errors['expirydategr'] = get_string('error:invalidexpireperiod', 'badges');
        }

        if ($data['expiry'] == 1 && $data['expiredate'] <= time()) {
            $errors['expirydategr'] = get_string('error:invalidexpiredate', 'badges');
        }

        if ($data['imageauthoremail'] && !validate_email($data['imageauthoremail'])) {
            $errors['imageauthoremail'] = get_string('invalidemail');
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

        if ($data['imageauthorurl'] && !preg_match('@^https?://.+@', $data['imageauthorurl'])) {
            $errors['imageauthorurl'] = get_string('invalidurl', 'badges');
        }

        return $errors;
    }
}

