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
 * Course publish renderer.
 * @package   course
 * @subpackage publish
 * @copyright 2010 Moodle Pty Ltd (http://moodle.com)
 * @author    Jerome Mouneyrac
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_publish_renderer extends plugin_renderer_base {

    /**
     * Display the page to publish a course on Moodle.org or on a specific hub
     */
    public function publicationselector($courseid, $registeredonmoodleorg, $registeredonhub) {
        global $OUTPUT;

        $table = new html_table();
        $table->head  = array(get_string('moodleorg', 'hub'), get_string('specifichub', 'hub'));
        $table->size = array('50%', '50%');

        //Moodle.org information cell
        $moodleorgcell = get_string('moodleorgpublicationdetail', 'hub');
        $moodleorgcell .= html_writer::empty_tag('br').html_writer::empty_tag('br');
        $moodleorgcell = html_writer::tag('div', $moodleorgcell, array('class' => 'justifytext'));

        //Specific hub information cell
        $specifichubcell = get_string('specifichubpublicationdetail', 'hub');
        $specifichubcell .= html_writer::empty_tag('br').html_writer::empty_tag('br');
        $specifichubcell = html_writer::tag('div', $specifichubcell, array('class' => 'justifytext'));

        //add information cells
        $cells = array($moodleorgcell, $specifichubcell);
        $row = new html_table_row($cells);
        $table->data[] = $row;

        //Moodle.org button cell
        if ($registeredonmoodleorg) {
            $advertiseonmoodleorgurl = new moodle_url("/course/publish/metadata.php",
                                array('sesskey' => sesskey(), 'huburl' => MOODLEORGHUBURL,  'id' => $courseid
                                    , 'hubname' => 'Moodle.org', 'advertise' => true));
            $advertiseonmoodleorgbutton = new single_button($advertiseonmoodleorgurl, get_string('advertiseonmoodleorg', 'hub'));
            $advertiseonmoodleorgbutton->class = 'centeredbutton';
            $advertiseonmoodleorgbuttonhtml = $OUTPUT->render($advertiseonmoodleorgbutton);

            $shareonmoodleorgurl = new moodle_url("/course/publish/metadata.php",
                                array('sesskey' => sesskey(), 'huburl' => MOODLEORGHUBURL,  'id' => $courseid
                                    , 'hubname' => 'Moodle.org', 'share' => true));
            $shareonmoodleorgbutton = new single_button($shareonmoodleorgurl, get_string('shareonmoodleorg', 'hub'));
            $shareonmoodleorgbutton->class = 'centeredbutton';
            $shareonmoodleorgbuttonhtml = $OUTPUT->render($shareonmoodleorgbutton);

            $moodleorgcell = $advertiseonmoodleorgbuttonhtml." ".$shareonmoodleorgbuttonhtml;
        } else {
            $moodleorgcell = html_writer::tag('span',
                    get_string('notregisteredonmoodleorg', 'hub') , array('class' => 'publicationwarning'));
        }

         //Specific hub button cell
        if ($registeredonhub) {
            $advertisespecifichuburl = new moodle_url("/course/publish/hubselector.php",
                                array('sesskey' => sesskey(), 'id' => $courseid, 'advertise' => true));
            $advertiseonspecifichubbutton = new single_button($advertisespecifichuburl, get_string('advertiseonspecifichub', 'hub'));
            $advertiseonspecifichubbutton->class = 'centeredbutton';
            $advertiseonspecifichubbuttonhtml = $OUTPUT->render($advertiseonspecifichubbutton);

            $sharespecifichuburl = new moodle_url("/course/publish/hubselector.php",
                                array('sesskey' => sesskey(), 'id' => $courseid, 'share' => true));
            $shareonspecifichubbutton = new single_button($sharespecifichuburl, get_string('shareonspecifichub', 'hub'));
            $shareonspecifichubbutton->class = 'centeredbutton';
            $shareonspecifichubbuttonhtml = $OUTPUT->render($shareonspecifichubbutton);
            $specifichubcell = $advertiseonspecifichubbuttonhtml. " " . $shareonspecifichubbuttonhtml;
          } else {
            $specifichubcell = html_writer::tag('span',
                    get_string('notregisteredonhub', 'hub') , array('class' => 'publicationwarning'));
        }

        //add button cells
        $cells = array($moodleorgcell, $specifichubcell);
        $row = new html_table_row($cells);
        $table->data[] = $row;

        return html_writer::table($table);

    }

}