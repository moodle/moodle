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

require_once(dirname(__FILE__) . '/../../config.php'); // Creates $PAGE.
require_once('lib.php');
require_once($CFG->libdir . '/formslib.php');

$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);
$companyid = optional_param('companyid', 0, PARAM_INTEGER);
$courseid = optional_param('courseid', 0, PARAM_INTEGER);
$departmentid = optional_param('departmentid', 0, PARAM_INTEGER);
$licenseid = optional_param('licenseid', 0, PARAM_INTEGER);
$parentid = optional_param('parentid', 0, PARAM_INTEGER);

$context = context_system::instance();
require_login();

// Set the companyid
$companyid = iomad::get_my_companyid($context);
$company = new company($companyid);

if (empty($parentid)) {
    if (!empty($licenseid) && $company->is_child_license($licenseid)) {
        iomad::require_capability('block/iomad_company_admin:edit_my_licenses', $context);
    } else {
        iomad::require_capability('block/iomad_company_admin:edit_licenses', $context);
    }
} else {
    iomad::require_capability('block/iomad_company_admin:edit_my_licenses', $context);
}

$PAGE->set_context($context);

$urlparams = array('companyid' => $companyid);
if ($returnurl) {
    $urlparams['returnurl'] = $returnurl;
}
if ($courseid) {
    $urlparams['courseid'] = $courseid;
}

// Correct the navbar .
// Set the name for the page.
$linktext = get_string('managelicenses', 'block_iomad_company_admin');
$listtext = get_string('company_license_list_title', 'block_iomad_company_admin');
// Set the url.
$linkurl = new moodle_url('/blocks/iomad_company_admin/company_license_edit_form.php');
$listurl = new moodle_url('/blocks/iomad_company_admin/company_license_list.php');

// Print the page header.
$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($linktext);
$PAGE->set_heading(get_string('edit_licenses_title', 'block_iomad_company_admin'));
if (empty($CFG->defaulthomepage)) {
    $PAGE->navbar->add(get_string('dashboard', 'block_iomad_company_admin'), new moodle_url($CFG->wwwroot . '/my'));
}
$PAGE->navbar->add($listtext, $listurl);
$PAGE->navbar->add($linktext);

// If we are editing a license, check that the parent id is set.
if (!empty($licenseid)) {
    $licenseinfo = $DB->get_record('companylicense', array('id' => $licenseid));
    $parentid = $licenseinfo->parentid;
}

// Set up the form.
$mform = new block_iomad_company_admin\forms\company_license_form($PAGE->url, $context, $companyid, $departmentid, $licenseid, $parentid);
if ($licenseinfo = $DB->get_record('companylicense', array('id' => $licenseid))) {
    if ($currentcourses = $DB->get_records('companylicense_courses', array('licenseid' => $licenseid), null, 'courseid')) {
        foreach ($currentcourses as $currentcourse) {
            $licenseinfo->licensecourses[] = $currentcourse->courseid;
        }
    }

    // Deal with the amount for program courses.
    if (!empty($licenseinfo->program)) {
        $licenseinfo->allocation = $licenseinfo->allocation / count($currentcourses);
    }

    $mform->set_data($licenseinfo);
} else {
    $licenseinfo = new stdclass();
    $licenseinfo->expirydate = strtotime('+ 1 year');
    if (!empty($parentid)) {
        if ($currentcourses = $DB->get_records('companylicense_courses', array('licenseid' => $parentid), null, 'courseid')) {
            foreach ($currentcourses as $currentcourse) {
                $licenseinfo->licensecourses[] = $currentcourse->courseid;
            }
        }
    }
    $mform->set_data($licenseinfo);
}

