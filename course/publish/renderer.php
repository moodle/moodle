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
     * Display the selector to advertise or publish a course
     */
    public function publicationselector($courseid) {
        global $OUTPUT;

        $text = '';

        $advertiseurl = new moodle_url("/course/publish/hubselector.php",
                            array('sesskey' => sesskey(), 'id' => $courseid, 'advertise' => true));
        $advertisebutton = new single_button($advertiseurl, get_string('advertise', 'hub'));
        $text .= $OUTPUT->render($advertisebutton);
        $text .= html_writer::tag('div', get_string('advertisepublication_help', 'hub'), array('class' => 'publishhelp'));

        $text .= html_writer::empty_tag('br');  /// TODO Delete

        $uploadurl = new moodle_url("/course/publish/hubselector.php",
                            array('sesskey' => sesskey(), 'id' => $courseid, 'share' => true));
        $uploadbutton = new single_button($uploadurl, get_string('share', 'hub'));
        $text .= $OUTPUT->render($uploadbutton);
        $text .= html_writer::tag('div', get_string('sharepublication_help', 'hub'), array('class' => 'publishhelp'));

        return $text;
    }

     /**
     * Display the listing of hub where a course is registered on
     */
    public function registeredonhublisting($courseid, $publications) {
        global $OUTPUT, $CFG;

        $table = new html_table();
        $table->head  = array(get_string('type', 'hub'), get_string('hub', 'hub'), 
            get_string('date'), get_string('status', 'hub'), get_string('operation', 'hub'));
        $table->size = array('10%', '40%', '20%', '%10', '%15');

        $brtag = html_writer::empty_tag('br');

        foreach ($publications as $publication) {

            $updatebuttonhtml = '';

            $params = array('sesskey' => sesskey(), 'id' => $publication->courseid, 'hubcourseid' => $publication->hubcourseid,
                    'huburl' => $publication->huburl, 'hubname' => $publication->hubname,
                    'cancel' => true, 'publicationid' => $publication->id, 'timepublished' => $publication->timepublished);
            $cancelurl = new moodle_url("/course/publish/index.php", $params);
            $cancelbutton = new single_button($cancelurl, get_string('removefromhub', 'hub'));
            $cancelbutton->class = 'centeredbutton';
            $cancelbuttonhtml = $OUTPUT->render($cancelbutton);

            if ($publication->enrollable) {
                $params = array('sesskey' => sesskey(), 'id' => $publication->courseid,
                    'huburl' => $publication->huburl, 'hubname' => $publication->hubname,
                    'share' => !$publication->enrollable, 'advertise' => $publication->enrollable);
                $updateurl = new moodle_url("/course/publish/metadata.php", $params);
                $updatebutton = new single_button($updateurl, get_string('update', 'hub'));
                $updatebutton->class = 'centeredbutton';
                $updatebuttonhtml = $OUTPUT->render($updatebutton);

                $operations = $updatebuttonhtml . $brtag . $cancelbuttonhtml;
            } else {
                $operations = $cancelbuttonhtml;
            }

            $hubname = html_writer::tag('a', $publication->hubname?$publication->hubname:$publication->huburl,
                    array('href' => $publication->huburl));
            if ($publication->timechecked > 1273127954) { //if the publication check time if bigger than May 2010, it has been checked
                if ($publication->status == 0) {
                    $status = get_string('statusunpublished', 'hub');
                } else {
                    $status = get_string('statuspublished', 'hub');
                }

                $status .= $brtag . html_writer::tag('a', get_string('updatestatus', 'hub'),
                        array('href' => $CFG->wwwroot.'/course/publish/index.php?id='.$courseid.
                        "&updatestatusid=".$publication->id."&sesskey=".sesskey())) .
                        $brtag . get_string('lasttimechecked', 'hub') . ": " . format_time(time() - $publication->timechecked) ;
            } else {
                $status = get_string('neverchecked', 'hub') . $brtag .html_writer::tag('a', get_string('updatestatus', 'hub'),
                        array('href' => $CFG->wwwroot.'/course/publish/index.php?id='.$courseid.
                        "&updatestatusid=".$publication->id."&sesskey=".sesskey()));
            }
            //add button cells     
            $cells = array($publication->enrollable?get_string('advertised', 'hub'):get_string('shared', 'hub'),
                $hubname,  userdate($publication->timepublished, get_string('strftimedatetimeshort')), $status, $operations);
            $row = new html_table_row($cells);
            $table->data[] = $row;

        }

        $contenthtml =  html_writer::table($table);
        
        return $contenthtml;

    }

    /**
     * Display unpublishing confirmation page
     * @param object $publication
     *      $publication->courseshortname
            $publication->courseid
            $publication->hubname
            $publication->huburl
            $publication->id
     */
    public function confirmunpublishing($publication) {
        global $OUTPUT;
        $optionsyes = array('sesskey' => sesskey(), 'id' => $publication->courseid, 'hubcourseid' => $publication->hubcourseid,
                    'huburl' => $publication->huburl, 'hubname' => $publication->hubname,
                    'cancel' => true, 'publicationid' => $publication->id, 'confirm' => true);
        $optionsno  = array('sesskey'=>sesskey(), 'id' => $publication->courseid);
        $publication->hubname = html_writer::tag('a', $publication->hubname,
                array('href' => $publication->huburl));
        $formcontinue = new single_button(new moodle_url("/course/publish/index.php", $optionsyes), get_string('unpublish', 'hub'), 'post');
        $formcancel = new single_button(new moodle_url("/course/publish/index.php", $optionsno), get_string('cancel'), 'get');
        return $OUTPUT->confirm(get_string('unpublishconfirmation', 'hub', $publication), $formcontinue, $formcancel);
    }

}
