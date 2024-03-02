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
 * Script to let a user edit the properties of a particular company.
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir . '/formslib.php');
require_once(dirname(__FILE__) . '/includes/colourpicker.php');
require_once('lib.php');

\MoodleQuickForm::registerElementType('iomad_colourpicker',
    $CFG->dirroot . '/blocks/iomad_company_admin/includes/colourpicker.php', 'MoodleQuickForm_iomad_colourpicker');


$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);
$companyid = optional_param('companyid', 0, PARAM_INT);
$parentid = optional_param('parentid', 0, PARAM_INT);
$new = optional_param('createnew', 0, PARAM_INT);

$systemcontext = context_system::instance();
require_login();

// Correct the navbar.
// Set the name for the page.
if (!$new) {
    $linktext = get_string('editcompany', 'block_iomad_company_admin');
} else {
    if (!empty($parentid)) {
        $linktext = get_string('createchildcompany', 'block_iomad_company_admin');
    } else {
        $linktext = get_string('addnewcompany', 'block_iomad_company_admin');
    }
}

// What type of company is this?
$child = false;
if (!$new) {
    // Set the companyid
    $companyid = iomad::get_my_companyid($systemcontext);
    $companycontext =  \core\context\company::instance($companyid);

    iomad::require_capability('block/iomad_company_admin:company_edit', $companycontext);

    $isadding = false;
    $companyrecord = $DB->get_record('company', array('id' => $companyid), '*', MUST_EXIST);
    if ($companyrecord->previousroletemplateid == -1 ) {
        $companyrecord->previousroletemplateid = 'i';
    }
    $companyrecord->templates = array();
    if ($companytemplates = $DB->get_records('company_role_templates_ass', array('companyid' => $companyid), null, 'templateid')) {
        $companyrecord->templates = array_keys($companytemplates);
    }
} else {
    $isadding = true;
    $companyid = 0;
    $companyrecord = new stdClass;
    $companyrecord->templates = null;
    $companyrecord->previousroletemplateid = 0;
    $companyrecord->previousemailtemplateid = 0;
    $companyrecord->maxusers = 0;
    $companycontext = $systemcontext;
    if ($emailtemplateset = $DB->get_record('email_templateset', ['isdefault' => 1])) {
        $companyrecord->emailtemplate = $emailtemplateset->id;
    }

    if (!empty($parentid)) {
        $companycontext = \core\context\company::instance($parentid);
        iomad::require_capability('block/iomad_company_admin:company_add_child', $companycontext);

        // We are adding a child company.
        $child = true;
        // Can this user manage this parentid?
        if (!iomad::has_capability('block/iomad_company_admin:company_add', $companycontext) &&
            !$DB->get_record('company_users', array('companyid' => $parentid, 'userid' => $USER->id, 'managertype' => 1))) {
            throw new moodle_exception(get_string('invalidcompany', 'block_iomad_company_admin'), 'error', new moodle_url($CFG->wwwroot .'/blocks/iomad_company_admin/index.php'));
            die;
        }

        // Deal with any already set form values from redirect/$SESSION.
        if (!empty($SESSION->current_editing_company_data)) {
            foreach ($SESSION->current_editing_company_data as $index => $value) {
                // Strip out certificate and CSS parts.
                if (in_array($index, ['bgcolor_content',
                                      'bgcolor_header',
                                      'companycertificateborder',
                                      'companycertificateseal',
                                      'companycertificatesignatue',
                                      'companycertificatewatermark',
                                      'compayfavicon',
                                      'companylogo',
                                      'companylogocompact',
                                      'currentparentid',
                                      'customcss',
                                      'headingcolor',
                                      'linkcolor',
                                      'showgrade',
                                      'maincolor',
                                      'useborder',
                                      'uselogo',
                                      'usesignature',
                                      'usewatermark'])) {
                    continue;
                } else {
                    $companyrecord->$index = $value;
                }
            }
            unset($SESSION->current_editing_company_data);
        }
    } else {
        iomad::require_capability('block/iomad_company_admin:company_add', $companycontext);
    }
}

