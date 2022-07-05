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

class checkout_form extends moodleform {
    public function __construct($actionurl) {
        global $CFG;

        parent::__construct($actionurl);
    }

    public function definition() {
        global $CFG, $USER;

        $mform =& $this->_form;

        $mform->addElement('header', 'header', get_string('purchaser_details', 'block_iomad_commerce'));

        $mform->addElement('html', get_string('checkoutpreamble', 'block_iomad_commerce'));

        $strrequired = get_string('required');

        $mform->addElement('text', 'firstname', get_string('firstname'), 'maxlength="100" size="50"');
        $mform->addRule('firstname', $strrequired, 'required', null, 'client');
        $mform->setType('firstname', PARAM_NOTAGS);

        $mform->addElement('text', 'lastname', get_string('lastname'), 'maxlength="100" size="50"');
        $mform->addRule('lastname', $strrequired, 'required', null, 'client');
        $mform->setType('lastname', PARAM_NOTAGS);

        $mform->addElement('text', 'company', get_string('company', 'block_iomad_company_admin'), 'maxlength="40" size="50"');
        $mform->addRule('company', $strrequired, 'required', null, 'client');
        $mform->setType('company', PARAM_NOTAGS);

        $mform->addElement('text', 'address', get_string('address'), 'maxlength="70" size="50"');
        $mform->addRule('address', $strrequired, 'required', null, 'client');
        $mform->setType('address', PARAM_NOTAGS);

        $mform->addElement('text', 'city', get_string('city'), 'maxlength="120" size="50"');
        $mform->addRule('city', $strrequired, 'required', null, 'client');
        $mform->setType('city', PARAM_NOTAGS);

        $mform->addElement('text', 'postcode', get_string('postcode', 'block_iomad_commerce'), 'maxlength="20" size="20"');
        $mform->addRule('postcode', $strrequired, 'required', null, 'client');
        $mform->setType('postcode', PARAM_NOTAGS);

        $mform->addElement('text', 'state', get_string('state', 'block_iomad_commerce'), 'maxlength="20" size="20"');
        $mform->addRule('state', $strrequired, 'required', null, 'client');
        $mform->setType('state', PARAM_NOTAGS);

        $choices = get_string_manager()->get_list_of_countries();
        $choices = array('' => get_string('selectacountry').'...') + $choices;
        $mform->addElement('select', 'country', get_string('selectacountry'), $choices);
        $mform->addRule('country', $strrequired, 'required', null, 'client');

        $mform->addElement('text', 'email', get_string('email'), 'maxlength="100" size="50"');
        $mform->addRule('email', $strrequired, 'required', null, 'client');
        $mform->setType('email', PARAM_NOTAGS);

        $mform->addElement('text', 'phone1', get_string('phone'), 'maxlength="20" size="50"');
        $mform->setType('phone1', PARAM_NOTAGS);

        $mform->addElement('header', 'header', get_string('payment_options', 'block_iomad_commerce' ));

        $choices = array();
        foreach (get_enabled_payment_providers() as $p) {
            $choices[$p] = get_payment_provider_displayname($p);
        }

        if (!$choices) {
            $mform->addElement('html', '<div class="alert">' . get_string('noproviders', 'block_iomad_commerce') . '</div>');
        } else {
            $mform->addElement('select', 'paymentprovider', get_string('paymentprovider', 'block_iomad_commerce'), $choices);
            $mform->addRule('paymentprovider', $strrequired, 'required', null, 'client');
        }

        $mform->addElement('hidden', 'userid', $USER->id);
        $mform->setType('userid', PARAM_INT);

        $this->add_action_buttons(true, get_string('continue'));
    }

}

require_login(null, false); // Adds to $PAGE, creates $OUTPUT.
$context = context_system::instance();

// Correct the navbar.
// Set the name for the page.
$linktext = get_string('course_shop_title', 'block_iomad_commerce');
// Set the url.
$linkurl = new moodle_url('/blocks/iomad_commerce/checkout.php');

// Print the page header.
$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('base');
$PAGE->set_title($linktext);
$PAGE->set_heading(get_string('checkout', 'block_iomad_commerce'));

// Build the nav bar.
$PAGE->navbar->add($linktext, $linkurl);
$PAGE->navbar->add(get_string('checkout', 'block_iomad_commerce'));


$data = clone $USER;
if (!empty($USER->company->name)) {
    $data->company = $USER->company->name;
} else {
    $data->company = "";
}

$mform = new checkout_form($PAGE->url);
$mform->set_data($data);

$error = '';
$displaypage = 1;

$basketid = get_basket_id();

if ($mform->is_cancelled()) {
    redirect('basket.php');

} else if ($data = $mform->get_data()) {
    $displaypage = 0;

    $data->id = $basketid;

    $DB->update_record('invoice', $data, array('id' => $data->id));

    $pp = get_payment_provider_instance($data->paymentprovider);
    $error = $pp->init();
    if ($error) {
        $displaypage = 1;
    }
}

if ($displaypage && !$error) {
    require_once(dirname(__FILE__) . '/processor/processor.php');
    processor::trigger_oncheckout($basketid);
}

echo $OUTPUT->header();

echo $error;

$mform->display();

echo get_basket_html();

echo $OUTPUT->footer();
