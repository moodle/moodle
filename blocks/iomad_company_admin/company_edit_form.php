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
 * Script to let a user edit the properties of a particular company.
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir . '/formslib.php');
require_once('lib.php');

class company_edit_form extends company_moodleform {
    protected $firstcompany;
    protected $isadding;
    protected $title = '';
    protected $description = '';
    protected $companyid;
    protected $companyrecord;

    public function __construct($actionurl, $isadding, $companyid, $companyrecord, $firstcompany = false) {
        $this->isadding = $isadding;
        $this->companyid = $companyid;
        $this->companyrecord = $companyrecord;
        $this->firstcompany = $firstcompany;
        if (empty($this->companyrecord->theme)) {
            $this->companyrecord->theme = 'iomadbootstrap';
        }
        parent::__construct($actionurl);
    }

    public function definition() {
        global $CFG, $PAGE, $DB;
        $context = context_system::instance();

        $mform = & $this->_form;

        $strrequired = get_string('required');

        $mform->addElement('hidden', 'companyid', $this->companyid);
        $mform->setType('companyid', PARAM_INT);

        // Then show the fields about where this block appears.
        if ($this->isadding) {
            $mform->addElement('header', 'header', get_string('addnewcompany', 'block_iomad_company_admin'));
        } else {
            $mform->addElement('header', 'header', get_string('editcompany', 'block_iomad_company_admin'));
        }

        // If this is the first company then some extra help is displayed.
        if ($this->firstcompany) {
            $mform->addElement('html', '<div class="alert alert-info">' . get_string('firstcompany', 'block_iomad_company_admin') . '</div>');
        }

        $mform->addElement('text', 'name',
                            get_string('companyname', 'block_iomad_company_admin'),
                            array('size' => 50));
        $mform->setType('name', PARAM_NOTAGS);
        $mform->addRule('name', $strrequired, 'required');

        $mform->addElement('text', 'shortname',
                            get_string('companyshortname', 'block_iomad_company_admin'),
                            array('size' => 25));
        $mform->setType('shortname', PARAM_NOTAGS);
        $mform->addRule('shortname', $strrequired, 'required');

        $mform->addElement('text', 'city',
                            get_string('companycity', 'block_iomad_company_admin'),
                            array('size' => 50));
        $mform->setType('city', PARAM_NOTAGS);
        $mform->addRule('city', $strrequired, 'required');

        /* copied from user/editlib.php */
        $choices = get_string_manager()->get_list_of_countries();
        $choices = array('' => get_string('selectacountry').'...') + $choices;
        $mform->addElement('select', 'country', get_string('selectacountry'), $choices);
        $mform->addRule('country', $strrequired, 'required', null, 'client');
        if (!empty($CFG->country)) {
            $mform->setDefault('country', $CFG->country);
        }

        $mform->addElement('textarea', 'companydomains', get_string('companydomains', 'block_iomad_company_admin'), array('display' => 'noofficial'));
        $mform->setType('companydomains', PARAM_NOTAGS);


        /* === User defaults === */
        $mform->addElement('header', 'userdefaults',
                            get_string('userdefaults', 'block_iomad_company_admin'));

        $choices = array();
        $choices['0'] = get_string('emaildisplayno');
        $choices['1'] = get_string('emaildisplayyes');
        $choices['2'] = get_string('emaildisplaycourse');
        $mform->addElement('select', 'maildisplay', get_string('emaildisplay'), $choices);
        $mform->setDefault('maildisplay', 2);

        $choices = array();
        $choices['0'] = get_string('textformat');
        $choices['1'] = get_string('htmlformat');
        $mform->addElement('select', 'mailformat', get_string('emailformat'), $choices);
        $mform->setDefault('mailformat', 1);

        $choices = array();
        $choices['0'] = get_string('emaildigestoff');
        $choices['1'] = get_string('emaildigestcomplete');
        $choices['2'] = get_string('emaildigestsubjects');
        $mform->addElement('select', 'maildigest', get_string('emaildigest'), $choices);
        $mform->setDefault('maildigest', 0);

        $choices = array();
        $choices['1'] = get_string('autosubscribeyes');
        $choices['0'] = get_string('autosubscribeno');
        $mform->addElement('select', 'autosubscribe', get_string('autosubscribe'), $choices);
        $mform->setDefault('autosubscribe', 1);

        if (!empty($CFG->forum_trackreadposts)) {
            $choices = array();
            $choices['0'] = get_string('trackforumsno');
            $choices['1'] = get_string('trackforumsyes');
            $mform->addElement('select', 'trackforums', get_string('trackforums'), $choices);
            $mform->setDefault('trackforums', 0);
        }

        $editors = editors_get_enabled();
        if (count($editors) > 1) {
            $choices = array();
            $choices['0'] = get_string('texteditor');
            $choices['1'] = get_string('htmleditor');
            $mform->addElement('select', 'htmleditor', get_string('textediting'), $choices);
            $mform->setDefault('htmleditor', 1);
        } else {
            $mform->addElement('hidden', 'htmleditor');
            $mform->setDefault('htmleditor', 1);
            $mform->setType('htmleditor', PARAM_INT);
        }

        $choices = core_date::get_list_of_timezones();
        $choices['99'] = get_string('serverlocaltime');
        if ($CFG->forcetimezone != 99) {
            $mform->addElement('static', 'forcedtimezone',
                                get_string('timezone'), $choices[$CFG->forcetimezone]);
        } else {
            $mform->addElement('select', 'timezone', get_string('timezone'), $choices);
            $mform->setDefault('timezone', '99');
        }

        $mform->addElement('select', 'lang', get_string('preferredlanguage'),
                                             get_string_manager()->get_list_of_translations());
        $mform->setDefault('lang', $CFG->lang);

        /* === end user defaults === */
        $companyTheme = $this->companyrecord->theme;
        $ischild = false;
        try {
            $theme = theme_config::load($companyTheme);
            foreach ($theme->parents as $parentsTheme) {
                if($parentsTheme == 'iomad' || $parentsTheme == 'bootstrap' ){
                    $ischild = true;
                    break;
                }
            }
        } catch (Exception $e) {
            // Bad theme
        }
        // Only show the Appearence section if the theme is iomad or you have abilities
        // to change that.
        if (iomad::has_capability('block/iomad_company_admin:company_add', $context) ||
            $this->companyrecord->theme == 'iomad' || $this->companyrecord->theme == 'bootstrap' || $ischild) {

            $mform->addElement('header', 'appearance',
                                    get_string('appearance', 'block_iomad_company_admin'));

            // If has the edit all companies capability we want to add a theme selector.
            if (iomad::has_capability('block/iomad_company_admin:company_add', $context)) {

                // Get the list of themes.
                $themes = get_plugin_list('theme');
                $themeselectarray = array();
                foreach ($themes as $themename => $themedir) {

                    // Load the theme config.
                    try {
                        $theme = theme_config::load($themename);
                    } catch (Exception $e) {
                        // Bad theme, just skip it for now.
                        continue;
                    }
                    if ($themename !== $theme->name) {
                        // Obsoleted or broken theme, just skip for now.
                        continue;
                    }
                    if (!$CFG->themedesignermode && $theme->hidefromselector) {
                        // The theme doesn't want to be shown in the theme selector and as theme
                        // designer mode is switched off we will respect that decision.
                        continue;
                    }

                    // Build the theme selection list.
                    $themeselectarray[$themename] = $themename;
                }
                $mform->addElement('select', 'theme',
                                    get_string('selectatheme', 'block_iomad_company_admin'),
                                    $themeselectarray);
            } else {
                $mform->addElement('hidden', 'theme', $this->companyrecord->theme);
                $mform->setType('theme', PARAM_TEXT);
            }

            // If theme is already set to a real theme, dont show this.
            if (!isset($this->companyrecord->theme)) {
                $this->companyrecord->theme = 'iomadbootstrap';
            }
            if ($this->companyrecord->theme == 'iomad' || $this->companyrecord->theme == 'iomadbootstrap' || $ischild) {
                $mform->addElement('HTML', get_string('theoptionsbelow',
                                                      'block_iomad_company_admin'));
                $mform->addElement('filemanager', 'companylogo',
                                    get_string('companylogo', 'block_iomad_company_admin'), null,
                                    array('subdirs' => 0,
                                         'maxbytes' => 150 * 1024,
                                         'maxfiles' => 1,
                                         'accepted_types' => array('*.jpg', '*.gif', '*.png')));

                $mform->addElement('textarea', 'customcss',
                                    get_string('customcss', 'block_iomad_company_admin'),
                                    'wrap="virtual" rows="20" cols="75"');
                $mform->setType('customcss', PARAM_CLEAN);
            } else {
                $mform->addElement('hidden', 'id_companylogo', $this->companyrecord->companylogo);
                $mform->addElement('hidden', 'companylogo', $this->companyrecord->companylogo);
                $mform->setType('companylogo', PARAM_CLEAN);
                $mform->setType('id_companylogo', PARAM_CLEAN);
                $mform->addElement('hidden', 'customcss');
                $mform->setType('customcss', PARAM_CLEAN);
            }
        } else {
                $mform->addElement('hidden', 'theme', $this->companyrecord->theme);
                $mform->setType('theme', PARAM_TEXT);
                $mform->addElement('hidden', 'companylogo', $this->companyrecord->companylogo);
                $mform->setType('companylogo', PARAM_CLEAN);
                $mform->addElement('hidden', 'customcss');
                $mform->setType('customcss', PARAM_CLEAN);
        }

        $submitlabel = null; // Default.
        if ($this->isadding) {
            $submitlabel = get_string('saveasnewcompany', 'block_iomad_company_admin');
            $mform->addElement('hidden', 'createnew', 1);
            $mform->setType('createnew', PARAM_INT);
        }
        $this->add_action_buttons(true, $submitlabel);
    }

