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
 * End of branch table
 *
 * @package mod_lesson
 * @copyright  2009 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

defined('MOODLE_INTERNAL') || die();

 /** End of Branch page */
define("LESSON_PAGE_ENDOFBRANCH",   "21");

class lesson_page_type_endofbranch extends lesson_page {

    protected $type = lesson_page::TYPE_STRUCTURE;
    protected $typeidstring = 'endofbranch';
    protected $typeid = LESSON_PAGE_ENDOFBRANCH;
    protected $string = null;
    protected $jumpto = null;

    public function display($renderer, $attempt) {
        return '';
    }
    public function get_typeid() {
        return $this->typeid;
    }
    public function get_typestring() {
        if ($this->string===null) {
            $this->string = get_string($this->typeidstring, 'lesson');
        }
        return $this->string;
    }
    public function get_idstring() {
        return $this->typeidstring;
    }
    public function callback_on_view($canmanage) {
        $this->redirect_to_first_answer($canmanage);
        exit;
    }

    public function redirect_to_first_answer($canmanage) {
        global $USER, $PAGE;
        $answer = array_shift($this->get_answers());
        $jumpto = $answer->jumpto;
        if ($jumpto == LESSON_RANDOMBRANCH) {

            $jumpto = lesson_unseen_branch_jump($this->lesson, $USER->id);

        } elseif ($jumpto == LESSON_CLUSTERJUMP) {

            if (!$canmanage) {
                $jumpto = $this->lesson->cluster_jump($this->properties->id);
            } else {
                if ($this->properties->nextpageid == 0) {
                    $jumpto = LESSON_EOL;
                } else {
                    $jumpto = $this->properties->nextpageid;
                }
            }

        } else if ($answer->jumpto == LESSON_NEXTPAGE) {

            if ($this->properties->nextpageid == 0) {
                $jumpto = LESSON_EOL;
            } else {
                $jumpto = $this->properties->nextpageid;
            }

        } else if ($jumpto == 0) {

            $jumpto = $this->properties->id;

        } else if ($jumpto == LESSON_PREVIOUSPAGE) {

            $jumpto = $this->properties->prevpageid;

        }
        redirect(new moodle_url('/mod/lesson/view.php', array('id'=>$PAGE->cm->id,'pageid'=>$jumpto)));
    }
    public function get_grayout() {
        return 1;
    }

    public function add_page_link($previd) {
        global $PAGE, $CFG;
        if ($previd != 0) {
            $addurl = new moodle_url('/mod/lesson/editpage.php', array('id'=>$PAGE->cm->id, 'pageid'=>$previd, 'sesskey'=>sesskey(), 'qtype'=>LESSON_PAGE_ENDOFBRANCH));
            return array('addurl'=>$addurl, 'type'=>LESSON_PAGE_ENDOFBRANCH, 'name'=>get_string('addanendofbranch', 'lesson'));
        }
        return false;
    }
    public function valid_page_and_view(&$validpages, &$pageviews) {
        return $this->properties->nextpageid;
    }
}

class lesson_add_page_form_endofbranch extends lesson_add_page_form_base {

    public $qtype = LESSON_PAGE_ENDOFBRANCH;
    public $qtypestring = 'endofbranch';
    protected $standard = false;

    public function custom_definition() {
        global $PAGE;

        $mform = $this->_form;
        $lesson = $this->_customdata['lesson'];
        $jumptooptions = lesson_page_type_branchtable::get_jumptooptions(optional_param('firstpage', false, PARAM_BOOL), $lesson);

        $mform->addElement('hidden', 'firstpage');
        $mform->setType('firstpage', PARAM_BOOL);

        $mform->addElement('hidden', 'qtype');
        $mform->setType('qtype', PARAM_TEXT);

        $mform->addElement('text', 'title', get_string("pagetitle", "lesson"), array('size'=>70));
        $mform->setType('title', PARAM_TEXT);

        $this->editoroptions = array('noclean'=>true, 'maxfiles'=>EDITOR_UNLIMITED_FILES, 'maxbytes'=>$PAGE->course->maxbytes);
        $mform->addElement('editor', 'contents_editor', get_string("pagecontents", "lesson"), null, $this->editoroptions);
        $mform->setType('contents_editor', PARAM_RAW);

        $this->add_jumpto(0);
    }

    public function construction_override($pageid, lesson $lesson) {
        global $DB, $CFG, $PAGE;
        require_sesskey();

        // first get the preceeding page

        $timenow = time();

        // the new page is not the first page (end of branch always comes after an existing page)
        if (!$page = $DB->get_record("lesson_pages", array("id" => $pageid))) {
            print_error('cannotfindpagerecord', 'lesson');
        }
        // chain back up to find the (nearest branch table)
        $btpage = clone($page);
        $btpageid = $btpage->id;
        while (($btpage->qtype != LESSON_PAGE_BRANCHTABLE) && ($btpage->prevpageid > 0)) {
            $btpageid = $btpage->prevpageid;
            if (!$btpage = $DB->get_record("lesson_pages", array("id" => $btpageid))) {
                print_error('cannotfindpagerecord', 'lesson');
            }
        }

        if ($btpage->qtype == LESSON_PAGE_BRANCHTABLE) {
            $newpage = new stdClass;
            $newpage->lessonid = $lesson->id;
            $newpage->prevpageid = $pageid;
            $newpage->nextpageid = $page->nextpageid;
            $newpage->qtype = $this->qtype;
            $newpage->timecreated = $timenow;
            $newpage->title = get_string("endofbranch", "lesson");
            $newpage->contents = get_string("endofbranch", "lesson");
            $newpageid = $DB->insert_record("lesson_pages", $newpage);
            // update the linked list...
            $DB->set_field("lesson_pages", "nextpageid", $newpageid, array("id" => $pageid));
            if ($page->nextpageid) {
                // the new page is not the last page
                $DB->set_field("lesson_pages", "prevpageid", $newpageid, array("id" => $page->nextpageid));
            }
            // ..and the single "answer"
            $newanswer = new stdClass;
            $newanswer->lessonid = $lesson->id;
            $newanswer->pageid = $newpageid;
            $newanswer->timecreated = $timenow;
            $newanswer->jumpto = $btpageid;
            $newanswerid = $DB->insert_record("lesson_answers", $newanswer);
            $lesson->add_message(get_string('addedanendofbranch', 'lesson'), 'notifysuccess');
        } else {
            $lesson->add_message(get_string('nobranchtablefound', 'lesson'));
        }

        redirect($CFG->wwwroot."/mod/lesson/edit.php?id=".$PAGE->cm->id);
    }
}
