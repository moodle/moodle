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

namespace block_iomad_company_admin\forms;

defined('MOODLE_INTERNAL') || die;

/**
 * User Filter form used on the Iomad pages.
 *
 */
class iomad_company_filter_form extends \moodleform {
    protected $companyid;

    public function definition() {
        global $CFG, $DB, $USER, $SESSION;

        $mform =& $this->_form;
        $filtergroup = array();
        $mform->addElement('header', '', format_string(get_string('companysearchfields', 'local_iomad')));
        $mform->addElement('text', 'name', get_string('name'), 'size="20"');
        $mform->addElement('text', 'city', get_string('companycity', 'block_iomad_company_admin'), 'size="20"');
        $mform->addElement('text', 'country', get_string('selectacountry'), 'size="20"');
        $mform->addElement('checkbox', 'showsuspended', get_string('show_suspended_companies', 'local_iomad'));
        $mform->setType('showsuspended', PARAM_INT);

        // Additional fields.
        $mform->addElement('header', 'advanced', get_string('companyadvancedsearchfields', 'block_iomad_company_admin'));
        $mform->addElement('text', 'code', get_string('companycode', 'block_iomad_company_admin'), 'size="20"');
        $mform->addElement('text', 'address', get_string('address'), 'size="20"');
        $mform->addElement('text', 'region', get_string('companyregion', 'block_iomad_company_admin'), 'size="20"');
        $mform->addElement('text', 'postcode', get_string('postcode', 'block_iomad_company_admin'), 'size="20"');
        $mform->addElement('text', 'custom1', get_string('custom1', 'block_iomad_company_admin'), 'size="20"');
        $mform->addElement('text', 'custom2', get_string('custom2', 'block_iomad_company_admin'), 'size="20"');
        $mform->addElement('text', 'custom3', get_string('custom3', 'block_iomad_company_admin'), 'size="20"');
        $mform->setType('name', PARAM_CLEAN);
        $mform->setType('code', PARAM_CLEAN);
        $mform->setType('address', PARAM_CLEAN);
        $mform->setType('city', PARAM_CLEAN);
        $mform->setType('region', PARAM_CLEAN);
        $mform->setType('postcode', PARAM_CLEAN);
        $mform->setType('country', PARAM_CLEAN);
        $mform->setType('custom1', PARAM_CLEAN);
        $mform->setType('custom2', PARAM_CLEAN);
        $mform->setType('custom3', PARAM_CLEAN);

        $mform->addElement('checkbox', 'showchild', get_string('showhierarchy', 'block_iomad_company_admin'));
        $mform->setType('showchild', PARAM_INT);
        $mform->setDefault('showchild', 1);
        $mform->setExpanded('advanced', false);
        $mform->closeHeaderBefore('buttonar');

        // Action buttons.
        $buttonarray = array();
        $buttonarray[] = $mform->createElement('submit', 'submitbutton', get_string('companyfilter', 'local_iomad'));
        $buttonarray[] = $mform->createElement('submit', 'resetbutton', get_string('reset'), null, false);
        $mform->addGroup($buttonarray, 'buttonar', '', ' ', false);
    }
}

