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
     * Display Moodle.org registration message about benefit to register on Moodle.org
     *
     * @return string
     */
    public function moodleorg_registration_message() {

        $moodleorgstatslink = html_writer::link('http://moodle.net/stats',
                                               get_string('statsmoodleorg', 'admin'),
                                               array('target' => '_blank'));

        $hublink = html_writer::link('https://moodle.net/mod/page/view.php?id=1',
                                      get_string('moodleorghubname', 'admin'),
                                      array('target' => '_blank'));

        $moodleorgregmsg = get_string('registermoodleorg', 'admin', $hublink);
        $items = array(get_string('registermoodleorgli1', 'admin'),
                       get_string('registermoodleorgli2', 'admin', $moodleorgstatslink));
        $moodleorgregmsg .= html_writer::alist($items);
        return $moodleorgregmsg;
    }

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
