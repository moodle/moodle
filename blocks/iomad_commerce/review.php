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

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir . '/formslib.php');
require_once(dirname(__FILE__) . '/../iomad_company_admin/lib.php');
require_once('lib.php');

require_commerce_enabled();

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

$context = context_system::instance();
require_login();

// Correct the navbar.
// Set the name for the page.
$linktext = get_string('course_shop_title', 'block_iomad_commerce');
// Set the url.
$linkurl = new moodle_url('/blocks/iomad_commerce/review.php');

// Print the page header.
$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('base');
$PAGE->set_title($linktext);
$PAGE->set_heading(get_string('review', 'block_iomad_commerce'));

// Build the nav bar.
$PAGE->navbar->add($linktext, $linkurl);
$PAGE->navbar->add(get_string('review', 'block_iomad_commerce'));

// Don't do the pre_order_review_processing on postback.
if (array_key_exists('submitbutton', $_POST)) {
    $basket = get_basket();
    $pp = get_payment_provider_instance($basket->checkout_method);
} else {
    // Add the rest of the stuff to the basket invoice.
    $basket = get_basket();
    $pp = get_payment_provider_instance($basket->checkout_method);
    $pp->pre_order_review_processing();
    // Refresh basket info after processing.
    $basket = get_basket();
}

$mform = new confirmation_form($PAGE->url, $basket, $pp);
$mform->set_data($basket);

$error = '';

if ($mform->is_cancelled()) {
    redirect('basket.php');

} else if ($data = $mform->get_data()) {

    $error = $pp->confirm();
    if (!$error) {
        redirect('confirm.php?u=' . $basket->reference);
    }
}

echo $OUTPUT->header();

echo $error;

$mform->display();

echo $OUTPUT->footer();
