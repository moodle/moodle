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
 * Contains functions used by importodp.php that naturally pertain to importing
 * powerpoint presentations into the lesson module
 *
 * @package    mod
 * @subpackage lesson
 * @copyright  2009 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

defined('MOODLE_INTERNAL') || die();

/**
 *  Creates objects an object with the page and answers that are to be inserted into the database
 *
 * @param array $pageobjects
 * @param int $lessonid
 * @return array
 */
function lesson_create_objects($pageobjects, $lessonid) {

    $branchtables = array();
    $branchtable = new stdClass;

    // all pages have this info
    $page = new stdClass();
    $page->lessonid = $lessonid;
    $page->prevpageid = 0;
    $page->nextpageid = 0;
    $page->qtype = LESSON_PAGE_BRANCHTABLE;
    $page->qoption = 0;
    $page->layout = 1;
    $page->display = 1;
    $page->timecreated = time();
    $page->timemodified = 0;

    // all answers are the same
    $answer = new stdClass();
    $answer->lessonid = $lessonid;
    $answer->jumpto = LESSON_NEXTPAGE;
    $answer->grade = 0;
    $answer->score = 0;
    $answer->flags = 0;
    $answer->timecreated = time();
    $answer->timemodified = 0;
    $answer->answer = "Next";
    $answer->response = "";

    $answers[] = clone($answer);

    $answer->jumpto = LESSON_PREVIOUSPAGE;
    $answer->answer = "Previous";

    $answers[] = clone($answer);

    $branchtable->answers = $answers;

    $i = 1;

    foreach ($pageobjects as $pageobject) {
        if ($pageobject->title == '') {
            $page->title = "Page $i";  // no title set so make a generic one
        } else {
            $page->title = $pageobject->title;
        }
        $page->contents = '';

        // nab all the images first
        $page->images = $pageobject->images;
        foreach ($page->images as $image) {
            $imagetag = '<img src="@@PLUGINFILE@@'.$image->get_filepath().$image->get_filename().'" title="'.$image->get_filename().'" />';
            $imagetag = str_replace("\n", '', $imagetag);
            $imagetag = str_replace("\r", '', $imagetag);
            $imagetag = str_replace("'", '"', $imagetag);  // imgstyle
            $page->contents .= $imagetag;
        }
        // go through the contents array and put <p> tags around each element and strip out \n which I have found to be unneccessary
        foreach ($pageobject->contents as $content) {
            $content = str_replace("\n", '', $content);
            $content = str_replace("\r", '', $content);
            $content = str_replace('&#13;', '', $content);  // puts in returns?
            $content = '<p>'.$content.'</p>';
            $page->contents .= $content;
        }

        $branchtable->page = clone($page);  // add the page
        $branchtables[] = clone($branchtable);  // add it all to our array
        $i++;
    }

    return $branchtables;
}

/**
 * Form displayed to the user asking them to select a file to upload
 *
 * @copyright  2009 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class lesson_importodp_form extends moodleform {

    public function definition() {
        global $COURSE;

        $mform = $this->_form;

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'pageid');
        $mform->setType('pageid', PARAM_INT);

        $filepickeroptions = array();
        $filepickeroptions['filetypes'] = array('*.zip');
        $filepickeroptions['maxbytes'] = $COURSE->maxbytes;
        $mform->addElement('filepicker', 'odpzip', get_string('upload'), null, $filepickeroptions);
        $mform->addRule('odpzip', null, 'required', null, 'client');

        $this->add_action_buttons(null, get_string("uploadthisfile"));
    }

}
?>