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
 * This file contains the definition for the library class for comment feedback plugin
 *
 * @package   assignfeedback_comments
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Library class for comment feedback plugin extending feedback plugin base class.
 *
 * @package   assignfeedback_comments
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_feedback_comments extends assign_feedback_plugin {

    /**
     * Get the name of the online comment feedback plugin.
     * @return string
     */
    public function get_name() {
        return get_string('pluginname', 'assignfeedback_comments');
    }

    /**
     * Get the feedback comment from the database.
     *
     * @param int $gradeid
     * @return stdClass|false The feedback comments for the given grade if it exists.
     *                        False if it doesn't.
     */
    public function get_feedback_comments($gradeid) {
        global $DB;
        return $DB->get_record('assignfeedback_comments', array('grade'=>$gradeid));
    }

    /**
     * Get quickgrading form elements as html.
     *
     * @param int $userid The user id in the table this quickgrading element relates to
     * @param mixed $grade - The grade data - may be null if there are no grades for this user (yet)
     * @return mixed - A html string containing the html form elements required for quickgrading
     */
    public function get_quickgrading_html($userid, $grade) {
        $commenttext = '';
        if ($grade) {
            $feedbackcomments = $this->get_feedback_comments($grade->id);
            if ($feedbackcomments) {
                $commenttext = $feedbackcomments->commenttext;
            }
        }

        $pluginname = get_string('pluginname', 'assignfeedback_comments');
        $labeloptions = array('for'=>'quickgrade_comments_' . $userid,
                              'class'=>'accesshide');
        $textareaoptions = array('name'=>'quickgrade_comments_' . $userid,
                                 'id'=>'quickgrade_comments_' . $userid,
                                 'class'=>'quickgrade');
        return html_writer::tag('label', $pluginname, $labeloptions) .
               html_writer::tag('textarea', $commenttext, $textareaoptions);
    }

    /**
     * Has the plugin quickgrading form element been modified in the current form submission?
     *
     * @param int $userid The user id in the table this quickgrading element relates to
     * @param stdClass $grade The grade
     * @return boolean - true if the quickgrading form element has been modified
     */
    public function is_quickgrading_modified($userid, $grade) {
        $commenttext = '';
        if ($grade) {
            $feedbackcomments = $this->get_feedback_comments($grade->id);
            if ($feedbackcomments) {
                $commenttext = $feedbackcomments->commenttext;
            }
        }
        // Note that this handles the difference between empty and not in the quickgrading
        // form at all (hidden column).
        $newvalue = optional_param('quickgrade_comments_' . $userid, false, PARAM_TEXT);
        return ($newvalue !== false) && ($newvalue != $commenttext);
    }


    /**
     * Override to indicate a plugin supports quickgrading.
     *
     * @return boolean - True if the plugin supports quickgrading
     */
    public function supports_quickgrading() {
        return true;
    }

    /**
     * Return a list of the text fields that can be imported/exported by this plugin.
     *
     * @return array An array of field names and descriptions. (name=>description, ...)
     */
    public function get_editor_fields() {
        return array('comments' => get_string('pluginname', 'assignfeedback_comments'));
    }

    /**
     * Get the saved text content from the editor.
     *
     * @param string $name
     * @param int $gradeid
     * @return string
     */
    public function get_editor_text($name, $gradeid) {
        if ($name == 'comments') {
            $feedbackcomments = $this->get_feedback_comments($gradeid);
            if ($feedbackcomments) {
                return $feedbackcomments->commenttext;
            }
        }

        return '';
    }

    /**
     * Get the saved text content from the editor.
     *
     * @param string $name
     * @param string $value
     * @param int $gradeid
     * @return string
     */
    public function set_editor_text($name, $value, $gradeid) {
        global $DB;

        if ($name == 'comments') {
            $feedbackcomment = $this->get_feedback_comments($gradeid);
            if ($feedbackcomment) {
                $feedbackcomment->commenttext = $value;
                return $DB->update_record('assignfeedback_comments', $feedbackcomment);
            } else {
                $feedbackcomment = new stdClass();
                $feedbackcomment->commenttext = $value;
                $feedbackcomment->commentformat = FORMAT_HTML;
                $feedbackcomment->grade = $gradeid;
                $feedbackcomment->assignment = $this->assignment->get_instance()->id;
                return $DB->insert_record('assignfeedback_comments', $feedbackcomment) > 0;
            }
        }

        return false;
    }

    /**
     * Save quickgrading changes.
     *
     * @param int $userid The user id in the table this quickgrading element relates to
     * @param stdClass $grade The grade
     * @return boolean - true if the grade changes were saved correctly
     */
    public function save_quickgrading_changes($userid, $grade) {
        global $DB;
        $feedbackcomment = $this->get_feedback_comments($grade->id);
        $feedbackpresent = optional_param('quickgrade_comments_' . $userid, false, PARAM_TEXT) !== false;
        if (!$feedbackpresent) {
            // Nothing to save (e.g. hidden column).
            return true;
        }
        if ($feedbackcomment) {
            $feedbackcomment->commenttext = optional_param('quickgrade_comments_' . $userid, '', PARAM_TEXT);
            return $DB->update_record('assignfeedback_comments', $feedbackcomment);
        } else {
            $feedbackcomment = new stdClass();
            $feedbackcomment->commenttext = optional_param('quickgrade_comments_' . $userid, '', PARAM_TEXT);
            $feedbackcomment->commentformat = FORMAT_HTML;
            $feedbackcomment->grade = $grade->id;
            $feedbackcomment->assignment = $this->assignment->get_instance()->id;
            return $DB->insert_record('assignfeedback_comments', $feedbackcomment) > 0;
        }
    }

    /**
     * Get form elements for the grading page
     *
     * @param stdClass|null $grade
     * @param MoodleQuickForm $mform
     * @param stdClass $data
     * @return bool true if elements were added to the form
     */
    public function get_form_elements($grade, MoodleQuickForm $mform, stdClass $data) {
        if ($grade) {
            $feedbackcomments = $this->get_feedback_comments($grade->id);
            if ($feedbackcomments) {
                $data->assignfeedbackcomments_editor['text'] = $feedbackcomments->commenttext;
                $data->assignfeedbackcomments_editor['format'] = $feedbackcomments->commentformat;
            }
        }

        $mform->addElement('editor', 'assignfeedbackcomments_editor', html_writer::tag('span', $this->get_name(),
            array('class' => 'accesshide')), null, null);

        return true;
    }

    /**
     * Saving the comment content into database.
     *
     * @param stdClass $grade
     * @param stdClass $data
     * @return bool
     */
    public function save(stdClass $grade, stdClass $data) {
        global $DB;
        $feedbackcomment = $this->get_feedback_comments($grade->id);
        if ($feedbackcomment) {
            $feedbackcomment->commenttext = $data->assignfeedbackcomments_editor['text'];
            $feedbackcomment->commentformat = $data->assignfeedbackcomments_editor['format'];
            return $DB->update_record('assignfeedback_comments', $feedbackcomment);
        } else {
            $feedbackcomment = new stdClass();
            $feedbackcomment->commenttext = $data->assignfeedbackcomments_editor['text'];
            $feedbackcomment->commentformat = $data->assignfeedbackcomments_editor['format'];
            $feedbackcomment->grade = $grade->id;
            $feedbackcomment->assignment = $this->assignment->get_instance()->id;
            return $DB->insert_record('assignfeedback_comments', $feedbackcomment) > 0;
        }
    }

    /**
     * Display the comment in the feedback table.
     *
     * @param stdClass $grade
     * @param bool $showviewlink Set to true to show a link to view the full feedback
     * @return string
     */
    public function view_summary(stdClass $grade, & $showviewlink) {
        $feedbackcomments = $this->get_feedback_comments($grade->id);
        if ($feedbackcomments) {
            $text = format_text($feedbackcomments->commenttext,
                                $feedbackcomments->commentformat,
                                array('context' => $this->assignment->get_context()));
            $short = shorten_text($text, 140);

            // Show the view all link if the text has been shortened.
            $showviewlink = $short != $text;
            return $short;
        }
        return '';
    }

    /**
     * Display the comment in the feedback table.
     *
     * @param stdClass $grade
     * @return string
     */
    public function view(stdClass $grade) {
        $feedbackcomments = $this->get_feedback_comments($grade->id);
        if ($feedbackcomments) {
            return format_text($feedbackcomments->commenttext,
                               $feedbackcomments->commentformat,
                               array('context' => $this->assignment->get_context()));
        }
        return '';
    }

    /**
     * Return true if this plugin can upgrade an old Moodle 2.2 assignment of this type
     * and version.
     *
     * @param string $type old assignment subtype
     * @param int $version old assignment version
     * @return bool True if upgrade is possible
     */
    public function can_upgrade($type, $version) {

        if (($type == 'upload' || $type == 'uploadsingle' ||
             $type == 'online' || $type == 'offline') && $version >= 2011112900) {
            return true;
        }
        return false;
    }

    /**
     * Upgrade the settings from the old assignment to the new plugin based one
     *
     * @param context $oldcontext - the context for the old assignment
     * @param stdClass $oldassignment - the data for the old assignment
     * @param string $log - can be appended to by the upgrade
     * @return bool was it a success? (false will trigger a rollback)
     */
    public function upgrade_settings(context $oldcontext, stdClass $oldassignment, & $log) {
        return true;
    }

    /**
     * Upgrade the feedback from the old assignment to the new one
     *
     * @param context $oldcontext - the database for the old assignment context
     * @param stdClass $oldassignment The data record for the old assignment
     * @param stdClass $oldsubmission The data record for the old submission
     * @param stdClass $grade The data record for the new grade
     * @param string $log Record upgrade messages in the log
     * @return bool true or false - false will trigger a rollback
     */
    public function upgrade(context $oldcontext,
                            stdClass $oldassignment,
                            stdClass $oldsubmission,
                            stdClass $grade,
                            & $log) {
        global $DB;

        $feedbackcomments = new stdClass();
        $feedbackcomments->commenttext = $oldsubmission->submissioncomment;
        $feedbackcomments->commentformat = FORMAT_HTML;

        $feedbackcomments->grade = $grade->id;
        $feedbackcomments->assignment = $this->assignment->get_instance()->id;
        if (!$DB->insert_record('assignfeedback_comments', $feedbackcomments) > 0) {
            $log .= get_string('couldnotconvertgrade', 'mod_assign', $grade->userid);
            return false;
        }

        return true;
    }

    /**
     * If this plugin adds to the gradebook comments field, it must specify the format of the text
     * of the comment
     *
     * Only one feedback plugin can push comments to the gradebook and that is chosen by the assignment
     * settings page.
     *
     * @param stdClass $grade The grade
     * @return int
     */
    public function format_for_gradebook(stdClass $grade) {
        $feedbackcomments = $this->get_feedback_comments($grade->id);
        if ($feedbackcomments) {
            return $feedbackcomments->commentformat;
        }
        return FORMAT_MOODLE;
    }

    /**
     * If this plugin adds to the gradebook comments field, it must format the text
     * of the comment
     *
     * Only one feedback plugin can push comments to the gradebook and that is chosen by the assignment
     * settings page.
     *
     * @param stdClass $grade The grade
     * @return string
     */
    public function text_for_gradebook(stdClass $grade) {
        $feedbackcomments = $this->get_feedback_comments($grade->id);
        if ($feedbackcomments) {
            return $feedbackcomments->commenttext;
        }
        return '';
    }

    /**
     * The assignment has been deleted - cleanup
     *
     * @return bool
     */
    public function delete_instance() {
        global $DB;
        // Will throw exception on failure.
        $DB->delete_records('assignfeedback_comments',
                            array('assignment'=>$this->assignment->get_instance()->id));
        return true;
    }

    /**
     * Returns true if there are no feedback comments for the given grade.
     *
     * @param stdClass $grade
     * @return bool
     */
    public function is_empty(stdClass $grade) {
        return $this->view($grade) == '';
    }

    /**
     * Return a description of external params suitable for uploading an feedback comment from a webservice.
     *
     * @return external_description|null
     */
    public function get_external_parameters() {
        $editorparams = array('text' => new external_value(PARAM_TEXT, 'The text for this feedback.'),
                              'format' => new external_value(PARAM_INT, 'The format for this feedback'));
        $editorstructure = new external_single_structure($editorparams);
        return array('assignfeedbackcomments_editor' => $editorstructure);
    }

}
