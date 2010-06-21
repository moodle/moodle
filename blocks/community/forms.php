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

require_once($CFG->dirroot.'/lib/formslib.php');
require_once($CFG->dirroot.'/course/publish/lib.php');

class community_hub_search_form extends moodleform {

    public function definition() {
        global $CFG, $USER;
        $strrequired = get_string('required');
        $mform =& $this->_form;
        $search = $this->_customdata['search'];
        $mform->addElement('header', 'site', get_string('search', 'block_community'));

        //retrieve the hub list on the hub directory by web service
        $function = 'hubdirectory_get_hubs';
        $params = array();
        $serverurl = HUB_HUBDIRECTORYURL."/local/hubdirectory/webservice/webservices.php";
        require_once($CFG->dirroot."/webservice/xmlrpc/lib.php");
        $xmlrpcclient = new webservice_xmlrpc_client();
        $hubs = $xmlrpcclient->call($serverurl, 'publichubdirectory', $function, $params);

        //sort hubs by trusted/prioritize

        //Public hub list
        $options = array();
       // $mform->addElement('static','huburlstring',get_string('selecthub', 'block_community'));
        
        foreach ($hubs as $hub) {
            $params = array('hubid' => $hub['id'],
                'filetype' => HUB_HUBSCREENSHOT_FILE_TYPE);
            $imgurl = new moodle_url(HUB_HUBDIRECTORYURL . "/local/hubdirectory/webservice/download.php", $params);
            $ascreenshothtml = html_writer::empty_tag('img', array('src' => $imgurl, 'alt' => $hub['name']));
            $brtag = html_writer::empty_tag('br');
            $hubdescription =   '&nbsp;&nbsp;'.$hub['name'];
            $hubdescription .= $brtag;
            $hubdescription .=  html_writer::tag('span', $ascreenshothtml, array('class' => 'hubscreenshot'));
            $hubdescription .= html_writer::tag('span', $hub['description'], array('class' => 'hubdescription'));
            $hubdescription .= $brtag;
            $additionaldesc = get_string('sites', 'block_community'). ': ' . $hub['sites'] . ' - ' .
                                get_string('courses', 'block_community'). ': ' . $hub['courses'];
            $hubdescription .= html_writer::tag('span', $additionaldesc, array('class' => 'hubadditionaldesc'));
            $hubdescription .= $brtag;
            $hubdescription .= html_writer::tag('span',
                    $hub['trusted']?get_string('hubtrusted', 'block_community'):get_string('hubnottrusted', 'block_community'),
                    array('class' => $hub['trusted']?'trusted':'nottrusted'));
             $hubdescription = html_writer::tag('span',
                    $hubdescription,
                    array('class' => $hub['trusted']?'hubtrusted':'hubnottrusted'));

            if (empty($firsthub)) {
                $mform->addElement('radio','huburl',get_string('selecthub', 'block_community'),
                        $hubdescription, $hub['url']);
                $mform->setDefault('huburl', HUB_MOODLEORGHUBURL);
                $firsthub = true;
            } else {
                $mform->addElement('radio','huburl','', $hubdescription, $hub['url']);
            }
        }

        //display enrol/download select box if the USER has the download capability
        if (has_capability('moodle/community:download', get_context_instance(CONTEXT_USER, $USER->id))) {
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
        $mform->setDefault('audience', 'all');
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
        $mform->addElement('select', 'educationallevel', get_string('educationallevel', 'block_community'), $options);
        $mform->setDefault('educationallevel', 'all');
        unset($options);
        $mform->addHelpButton('educationallevel', 'educationallevel', 'block_community');

        $options = get_string_manager()->load_component_strings('edufields', current_language());
        foreach ($options as $key => &$option) {
            $keylength = strlen ( $key );
            if ( $keylength == 10) {
                $option = "&nbsp;&nbsp;" . $option;
            } else  if ( $keylength == 12) {
                $option = "&nbsp;&nbsp;&nbsp;&nbsp;" . $option;
            }
        }
        $options = array_merge (array('all' => get_string('any')),$options);
        $mform->addElement('select', 'subject', get_string('subject', 'block_community'), $options, array('id'=>'communitysubject'));
        $mform->setDefault('subject', 'all');
        unset($options);
        $mform->addHelpButton('subject', 'subject', 'block_community');
        $this->init_javascript_enhancement('subject', 'smartselect', array('selectablecategories' => true, 'mode'=>'compact'));

        require_once($CFG->dirroot."/lib/licenselib.php");
        $licensemanager = new license_manager();
        $licences = $licensemanager->get_licenses();
        $options = array();
        $options['all'] = get_string('any');
        foreach ($licences as $license) {
            $options[$license->shortname] = get_string($license->shortname, 'license');
        }
        $mform->addElement('select', 'licence', get_string('licence', 'block_community'), $options);
        $mform->setDefault('licence', 'cc');
        unset($options);
        $mform->addHelpButton('licence', 'licence', 'block_community');
        $mform->setDefault('licence', 'all');

        $languages = get_string_manager()->get_list_of_languages();
        asort($languages, SORT_LOCALE_STRING);
        $languages = array_merge (array('all' => get_string('any')),$languages);
        $mform->addElement('select', 'language',get_string('language'), $languages);
        $mform->setDefault('language', 'all');
        $mform->addHelpButton('language', 'language', 'block_community');


        $mform->addElement('text','search' , get_string('keywords', 'block_community'));
        $mform->addHelpButton('search', 'keywords', 'block_community');

        $mform->addElement('submit', 'submitbutton', get_string('search', 'block_community'));
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