// Set the url.
$linkurl = new moodle_url('/blocks/iomad_company_admin/company_edit_form.php', [
    'returnurl' => $returnurl,
    'companyid' => $companyid,
    'parentid' => $parentid,
    'createnew' => $new,
]);

$PAGE->set_context($companycontext);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('base');
$PAGE->set_title($linktext);

// Set the page heading.
$PAGE->set_heading($linktext);

// Are there any existing companies?
$firstcompany = !$DB->record_exists('company', array());

$urlparams = array('companyid' => $companyid);
if ($returnurl) {
    $urlparams['returnurl'] = $returnurl;
}
$companylist = new moodle_url('/blocks/iomad_company_admin/index.php', $urlparams);

// Get the company logos etc.
$draftcompanylogoid = file_get_submitted_draft_itemid('companylogo');
file_prepare_draft_area($draftcompanylogoid,
                        $systemcontext->id,
                        'core_admin',
                        'logo' . $companyid, 0,
                        array('subdirs' => 0, 'maxbytes' => 15 * 1024, 'maxfiles' => 1));
$companyrecord->companylogo = $draftcompanylogoid;

$draftcompanylogocompactid = file_get_submitted_draft_itemid('companylogocompact');
file_prepare_draft_area($draftcompanylogocompactid,
                        $systemcontext->id,
                        'core_admin',
                        'logocompact' . $companyid, 0,
                        ['maxfiles' => 1]);
$companyrecord->companylogocompact = $draftcompanylogocompactid;

$draftcompanyfaviconid = file_get_submitted_draft_itemid('companyfavicon');
file_prepare_draft_area($draftcompanyfaviconid,
                        $systemcontext->id,
                        'core_admin',
                        'favicon' . $companyid, 0,
                        ['maxfiles' => 1]);
$companyrecord->companyfavicon = $draftcompanyfaviconid;

