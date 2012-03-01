<?php

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// This file is part of Moodle - http://moodle.org/                      //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//                                                                       //
// Moodle is free software: you can redistribute it and/or modify        //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation, either version 3 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// Moodle is distributed in the hope that it will be useful,             //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details.                          //
//                                                                       //
// You should have received a copy of the GNU General Public License     //
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.       //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/*
 * @package    course
 * @subpackage publish
 * @author     Jerome Mouneyrac <jerome@mouneyrac.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 1999 onwards Martin Dougiamas  http://dougiamas.com
 *
 * The forms used for course publication
 */


require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . "/" . $CFG->admin . "/registration/lib.php");
require_once($CFG->dirroot . "/course/publish/lib.php");

/*
 * Hub selector to choose on which hub we want to publish.
 */

class hub_publish_selector_form extends moodleform {

    public function definition() {
        global $CFG;
        $mform = & $this->_form;
        $share = $this->_customdata['share'];

        $mform->addElement('header', 'site', get_string('selecthub', 'hub'));

        $mform->addElement('static', 'info', '', get_string('selecthubinfo', 'hub') . html_writer::empty_tag('br'));

        $registrationmanager = new registration_manager();
        $registeredhubs = $registrationmanager->get_registered_on_hubs();

        //Public hub list
        $options = array();
        foreach ($registeredhubs as $hub) {

            $hubname = $hub->hubname;
            $mform->addElement('hidden', clean_param($hub->huburl, PARAM_ALPHANUMEXT), $hubname);
            if (empty($hubname)) {
                $hubname = $hub->huburl;
            }
            $mform->addElement('radio', 'huburl', null, ' ' . $hubname, $hub->huburl);
            if ($hub->huburl == HUB_MOODLEORGHUBURL) {
                $mform->setDefault('huburl', $hub->huburl);
            }
        }

        $mform->addElement('hidden', 'id', $this->_customdata['id']);

        if ($share) {
            $buttonlabel = get_string('shareonhub', 'hub');
            $mform->addElement('hidden', 'share', true);
        } else {
            $buttonlabel = get_string('advertiseonhub', 'hub');
            $mform->addElement('hidden', 'advertise', true);
        }

        $this->add_action_buttons(false, $buttonlabel);
    }

}

/*
 * Course publication form
 */

class course_publication_form extends moodleform {

