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
 * Question type class for the drag-and-drop onto image question type.
 *
 * @package    qtype_ddimageortext
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->dirroot . '/question/engine/lib.php');
require_once($CFG->dirroot . '/question/format/xml/format.php');
require_once($CFG->dirroot . '/question/type/gapselect/questiontypebase.php');

/**
 * The drag-and-drop onto image question type class.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_ddtoimage_base extends question_type {
    protected function choice_group_key() {
        return 'draggroup';
    }

    public function get_question_options($question) {
        global $DB;
        $dbprefix = 'qtype_'.$this->name();
        $question->options = $DB->get_record($dbprefix,
                array('questionid' => $question->id), '*', MUST_EXIST);
        $question->options->drags = $DB->get_records($dbprefix.'_drags',
                array('questionid' => $question->id), 'no ASC', '*');
        $question->options->drops = $DB->get_records($dbprefix.'_drops',
                array('questionid' => $question->id), 'no ASC', '*');
        parent::get_question_options($question);
    }

    protected function initialise_question_instance(question_definition $question, $questiondata) {
        parent::initialise_question_instance($question, $questiondata);
        $question->shufflechoices = $questiondata->options->shuffleanswers;

        $this->initialise_combined_feedback($question, $questiondata, true);

        $question->choices = array();
        $choiceindexmap = array();

        // Store the choices in arrays by group.
        // This code is weird. The first choice in each group gets key 1 in the
        // $question->choices[$choice->choice_group()] array, and the others get
        // key $choice->no. Therefore you need to think carefully whether you
        // are using the key, or $choice->no. This is presumably a mistake, but
        // one that is now essentially un-fixable, since many questions of this
        // type have been attempted, and theys keys get stored in the attempt data.
        foreach ($questiondata->options->drags as $dragdata) {

            $choice = $this->make_choice($dragdata);

            if (array_key_exists($choice->choice_group(), $question->choices)) {
                $question->choices[$choice->choice_group()][$dragdata->no] = $choice;
            } else {
                $question->choices[$choice->choice_group()][1] = $choice;
            }

            end($question->choices[$choice->choice_group()]);
            $choiceindexmap[$dragdata->no] = array($choice->choice_group(),
                    key($question->choices[$choice->choice_group()]));
        }

        $question->places = array();
        $question->rightchoices = array();

        $i = 1;

        foreach ($questiondata->options->drops as $dropdata) {
            list($group, $choiceindex) = $choiceindexmap[$dropdata->choice];
            $dropdata->group = $group;
            $question->places[$dropdata->no] = $this->make_place($dropdata);
            $question->rightchoices[$dropdata->no] = $choiceindex;
        }
    }

    public static function constrain_image_size_in_draft_area($draftitemid, $maxwidth, $maxheight) {
        global $USER;
        $usercontext = context_user::instance($USER->id);
        $fs = get_file_storage();
        $draftfiles = $fs->get_area_files($usercontext->id, 'user', 'draft', $draftitemid, 'id');
        if ($draftfiles) {
            foreach ($draftfiles as $file) {
                if ($file->is_directory()) {
                    continue;
                }
                $imageinfo = $file->get_imageinfo();
                $width    = $imageinfo['width'];
                $height   = $imageinfo['height'];
                $mimetype = $imageinfo['mimetype'];
                switch ($mimetype) {
                    case 'image/jpeg' :
                        $quality = 80;
                        break;
                    case 'image/png' :
                        $quality = 8;
                        break;
                    default :
                        $quality = null;
                }
                $newwidth = min($maxwidth, $width);
                $newheight = min($maxheight, $height);
                if ($newwidth != $width || $newheight != $height) {
                    $newimagefilename = $file->get_filename();
                    $newimagefilename =
                        preg_replace('!\.!', "_{$newwidth}x{$newheight}.", $newimagefilename, 1);
                    $newrecord = new stdClass();
                    $newrecord->contextid = $usercontext->id;
                    $newrecord->component = 'user';
                    $newrecord->filearea  = 'draft';
                    $newrecord->itemid    = $draftitemid;
                    $newrecord->filepath  = '/';
                    $newrecord->filename  = $newimagefilename;
                    $fs->convert_image($newrecord, $file, $newwidth, $newheight, true, $quality);
                    $file->delete();
                }
            }
        }
    }

    /**
     * Convert files into text output in the given format.
     * This method is copied from qformat_default as a quick fix, as the method there is
     * protected.
     * @param array
     * @param string encoding method
     * @return string $string
     */
    public function write_files($files, $indent) {
        if (empty($files)) {
            return '';
        }
        $string = '';
        foreach ($files as $file) {
            if ($file->is_directory()) {
                continue;
            }
            $string .= str_repeat('  ', $indent);
            $string .= '<file name="' . $file->get_filename() . '" encoding="base64">';
            $string .= base64_encode($file->get_content());
            $string .= "</file>\n";
        }
        return $string;
    }

    public function get_possible_responses($questiondata) {
        $question = $this->make_question($questiondata);

        $parts = array();
        foreach ($question->places as $placeno => $place) {
            $choices = array();

            foreach ($question->choices[$place->group] as $i => $choice) {
                $correct = $question->rightchoices[$placeno] == $i;
                $choices[$choice->no] = new question_possible_response($choice->summarise(), $correct ? 1 : 0);
            }
            $choices[null] = question_possible_response::no_response();

            $parts[$placeno] = $choices;
        }

        return $parts;
    }

    public function get_random_guess_score($questiondata) {
        $question = $this->make_question($questiondata);
        return $question->get_random_guess_score();
    }
    public function delete_question($questionid, $contextid) {
        global $DB;
        $DB->delete_records('qtype_'.$this->name(), array('questionid' => $questionid));
        $DB->delete_records('qtype_'.$this->name().'_drags', array('questionid' => $questionid));
        $DB->delete_records('qtype_'.$this->name().'_drops', array('questionid' => $questionid));
        return parent::delete_question($questionid, $contextid);
    }
}
