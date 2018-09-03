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
 * Class course_publication_form
 *
 * @package    core
 * @copyright  2017 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\hub;
defined('MOODLE_INTERNAL') || die();

use stdClass;
use license_manager;
use moodle_url;
use core_collator;

global $CFG;
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/licenselib.php');

/**
 * The forms used for course publication
 *
 * @package    core
 * @copyright  2017 Marina Glancy
 * @author     Jerome Mouneyrac <jerome@mouneyrac.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_publication_form extends \moodleform {

    /**
     * Form definition
     */
    public function definition() {
        global $CFG, $USER, $PAGE;

        $strrequired = get_string('required');
        $mform = & $this->_form;
        $course = $this->_customdata['course'];
        if (!empty($this->_customdata['publication'])) {
            // We are editing existing publication.
            $publication = $this->_customdata['publication'];
            $advertise = $publication->enrollable;
            $publishedcourse = publication::get_published_course($publication);
        } else {
            $publication = null;
            $advertise = $this->_customdata['advertise'];
        }
        $share = !$advertise;

        if (!empty($publishedcourse)) {
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
            $screenshotbaseurl = $publishedcourse['screenshotbaseurl'];
            $privacy = $publishedcourse['privacy'];
            if (($screenshotsnumber > 0) and !empty($privacy)) {
                $PAGE->requires->yui_module('moodle-block_community-imagegallery',
                    'M.blocks_community.init_imagegallery',
                    array(array('imageids' => array($hubcourseid),
                        'imagenumbers' => array($screenshotsnumber),
                        'huburl' => HUB_MOODLEORGHUBURL)));
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
            $defaultaudience = publication::HUB_AUDIENCE_STUDENTS;
            $defaulteducationallevel = publication::HUB_EDULEVEL_TERTIARY;
            $defaultcreatornotes = '';
            $defaultcreatornotesformat = FORMAT_HTML;
            $screenshotsnumber = 0;
            $screenshotbaseurl = null;
        }

        // The input parameters.
        $mform->addElement('header', 'moodle', get_string('publicationinfo', 'hub'));

        $mform->addElement('text', 'name', get_string('coursename', 'hub'),
            array('class' => 'metadatatext'));
        $mform->addRule('name', $strrequired, 'required', null, 'client');
        $mform->setType('name', PARAM_TEXT);
        $mform->setDefault('name', $defaultfullname);
        $mform->addHelpButton('name', 'name', 'hub');

        $mform->addElement('hidden', 'id', $course->id);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'publicationid', $publication ? $publication->id : null);
        $mform->setType('publicationid', PARAM_INT);

        if ($share) {
            $buttonlabel = get_string('shareon', 'hub', 'Moodle.net');

            $mform->addElement('hidden', 'share', $share);
            $mform->setType('share', PARAM_BOOL);
            $mform->addElement('text', 'demourl', get_string('demourl', 'hub'),
                array('class' => 'metadatatext'));
            $mform->setType('demourl', PARAM_URL);
            $mform->setDefault('demourl', new moodle_url("/course/view.php?id=" . $course->id));
            $mform->addHelpButton('demourl', 'demourl', 'hub');
        }

        if ($advertise) {
            if (!$publication) {
                $buttonlabel = get_string('advertiseon', 'hub', 'Moodle.net');
            } else {
                $buttonlabel = get_string('readvertiseon', 'hub', 'Moodle.net');
            }
            $mform->addElement('hidden', 'advertise', $advertise);
            $mform->setType('advertise', PARAM_BOOL);
            $mform->addElement('hidden', 'courseurl', $CFG->wwwroot . "/course/view.php?id=" . $course->id);
            $mform->setType('courseurl', PARAM_URL);
            $mform->addElement('static', 'courseurlstring', get_string('courseurl', 'hub'));
            $mform->setDefault('courseurlstring', new moodle_url("/course/view.php?id=" . $course->id));
            $mform->addHelpButton('courseurlstring', 'courseurl', 'hub');
        }

        $mform->addElement('text', 'courseshortname', get_string('courseshortname', 'hub'),
            array('class' => 'metadatatext'));
        $mform->setDefault('courseshortname', $defaultshortname);
        $mform->addHelpButton('courseshortname', 'courseshortname', 'hub');
        $mform->setType('courseshortname', PARAM_TEXT);
        $mform->addElement('textarea', 'description', get_string('description', 'hub'), array('rows' => 10,
            'cols' => 57));
        $mform->addRule('description', $strrequired, 'required', null, 'client');
        $mform->setDefault('description', $defaultsummary);
        $mform->setType('description', PARAM_TEXT);
        $mform->addHelpButton('description', 'description', 'hub');

        $languages = get_string_manager()->get_list_of_languages();
        core_collator::asort($languages);
        $mform->addElement('select', 'language', get_string('language'), $languages);
        $mform->setDefault('language', $defaultlanguage);
        $mform->addHelpButton('language', 'language', 'hub');

        $mform->addElement('text', 'publishername', get_string('publishername', 'hub'),
            array('class' => 'metadatatext'));
        $mform->setDefault('publishername', $defaultpublishername);
        $mform->addRule('publishername', $strrequired, 'required', null, 'client');
        $mform->addHelpButton('publishername', 'publishername', 'hub');
        $mform->setType('publishername', PARAM_NOTAGS);

        $mform->addElement('text', 'publisheremail', get_string('publisheremail', 'hub'),
            array('class' => 'metadatatext'));
        $mform->setDefault('publisheremail', $defaultpublisheremail);
        $mform->addRule('publisheremail', $strrequired, 'required', null, 'client');
        $mform->addHelpButton('publisheremail', 'publisheremail', 'hub');
        $mform->setType('publisheremail', PARAM_EMAIL);

        $mform->addElement('text', 'creatorname', get_string('creatorname', 'hub'),
            array('class' => 'metadatatext'));
        $mform->addRule('creatorname', $strrequired, 'required', null, 'client');
        $mform->setType('creatorname', PARAM_NOTAGS);
        $mform->setDefault('creatorname', $defaultcreatorname);
        $mform->addHelpButton('creatorname', 'creatorname', 'hub');

        $mform->addElement('text', 'contributornames', get_string('contributornames', 'hub'),
            array('class' => 'metadatatext'));
        $mform->setDefault('contributornames', $defaultcontributornames);
        $mform->addHelpButton('contributornames', 'contributornames', 'hub');
        $mform->setType('contributornames', PARAM_NOTAGS);

        $mform->addElement('text', 'coverage', get_string('tags', 'hub'),
            array('class' => 'metadatatext'));
        $mform->setType('coverage', PARAM_TEXT);
        $mform->setDefault('coverage', $defaultcoverage);
        $mform->addHelpButton('coverage', 'tags', 'hub');

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

        $options = publication::get_sorted_subjects();

        $mform->addElement('searchableselector', 'subject',
            get_string('subject', 'hub'), $options);
        unset($options);
        $mform->addHelpButton('subject', 'subject', 'hub');
        $mform->setDefault('subject', $defaultsubject);
        $mform->addRule('subject', $strrequired, 'required', null, 'client');

        $options = publication::audience_options();
        $mform->addElement('select', 'audience', get_string('audience', 'hub'), $options);
        $mform->setDefault('audience', $defaultaudience);
        unset($options);
        $mform->addHelpButton('audience', 'audience', 'hub');

        $options = publication::educational_level_options();
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
                    $screenshothtml = \html_writer::empty_tag('img',
                        array('src' => $screenshotbaseurl, 'alt' => $defaultfullname));
                    $screenshothtml = \html_writer::tag('div', $screenshothtml,
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
            $mform->setType('existingscreenshotnumber', PARAM_INT);
        }

        $mform->addElement('filemanager', 'screenshots', get_string('addscreenshots', 'hub'), null,
            array('subdirs' => 0,
                'maxbytes' => 1000000,
                'maxfiles' => 3
            ));
        $mform->addHelpButton('screenshots', 'screenshots', 'hub');

        $this->add_action_buttons(false, $buttonlabel);

        // Set default value for creatornotes editor.
        $data = new stdClass();
        $data->creatornotes = array();
        $data->creatornotes['text'] = $defaultcreatornotes;
        $data->creatornotes['format'] = $defaultcreatornotesformat;
        $this->set_data($data);
    }

    /**
     * Custom form validation
     *
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if ($this->_form->_submitValues['subject'] == 'none') {
            $errors['subject'] = get_string('mustselectsubject', 'hub');
        }

        return $errors;
    }
}