    public function definition() {
        global $CFG, $DB, $USER, $OUTPUT;

        $strrequired = get_string('required');
        $mform = & $this->_form;
        $huburl = $this->_customdata['huburl'];
        $hubname = $this->_customdata['hubname'];
        $course = $this->_customdata['course'];
        $advertise = $this->_customdata['advertise'];
        $share = $this->_customdata['share'];
        $page = $this->_customdata['page'];
        $site = get_site();

        //hidden parameters
        $mform->addElement('hidden', 'huburl', $huburl);
        $mform->addElement('hidden', 'hubname', $hubname);

        //check on the hub if the course has already been published
        $registrationmanager = new registration_manager();
        $registeredhub = $registrationmanager->get_registeredhub($huburl);
        $publicationmanager = new course_publish_manager();
        $publications = $publicationmanager->get_publications($registeredhub->huburl, $course->id, $advertise);

        if (!empty($publications)) {
            //get the last publication of this course
            $publication = array_pop($publications);

            $function = 'hub_get_courses';
            $options = new stdClass();
            $options->ids = array($publication->hubcourseid);
            $options->allsitecourses = 1;
            $params = array('search' => '', 'downloadable' => $share,
                'enrollable' => !$share, 'options' => $options);
            $serverurl = $huburl . "/local/hub/webservice/webservices.php";
            require_once($CFG->dirroot . "/webservice/xmlrpc/lib.php");
            $xmlrpcclient = new webservice_xmlrpc_client($serverurl, $registeredhub->token);
            try {
                $result = $xmlrpcclient->call($function, $params);
                $publishedcourses = $result['courses'];
            } catch (Exception $e) {
                $error = $OUTPUT->notification(get_string('errorcourseinfo', 'hub', $e->getMessage()));
                $mform->addElement('static', 'errorhub', '', $error);
            }
        }

        if (!empty($publishedcourses)) {
            $publishedcourse = $publishedcourses[0];
            $hubcourseid = $publishedcourse['id'];
            $defaultfullname = $publishedcourse['fullname'];
            $defaultshortname = $publishedcourse['shortname'];
            $defaultsummary = $publishedcourse['description'];
            $defaultlanguage = $publishedcourse['language'];
            $defaultpublishername = $publishedcourse['publishername'];
            $defaultpublisheremail = $publishedcourse['publisheremail'];
            $defaultcontributornames = $publishedcourse['contributornames'];
            $defaultcoverage = $publishedcourse['coverage'];
            $defaultcreatorname = $publishedcourse['creatorname'];
            $defaultlicenceshortname = $publishedcourse['licenceshortname'];
            $defaultsubject = $publishedcourse['subject'];
            $defaultaudience = $publishedcourse['audience'];
            $defaulteducationallevel = $publishedcourse['educationallevel'];
            $defaultcreatornotes = $publishedcourse['creatornotes'];
            $defaultcreatornotesformat = $publishedcourse['creatornotesformat'];
            $screenshotsnumber = $publishedcourse['screenshots'];
            $privacy = $publishedcourse['privacy'];
            if (($screenshotsnumber > 0) and !empty($privacy)) {
                $page->requires->yui_module('moodle-block_community-imagegallery',
                        'M.blocks_community.init_imagegallery',
                        array(array('imageids' => array($hubcourseid),
                                'imagenumbers' => array($screenshotsnumber),
                                'huburl' => $huburl)));
            }
        } else {
            $defaultfullname = $course->fullname;
            $defaultshortname = $course->shortname;
            $defaultsummary = clean_param($course->summary, PARAM_TEXT);
            if (empty($course->lang)) {
                $language = get_site()->lang;
                if (empty($language)) {
                    $defaultlanguage = current_language();
                } else {
                    $defaultlanguage = $language;
                }
            } else {
                $defaultlanguage = $course->lang;
            }
            $defaultpublishername = $USER->firstname . ' ' . $USER->lastname;
            $defaultpublisheremail = $USER->email;
            $defaultcontributornames = '';
            $defaultcoverage = '';
            $defaultcreatorname = $USER->firstname . ' ' . $USER->lastname;
            $defaultlicenceshortname = 'cc';
            $defaultsubject = 'none';
            $defaultaudience = HUB_AUDIENCE_STUDENTS;
            $defaulteducationallevel = HUB_EDULEVEL_TERTIARY;
            $defaultcreatornotes = '';
            $defaultcreatornotesformat = FORMAT_HTML;
            $screenshotsnumber = 0;
        }

        //the input parameters
        $mform->addElement('header', 'moodle', get_string('publicationinfo', 'hub'));

        $mform->addElement('text', 'name', get_string('coursename', 'hub'),
                array('class' => 'metadatatext'));
        $mform->addRule('name', $strrequired, 'required', null, 'client');
        $mform->setType('name', PARAM_TEXT);
        $mform->setDefault('name', $defaultfullname);
        $mform->addHelpButton('name', 'name', 'hub');

        $mform->addElement('hidden', 'id', $this->_customdata['id']);

        if ($share) {
            $buttonlabel = get_string('shareon', 'hub', !empty($hubname) ? $hubname : $huburl);

            $mform->addElement('hidden', 'share', $share);

            $mform->addElement('text', 'demourl', get_string('demourl', 'hub'),
                    array('class' => 'metadatatext'));
            $mform->setType('demourl', PARAM_URL);
            $mform->setDefault('demourl', new moodle_url("/course/view.php?id=" . $course->id));
            $mform->addHelpButton('demourl', 'demourl', 'hub');
        }

        if ($advertise) {
            if (empty($publishedcourses)) {
                $buttonlabel = get_string('advertiseon', 'hub', !empty($hubname) ? $hubname : $huburl);
            } else {
                $buttonlabel = get_string('readvertiseon', 'hub', !empty($hubname) ? $hubname : $huburl);
            }
            $mform->addElement('hidden', 'advertise', $advertise);
            $mform->addElement('hidden', 'courseurl', $CFG->wwwroot . "/course/view.php?id=" . $course->id);
            $mform->addElement('static', 'courseurlstring', get_string('courseurl', 'hub'));
            $mform->setDefault('courseurlstring', new moodle_url("/course/view.php?id=" . $course->id));
            $mform->addHelpButton('courseurlstring', 'courseurl', 'hub');
        }

        $mform->addElement('text', 'courseshortname', get_string('courseshortname', 'hub'),
                array('class' => 'metadatatext'));
        $mform->setDefault('courseshortname', $defaultshortname);
        $mform->addHelpButton('courseshortname', 'courseshortname', 'hub');

        $mform->addElement('textarea', 'description', get_string('description'), array('rows' => 10,
            'cols' => 57));
        $mform->addRule('description', $strrequired, 'required', null, 'client');
        $mform->setDefault('description', $defaultsummary);
        $mform->setType('description', PARAM_TEXT);
        $mform->addHelpButton('description', 'description', 'hub');

        $languages = get_string_manager()->get_list_of_languages();
        collatorlib::asort($languages);
        $mform->addElement('select', 'language', get_string('language'), $languages);
        $mform->setDefault('language', $defaultlanguage);
        $mform->addHelpButton('language', 'language', 'hub');


        $mform->addElement('text', 'publishername', get_string('publishername', 'hub'),
                array('class' => 'metadatatext'));
        $mform->setDefault('publishername', $defaultpublishername);
        $mform->addRule('publishername', $strrequired, 'required', null, 'client');
        $mform->addHelpButton('publishername', 'publishername', 'hub');

        $mform->addElement('text', 'publisheremail', get_string('publisheremail', 'hub'),
                array('class' => 'metadatatext'));
        $mform->setDefault('publisheremail', $defaultpublisheremail);
        $mform->addRule('publisheremail', $strrequired, 'required', null, 'client');
        $mform->addHelpButton('publisheremail', 'publisheremail', 'hub');

        $mform->addElement('text', 'creatorname', get_string('creatorname', 'hub'),
                array('class' => 'metadatatext'));
        $mform->addRule('creatorname', $strrequired, 'required', null, 'client');
        $mform->setType('creatorname', PARAM_TEXT);
        $mform->setDefault('creatorname', $defaultcreatorname);
        $mform->addHelpButton('creatorname', 'creatorname', 'hub');

        $mform->addElement('text', 'contributornames', get_string('contributornames', 'hub'),
                array('class' => 'metadatatext'));
        $mform->setDefault('contributornames', $defaultcontributornames);
        $mform->addHelpButton('contributornames', 'contributornames', 'hub');

        $mform->addElement('text', 'coverage', get_string('tags', 'hub'),
                array('class' => 'metadatatext'));
        $mform->setType('coverage', PARAM_TEXT);
        $mform->setDefault('coverage', $defaultcoverage);
        $mform->addHelpButton('coverage', 'tags', 'hub');



        require_once($CFG->libdir . "/licenselib.php");
        $licensemanager = new license_manager();
        $licences = $licensemanager->get_licenses();
        $options = array();
        foreach ($licences as $license) {
            $options[$license->shortname] = get_string($license->shortname, 'license');
        }
        $mform->addElement('select', 'licence', get_string('license'), $options);
        $mform->setDefault('licence', $defaultlicenceshortname);
        unset($options);
        $mform->addHelpButton('licence', 'licence', 'hub');

        $options = $publicationmanager->get_sorted_subjects();

        //prepare data for the smartselect
        foreach ($options as $key => &$option) {
            $keylength = strlen($key);
            if ($keylength == 10) {
                $option = "&nbsp;&nbsp;" . $option;
            } else if ($keylength == 12) {
                $option = "&nbsp;&nbsp;&nbsp;&nbsp;" . $option;
            }
        }

        $options = array('none' => get_string('none', 'hub')) + $options;
        $mform->addElement('select', 'subject', get_string('subject', 'hub'), $options);
        unset($options);
        $mform->addHelpButton('subject', 'subject', 'hub');
        $mform->setDefault('subject', $defaultsubject);
        $mform->addRule('subject', $strrequired, 'required', null, 'client');
        $this->init_javascript_enhancement('subject', 'smartselect', array('selectablecategories' => false, 'mode' => 'compact'));

        $options = array();
        $options[HUB_AUDIENCE_EDUCATORS] = get_string('audienceeducators', 'hub');
        $options[HUB_AUDIENCE_STUDENTS] = get_string('audiencestudents', 'hub');
        $options[HUB_AUDIENCE_ADMINS] = get_string('audienceadmins', 'hub');
        $mform->addElement('select', 'audience', get_string('audience', 'hub'), $options);
        $mform->setDefault('audience', $defaultaudience);
        unset($options);
        $mform->addHelpButton('audience', 'audience', 'hub');

        $options = array();
        $options[HUB_EDULEVEL_PRIMARY] = get_string('edulevelprimary', 'hub');
        $options[HUB_EDULEVEL_SECONDARY] = get_string('edulevelsecondary', 'hub');
        $options[HUB_EDULEVEL_TERTIARY] = get_string('eduleveltertiary', 'hub');
        $options[HUB_EDULEVEL_GOVERNMENT] = get_string('edulevelgovernment', 'hub');
        $options[HUB_EDULEVEL_ASSOCIATION] = get_string('edulevelassociation', 'hub');
        $options[HUB_EDULEVEL_CORPORATE] = get_string('edulevelcorporate', 'hub');
        $options[HUB_EDULEVEL_OTHER] = get_string('edulevelother', 'hub');
        $mform->addElement('select', 'educationallevel', get_string('educationallevel', 'hub'), $options);
        $mform->setDefault('educationallevel', $defaulteducationallevel);
        unset($options);
        $mform->addHelpButton('educationallevel', 'educationallevel', 'hub');

        $editoroptions = array('maxfiles' => 0, 'maxbytes' => 0, 'trusttext' => false, 'forcehttps' => false);
        $mform->addElement('editor', 'creatornotes', get_string('creatornotes', 'hub'), '', $editoroptions);
        $mform->addRule('creatornotes', $strrequired, 'required', null, 'client');
        $mform->setType('creatornotes', PARAM_CLEANHTML);
        $mform->addHelpButton('creatornotes', 'creatornotes', 'hub');

        if ($advertise) {
            if (!empty($screenshotsnumber)) {

                if (!empty($privacy)) {
                    $baseurl = new moodle_url($huburl . '/local/hub/webservice/download.php',
                                    array('courseid' => $hubcourseid, 'filetype' => HUB_SCREENSHOT_FILE_TYPE));
                    $screenshothtml = html_writer::empty_tag('img',
                                    array('src' => $baseurl, 'alt' => $defaultfullname));
                    $screenshothtml = html_writer::tag('div', $screenshothtml,
                                    array('class' => 'coursescreenshot',
                                        'id' => 'image-' . $hubcourseid));
                } else {
                    $screenshothtml = get_string('existingscreenshotnumber', 'hub', $screenshotsnumber);
                }
                $mform->addElement('static', 'existingscreenshots', get_string('existingscreenshots', 'hub'), $screenshothtml);
                $mform->addHelpButton('existingscreenshots', 'deletescreenshots', 'hub');
                $mform->addElement('checkbox', 'deletescreenshots', '', ' ' . get_string('deletescreenshots', 'hub'));
            }

            $mform->addElement('hidden', 'existingscreenshotnumber', $screenshotsnumber);
        }

        $mform->addElement('filemanager', 'screenshots', get_string('addscreenshots', 'hub'), null,
                array('subdirs' => 0,
                    'maxbytes' => 1000000,
                    'maxfiles' => 3
        ));
        $mform->addHelpButton('screenshots', 'screenshots', 'hub');

        $this->add_action_buttons(false, $buttonlabel);

        //set default value for creatornotes editor
        $data = new stdClass();
        $data->creatornotes = array();
        $data->creatornotes['text'] = $defaultcreatornotes;
        $data->creatornotes['format'] = $defaultcreatornotesformat;
        $this->set_data($data);
    }

    function validation($data, $files) {
        global $CFG;

        $errors = array();

        if ($this->_form->_submitValues['subject'] == 'none') {
            $errors['subject'] = get_string('mustselectsubject', 'hub');
        }

        return $errors;
    }

}

