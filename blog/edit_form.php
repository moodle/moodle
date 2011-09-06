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

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir.'/formslib.php');

class blog_edit_form extends moodleform {
    public $modnames = array();

    function definition() {
        global $CFG, $DB;

        $mform =& $this->_form;

        $entry = $this->_customdata['entry'];
        $courseid = $this->_customdata['courseid'];
        $modid = $this->_customdata['modid'];
        $summaryoptions = $this->_customdata['summaryoptions'];
        $attachmentoptions = $this->_customdata['attachmentoptions'];
        $sitecontext = $this->_customdata['sitecontext'];

        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'subject', get_string('entrytitle', 'blog'), 'size="60"');
        $mform->addElement('editor', 'summary_editor', get_string('entrybody', 'blog'), null, $summaryoptions);

        $mform->setType('subject', PARAM_TEXT);
        $mform->addRule('subject', get_string('emptytitle', 'blog'), 'required', null, 'client');

        $mform->setType('summary_editor', PARAM_RAW);
        $mform->addRule('summary_editor', get_string('emptybody', 'blog'), 'required', null, 'client');

        $mform->addElement('filemanager', 'attachment_filemanager', get_string('attachment', 'forum'), null, $attachmentoptions);

        //disable publishstate options that are not allowed
        $publishstates = array();
        $i = 0;

        foreach (blog_entry::get_applicable_publish_states() as $state => $desc) {
            $publishstates[$state] = $desc;   //no maximum was set
            $i++;
        }

        $mform->addElement('select', 'publishstate', get_string('publishto', 'blog'), $publishstates);
        $mform->addHelpButton('publishstate', 'publishto', 'blog');
        $mform->setDefault('publishstate', 0);

        if (!empty($CFG->usetags)) {
            $mform->addElement('header', 'tagshdr', get_string('tags', 'tag'));
            $mform->addElement('tags', 'tags', get_string('tags'));
        }

        $allmodnames = array();

        if (!empty($CFG->useblogassociations)) {
            if ((!empty($entry->courseassoc) || (!empty($courseid) && empty($modid))) && has_capability('moodle/blog:associatecourse', $sitecontext)) {
                if (!empty($courseid)) {
                    $course = $DB->get_record('course', array('id' => $courseid));
                    $mform->addElement('header', 'assochdr', get_string('associations', 'blog'));
                    $context = get_context_instance(CONTEXT_COURSE, $courseid);
                    $a = new stdClass();
                    $a->coursename = format_string($course->fullname, true, array('context' => $context));
                    $contextid = $context->id;
                } else {
                    $sql = 'SELECT fullname FROM {course} cr LEFT JOIN {context} ct ON ct.instanceid = cr.id WHERE ct.id = ?';
                    $a = new stdClass();
                    $a->coursename = $DB->get_field_sql($sql, array($entry->courseassoc));
                    $contextid = $entry->courseassoc;
                }

                $mform->addElement('advcheckbox', 'courseassoc', get_string('associatewithcourse', 'blog', $a), null, null, array(0, $contextid));
                $mform->setDefault('courseassoc', $contextid);
            } else if ((!empty($entry->modassoc) || !empty($modid)) && has_capability('moodle/blog:associatemodule', $sitecontext)) {
                if (!empty($modid)) {
                    $mod = get_coursemodule_from_id(false, $modid);
                    $a = new stdClass();
                    $a->modtype = get_string('modulename', $mod->modname);
                    $a->modname = $mod->name;
                    $context = get_context_instance(CONTEXT_MODULE, $modid);
                } else {
                    $context = $DB->get_record('context', array('id' => $entry->modassoc));
                    $cm = $DB->get_record('course_modules', array('id' => $context->instanceid));
                    $a = new stdClass();
                    $a->modtype = $DB->get_field('modules', 'name', array('id' => $cm->module));
                    $a->modname = $DB->get_field($a->modtype, 'name', array('id' => $cm->instance));
                }

                $mform->addElement('header', 'assochdr', get_string('associations', 'blog'));
                $mform->addElement('advcheckbox', 'modassoc', get_string('associatewithmodule', 'blog', $a), null, null, array(0, $context->id));
                $mform->setDefault('modassoc', $context->id);
            }
        }

        $this->add_action_buttons();
        $mform->addElement('hidden', 'action');
        $mform->setType('action', PARAM_ACTION);
        $mform->setDefault('action', '');

        $mform->addElement('hidden', 'entryid');
        $mform->setType('entryid', PARAM_INT);
        $mform->setDefault('entryid', $entry->id);

        $mform->addElement('hidden', 'modid');
        $mform->setType('modid', PARAM_INT);
        $mform->setDefault('modid', $modid);

        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);
        $mform->setDefault('courseid', $courseid);
    }

    function validation($data, $files) {
        global $CFG, $DB, $USER;

        $errors = array();
        $sitecontext = get_context_instance(CONTEXT_SYSTEM);

        // validate course association
        if (!empty($data['courseassoc']) && has_capability('moodle/blog:associatecourse', $sitecontext)) {
            $coursecontext = $DB->get_record('context', array('id' => $data['courseassoc'], 'contextlevel' => CONTEXT_COURSE));

            if ($coursecontext)  {
                if (!is_enrolled($coursecontext) and !is_viewing($coursecontext)) {
                    $errors['courseassoc'] = get_string('studentnotallowed', '', fullname($USER, true));
                }
            } else {
                $errors['courseassoc'] = get_string('invalidcontextid', 'blog');
            }
        }

        // validate mod association
        if (!empty($data['modassoc'])) {
            $modcontextid = $data['modassoc'];

            $modcontext = $DB->get_record('context', array('id' => $modcontextid, 'contextlevel' => CONTEXT_MODULE));

            if ($modcontext) {
                // get context of the mod's course
                $path = explode('/', $modcontext->path);
                $coursecontext = $DB->get_record('context', array('id' => $path[(count($path) - 2)]));

                // ensure only one course is associated
                if (!empty($data['courseassoc'])) {
                    if ($data['courseassoc'] != $coursecontext->id) {
                        $errors['modassoc'] = get_string('onlyassociateonecourse', 'blog');
                    }
                } else {
                    $data['courseassoc'] = $coursecontext->id;
                }

                // ensure the user has access to each mod's course
                if (!is_enrolled($modcontext) and !is_viewing($modcontext)) {
                    $errors['modassoc'] = get_string('studentnotallowed', '', fullname($USER, true));
                }
            } else {
                $errors['modassoc'] = get_string('invalidcontextid', 'blog');
            }
        }

        if ($errors) {
            return $errors;
        }
        return true;
    }
}
