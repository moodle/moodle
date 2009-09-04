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

require_once($CFG->libdir.'/formslib.php');

class blog_edit_form extends moodleform {
    public $modnames = array();

    function definition() {
        global $CFG, $COURSE, $USER, $DB, $PAGE;

        $mform    =& $this->_form;

        $existing       = $this->_customdata['existing'];
        $summaryoptions = $this->_customdata['textfieldoptions'];

        if (!empty($this->_customdata['assignmentdata'])) {
            $assignmentdata = $this->_customdata['assignmentdata'];
        }

        $existing = $this->_customdata['existing'];
        $sitecontext = $this->_customdata['sitecontext'];

        //determine if content elements should be deactivated for a past due blog assignment
        $noedit = false;
        if (!empty($assignmentdata)) {
            if ((time() > $assignmentdata->timedue && $assignmentdata->preventlate) || $assignmentdata->grade != -1) {
                $noedit = true;
            }
        }

        $mform->addElement('header', 'general', get_string('general', 'form'));

        if ($noedit) { //show disabled form elements, but provide hidden elements so that the data is transferred
            $mform->addElement('text', 'fakesubject', get_string('entrytitle', 'blog'), array('size'=>60, 'disabled'=>'disabled'));
            $mform->addElement('textarea', 'fakesummary', get_string('entrybody', 'blog'), array('rows'=>25, 'cols'=>40, 'disabled'=>'disabled'));
            $mform->setHelpButton('fakesummary', array('writing', 'richtext'), false, 'editorhelpbutton');
            $mform->addElement('hidden', 'subject');
            $mform->addElement('hidden', 'summary');

        } else {  //insert normal form elements
            $mform->addElement('text', 'subject', get_string('entrytitle', 'blog'), 'size="60"');
            $textfieldoptions = array('trusttext'=>true, 'subdirs'=>true);
            $mform->addElement('editor', 'summary_editor', get_string('entrybody', 'blog'), null, $summaryoptions);
        }

        $mform->setType('subject', PARAM_TEXT);
        $mform->addRule('subject', get_string('emptytitle', 'blog'), 'required', null, 'client');

        $mform->setType('summary_editor', PARAM_RAW);
        $mform->addRule('summary_editor', get_string('emptybody', 'blog'), 'required', null, 'client');
        $mform->setHelpButton('summary_editor', array('writing', 'richtext2'), false, 'editorhelpbutton');

        $mform->addElement('format', 'format', get_string('format'));

        $mform->addElement('file', 'attachment', get_string('attachment', 'forum'));

        //disable publishstate options that are not allowed
        $publishstates = array();
        $i = 0;

        foreach (blog_entry::get_applicable_publish_states() as $state => $desc) {
            if (!empty($assignmentdata)) {
                if ($i <= $assignmentdata->var2)  { //var2 is the maximum publish state allowed
                    $publishstates[$state] = $desc;
                }
            } else {
                $publishstates[$state] = $desc;   //no maximum was set
            }

            $i++;
        }

        $mform->addElement('select', 'publishstate', get_string('publishto', 'blog'), $publishstates);
        $mform->setHelpButton('publishstate', array('publish_state', get_string('publishto', 'blog'), 'blog'));


        if (!empty($CFG->usetags)) {
            $mform->addElement('header', 'tagshdr', get_string('tags', 'tag'));
            $mform->addElement('tags', 'tags', get_string('tags'));
        }

        $allmodnames = array();

        if (!empty($CFG->useblogassociations)) {
            $mform->addElement('header', 'assochdr', get_string('associations', 'blog'));

            if (has_capability('moodle/site:doanything', get_context_instance(CONTEXT_USER, $USER->id))) {
                $courses = get_courses('all', 'visible DESC, fullname ASC');
            } else {
                $courses = get_my_courses($USER->id, 'visible DESC, fullname ASC');
            }

            $course_names[0] = 'none';

            if (!empty($courses)) {

                foreach ($courses as $course) {
                    $course_names[$course->context->id] = $course->fullname;
                    $modinfo = get_fast_modinfo($course, $USER->id);
                    $course_context_path = $DB->get_field('context', 'path', array('id' => $course->context->id));

                    foreach ($modinfo->instances as $modname => $instances) {

                        foreach ($instances as $modid => $mod) {
                            $mod_context_id = $DB->get_field_select('context', 'id',
                                'instanceid = '.$mod->id.' AND ' .
                                'contextlevel = ' . CONTEXT_MODULE . ' AND ' .
                                'path LIKE \''.$course_context_path.'/%\'');

                            $mod_string = $mod->name . ' (' . get_plugin_name($modname) . ')';
                            $this->modnames[$course->context->id][$mod_context_id] = $mod_string;
                            $allmodnames[$mod_context_id] = $course->shortname . " - " . $mod_string;
                        }
                    }
                }
            }
            $mform->addElement('select', 'courseassoc', get_string('course'), $course_names, 'onchange="addCourseAssociations()"');
            $selectassoc = &$mform->addElement('select', 'modassoc', get_string('managemodules'), $allmodnames);
            $selectassoc->setMultiple(true);
            $PAGE->requires->data_for_js('blog_edit_form_modnames', $this->modnames);

        }

        $this->add_action_buttons();

        $mform->addElement('hidden', 'action');
        $mform->setType('action', PARAM_ACTION);
        $mform->setDefault('action', '');

        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->setDefault('id', 0);

        $mform->addElement('hidden', 'modid');
        $mform->setType('modid', PARAM_INT);
        $mform->setDefault('modid', 0);

        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);
        $mform->setDefault('courseid', 0);

