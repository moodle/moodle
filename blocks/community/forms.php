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
 * @package    blocks
 * @subpackage community
 * @author     Jerome Mouneyrac <jerome@mouneyrac.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 1999 onwards Martin Dougiamas  http://dougiamas.com
 *
 * Form for community search
 */

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/course/publish/lib.php');
require_once($CFG->dirroot . '/' . $CFG->admin . '/registration/lib.php');

class community_hub_search_form extends moodleform {

    public function definition() {
        global $CFG, $USER, $OUTPUT;
        $strrequired = get_string('required');
        $mform = & $this->_form;

        //set default value
        $search = $this->_customdata['search'];
        if (isset($this->_customdata['coverage'])) {
            $coverage = $this->_customdata['coverage'];
        } else {
            $coverage = 'all';
        }
        if (isset($this->_customdata['licence'])) {
            $licence = $this->_customdata['licence'];
        } else {
            $licence = 'all';
        }
        if (isset($this->_customdata['subject'])) {
            $subject = $this->_customdata['subject'];
        } else {
            $subject = 'all';
        }
        if (isset($this->_customdata['audience'])) {
            $audience = $this->_customdata['audience'];
        } else {
            $audience = 'all';
        }
        if (isset($this->_customdata['language'])) {
            $language = $this->_customdata['language'];
        } else {
            $language = current_language();
        }
        if (isset($this->_customdata['educationallevel'])) {
            $educationallevel = $this->_customdata['educationallevel'];
        } else {
            $educationallevel = 'all';
        }
        if (isset($this->_customdata['downloadable'])) {
            $downloadable = $this->_customdata['downloadable'];
        } else {
            $downloadable = 0;
        }
        if (isset($this->_customdata['orderby'])) {
            $orderby = $this->_customdata['orderby'];
        } else {
            $orderby = 'newest';
        }
        if (isset($this->_customdata['huburl'])) {
            $huburl = $this->_customdata['huburl'];
        } else {
            $huburl = HUB_MOODLEORGHUBURL;
        }

        $mform->addElement('header', 'site', get_string('search', 'block_community'));

        //add the course id (of the context)
        $mform->addElement('hidden', 'courseid', $this->_customdata['courseid']);
        $mform->addElement('hidden', 'executesearch', 1);

        //retrieve the hub list on the hub directory by web service
        $function = 'hubdirectory_get_hubs';
        $params = array();
        $serverurl = HUB_HUBDIRECTORYURL . "/local/hubdirectory/webservice/webservices.php";
        require_once($CFG->dirroot . "/webservice/xmlrpc/lib.php");
        $xmlrpcclient = new webservice_xmlrpc_client($serverurl, 'publichubdirectory');
        try {
            $hubs = $xmlrpcclient->call($function, $params);
        } catch (Exception $e) {
            $hubs = array();
            $error = $OUTPUT->notification(get_string('errorhublisting', 'block_community', $e->getMessage()));
            $mform->addElement('static', 'errorhub', '', $error);
        }

        //display list of registered on hub
        $registrationmanager = new registration_manager();
        $registeredhubs = $registrationmanager->get_registered_on_hubs();
        //retrieve some additional hubs that we will add to
        //the hub list got from the hub directory
        $additionalhubs = array();
        foreach ($registeredhubs as $registeredhub) {
            $inthepubliclist = false;
            foreach ($hubs as $hub) {
                if ($hub['url'] == $registeredhub->huburl) {
                    $inthepubliclist = true;
                    $hub['registeredon'] = true;
                }
            }
            if (!$inthepubliclist) {
                $additionalhub = array();
                $additionalhub['name'] = $registeredhub->hubname;
                $additionalhub['url'] = $registeredhub->huburl;
                $additionalhubs[] = $additionalhub;
            }
        }
        if (!empty($additionalhubs)) {
            $hubs = array_merge($hubs, $additionalhubs);
        }

        if (!empty($hubs)) {
            //TODO: sort hubs by trusted/prioritize
            //Public hub list
            $options = array();
            $firsthub = false;
            foreach ($hubs as $hub) {
                if (key_exists('id', $hub)) {
                    $params = array('hubid' => $hub['id'],
                        'filetype' => HUB_HUBSCREENSHOT_FILE_TYPE);
                    $imgurl = new moodle_url(HUB_HUBDIRECTORYURL .
                                    "/local/hubdirectory/webservice/download.php", $params);
                    $ascreenshothtml = html_writer::empty_tag('img',
                                    array('src' => $imgurl, 'alt' => $hub['name']));

                    $hubdescription = html_writer::tag('a', $hub['name'],
                                    array('class' => 'hublink clearfix', 'href' => $hub['url'],
                                        'onclick' => 'this.target="_blank"'));
                    $hubdescription .= html_writer::tag('span', $ascreenshothtml,
                                    array('class' => 'hubscreenshot'));
                    $hubdescriptiontext = html_writer::tag('span', format_text($hub['description'], FORMAT_PLAIN),
                                    array('class' => 'hubdescription'));
                    if (isset($hub['enrollablecourses'])) { //check needed to avoid warnings for Moodle version < 2011081700
                        $additionaldesc = get_string('enrollablecourses', 'block_community') . ': ' . $hub['enrollablecourses'] . ' - ' .
                                get_string('downloadablecourses', 'block_community') . ': ' . $hub['downloadablecourses'];
                        $hubdescriptiontext .= html_writer::tag('span', $additionaldesc,
                                        array('class' => 'hubadditionaldesc'));
                    }
                    if ($hub['trusted']) {
                    $hubtrusted =  get_string('hubtrusted', 'block_community');
                    $hubdescriptiontext .= html_writer::tag('span',
                                    $hubtrusted . ' ' . $OUTPUT->doc_link('trusted_hubs'),
                                    array('class' => 'trusted'));

                    }
                    $hubdescriptiontext = html_writer::tag('span', $hubdescriptiontext,
                            array('class' => 'hubdescriptiontext'));

                    $hubdescription = html_writer::tag('span',
                                    $hubdescription . $hubdescriptiontext,
                                    array('class' => $hub['trusted'] ? 'hubtrusted' : 'hubnottrusted'));
                } else {
                    $hubdescription = html_writer::tag('a', $hub['name'],
                                    array('class' => 'hublink hubtrusted', 'href' => $hub['url']));
                }

                if (empty($firsthub)) {
                    $mform->addElement('radio', 'huburl', get_string('selecthub', 'block_community'),
                            $hubdescription, $hub['url']);
                    $mform->setDefault('huburl', $huburl);
                    $firsthub = true;
                } else {
                    $mform->addElement('radio', 'huburl', '', $hubdescription, $hub['url']);
                }
            }

            //display enrol/download select box if the USER has the download capability on the course
            if (has_capability('moodle/community:download',
                            get_context_instance(CONTEXT_COURSE, $this->_customdata['courseid']))) {
                $options = array(0 => get_string('enrollable', 'block_community'),
                    1 => get_string('downloadable', 'block_community'));
                $mform->addElement('select', 'downloadable', get_string('enroldownload', 'block_community'),
                        $options);
                $mform->addHelpButton('downloadable', 'enroldownload', 'block_community');
            } else {
                $mform->addElement('hidden', 'downloadable', 0);
            }

            $options = array();
            $options['all'] = get_string('any');
            $options[HUB_AUDIENCE_EDUCATORS] = get_string('audienceeducators', 'hub');
            $options[HUB_AUDIENCE_STUDENTS] = get_string('audiencestudents', 'hub');
            $options[HUB_AUDIENCE_ADMINS] = get_string('audienceadmins', 'hub');
            $mform->addElement('select', 'audience', get_string('audience', 'block_community'), $options);
            $mform->setDefault('audience', $audience);
            unset($options);
            $mform->addHelpButton('audience', 'audience', 'block_community');

            $options = array();
            $options['all'] = get_string('any');
            $options[HUB_EDULEVEL_PRIMARY] = get_string('edulevelprimary', 'hub');
            $options[HUB_EDULEVEL_SECONDARY] = get_string('edulevelsecondary', 'hub');
            $options[HUB_EDULEVEL_TERTIARY] = get_string('eduleveltertiary', 'hub');
            $options[HUB_EDULEVEL_GOVERNMENT] = get_string('edulevelgovernment', 'hub');
            $options[HUB_EDULEVEL_ASSOCIATION] = get_string('edulevelassociation', 'hub');
            $options[HUB_EDULEVEL_CORPORATE] = get_string('edulevelcorporate', 'hub');
            $options[HUB_EDULEVEL_OTHER] = get_string('edulevelother', 'hub');
            $mform->addElement('select', 'educationallevel',
                    get_string('educationallevel', 'block_community'), $options);
            $mform->setDefault('educationallevel', $educationallevel);
            unset($options);
            $mform->addHelpButton('educationallevel', 'educationallevel', 'block_community');

            $publicationmanager = new course_publish_manager();
            $options = $publicationmanager->get_sorted_subjects();
            foreach ($options as $key => &$option) {
                $keylength = strlen($key);
                if ($keylength == 10) {
                    $option = "&nbsp;&nbsp;" . $option;
                } else if ($keylength == 12) {
                    $option = "&nbsp;&nbsp;&nbsp;&nbsp;" . $option;
                }
            }
            $options = array_merge(array('all' => get_string('any')), $options);
            $mform->addElement('select', 'subject', get_string('subject', 'block_community'),
                    $options, array('id' => 'communitysubject'));
            $mform->setDefault('subject', $subject);
            unset($options);
            $mform->addHelpButton('subject', 'subject', 'block_community');
            $this->init_javascript_enhancement('subject', 'smartselect',
                    array('selectablecategories' => true, 'mode' => 'compact'));

            require_once($CFG->libdir . "/licenselib.php");
            $licensemanager = new license_manager();
            $licences = $licensemanager->get_licenses();
            $options = array();
            $options['all'] = get_string('any');
            foreach ($licences as $license) {
                $options[$license->shortname] = get_string($license->shortname, 'license');
            }
            $mform->addElement('select', 'licence', get_string('licence', 'block_community'), $options);
            unset($options);
            $mform->addHelpButton('licence', 'licence', 'block_community');
            $mform->setDefault('licence', $licence);

            $languages = get_string_manager()->get_list_of_languages();
            textlib_get_instance()->asort($languages);
            $languages = array_merge(array('all' => get_string('any')), $languages);
            $mform->addElement('select', 'language', get_string('language'), $languages);
            $mform->setDefault('language', $language);
            $mform->addHelpButton('language', 'language', 'block_community');

            $mform->addElement('radio', 'orderby', get_string('orderby', 'block_community'),
                    get_string('orderbynewest', 'block_community'), 'newest');
            $mform->addElement('radio', 'orderby', null,
                    get_string('orderbyeldest', 'block_community'), 'eldest');
            $mform->addElement('radio', 'orderby', null,
                    get_string('orderbyname', 'block_community'), 'fullname');
            $mform->addElement('radio', 'orderby', null,
                    get_string('orderbypublisher', 'block_community'), 'publisher');
            $mform->addElement('radio', 'orderby', null,
                    get_string('orderbyratingaverage', 'block_community'), 'ratingaverage');
            $mform->setDefault('orderby', $orderby);
            $mform->setType('orderby', PARAM_ALPHA);

            $mform->addElement('text', 'search', get_string('keywords', 'block_community'));
            $mform->addHelpButton('search', 'keywords', 'block_community');


            $mform->addElement('submit', 'submitbutton', get_string('search', 'block_community'));
        }
    }

    function validation($data, $files) {
        global $CFG;

        $errors = array();

        if (empty($this->_form->_submitValues['huburl'])) {
            $errors['huburl'] = get_string('nohubselected', 'hub');
        }

        return $errors;
    }

}