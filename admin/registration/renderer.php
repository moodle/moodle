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

/**
 * Registration renderer.
 * @package   moodle
 * @subpackage registration
 * @copyright 2010 Moodle Pty Ltd (http://moodle.com)
 * @author    Jerome Mouneyrac
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_register_renderer extends plugin_renderer_base {

    /**
     * Display a box message confirming a site registration (add or update)
     * @param string $confirmationmessage
     * @return string
     */
    public function registration_confirmation($confirmationmessage) {
        $linktositelist = html_writer::tag('a', get_string('sitelist', 'hub'),
                        array('href' => new moodle_url('/local/hub/index.php')));
        $message = $confirmationmessage . html_writer::empty_tag('br') . $linktositelist;
        return $this->output->box($message);
    }

    /**
     * Display the page to register on Moodle.org or on a specific hub
     */
    public function registrationselector($updatemoodleorg = false) {
        global $CFG;
        $table = new html_table();
        $table->head = array(get_string('moodleorg', 'hub'), get_string('specifichub', 'hub'));
        $table->size = array('50%', '50%');
        //$table->attributes['class'] = 'registerindextable';
        //Moodle.org information cell
        $moodleorgcell = get_string('moodleorgregistrationdetail', 'hub');
        $moodleorgcell .= html_writer::empty_tag('br') . html_writer::empty_tag('br');
        $moodleorgcell = html_writer::tag('div', $moodleorgcell, array('class' => 'justifytext'));

        //Specific hub information cell
        $specifichubcell = get_string('specifichubregistrationdetail', 'hub');
        $specifichubcell .= html_writer::empty_tag('br') . html_writer::empty_tag('br');
        $specifichubcell = html_writer::tag('div', $specifichubcell, array('class' => 'justifytext'));

        //add information cells
        $cells = array($moodleorgcell, $specifichubcell);
        $row = new html_table_row($cells);
        $table->data[] = $row;

        //Moodle.org button cell
        $registeronmoodleorgurl = new moodle_url("/" . $CFG->admin . "/registration/register.php",
                        array('sesskey' => sesskey(), 'huburl' => HUB_MOODLEORGHUBURL
                            , 'hubname' => 'Moodle.org'));
        $registeronmoodleorgbutton = new single_button($registeronmoodleorgurl,
                        $updatemoodleorg ? get_string('updatesite', 'hub', 'Moodle.org') : get_string('registeronmoodleorg', 'hub'));
        $registeronmoodleorgbutton->class = 'centeredbutton';
        $registeronmoodleorgbuttonhtml = $this->output->render($registeronmoodleorgbutton);
        $moodleorgcell = $registeronmoodleorgbuttonhtml;

        //Specific hub button cell
        $registeronspecifichuburl = new moodle_url("/" . $CFG->admin . "/registration/hubselector.php",
                        array('sesskey' => sesskey()));
        $registeronspecifichubbutton = new single_button($registeronspecifichuburl,
                        get_string('registeronspecifichub', 'hub'));
        $registeronspecifichubbutton->class = 'centeredbutton';
        $registeronspecifichubbuttonhtml = $this->output->render($registeronspecifichubbutton);
        $specifichubcell = $registeronspecifichubbuttonhtml;

        //add button cells
        $cells = array($moodleorgcell, $specifichubcell);
        $row = new html_table_row($cells);
        $table->data[] = $row;

        return html_writer::table($table);
    }

    /**
     * Display the listing of registered on hub
     */
    public function registeredonhublisting($hubs) {
        global $CFG;
        $table = new html_table();
        $table->head = array(get_string('hub', 'hub'), get_string('operation', 'hub'));
        $table->size = array('80%', '20%');

        foreach ($hubs as $hub) {
            if ($hub->huburl == HUB_MOODLEORGHUBURL) {
                $hub->hubname = get_string('registeredmoodleorg', 'hub', $hub->hubname);
            }
            $hublink = html_writer::tag('a', $hub->hubname, array('href' => $hub->huburl));
            $hublinkcell = html_writer::tag('div', $hublink, array('class' => 'registeredhubrow'));

            $unregisterhuburl = new moodle_url("/" . $CFG->admin . "/registration/index.php",
                            array('sesskey' => sesskey(), 'huburl' => $hub->huburl,
                                'unregistration' => 1));
            $unregisterbutton = new single_button($unregisterhuburl,
                            get_string('unregister', 'hub'));
            $unregisterbutton->class = 'centeredbutton';
            $unregisterbuttonhtml = $this->output->render($unregisterbutton);

            //add button cells
            $cells = array($hublinkcell, $unregisterbuttonhtml);
            $row = new html_table_row($cells);
            $table->data[] = $row;
        }

        return html_writer::table($table);
    }

}