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

namespace block_iomad_commerce\forms;

use \moodleform;
use \context_system;

class confirmation_form extends moodleform {
    protected $basket = null;
    protected $paymentprovider = null;

    function __construct($actionurl, $basket, $paymentprovider) {
        global $CFG;

        $this->basket = $basket;
        $this->paymentprovider = $paymentprovider;
        parent::__construct($actionurl);
    }

    function definition() {
        global $CFG;

        $mform =& $this->_form;

        $mform->addElement('html', $this->paymentprovider->get_order_review_html());
        $mform->addElement('static', 'firstname', get_string('firstname'));
        $mform->addElement('static', 'lastname',  get_string('lastname'));
        $mform->addElement('static', 'company', get_string('company', 'block_iomad_company_admin'));
        $mform->addElement('static', 'address', get_string('address'));
        $mform->addElement('static', 'city', get_string('city'));
        $mform->addElement('static', 'state', get_string('state'));
        $mform->addElement('static', 'postcode', get_string('postcode', 'block_iomad_commerce'));
        $mform->addElement('static', 'country', get_string('country'));
        $mform->addElement('static', 'email', get_string('email'));
        $mform->addElement('static', 'phone1', get_string('phone'));

        $mform->addElement('html', get_basket_html());

        $this->add_action_buttons(true, get_string('confirm'));
    }
}