        if (!empty($assignmentdata)) {   //dont allow associations for blog assignments
            $courseassoc = $mform->getElement('courseassoc');
            $modassoc = $mform->getElement('modassoc');
            $courseassoc->updateAttributes(array('disabled' => 'disabled'));
            $modassoc->updateAttributes(array('disabled' => 'disabled'));
        }

        if ($noedit) {  //disable some other fields when editing is not allowed
            $subject = $mform->getElement('subject');
            $summary = $mform->getElement('summary');
            $attachment = $mform->getElement('attachment');
            $format = $mform->getElement('format');
            $attachment->updateAttributes(array('disabled' => 'disabled'));
            $format->updateAttributes(array('disabled' => 'disabled'));
        }

        $this->set_data($existing);
    }

    function validation($data, $files) {
        global $CFG, $DB, $USER;

        $errors = array();

        //check to see if it's part of a submitted blog assignment
        if ($blogassignment = $DB->get_record_sql('SELECT a.timedue, a.preventlate, a.emailteachers, a.var2, asub.grade
                                          FROM {assignment} a, {assignment_submissions} as asub WHERE
                                          a.id = asub.assignment AND userid = '.$USER->id.' AND a.assignmenttype = \'blog\'
                                          AND asub.data1 = \''.$data['id'].'\'')) {

            $original = $DB->get_record('post', array('id' => $data['id']));
            //don't allow updates of the summary, subject, or attachment
            $changed = ($original->summary != $data['summary'] ||
                        $original->subject != $data['subject'] ||
                        !empty($files));


            //determine numeric value for publish state (for comparison purposes)
            $postaccess = -1;
            $i=0;

            foreach (blog_applicable_publish_states() as $state => $desc) {
                if ($state == $data['publishstate']) {
                    $postaccess = $i;
                }
                $publishstates[$i++] = $state;
            }

            //send an error if improper changes are being made
            if (($changed and time() > $blogassignment->timedue and $blogassignment->preventlate = 1) or
                ($changed and $blogassignment->grade != -1) or
                (time() < $blogassignment->timedue and ($postaccess > $blogassignment->var2 || $postaccess == -1))) {

                //too late to edit this entry
                if ($original->subject != $data['subject']) {
                    $errors['subject'] = get_string('canteditblogassignment', 'blog');
                }
                if ($original->summary != $data['summary']) {
                    $errors['summary'] = get_string('canteditblogassignment', 'blog');
                }
                if (!empty($files)) {
                    $errors['attachment'] = get_string('canteditblogassignment', 'blog');
                }
            }

            //insure the publishto value is within proper constraints

            if (time() < $blogassignment->timedue and ($postaccess > $blogassignment->var2 || $postaccess == -1)) {
                $errors['publishto'] = get_string('canteditblogassignment', 'blog');
            }

        } else {
            if (empty($data['courseassoc']) && ($data['publishstate'] == 'course' || $data['publishstate'] == 'group') && !empty($CFG->useblogassociations)) {
                return array('publishstate' => get_string('mustassociatecourse', 'blog'));
            }
        }


        //validate course association
        if (!empty($data['courseassoc'])) {
            $coursecontext = $DB->get_record('context', array('id' => $data['courseassoc'], 'contextlevel' => CONTEXT_COURSE));

            if ($coursecontext)  {    //insure associated course has a valid context id
                //insure the user has access to this course

                if (!has_capability('moodle/course:view', $coursecontext, $USER->id)) {
                    $errors['courseassoc'] = get_string('studentnotallowed', '', fullname($USER, true));
                }
            } else {
                $errors['courseassoc'] = get_string('invalidcontextid', 'blog');
            }
        }

        //validate mod associations
        if (!empty($data['modassoc'])) {
            //insure mods are valid

            foreach ($data['modassoc'] as $modid) {
                $modcontext = $DB->get_record('context', array('id' => $modid, 'contextlevel' => CONTEXT_MODULE));

                if ($modcontext) {  //insure associated mod has a valid context id
                    //get context of the mod's course
                    $path = split('/', $modcontext->path);
                    $coursecontext = $DB->get_record('context', array('id' => $path[3]));

                    //insure only one course is associated
                    if (!empty($data['courseassoc'])) {
                        if ($data['courseassoc'] != $coursecontext->id) {
                            $errors['modassoc'] = get_string('onlyassociateonecourse', 'blog');
                        }
                    } else {
                        $data['courseassoc'] = $coursecontext->id;
                    }

                    //insure the user has access to each mod's course
                    if (!has_capability('moodle/course:view', $coursecontext)) {
                        $errors['modassoc'] = get_string('studentnotallowed', '', fullname($USER, true));
                    }
                } else {
                    $errors['modassoc'] = get_string('invalidcontextid', 'blog');
                }
            }
        }

        if ($errors) {
            return $errors;
        }
        return true;
    }

    /**
     * This function sets up options of otag select element. This is called from definition and also
     * after adding new official tags with the add tag button.
     *
     */
    function otags_select_setup(){
        global $DB;

        $mform =& $this->_form;
        if ($otagsselect =& $mform->getElement('otags')) {
            $otagsselect->removeOptions();
        }
        $namefield = empty($CFG->keeptagnamecase) ? 'name' : 'rawname';
        if ($otags = $DB->get_records_sql_menu("SELECT id, $namefield FROM {tag} WHERE tagtype='official' ORDER by $namefield ASC")) {
            $otagsselect->loadArray($otags);
        }

    }

}
