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
 * turnitin_errors.php - Displays Turnitin files with a current error state.
 *
 * @package   administration
 * @author    Dan Marsden <dan@danmarsden.com>
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

    require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
    require_once($CFG->libdir.'/adminlib.php');
    require_once($CFG->libdir.'/plagiarismlib.php');
    require_once('plagiarism_form.php');

    require_login();
    admin_externalpage_setup('plagiarism');

    $context = get_context_instance(CONTEXT_SYSTEM);

    $fileid = optional_param('fileid',0,PARAM_INT);
    $resetuser = optional_param('reset',0,PARAM_INT);
    $page = optional_param('page', 0, PARAM_INT);

    $mform = new plagiarism_defaults_form(null);
    $plagiarismdefaults = $DB->get_records_menu('plagiarism_config', array('cm'=>0),'','name,value'); //cmid(0) is the default list.
    if (!empty($plagiarismdefaults)) {
        $mform->set_data($plagiarismdefaults);
    }
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('plagiarismdefaults', 'plagiarism'));
    $currenttab='plagiarismdefaults';
    require_once('plagiarism_tabs.php');
    if (($data = $mform->get_data()) && confirm_sesskey()) {
        $plagiarismelements = plagiarism_config_options();
        foreach($plagiarismelements as $element) {
            if (isset($data->$element)) {
                $newelement = new object();
                $newelement->cm = 0;
                $newelement->name = $element;
                $newelement->value = $data->$element;
                if (isset($plagiarismdefaults[$element])) { //update
                    $newelement->id = $DB->get_field('plagiarism_config', 'id', (array('cm'=>0, 'name'=>$element)));
                    $DB->update_record('plagiarism_config', $newelement);
                } else { //insert
                    $DB->insert_record('plagiarism_config', $newelement);
                }
            }
        }
        notify(get_string('plagiarismdefaultupdated','plagiarism'),'notifysuccess');
    }
    echo $OUTPUT->box(get_string('plagiarismdefaultsdesc', 'plagiarism'));

    $mform->display();
    echo $OUTPUT->footer();
