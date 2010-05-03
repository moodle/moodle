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
 * Block community renderer.
 * @package    blocks
 * @subpackage community
 * @copyright 2010 Moodle Pty Ltd (http://moodle.com)
 * @author    Jerome Mouneyrac
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_community_renderer extends plugin_renderer_base {

     /**
     * Display a list of courses
     * @param array $courses
     * @param boolean $withwriteaccess
     * @return string
     */
    public function course_list($courses, $huburl) {
        global $OUTPUT, $CFG;

        $renderedhtml = '';

        $table = new html_table();


        $table->head  = array(get_string('coursename', 'block_community'),
                get_string('coursedesc', 'block_community'),
                get_string('courselang', 'block_community'),
                get_string('operation', 'block_community'));

        $table->align = array('left', 'left', 'center', 'center');
        $table->size = array('25%', '40%', '5%', '%5');


        if (empty($courses)) {
            $renderedhtml .= get_string('nocourse', 'block_community');
        } else {

            $table->width = '100%';
            $table->data  = array();
            $table->attributes['class'] = 'sitedirectory';

            // iterate through sites and add to the display table
            foreach ($courses as $course) {

                if (is_array($course)) {
                    $course = (object) $course;
                }

                //create site name with link
                if (!empty($course->courseurl)) {
                    $courseurl = new moodle_url($course->courseurl);
                } else {
                    $courseurl = new moodle_url($course->demourl);
                }
                $courseatag = html_writer::tag('a', $course->fullname, array('href' => $courseurl));

                $coursenamehtml = html_writer::tag('span', $courseatag, array());


                //create description to display
                $deschtml = $course->description; //the description
                /// courses and sites number display under the description, in smaller
                $deschtml .= html_writer::empty_tag('br');
                $additionaldesc = get_string('additionalcoursedesc', 'block_community', $course);
                $deschtml .= html_writer::tag('span', $additionaldesc, array('class' => 'additionaldesc'));

                //retrieve language string
                //construct languages array
                if (!empty($course->language)) {
                    $languages = get_string_manager()->get_list_of_languages();
                    $language = $languages[$course->language];
                } else {
                    $language= '';
                }

                if ($course->enrollable) {
                    //Add link TODO make it a button and send by post
                    $addurl = new moodle_url("/blocks/community/communitycourse.php",
                            array('sesskey' => sesskey(), 'add' => 1, 'confirmed' => 1,
                                'coursefullname' => $course->fullname, 'courseurl' => $courseurl,
                                'coursedescription' => $course->description));
                    $addlinkhtml = html_writer::tag('a', get_string('add'), array('href' => $addurl));
                } else {
//                    Add link TODO make it a button and send by post
                    $addurl = new moodle_url("/blocks/community/communitycourse.php",
                            array('sesskey' => sesskey(), 'download' => 1, 'confirmed' => 1,
                                'courseid' => $course->id, 'huburl' => $huburl));
                    $addlinkhtml = html_writer::tag('a', get_string('download', 'block_community'), array('href' => $addurl));
                
//                    $addlinkhtml = "Download not implemented yet";
                }

                // add a row to the table
                $cells = array($coursenamehtml, $deschtml, $language, $addlinkhtml);


                $row = new html_table_row($cells);

                $table->data[] = $row;
            }
            $renderedhtml .= html_writer::table($table);
        }
        return $renderedhtml;
    }

}