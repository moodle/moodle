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

namespace core_badges\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/badgeslib.php');
require_once($CFG->libdir . '/filelib.php');

use moodleform;

/**
 * Form classes for editing badges
 *
 * @package    core_badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */
class badge extends moodleform {

    /**
     * Defines the form
     */
    public function definition() {
        global $CFG, $SITE;

        $mform = $this->_form;
        $badge = (isset($this->_customdata['badge'])) ? $this->_customdata['badge'] : false;
        $action = $this->_customdata['action'];
        if (array_key_exists('courseid', $this->_customdata)) {
            $courseid = $this->_customdata['courseid'];
        } else if (array_key_exists('badge', $this->_customdata)) {
            $courseid = $this->_customdata['badge']->courseid;
        }
        if (!empty($courseid)) {
            $mform->addElement('hidden', 'courseid', $courseid);
            $mform->setType('courseid', PARAM_INT);
        }

        $mform->addElement('header', 'badgedetails', get_string('badgedetails', 'badges'));
        $mform->addElement('text', 'name', get_string('name'), ['size' => '70']);
        // When downloading badge, it will be necessary to clean the name as PARAM_FILE.
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        $mform->addElement('text', 'version', get_string('version', 'badges'), ['size' => '70']);
        $mform->setType('version', PARAM_TEXT);

        $languages = get_string_manager()->get_list_of_languages();
        $mform->addElement('select', 'language', get_string('language'), $languages);

        $mform->addElement(
            'textarea',
            'description',
            get_string('description', 'badges'),
            'wrap="virtual" rows="8" cols="70" placeholder="' . s(get_string('descriptioninfo', 'badges')) . '"',
        );
        $mform->setType('description', PARAM_NOTAGS);
        $mform->addRule('description', null, 'required');

        $str = $action == 'new' ? get_string('badgeimage', 'badges') : get_string('newimage', 'badges');
        $imageoptions = ['maxbytes' => 262144, 'accepted_types' => ['optimised_image']];
        $mform->addElement('filepicker', 'image', $str, null, $imageoptions);

        if ($action == 'new') {
            $mform->addRule('image', null, 'required');
        } else {
            $currentimage = $mform->createElement('static', 'currentimage', get_string('currentimage', 'badges'));
            $mform->insertElementBefore($currentimage, 'image');
        }
        $mform->addElement('static', 'imageinfo', null, get_string('badgeimageinfo', 'badges'));

        $mform->addElement('text', 'imagecaption', get_string('imagecaption', 'badges'), ['size' => '70']);
        $mform->setType('imagecaption', PARAM_TEXT);

        $mform->addElement('tags', 'tags', get_string('tags', 'badges'), ['itemtype' => 'badge', 'component' => 'core_badges']);

        $mform->addElement('header', 'issuerdetails', get_string('issuerdetails', 'badges'));

        $mform->addElement('text', 'issuername', get_string('issuername', 'badges'), ['size' => '70']);
        $mform->setType('issuername', PARAM_NOTAGS);
        $mform->addRule('issuername', null, 'required');
        $site = get_site();
        $issuername = $CFG->badges_defaultissuername ?: $site->fullname;
        $mform->setDefault('issuername', $issuername);

        $mform->addElement('text', 'issuercontact', get_string('contact', 'badges'), ['size' => '70']);
        if (isset($CFG->badges_defaultissuercontact)) {
            $mform->setDefault('issuercontact', $CFG->badges_defaultissuercontact);
        }
        $mform->setType('issuercontact', PARAM_RAW);
        $mform->addRule('issuercontact', null, 'email');

        // Set issuer URL.
        // Have to parse URL because badge issuer origin cannot be a subfolder in wwwroot.
        $url = parse_url($CFG->wwwroot);
        $mform->addElement('hidden', 'issuerurl', $url['scheme'] . '://' . $url['host']);
        $mform->setType('issuerurl', PARAM_URL);

        $mform->addElement('header', 'issuancedetails', get_string('issuancedetails', 'badges'));

        $issuancedetails = [];
        $issuancedetails[] = $mform->createElement('radio', 'expiry', '', get_string('never', 'badges'), 0);
        $issuancedetails[] = $mform->createElement('static', 'none_break', null, '<br/>');
        $issuancedetails[] = $mform->createElement('radio', 'expiry', '', get_string('fixed', 'badges'), 1);
        $issuancedetails[] = $mform->createElement('date_selector', 'expiredate', '');
        $issuancedetails[] = $mform->createElement('static', 'expirydate_break', null, '<br/>');
        $issuancedetails[] = $mform->createElement('radio', 'expiry', '', get_string('relative', 'badges'), 2);
        $issuancedetails[] = $mform->createElement('duration', 'expireperiod', '', ['defaultunit' => 86400, 'optional' => false]);
        $issuancedetails[] = $mform->createElement('static', 'expiryperiods_break', null, get_string('after', 'badges'));

        $mform->addGroup($issuancedetails, 'expirydategr', get_string('expirydate', 'badges'), [' '], false);
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
                $mform->hardFreezeAllVisibleExcept([]);
            }
        }
    }

    /**
     * Load in existing data as form defaults
     *
     * @param \core_badges\badge $badge object or array of default values
     */
    public function set_data($badge) {
        $defaultvalues = [];
        parent::set_data((object) $badge);

        if (!empty($badge->expiredate)) {
            $defaultvalues['expiry'] = 1;
            $defaultvalues['expiredate'] = $badge->expiredate;
        } else if (!empty($badge->expireperiod)) {
            $defaultvalues['expiry'] = 2;
            $defaultvalues['expireperiod'] = $badge->expireperiod;
        }

        if (!empty($badge->name)) {
            $defaultvalues['name'] = trim($badge->name);
        }

        $defaultvalues['tags'] = \core_tag_tag::get_item_tags_array('core_badges', 'badge', $badge->id);
        $defaultvalues['currentimage'] = print_badge_image($badge, $badge->get_context(), 'large');

        parent::set_data($defaultvalues);
    }

    /**
     * Validates form data
     */
    public function validation($data, $files) {
        global $DB, $SITE;

        // Trim badge name (to guarantee no badges are created with the same name but some extra spaces).
        $data['name'] = trim($data['name']);

        $errors = parent::validation($data, $files);

        if ($data['expiry'] == 2 && $data['expireperiod'] <= 0) {
            $errors['expirydategr'] = get_string('error:invalidexpireperiod', 'badges');
        }

        if ($data['expiry'] == 1 && $data['expiredate'] <= time()) {
            $errors['expirydategr'] = get_string('error:invalidexpiredate', 'badges');
        }

        return $errors;
    }
}