    public function get_data() {
        $data = parent::get_data();
        if ($data) {
            $data->title = '';
            $data->description = '';

            if ($this->title) {
                $data->title = $this->title;
            }

            if ($this->description) {
                $data->description = $this->description;
            }
        }
        return $data;
    }

    // Perform some extra moodle validation.
    public function validation($data, $files) {
        global $DB, $CFG;

        $errors = parent::validation($data, $files);

        if ($foundcompanies = $DB->get_records('company', array('name' => $data['name']))) {
            if (!empty($this->companyid)) {
                unset($foundcompanies[$this->companyid]);
            }
            if (!empty($foundcompanies)) {
                foreach ($foundcompanies as $foundcompany) {
                    $foundcompanynames[] = $foundcompany->name;
                }
                $foundcompanynamestring = implode(',', $foundcompanynames);
                $errors['name'] = get_string('companynametaken',
                                            'block_iomad_company_admin', $foundcompanynamestring);
            }
        }

        if ($foundcompanies = $DB->get_records('company', array('shortname' => $data['shortname']))) {
            if (!empty($this->companyid)) {
                unset($foundcompanies[$this->companyid]);
            }
            if (!empty($foundcompanies)) {
                foreach ($foundcompanies as $foundcompany) {
                    $foundcompanyshortnames[] = $foundcompany->shortname;
                }
                $foundcompanynamestring = implode(',', $foundcompanyshortnames);
                $errors['shortname'] = get_string('companyshortnametaken',
                                                 'block_iomad_company_admin',
                                                  $foundcompanynamestring);
            }
        }

        return $errors;
    }
}