if ( $mform->is_cancelled() || optional_param('cancel', false, PARAM_BOOL) ) {
    redirect(new moodle_url('/blocks/iomad_company_admin/company_license_list.php'));
} else {
    if ( $data = $mform->get_data() ) {
        global $DB, $USER;

        if (empty($data->instant)) {
            $data->instant = 0;
        }

        $new = false;
        $licensedata = array();
        $licensedata['name'] = trim($data->name);
        $licensedata['reference'] = trim($data->reference);
        if (empty($data->program)) {
            $licensedata['program'] = 0;
            $licensedata['allocation'] = $data->allocation;
        } else {
            $licensedata['program'] = $data->program;
            $licensedata['allocation'] = $data->allocation * count($data->licensecourses);
        }
        $licensedata['humanallocation'] = $data->allocation;
        $licensedata['instant'] = $data->instant;
        $licensedata['expirydate'] = $data->expirydate;
        $licensedata['startdate'] = $data->startdate;
        if (empty($data->languages)) {
            $data->languages = array();
        }
        if (empty($data->parentid)) {
            $licensedata['companyid'] = $data->companyid;
        } else {
            $licensedata['companyid'] = $data->designatedcompany;
            $licensedata['parentid'] = $data->parentid;
        }
        $licensedata['validlength'] = $data->validlength;
        $licensedata['type'] = $data->type;

        if (empty($data->cutoffdate)) {
            $licensedata['cutoffdate'] = 0;
        } else {
            $licensedata['cutoffdate'] = $data->cutoffdate;
        }

        if (empty($data->clearonexpire)) {
            $licensedata['clearonexpire'] = 0;
        } else {
            $licensedata['clearonexpire'] = $data->clearonexpire;
        }

        if ( !empty($licenseid) && $currlicensedata = $DB->get_record('companylicense', array('id' => $licenseid))) {
            $new = false;
            // Already in the table update it.
            $licensedata['id'] = $currlicensedata->id;
            $licensedata['used'] = $currlicensedata->used;
            $DB->update_record('companylicense', $licensedata);
        } else {
            $new = true;
            // New license being created.
            $licensedata['used'] = 0;
            $licenseid = $DB->insert_record('companylicense', $licensedata);
        }

        // Deal with course allocations if there are any.
        // Capture them for checking.
        $oldcourses = $DB->get_records('companylicense_courses', array('licenseid' => $licenseid), null, 'courseid');
        // Clear down all of them initially.
        $DB->delete_records('companylicense_courses', array('licenseid' => $licenseid));
        if (!empty($data->licensecourses)) {
            // Add the course license allocations.
            foreach ($data->licensecourses as $selectedcourse) {
                $DB->insert_record('companylicense_courses', array('licenseid' => $licenseid, 'courseid' => $selectedcourse));
            }
        }

        // Create an event to deal with an parent license allocations.
        $eventother = array('licenseid' => $licenseid,
                            'parentid' => $data->parentid);

        if ($new) {
            $event = \block_iomad_company_admin\event\company_license_created::create(array('context' => context_system::instance(),
                                                                                            'userid' => $USER->id,
                                                                                            'objectid' => $licenseid,
                                                                                            'other' => $eventother));
            $returnmessage = get_string('licensecreatedok', 'block_iomad_company_admin');
        } else {
            $eventother['oldcourses'] = json_encode($oldcourses);
            if ($currlicensedata->program != $data->program) {
                $eventother['programchange'] = true;
            }
            if ($currlicensedata->startdate != $data->startdate) {
                $eventother['oldstartdate'] = $currlicensedata->startdate;
            }
            if ($currlicensedata->type != $data->type) {
                $eventother['educatorchange'] = true;
            }
            $event = \block_iomad_company_admin\event\company_license_updated::create(array('context' => context_system::instance(),
                                                                                            'userid' => $USER->id,
                                                                                            'objectid' => $licenseid,
                                                                                            'other' => $eventother));
            $returnmessage = get_string('licenseupdatedok', 'block_iomad_company_admin');
        }
        $event->trigger();
        redirect(new moodle_url('/blocks/iomad_company_admin/company_license_list.php'), $returnmessage, null, \core\output\notification::NOTIFY_SUCCESS);
    }

    // Display the form.
    echo $OUTPUT->header();

    // Check the department is valid.
    if (!empty($departmentid) && !company::check_valid_department($companyid, $departmentid)) {
        print_error('invaliddepartment', 'block_iomad_company_admin');
    }

    // Check the license is valid.
    if (!empty($licenseid) && !company::check_valid_company_license($companyid, $licenseid)) {
        print_error('invalidlicense', 'block_iomad_company_admin');
    }

    $company = new company($companyid);
    echo "<h3>".$company->get_name()."</h3>";
    $mform->display();
    echo $OUTPUT->footer();
}
