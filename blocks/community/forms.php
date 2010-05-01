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
require_once($CFG->dirroot.'/lib/hublib.php');

class community_hub_search_form extends moodleform {

    public function definition() {
        global $CFG;
        $strrequired = get_string('required');
        $mform =& $this->_form;
        $search = $this->_customdata['search'];
        $mform->addElement('header', 'site', get_string('search', 'block_community'));

        //retrieve the hub list on the hub directory by web service
        $function = 'hubdirectory_get_hubs';
        $params = array();
        $serverurl = HUBDIRECTORYURL."/local/hubdirectory/webservice/webservices.php";
        require_once($CFG->dirroot."/webservice/xmlrpc/lib.php");
        $xmlrpcclient = new webservice_xmlrpc_client();
        $hubs = $xmlrpcclient->call($serverurl, 'publichubdirectory', $function, $params);

        //Public hub list
        $options = array();
        foreach ($hubs as $hub) {
            //to not display a name longer than 100 character (too big)
            if (strlen($hub['name'])>100) {
                $hubname = substr($hub['name'],0, 100);
                $hubname = $hubname."...";
            } else {
                $hubname = $hub['name'];
            }
            $options[$hub['url']] = $hubname;
            $mform->addElement('hidden', clean_param($hub['url'], PARAM_ALPHANUMEXT), $hubname);
        }
        $mform->addElement('select', 'huburl', get_string('hub','block_community'),
                $options, array("size" => 15));
        $mform->addRule('huburl', $strrequired, 'required', null, 'client');

        $options = array(0 => get_string('enrollable', 'block_community'),
                1 => get_string('downloadable', 'block_community'));
        $mform->addElement('select', 'downloadable', '',
                $options);

        $mform->addElement('text','search' , get_string('search', 'block_community'));

        $this->add_action_buttons(false, get_string('search', 'block_community'));
    }

}