$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);
$companyid = optional_param('companyid', 0, PARAM_INT);
$new = optional_param('createnew', 0, PARAM_INT);

$context = context_system::instance();
require_login();

$PAGE->set_context($context);
// Correct the navbar.
// Set the name for the page.
if (!$new) {
    $linktext = get_string('editcompany', 'block_iomad_company_admin');
} else {
    $linktext = get_string('addnewcompany', 'block_iomad_company_admin');
}
// Set the url.
$linkurl = new moodle_url('/blocks/iomad_company_admin/company_edit_form.php');
// Build the nav bar.
company_admin_fix_breadcrumb($PAGE, $linktext, $linkurl);

$blockpage = new blockpage($PAGE, $OUTPUT, 'iomad_company_admin', 'block',
                           ($new ? 'addnewcompany' : 'editacompany'));
$blockpage->setup();

if (!$new) {
    // Set the companyid
    $companyid = iomad::get_my_companyid($context);

    $isadding = false;
    $companyrecord = $DB->get_record('company', array('id' => $companyid), '*', MUST_EXIST);

    iomad::require_capability('block/iomad_company_admin:company_edit', $context);
} else {
    $isadding = true;
    $companyid = 0;
    $companyrecord = new stdClass;

    iomad::require_capability('block/iomad_company_admin:company_add', $context);
}