// Are we creating a child company?
if (!empty($new) && !empty($parentid)) {
    // Get the parent certificate files as default.
    $draftcompanycertificatesealid = file_get_submitted_draft_itemid('companycertificateseal');
    file_prepare_draft_area($draftcompanycertificatesealid,
                            $systemcontext->id,
                            'local_iomad',
                            'companycertificateseal', $parentid,
                            array('subdirs' => 0, 'maxbytes' => 15 * 1024, 'maxfiles' => 1));
    $companyrecord->companycertificateseal = $draftcompanycertificatesealid;
    $draftcompanycertificatesignatureid = file_get_submitted_draft_itemid('companycertificatesignature');
    file_prepare_draft_area($draftcompanycertificatesignatureid,
                            $systemcontext->id,
                            'local_iomad',
                            'companycertificatesignature', $parentid,
                            array('subdirs' => 0, 'maxbytes' => 15 * 1024, 'maxfiles' => 1));
    $companyrecord->companycertificatesignature = $draftcompanycertificatesignatureid;
    $draftcompanycertificateborderid = file_get_submitted_draft_itemid('companycertificateborder');
    file_prepare_draft_area($draftcompanycertificateborderid,
                            $systemcontext->id,
                            'local_iomad',
                            'companycertificateborder', $parentid,
                            array('subdirs' => 0, 'maxbytes' => 15 * 1024, 'maxfiles' => 1));
    $companyrecord->companycertificateborder = $draftcompanycertificateborderid;
    $draftcompanycertificatewatermarkid = file_get_submitted_draft_itemid('companycertificatewatermark');
    file_prepare_draft_area($draftcompanycertificatewatermarkid,
                            $systemcontext->id,
                            'local_iomad',
                            'companycertificatewatermark', $parentid,
                            array('subdirs' => 0, 'maxbytes' => 15 * 1024, 'maxfiles' => 1));
    $companyrecord->companycertificatewatermark = $draftcompanycertificatewatermarkid;

    // Deal with the image display options.
    $parentcompanyoptions = $DB->get_record('companycertificate', array('companyid' => $parentid));
    $companyrecord->uselogo = $parentcompanyoptions->uselogo;
    $companyrecord->usesignature = $parentcompanyoptions->usesignature;
    $companyrecord->useborder = $parentcompanyoptions->useborder;
    $companyrecord->usewatermark = $parentcompanyoptions->usewatermark;
    $companyrecord->showgrade = $parentcompanyoptions->showgrade;

    // Deal with all of the CSS and logo stuff too.
    if (!empty($parentcompanyoptions->bgcolor_header)) {
        $companyrecord->bgcolor_header = $parentcompanyoptions->bgcolor_header;
    }
    if (!empty($parentcompanyoptions->bgcolor_content)) {
        $companyrecord->bgcolor_content = $parentcompanyoptions->bgcolor_content;
    }
    if (!empty($parentcompanyoptions->theme)) {
        $companyrecord->theme = $parentcompanyoptions->theme;
    }
    if (!empty($parentcompanyoptions->customcss)) {
        $companyrecord->customcss = $parentcompanyoptions->customcss;
    }
    if (!empty($parentcompanyoptions->maincolor)) {
        $companyrecord->maincolor = $parentcompanyoptions->maincolor;
    }
    if (!empty($parentcompanyoptions->headingcolor)) {
        $companyrecord->headingcolor = $parentcompanyoptions->headingcolor;
    }
    if (!empty($parentcompanyoptions->linkcolor)) {
        $companyrecord->linkcolor = $parentcompanyoptions->linkcolor;
    }
    if (!empty($parentcompanyoptions->custommenuitems)) {
        $companyrecord->custommenuitems = $parentcompanyoptions->custommenuitems;
    }

    $draftcompanylogoid = file_get_submitted_draft_itemid('companylogo');
    file_prepare_draft_area($draftcompanylogoid,
                            $systemcontext->id,
                            'core_admin',
                            'logo' . $parentid, 0,
                            array('subdirs' => 0, 'maxbytes' => 15 * 1024, 'maxfiles' => 1));
    $companyrecord->companylogo = $draftcompanylogoid;

    $draftcompanylogocompactid = file_get_submitted_draft_itemid('companylogocompact');
    file_prepare_draft_area($draftcompanylogocompactid,
                            $systemcontext->id,
                            'core_admin',
                            'logocompact' . $parentid, 0,
                            ['maxfiles' => 1]);
    $companyrecord->companylogocompact = $draftcompanylogocompactid;

    $draftcompanyfaviconid = file_get_submitted_draft_itemid('companyfavicon');
    file_prepare_draft_area($draftcompanyfaviconid,
                            $systemcontext->id,
                            'core_admin',
                            'favicon' . $parentid, 0,
                            ['maxfiles' => 1]);
    $companyrecord->companyfavicon = $draftcompanyfaviconid;
} else {
    $draftcompanycertificatesealid = file_get_submitted_draft_itemid('companycertificateseal');
    file_prepare_draft_area($draftcompanycertificatesealid,
                            $systemcontext->id,
                            'local_iomad',
                            'companycertificateseal', $companyid,
                            array('subdirs' => 0, 'maxbytes' => 15 * 1024, 'maxfiles' => 1));
    $companyrecord->companycertificateseal = $draftcompanycertificatesealid;
    $draftcompanycertificatesignatureid = file_get_submitted_draft_itemid('companycertificatesignature');
    file_prepare_draft_area($draftcompanycertificatesignatureid,
                            $systemcontext->id,
                            'local_iomad',
                            'companycertificatesignature', $companyid,
                            array('subdirs' => 0, 'maxbytes' => 15 * 1024, 'maxfiles' => 1));
    $companyrecord->companycertificatesignature = $draftcompanycertificatesignatureid;
    $draftcompanycertificateborderid = file_get_submitted_draft_itemid('companycertificateborder');
    file_prepare_draft_area($draftcompanycertificateborderid,
                            $systemcontext->id,
                            'local_iomad',
                            'companycertificateborder', $companyid,
                            array('subdirs' => 0, 'maxbytes' => 15 * 1024, 'maxfiles' => 1));
    $companyrecord->companycertificateborder = $draftcompanycertificateborderid;
    $draftcompanycertificatewatermarkid = file_get_submitted_draft_itemid('companycertificatewatermark');
    file_prepare_draft_area($draftcompanycertificatewatermarkid,
                            $systemcontext->id,
                            'local_iomad',
                            'companycertificatewatermark', $companyid,
                            array('subdirs' => 0, 'maxbytes' => 15 * 1024, 'maxfiles' => 1));
    $companyrecord->companycertificatewatermark = $draftcompanycertificatewatermarkid;
}
if ($domains = $DB->get_records('company_domains', array('companyid' => $companyid))) {
    $companyrecord->companydomains = '';
    foreach ($domains as $domain) {
        $companyrecord->companydomains .= $domain->domain ."\n";
    }
}

