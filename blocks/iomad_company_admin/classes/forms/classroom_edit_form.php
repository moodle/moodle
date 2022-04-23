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
 * @package   block_iomad_company_admin
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Script to let a user edit the properties of a particular email template.
 */

namespace block_iomad_company_admin\forms;

use \moodleform;
use \context_system;

class classroom_edit_form extends moodleform {
    protected $isadding;
    protected $subject = '';
    protected $body = '';
    protected $classroomid;
    protected $companyid;

    public function __construct($actionurl, $isadding, $companyid, $classroomid) {
        $this->isadding = $isadding;
        $this->classroomid = $classroomid;
        $this->companyid = $companyid;
        parent::__construct($actionurl);
    }

    public function definition() {
        global $CFG, $PAGE, $DB;
        $context = context_system::instance();

        $mform =& $this->_form;

        $strrequired = get_string('required');

        $mform->addElement('hidden', 'id', $this->classroomid);
        $mform->addElement('hidden', 'companyid', $this->companyid);
        $mform->setType('id', PARAM_INT);
        $mform->setType('companyid', PARAM_INT);

        // Then show the fields about where this block appears.
        $mform->addElement('header', 'header',
                            get_string('classroom', 'block_iomad_company_admin'));

        $mform->addElement('text', 'name',
                            get_string('classroom_name', 'block_iomad_company_admin'),
                            'maxlength="100" size="50"');
        $mform->setType('name', PARAM_NOTAGS);
        $mform->addRule('name', $strrequired, 'required', null, 'client');

        $mform->addElement('checkbox', 'isvirtual', get_string('virtual', 'block_iomad_company_admin'));
        $mform->addElement('text', 'address', get_string('address'), 'maxlength="70" size="50"');
        $mform->setType('address', PARAM_NOTAGS);

        $mform->addElement('text', 'city', get_string('city'), 'maxlength="120" size="50"');
        $mform->setType('city', PARAM_NOTAGS);

        $mform->addElement('text', 'postcode',
                            get_string('postcode', 'block_iomad_commerce'),
                            'maxlength="20" size="20"');
        $mform->setType('postcode', PARAM_NOTAGS);

        $choices = get_string_manager()->get_list_of_countries();
        $choices = array('' => get_string('selectacountry').'...') + $choices;
        $mform->addElement('select', 'country', get_string('selectacountry'), $choices);

        $mform->addElement('text', 'capacity',
                            get_string('classroom_capacity', 'block_iomad_company_admin'));
        $mform->setType('capacity', PARAM_INTEGER);
        $mform->hideIf('address', 'isvirtual', 'checked');
        $mform->hideIf('city', 'isvirtual', 'checked');
        $mform->hideIf('postcode', 'isvirtual', 'checked');
        $mform->hideIf('country', 'isvirtual', 'checked');
        $mform->hideIf('capacity', 'isvirtual', 'checked');

        $this->add_action_buttons();
    }

    public function validation($data, $files) {
        global $CFG, $DB;

        $errors = [];

        if (empty($data['isvirtual'])) {
            if (empty($data['address'])) {
                $errors['address'] = get_string('required');
            }

            if (empty($data['city'])) {
                $errors['city'] = get_string('required');
            }

            if (empty($data['postcode'])) {
                $errors['postcode'] = get_string('required');
            }

            if (empty($data['country'])) {
                $errors['country'] = get_string('required');
            }

            if (empty($data['capacity'])) {
                $errors['capacity'] = get_string('required');
            }
        }

        return $errors;
    }
}