// Are there any existing companies?
$firstcompany = !$DB->record_exists('company', array());

$urlparams = array('companyid' => $companyid);
if ($returnurl) {
    $urlparams['returnurl'] = $returnurl;
}
$companylist = new moodle_url('/local/iomad_dashboard/index.php', $urlparams);

// Get the form data.
$draftitemid = file_get_submitted_draft_itemid('companylogo');
file_prepare_draft_area($draftitemid,
                        $context->id,
                        'theme_iomad',
                        'companylogo', $companyid,
                        array('subdirs' => 0, 'maxbytes' => 15 * 1024, 'maxfiles' => 1));
$companyrecord->companylogo = $draftitemid;
if ($domains = $DB->get_records('company_domains', array('companyid' => $companyid))) {
    $companyrecord->companydomains = '';
    foreach ($domains as $domain) {
        $companyrecord->companydomains .= $domain->domain ."\n";
    }
}

// Set up the form.
$mform = new company_edit_form($PAGE->url, $isadding, $companyid, $companyrecord, $firstcompany);
$mform->set_data($companyrecord);

if ($mform->is_cancelled()) {
    redirect($companylist);

} else if ($data = $mform->get_data()) {
    $data->userid = $USER->id;

    if ($isadding) {
        // Set up a profiles field category for this company.
        $catdata = new stdclass();
        $catdata->sortorder = $DB->count_records('user_info_category') + 1;
        $catdata->name = $data->shortname;
        $data->profileid = $DB->insert_record('user_info_category', $catdata, false);

        $companyid = $DB->insert_record('company', $data);

        // Set up default department.
        company::initialise_departments($companyid);
        $data->id = $companyid;

        // Set up course category for company.
        $coursecat = new stdclass();
        $coursecat->name = $data->name;
        $coursecat->sortorder = 999;
        $coursecat->id = $DB->insert_record('course_categories', $coursecat);
        $coursecat->context = context_coursecat::instance($coursecat->id);
        $categorycontext = $coursecat->context;
        $categorycontext->mark_dirty();
        $DB->update_record('course_categories', $coursecat);
        fix_course_sortorder();
        $companydetails = $DB->get_record('company', array('id' => $companyid));
        $companydetails->category = $coursecat->id;
        $DB->update_record('company', $companydetails);

    } else {
        $data->id = $companyid;

        $company = new company($companyid);
        $oldtheme = $company->get_theme();
        $themechanged = $oldtheme != $data->theme;
        $DB->update_record('company', $data);

        if ($themechanged) {
            $company->update_theme($data->theme);
        }

        //  Has the company name changed?
        $topdepartment = $company->get_company_parentnode($companyid);
        if ($topdepartment->name != $data->name) {
            $topdepartment->name = $data->name;
            $topdepartment->shorname = $data->shortname;
            $DB->update_record('department', $topdepartment);
        }

        if (company_user::is_company_user()) {
            company_user::reload_company();
        }
    }
    if (!empty($data->companylogo)) {
//echo "COMPANYLOGO HERE"; die;
        file_save_draft_area_files($data->companylogo,
                                   $context->id,
                                   'theme_iomad',
                                   'companylogo',
                                   $data->id,
                                   array('subdirs' => 0, 'maxbytes' => 150 * 1024, 'maxfiles' => 1));
    }
    if (!empty($data->companydomains)) {
        $domainsarray = preg_split('/[\r\n]+/', $data->companydomains, -1, PREG_SPLIT_NO_EMPTY);
        // Delete any recorded domains for this company.
        $DB->delete_records('company_domains', array('companyid' => $companyid));
        foreach ($domainsarray as $domain) {
            if (!empty($domain)) {
                $DB->insert_record('company_domains', array('companyid' => $companyid, 'domain' => $domain));
            }
        }
    }
    redirect($companylist);
}

$blockpage->display_header();

$mform->display();

echo $OUTPUT->footer();
