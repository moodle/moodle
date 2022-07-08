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
 * @package   local_email
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Script to let a user edit the properties of a particular email template.
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot . '/local/iomad/lib/company.php');
require_once($CFG->dirroot . '/blocks/iomad_company_admin/lib.php');
require_once($CFG->libdir . '/formslib.php');
require_once('lib.php');
require_once('config.php');

class template_apply_form extends moodleform {
    protected $isadding;
    protected $subject = '';
    protected $body = '';
    protected $templateid;
    protected $templaterecord;
    protected $companyid;
    protected $editing;

    public function __construct($actionurl, $templatesetid, $companies) {
        $this->templatesetid = $templatesetid;
        $this->companies = $companies;

        parent::__construct($actionurl);
    }

    public function definition() {
        global $CFG, $PAGE, $DB;
        $context = context_system::instance();

        $mform =& $this->_form;

        $strrequired = get_string('required');

        $mform->addElement('header', '', get_string('selectacompany', 'block_iomad_company_admin'));
        $mform->addElement('autocomplete', 'companies', '', $this->companies, array('multiple' => true));
        $mform->addElement('hidden', 'templatesetid', $this->templatesetid);
        $mform->setType('templatesetid', PARAM_INT);

        $this->add_action_buttons(true);

    }
}

$templatesetid = required_param('templatesetid', PARAM_INTEGER);
$context = context_system::instance();
require_login();
$templatesetinfo = $DB->get_record('email_templateset', array('id' => $templatesetid));

// Correct the navbar.
// Set the name for the page.
$linktext = get_string('applytemplateset', 'local_email', $templatesetinfo ->templatesetname);

// Set the url.
$linkurl = new moodle_url('/local/email/template_apply_form.php');

// Print the page header.
$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('base');
$PAGE->set_title($linktext);

// Set the page heading.
$PAGE->set_heading($linktext);

// Only display if you have the correct capability, or you are not in more than one company.
// Just display name of current company if no choice.
if (!iomad::has_capability('block/iomad_company_admin:company_view_all',$context)) {
    $companies = $DB->get_records_sql_menu("SELECT c.id, c.name
                                            FROM {company} c
                                            JOIN {company_user} cu
                                            ON (c.id = cu.companyid)
                                            WHERE c.suspended = 0
                                            AND cu.userid = :userid
                                            ORDER BY name",
                                            array('userid' => $USER->id));
} else {
    $companies = $DB->get_records_menu('company', array('suspended' => 0), 'name', 'id,name');
}

$menucompanies = array('-1' => get_string('all')) + $companies;
// Set up the form.
$mform = new template_apply_form($PAGE->url, $templatesetid, $menucompanies);
$templatelist = new moodle_url('/local/email/template_list.php', array('manage' => 1));

if ($mform->is_cancelled()) {
    redirect($templatelist);

} else if ($data = $mform->get_data()) {
    if (!in_array('-1', $data->companies)) {
        $selectedcompanies = $data->companies;
    } else {
        $selectedcompanies = array_keys($companies);
    }

    $table = new html_table();
    $table->head = array(get_string('company', 'block_iomad_company_admin'),
                         get_string('result', 'cache'));
    foreach ($selectedcompanies as $companyid) {
        $company = new company($companyid);
        if ($company->apply_email_templates($templatesetid)) {
            $result = get_string('success');
        } else {
            $result = get_string('error');
        }
        $table->data[] = array ($company->get_name(), $result);
    }
    echo $OUTPUT->header();
    echo "<h2>" . get_string('result', 'cache') . "</h2>";
    echo html_writer::table($table);
    echo '<a class="btn btn-primary" href="'.$templatelist.'">' .
                                           get_string('back') . '</a>';
    echo $OUTPUT->footer();
    die;
}

echo $OUTPUT->header();

$mform->display();

echo $OUTPUT->footer();
