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
            $downloadable = 1;
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
        $mform->setType('courseid', PARAM_INT);
        $mform->addElement('hidden', 'executesearch', 1);
        $mform->setType('executesearch', PARAM_INT);

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
            $htmlhubs = array();
            foreach ($hubs as $hub) {
                // Name can come from hub directory - need some cleaning.
                $hubname = clean_text($hub['name'], PARAM_TEXT);
                $smalllogohtml = '';
                if (array_key_exists('id', $hub)) {

                    // Retrieve hub logo + generate small logo.
                    $params = array('hubid' => $hub['id'], 'filetype' => HUB_HUBSCREENSHOT_FILE_TYPE);
                    $imgurl = new moodle_url(HUB_HUBDIRECTORYURL . "/local/hubdirectory/webservice/download.php", $params);
                    $imgsize = getimagesize($imgurl->out(false));
                    if ($imgsize[0] > 1) {
                        $ascreenshothtml = html_writer::empty_tag('img', array('src' => $imgurl, 'alt' => $hubname));
                        $smalllogohtml = html_writer::empty_tag('img', array('src' => $imgurl, 'alt' => $hubname
                                        , 'height' => 30, 'width' => 40));
                    } else {
                        $ascreenshothtml = '';
                    }
                    $hubimage = html_writer::tag('div', $ascreenshothtml, array('class' => 'hubimage'));

                    // Statistics + trusted info.
                    $hubstats = '';
                    if (isset($hub['enrollablecourses'])) { //check needed to avoid warnings for Moodle version < 2011081700
                        $additionaldesc = get_string('enrollablecourses', 'block_community') . ': ' . $hub['enrollablecourses'] . ' - ' .
                                get_string('downloadablecourses', 'block_community') . ': ' . $hub['downloadablecourses'];
                        $hubstats .= html_writer::tag('div', $additionaldesc);
                    }
                    if ($hub['trusted']) {
                        $hubtrusted =  get_string('hubtrusted', 'block_community');
                        $hubstats .= $OUTPUT->doc_link('trusted_hubs') . html_writer::tag('div', $hubtrusted);
                    }
                    $hubstats = html_writer::tag('div', $hubstats, array('class' => 'hubstats'));

                    // hub name link + hub description.
                    $hubnamelink = html_writer::link($hub['url'], html_writer::tag('h2',$hubname),
                                    array('class' => 'hubtitlelink'));
                    // The description can come from the hub directory - need to clean.
                    $hubdescription = clean_param($hub['description'], PARAM_TEXT);
                    $hubdescriptiontext = html_writer::tag('div', format_text($hubdescription, FORMAT_PLAIN),
                                    array('class' => 'hubdescription'));

                    $hubtext = html_writer::tag('div', $hubdescriptiontext . $hubstats, array('class' => 'hubtext'));

                    $hubimgandtext = html_writer::tag('div', $hubimage . $hubtext, array('class' => 'hubimgandtext'));

                    $hubfulldesc = html_writer::tag('div', $hubnamelink . $hubimgandtext, array('class' => 'hubmainhmtl'));
                } else {
                    $hubfulldesc = html_writer::link($hub['url'], $hubname);
                }

                // Add hub to the hub items.
                $hubinfo = new stdClass();
                $hubinfo->mainhtml = $hubfulldesc;
                $hubinfo->rowhtml = html_writer::tag('div', $smalllogohtml , array('class' => 'hubsmalllogo')) . $hubname;
                $hubitems[$hub['url']] = $hubinfo;
            }

            // Hub listing form element.
            $mform->addElement('listing','huburl', '', '', array('items' => $hubitems,
                'showall' => get_string('showall', 'block_community'),
                'hideall' => get_string('hideall', 'block_community')));
            $mform->setDefault('huburl', $huburl);

            //display enrol/download select box if the USER has the download capability on the course
            if (has_capability('moodle/community:download',
                            context_course::instance($this->_customdata['courseid']))) {
                $options = array(0 => get_string('enrollable', 'block_community'),
                    1 => get_string('downloadable', 'block_community'));
                $mform->addElement('select', 'downloadable', get_string('enroldownload', 'block_community'),
                        $options);
                $mform->addHelpButton('downloadable', 'enroldownload', 'block_community');

                $mform->setDefault('downloadable', $downloadable);
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
            collatorlib::asort($languages);
            $languages = array_merge(array('all' => get_string('any')), $languages);
            $mform->addElement('select', 'language', get_string('language'), $languages);

            $mform->setDefault('language', $language);
            $mform->addHelpButton('language', 'language', 'block_community');

            $mform->addElement('select', 'orderby', get_string('orderby', 'block_community'),
                array('newest' => get_string('orderbynewest', 'block_community'),
                    'eldest' => get_string('orderbyeldest', 'block_community'),
                    'fullname' => get_string('orderbyname', 'block_community'),
                    'publisher' => get_string('orderbypublisher', 'block_community'),
                    'ratingaverage' => get_string('orderbyratingaverage', 'block_community')));

            $mform->setDefault('orderby', $orderby);
            $mform->addHelpButton('orderby', 'orderby', 'block_community');
            $mform->setType('orderby', PARAM_ALPHA);

            $mform->setAdvanced('audience');
            $mform->setAdvanced('educationallevel');
            $mform->setAdvanced('subject');
            $mform->setAdvanced('licence');
            $mform->setAdvanced('language');
            $mform->setAdvanced('orderby');

            $mform->addElement('text', 'search', get_string('keywords', 'block_community'),
                array('size' => 30));
            $mform->addHelpButton('search', 'keywords', 'block_community');
            $mform->setType('search', PARAM_NOTAGS);

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