// Set up the form.
$mform = new block_iomad_company_admin\forms\company_edit_form($PAGE->url, $isadding, $companyid, $companyrecord, $firstcompany, $parentid, $child);
$companyrecord->templates = array();

// Set the parent company id if it's being passed.
if (!empty($companyrecord->parentid)) {
    $companyrecord->currentparentid = $companyrecord->parentid;
} else {
    $companyrecord->currentparentid = 0;
}
if (!empty($parentid)) {
    $companyrecord->parentid = $parentid;
}
if ($companytemplates = $DB->get_records('company_role_templates_ass', array('companyid' => $companyid), null, 'templateid')) {
    $companyrecord->templates = array_keys($companytemplates);
}
if ($certificateinfo = $DB->get_record('companycertificate', array('companyid' => $companyid))) {
    $companyrecord->uselogo = $certificateinfo->uselogo;
    $companyrecord->usesignature = $certificateinfo->usesignature;
    $companyrecord->useborder = $certificateinfo->useborder;
    $companyrecord->usewatermark = $certificateinfo->usewatermark;
    $companyrecord->showgrade = $certificateinfo->showgrade;
}
$mform->set_data($companyrecord);

if ($mform->is_cancelled()) {
    redirect($companylist);

} else if ($data = $mform->get_data()) {
    $data->userid = $USER->id;

    if (empty($data->validto)) {
        $data->validto = null;
    }
    if ($isadding) {
        // Set up a profiles field category for this company.
        $catdata = new stdclass();
        $catdata->sortorder = $DB->count_records('user_info_category') + 1;
        $catdata->name = $data->shortname;
        $data->profileid = $DB->insert_record('user_info_category', $catdata);

        // Deal with leading/trailing spaces
        $data->name = trim($data->name);
        $data->shortname = trim($data->shortname);
        $data->code = trim($data->code);
        $data->city = trim($data->city);
        $data->region = trim($data->region);
        $data->custom1 = trim($data->custom1);
        $data->custom2 = trim($data->custom2);
        $data->custom3 = trim($data->custom3);

        $companyid = $DB->insert_record('company', $data);
        $company = new company($companyid);

        $eventother = array('companyid' => $companyid);

        $event = \block_iomad_company_admin\event\company_created::create(array('context' => $systemcontext,
                                                                                'userid' => $USER->id,
                                                                                'objectid' => $companyid,
                                                                                'other' => $eventother));
        $event->trigger();

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
        $redirectmessage = get_string('companycreatedok', 'block_iomad_company_admin');

        // Deal with any parent company assignments.
        if (!empty($companydetails->parentid)) {
            $company = new company($companydetails->id);
            $company->assign_parent_managers($companydetails->parentid);
            $companylist = $linkurl;
            $redirectmessage = "";
        }

        // Deal with any assigned templates.
        if (!empty($data->templates)) {
            $company->assign_role_templates($data->templates);
        }

        // Deal with certificate info.
        $certificateinforec = array('companyid' => $companyid,
                                    'uselogo' => $data->uselogo,
                                    'usesignature' => $data->usesignature,
                                    'useborder' => $data->useborder,
                                    'usewatermark' => $data->usewatermark,
                                    'showgrade' => $data->showgrade);
        $DB->insert_record('companycertificate', $certificateinforec);

    } else {
        $data->id = $companyid;
        if (!empty($data->usedefaultpaymentaccount)) {
            $data->paymentaccount = '';
        }

        $company = new company($companyid);
        $oldcompany = $DB->get_record('company', array('id' => $companyid));
        $oldtheme = $company->get_theme();
        $themechanged = $oldtheme != $data->theme;

        // Check if we have a new expiration date.
        if (!empty($data->validto)) {
            if (!empty($oldcompany->companyterminated) && $data->validto > $oldcompany->validto) {
                $data->companyterminated = 0;
            }
        }

        if ($themechanged) {
            $company->update_theme($data->theme);
        }

        //  Has the company name changed?
        if ($topdepartment = $company->get_company_parentnode($companyid)) {
            if ($topdepartment->name != $data->name) {
                $topdepartment->name = $data->name;
                $topdepartment->shorname = $data->shortname;
                $DB->update_record('department', $topdepartment);
            }
        }

        $redirectmessage = get_string('companysavedok', 'block_iomad_company_admin');

        // Has the company parentid changed?
        $companyparent = $company->get_parentid();
        if ($companyparent != $data->parentid) {
            // Is there currently a company parent set?
            if (!empty($companyparent)) {
                // Clear the old ones.
                $company->unassign_parent_managers($companyparent);
            }

            // Update the company record.
            $DB->update_record('company', $data);

            if (!empty($data->parentid)) {
                // Assign the new ones.
                $company->assign_parent_managers($data->parentid);
            }

            // We only want to change the parent, not submit the form.
            $companylist = $linkurl;
            $redirectmessage = "";
        }

        // Did we apply a template?
        if (!empty($data->roletemplate)) {
            if ($data->roletemplate != 'i') {
                $data->previousroletemplateid = $data->roletemplate;
            } else {
                $data->previousroletemplateid = -1;
            }
        }

        // Did we apply an email template?
        if (!empty($data->emailtemplate)) {
            $data->previousemailtemplateid = $data->emailtemplate;
        }

        $DB->update_record('company', $data);
        // Fire an event for this.
        $eventother = array('companyid' => $companyid,
                            'oldcompany' => json_encode($oldcompany));

        $event = \block_iomad_company_admin\event\company_updated::create(array('context' => $companycontext,
                                                                                'userid' => $USER->id,
                                                                                'objectid' => $companyid,
                                                                                'other' => $eventother));
        $event->trigger();

        // Deal with certificate info.
        $certificateinforec = (array) $DB->get_record('companycertificate', array('companyid' => $companyid));
            if (!empty($certificateinforec['id'])) {
            $certificateinforec['uselogo'] = $data->uselogo;
            $certificateinforec['usesignature'] = $data->usesignature;
            $certificateinforec['useborder'] = $data->useborder;
            $certificateinforec['usewatermark'] = $data->usewatermark;
            $certificateinforec['showgrade'] = $data->showgrade;
            $DB->update_record('companycertificate', $certificateinforec);
        } else {
            $certificateinforec = array('companyid' => $companyid,
                                        'uselogo' => $data->uselogo,
                                        'usesignature' => $data->usesignature,
                                        'useborder' => $data->useborder,
                                        'usewatermark' => $data->usewatermark,
                                        'showgrade' => $data->showgrade);
            $DB->insert_record('companycertificate', $certificateinforec);
        }

        if (company_user::is_company_user()) {
            company_user::reload_company();
        }
    }

    $company = new company($data->id);

    // Deal with role templates.
    if (!empty($data->roletemplate)) {
        // We need to do something with the roles.
        if ($data->roletemplate == 'i') {
            if (!empty($data->parentid)) {
                // Apply the same roles as per the parent company.
                $company->apply_role_templates();
            }
        } else {
            $company->apply_role_templates($data->roletemplate);
        }
    }

    // Deal with email templates.
    if (!empty($data->emailtemplate) && iomad::has_capability('local/email:edit', $companycontext)) {
        // We need to do something with the email templates.
        $company->apply_email_templates($data->emailtemplate);
    }

    // Deal with any assigned templates.
    if (empty($data->templates)) {
        $data->templates = array();
    }
    $company->assign_role_templates($data->templates, true);

    // Deal with logo config settings.
    $fs = get_file_storage();
    if (!empty($data->companylogo)) {
        file_save_draft_area_files($data->companylogo,
                                   $systemcontext->id,
                                   'core_admin',
                                   'logo' . $data->id,
                                   0,
                                   ['maxfiles' => 1]);

        // Set the plugin config so it can actually be picked up.
        if ($files = $fs->get_area_files($systemcontext->id, 'core_admin', 'logo'. $data->id)) {
            foreach ($files as $file) {
                if ($file->get_filename() != '.') {
                    break;
                }
            }
            set_config('logo' . $data->id, $file->get_filepath() . $file->get_filename(), 'core_admin');
        } else {
            set_config('logo' . $data->id, '', 'core_admin');
        }
    }
    if (!empty($data->companylogocompact)) {
        file_save_draft_area_files($data->companylogocompact,
                                   $systemcontext->id,
                                   'core_admin',
                                   'logocompact' . $data->id,
                                   0,
                                   ['maxfiles' => 1]);

        // Set the plugin config so it can actually be picked up.
        if ($files = $fs->get_area_files($systemcontext->id, 'core_admin', 'logocompact'. $data->id)) {
            foreach ($files as $file) {
                if ($file->get_filename() != '.') {
                    break;
                }
            }
            set_config('logocompact' . $data->id, $file->get_filepath() . $file->get_filename(), 'core_admin');
        } else {
            set_config('logocompact' . $data->id, '', 'core_admin');
        }
    }
    if (!empty($data->companyfavicon)) {
        file_save_draft_area_files($data->companyfavicon,
                                   $systemcontext->id,
                                   'core_admin',
                                   'favicon' . $data->id,
                                   0,
                                   ['maxfiles' => 1]);

        // Set the plugin config so it can actually be picked up.
        if ($files = $fs->get_area_files($systemcontext->id, 'core_admin', 'favicon'. $data->id)) {
            foreach ($files as $file) {
                if ($file->get_filename() != '.') {
                    break;
                }
            }
            set_config('favicon' . $data->id, $file->get_filepath() . $file->get_filename(), 'core_admin');
        } else {
            set_config('favicon' . $data->id, '', 'core_admin');
        }
    }
    if (!empty($data->companycertificateseal)) {
        file_save_draft_area_files($data->companycertificateseal,
                                   $systemcontext->id,
                                   'local_iomad',
                                   'companycertificateseal',
                                   $data->id,
                                   array('subdirs' => 0, 'maxbytes' => 150 * 1024, 'maxfiles' => 1));
    }
    if (!empty($data->companycertificatesignature)) {
        file_save_draft_area_files($data->companycertificatesignature,
                                   $systemcontext->id,
                                   'local_iomad',
                                   'companycertificatesignature',
                                   $data->id,
                                   array('subdirs' => 0, 'maxbytes' => 150 * 1024, 'maxfiles' => 1));
    }
    if (!empty($data->companycertificateborder)) {
        file_save_draft_area_files($data->companycertificateborder,
                                   $systemcontext->id,
                                   'local_iomad',
                                   'companycertificateborder',
                                   $data->id,
                                   array('subdirs' => 0, 'maxbytes' => 150 * 1024, 'maxfiles' => 1));
    }
    if (!empty($data->companycertificatewatermark)) {
        file_save_draft_area_files($data->companycertificatewatermark,
                                   $systemcontext->id,
                                   'local_iomad',
                                   'companycertificatewatermark',
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

    redirect($companylist, $redirectmessage, \core\output\notification::NOTIFY_SUCCESS);
}

echo $OUTPUT->header();

$mform->display();

echo $OUTPUT->footer();