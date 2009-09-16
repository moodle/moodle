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

        $entryid  = $this->_customdata['id'];
        $existing = $this->_customdata['existing'];
        $sitecontext = $this->_customdata['sitecontext'];

        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'subject', get_string('entrytitle', 'blog'), 'size="60"');
        $mform->addElement('editor', 'summary', get_string('entrybody', 'blog'), null, array('trusttext'=>true, 'subdirs'=>true, 'maxfiles' => -1));

        $mform->setType('subject', PARAM_TEXT);
        $mform->addRule('subject', get_string('emptytitle', 'blog'), 'required', null, 'client');

        $mform->setType('summary', PARAM_RAW);
        $mform->addRule('summary', get_string('emptybody', 'blog'), 'required', null, 'client');
        $mform->setHelpButton('summary', array('writing', 'richtext2'), false, 'editorhelpbutton');

        $mform->addElement('format', 'summaryformat', get_string('format'));

        $mform->addElement('filemanager', 'attachment', get_string('attachment', 'forum'));

        //disable publishstate options that are not allowed
        $publishstates = array();
        $i = 0;

        foreach (blog_entry::get_applicable_publish_states() as $state => $desc) {
            $publishstates[$state] = $desc;   //no maximum was set
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
            $mform->addElement('static', 'assocdescription', '', get_string('assocdescription', 'blog'));
            if (has_capability('moodle/site:doanything', get_context_instance(CONTEXT_USER, $USER->id))) {
                $courses = get_courses('all', 'visible DESC, fullname ASC');
            } else {
                $courses = get_my_courses($USER->id, 'visible DESC, fullname ASC');
            }

            $coursenames[0] = 'none';

            if (!empty($courses)) {

                foreach ($courses as $course) {
                    $coursenames[$course->context->id] = $course->fullname;
                    $modinfo = get_fast_modinfo($course, $USER->id);
                    $coursecontextpath = $DB->get_field('context', 'path', array('id' => $course->context->id));

                    foreach ($modinfo->instances as $modname => $instances) {

                        foreach ($instances as $modid => $mod) {
                            $modcontextid = $DB->get_field_select('context', 'id',
                                'instanceid = '.$mod->id.' AND ' .
                                'contextlevel = ' . CONTEXT_MODULE . ' AND ' .
                                'path LIKE \''.$coursecontextpath.'/%\'');

                            $modstring = $mod->name . ' (' . get_plugin_name($modname) . ')';
                            $this->modnames[$course->context->id][$modcontextid] = $modstring;
                            $allmodnames[$modcontextid] = $course->shortname . " - " . $modstring;
                        }
                    }
                }
            }
            $mform->addElement('select', 'courseassoc', get_string('course'), $coursenames, 'onchange="addCourseAssociations()"');
            $mform->setAdvanced('courseassoc');
            $selectassoc = &$mform->addElement('select', 'modassoc', get_string('managemodules'), $allmodnames);
            $mform->setAdvanced('modassoc');
            $selectassoc->setMultiple(true);
            $PAGE->requires->data_for_js('blog_edit_form_modnames', $this->modnames);

        }

        $this->add_action_buttons();
        $mform->addElement('hidden', 'action');
        $mform->setType('action', PARAM_ACTION);
        $mform->setDefault('action', '');

        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);

        $mform->addElement('hidden', 'entryid');
        $mform->setType('entryid', PARAM_INT);
        $mform->setDefault('entryid', $entryid);

        $mform->addElement('hidden', 'modid');
        $mform->setType('modid', PARAM_INT);
        $mform->setDefault('modid', 0);

        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);
        $mform->setDefault('courseid', 0);

        // $this->set_data($existing);
    }

    function validation($data, $files) {
        global $CFG, $DB, $USER;

        $errors = array();

        if (empty($data['courseassoc']) && ($data['publishstate'] == 'course' || $data['publishstate'] == 'group') && !empty($CFG->useblogassociations)) {
            return array('publishstate' => get_string('mustassociatecourse', 'blog'));
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
                    $coursecontext = $DB->get_record('context', array('id' => $path[(count($path) - 2)]));

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
