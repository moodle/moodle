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
 * @package   block_iomad_commerce
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Script to let a user create a course for a particular company.
 */

namespace block_iomad_commerce\forms;

use \moodleform;
use \context_system;
use \block_iomad_commerce\helper;

class order_edit_form extends moodleform {
    protected $invoiceid = 0;
    protected $showaccount = false;
    protected $context = null;

    public function __construct($actionurl, $invoiceid, $showaccount = false) {
        global $CFG;

        $this->invoiceid = $invoiceid;
        $this->context = context_system::instance();
        $this->showaccount = $showaccount;

        parent::__construct($actionurl);
    }

    public function definition() {
        global $CFG;

        $mform =& $this->_form;

        $strrequired = get_string('required');

        $mform->addElement('hidden', 'id', $this->invoiceid);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('header', 'header', get_string('order', 'block_iomad_commerce'));

        $mform->addElement('static', 'reference', get_string('reference', 'block_iomad_commerce'));

        $choices = [];
        foreach ([\block_iomad_commerce\helper::INVOICESTATUS_UNPAID, \block_iomad_commerce\helper::INVOICESTATUS_PAID] as $status) {
            $choices[$status] = get_string('status_' . $status, 'block_iomad_commerce');
        }
        $mform->addElement('select', 'status', get_string('status'), $choices);
        $mform->addRule('status', $strrequired, 'required', null, 'client');
        $mform->disabledIf('status', 'id', 'ne', 0);

        $mform->addElement('header', 'header', get_string('purchaser_details', 'block_iomad_commerce'));

        $mform->addElement('static', 'firstname', get_string('firstname'));

        $mform->addElement('static', 'lastname', get_string('lastname'));
        $mform->addElement('static', 'company', get_string('company', 'block_iomad_company_admin'));
        $mform->addElement('static', 'address', get_string('address'));
        $mform->addElement('static', 'city', get_string('city'));
        $mform->addElement('static', 'postcode', get_string('postcode', 'block_iomad_commerce'));
        $mform->addElement('static', 'state', get_string('state', 'block_iomad_commerce'));
        $mform->addElement('static', 'country', get_string('selectacountry'));
        $mform->addElement('static', 'email', get_string('email'));
        $mform->addElement('static', 'phone1', get_string('phone'));

        $mform->addElement('header', 'header', get_string('basket', 'block_iomad_commerce'));

        $mform->addElement('html', '<p>' . get_string('process_help', 'block_iomad_commerce') . '</p>');
        $mform->addElement('html', \block_iomad_commerce\helper::get_invoice_html($this->invoiceid, 0, 0, 0));

        $mform->addElement('header', 'header', get_string('paymentprocessing', 'block_iomad_commerce'));

        $mform->addElement('static', 'checkout_method', get_string('paymentprovider', 'block_iomad_commerce'));

        if ($this->showaccount) {
            $mform->addElement('static', 'pp_account', get_string('paymentaccount', 'payment'));
        }

        $this->add_action_buttons(false, get_string('back'));
    }
}