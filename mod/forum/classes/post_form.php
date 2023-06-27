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
 * File containing the form definition to post in the forum.
 *
 * @package   mod_forum
 * @copyright Jamie Pratt <me@jamiep.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/repository/lib.php');

/**
 * Class to post in a forum.
 *
 * @package   mod_forum
 * @copyright Jamie Pratt <me@jamiep.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_forum_post_form extends moodleform {

    /**
     * Returns the options array to use in filemanager for forum attachments
     *
     * @param stdClass $forum
     * @return array
     */
    public static function attachment_options($forum) {
        global $COURSE, $PAGE, $CFG;
        $maxbytes = get_user_max_upload_file_size($PAGE->context, $CFG->maxbytes, $COURSE->maxbytes, $forum->maxbytes);
        return array(
            'subdirs' => 0,
            'maxbytes' => $maxbytes,
            'maxfiles' => $forum->maxattachments,
            'accepted_types' => '*',
            'return_types' => FILE_INTERNAL | FILE_CONTROLLED_LINK
        );
    }

    /**
     * Returns the options array to use in forum text editor
     *
     * @param context_module $context
     * @param int $postid post id, use null when adding new post
     * @return array
     */
    public static function editor_options(context_module $context, $postid) {
        global $COURSE, $PAGE, $CFG;
        // TODO: add max files and max size support
        $maxbytes = get_user_max_upload_file_size($PAGE->context, $CFG->maxbytes, $COURSE->maxbytes);
        return array(
            'maxfiles' => EDITOR_UNLIMITED_FILES,
            'maxbytes' => $maxbytes,
            'trusttext'=> true,
            'return_types'=> FILE_INTERNAL | FILE_EXTERNAL,
            'subdirs' => file_area_contains_subdirs($context, 'mod_forum', 'post', $postid)
        );
    }

    /**
     * Form definition
     *
     * @return void
     */
    function definition() {
        global $CFG, $OUTPUT;

        $mform =& $this->_form;

        $course = $this->_customdata['course'];
        $cm = $this->_customdata['cm'];
        $coursecontext = $this->_customdata['coursecontext'];
        $modcontext = $this->_customdata['modcontext'];
        $forum = $this->_customdata['forum'];
        $post = $this->_customdata['post'];
        $subscribe = $this->_customdata['subscribe'];
        $edit = $this->_customdata['edit'];
        $thresholdwarning = $this->_customdata['thresholdwarning'];
        $canreplyprivately = array_key_exists('canreplyprivately', $this->_customdata) ?
            $this->_customdata['canreplyprivately'] : false;
        $inpagereply = $this->_customdata['inpagereply'] ?? false;

        if (!$inpagereply) {
            // Fill in the data depending on page params later using set_data.
            $mform->addElement('header', 'general', '');
        }

        // If there is a warning message and we are not editing a post we need to handle the warning.
        if (!empty($thresholdwarning) && !$edit) {
            // Here we want to display a warning if they can still post but have reached the warning threshold.
            if ($thresholdwarning->canpost) {
                $message = get_string($thresholdwarning->errorcode, $thresholdwarning->module, $thresholdwarning->additional);
                $mform->addElement('html', $OUTPUT->notification($message));
            }
        }

        $mform->addElement('text', 'subject', get_string('subject', 'forum'), 'size="48"');
        $mform->setType('subject', PARAM_TEXT);
        $mform->addRule('subject', get_string('required'), 'required', null, 'client');
        $mform->addRule('subject', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        $mform->addElement('editor', 'message', get_string('message', 'forum'), null, self::editor_options($modcontext, (empty($post->id) ? null : $post->id)));
        $mform->setType('message', PARAM_RAW);
        $mform->addRule('message', get_string('required'), 'required', null, 'client');

        if (!$inpagereply) {
            $manageactivities = has_capability('moodle/course:manageactivities', $coursecontext);

            if (\mod_forum\subscriptions::is_forcesubscribed($forum)) {
                $mform->addElement('checkbox', 'discussionsubscribe', get_string('discussionsubscription', 'forum'));
                $mform->freeze('discussionsubscribe');
                $mform->setDefaults('discussionsubscribe', 0);
                $mform->addHelpButton('discussionsubscribe', 'forcesubscribed', 'forum');

            } else if (\mod_forum\subscriptions::subscription_disabled($forum) && !$manageactivities) {
                $mform->addElement('checkbox', 'discussionsubscribe', get_string('discussionsubscription', 'forum'));
                $mform->freeze('discussionsubscribe');
                $mform->setDefaults('discussionsubscribe', 0);
                $mform->addHelpButton('discussionsubscribe', 'disallowsubscription', 'forum');

            } else {
                $mform->addElement('checkbox', 'discussionsubscribe', get_string('discussionsubscription', 'forum'));
                $mform->addHelpButton('discussionsubscribe', 'discussionsubscription', 'forum');
            }

            if (forum_can_create_attachment($forum, $modcontext)) {
                $mform->addElement('filemanager', 'attachments', get_string('attachment', 'forum'), null,
                    self::attachment_options($forum));
                $mform->addHelpButton('attachments', 'attachment', 'forum');
            }

            if (!$post->parent && has_capability('mod/forum:pindiscussions', $modcontext)) {
                $mform->addElement('checkbox', 'pinned', get_string('discussionpinned', 'forum'));
                $mform->addHelpButton('pinned', 'discussionpinned', 'forum');
            }

            if (empty($post->id) && $manageactivities) {
                $mform->addElement('checkbox', 'mailnow', get_string('mailnow', 'forum'));
            }

            if ((empty($post->id) && $canreplyprivately) || (!empty($post) && !empty($post->privatereplyto))) {
                // Only show the option to change private reply settings if this is a new post and the user can reply
                // privately, or if this is already private reply, in which case the state is shown but is not editable.
                $mform->addElement('checkbox', 'isprivatereply', get_string('privatereply', 'forum'));
                $mform->addHelpButton('isprivatereply', 'privatereply', 'forum');
                if (!empty($post->privatereplyto)) {
                    $mform->setDefault('isprivatereply', 1);
                    $mform->freeze('isprivatereply');
                }
            }

            if ($groupmode = groups_get_activity_groupmode($cm, $course)) {
                $groupdata = groups_get_activity_allowed_groups($cm);
                $groupinfo = array();
                foreach ($groupdata as $groupid => $group) {
                    // Check whether this user can post in this group.
                    // We must make this check because all groups are returned for a visible grouped activity.
                    if (forum_user_can_post_discussion($forum, $groupid, null, $cm, $modcontext)) {
                        // Build the data for the groupinfo select.
                        $groupinfo[$groupid] = $group->name;
                    } else {
                        unset($groupdata[$groupid]);
                    }
                }
                $groupcount = count($groupinfo);

                // Check whether a user can post to all of their own groups.

                // Posts to all of my groups are copied to each group that the user is a member of. Certain conditions must be met.
                // 1) It only makes sense to allow this when a user is in more than one group.
                // Note: This check must come before we consider adding accessallgroups, because that is not a real group.
                $canposttoowngroups = empty($post->edit) && $groupcount > 1;

                // 2) Important: You can *only* post to multiple groups for a top level post. Never any reply.
                $canposttoowngroups = $canposttoowngroups && empty($post->parent);

                // 3) You also need the canposttoowngroups capability.
                $canposttoowngroups = $canposttoowngroups && has_capability('mod/forum:canposttomygroups', $modcontext);
                if ($canposttoowngroups) {
                    // This user is in multiple groups, and can post to all of their own groups.
                    // Note: This is not the same as accessallgroups. This option will copy a post to all groups that a
                    // user is a member of.
                    $mform->addElement('checkbox', 'posttomygroups', get_string('posttomygroups', 'forum'));
                    $mform->addHelpButton('posttomygroups', 'posttomygroups', 'forum');
                    $mform->disabledIf('groupinfo', 'posttomygroups', 'checked');
                }

                // Check whether this user can post to all groups.
                // Posts to the 'All participants' group go to all groups, not to each group in a list.
                // It makes sense to allow this, even if there currently aren't any groups because there may be in the future.
                if (forum_user_can_post_discussion($forum, -1, null, $cm, $modcontext)) {
                    // Note: We must reverse in this manner because array_unshift renumbers the array.
                    $groupinfo = array_reverse($groupinfo, true);
                    $groupinfo[-1] = get_string('allparticipants');
                    $groupinfo = array_reverse($groupinfo, true);
                    $groupcount++;
                }

                // Determine whether the user can select a group from the dropdown. The dropdown is available for several reasons.
                // 1) This is a new post (not an edit), and there are at least two groups to choose from.
                $canselectgroupfornew = empty($post->edit) && $groupcount > 1;

                // 2) This is editing of an existing post and the user is allowed to movediscussions.
                // We allow this because the post may have been moved from another forum where groups are not available.
                // We show this even if no groups are available as groups *may* have been available but now are not.
                $canselectgroupformove =
                    $groupcount && !empty($post->edit) && has_capability('mod/forum:movediscussions', $modcontext);

                // Important: You can *only* change the group for a top level post. Never any reply.
                $canselectgroup = empty($post->parent) && ($canselectgroupfornew || $canselectgroupformove);

                if ($canselectgroup) {
                    $mform->addElement('select', 'groupinfo', get_string('group'), $groupinfo);
                    $mform->setDefault('groupinfo', $post->groupid);
                    $mform->setType('groupinfo', PARAM_INT);
                } else {
                    if (empty($post->groupid)) {
                        $groupname = get_string('allparticipants');
                    } else {
                        $groupname = format_string($groupdata[$post->groupid]->name);
                    }
                    $mform->addElement('static', 'groupinfo', get_string('group'), $groupname);
                }
            }

            if (!empty($CFG->forum_enabletimedposts) && !$post->parent &&
                has_capability('mod/forum:viewhiddentimedposts', $coursecontext)) {
                $mform->addElement('header', 'displayperiod', get_string('displayperiod', 'forum'));

                $mform->addElement('date_time_selector', 'timestart', get_string('displaystart', 'forum'),
                    array('optional' => true));
                $mform->addHelpButton('timestart', 'displaystart', 'forum');

                $mform->addElement('date_time_selector', 'timeend', get_string('displayend', 'forum'),
                    array('optional' => true));
                $mform->addHelpButton('timeend', 'displayend', 'forum');

            } else {
                $mform->addElement('hidden', 'timestart');
                $mform->setType('timestart', PARAM_INT);
                $mform->addElement('hidden', 'timeend');
                $mform->setType('timeend', PARAM_INT);
                $mform->setConstants(array('timestart' => 0, 'timeend' => 0));
            }

            if (core_tag_tag::is_enabled('mod_forum', 'forum_posts')) {
                $mform->addElement('header', 'tagshdr', get_string('tags', 'tag'));

                $mform->addElement('tags', 'tags', get_string('tags'),
                    array('itemtype' => 'forum_posts', 'component' => 'mod_forum'));
            }
        }

        //-------------------------------------------------------------------------------
        // buttons
        if (isset($post->edit)) { // hack alert
            $submitstring = get_string('savechanges');
        } else {
            $submitstring = get_string('posttoforum', 'forum');
        }

        // Always register a no submit button so it can be picked up if redirecting to the original post form.
        $mform->registerNoSubmitButton('advancedadddiscussion');

        // This is an inpage add discussion which requires custom buttons.
        if ($inpagereply) {
            $mform->addElement('hidden', 'discussionsubscribe');
            $mform->setType('discussionsubscribe', PARAM_INT);
            $mform->disable_form_change_checker();
            $buttonarray = array();
            $buttonarray[] = &$mform->createElement('submit', 'submitbutton', $submitstring);
            $buttonarray[] = &$mform->createElement('button', 'cancelbtn',
                get_string('cancel', 'core'),
                // Additional attribs to handle collapsible div.
                ['data-toggle' => 'collapse', 'data-target' => "#collapseAddForm"]);
            $buttonarray[] = &$mform->createElement('submit', 'advancedadddiscussion',
                get_string('showadvancededitor'), null, null, ['customclassoverride' => 'btn-link']);

            $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
            $mform->closeHeaderBefore('buttonar');
        } else {
            $this->add_action_buttons(true, $submitstring);
        }

        $mform->addElement('hidden', 'course');
        $mform->setType('course', PARAM_INT);

        $mform->addElement('hidden', 'forum');
        $mform->setType('forum', PARAM_INT);

        $mform->addElement('hidden', 'discussion');
        $mform->setType('discussion', PARAM_INT);

        $mform->addElement('hidden', 'parent');
        $mform->setType('parent', PARAM_INT);

        $mform->addElement('hidden', 'groupid');
        $mform->setType('groupid', PARAM_INT);

        $mform->addElement('hidden', 'edit');
        $mform->setType('edit', PARAM_INT);

        $mform->addElement('hidden', 'reply');
        $mform->setType('reply', PARAM_INT);
    }

    /**
     * Form validation
     *
     * @param array $data data from the form.
     * @param array $files files uploaded.
     * @return array of errors.
     */
    function validation($data, $files) {
        $errors = parent::validation($data, $files);
        if (($data['timeend']!=0) && ($data['timestart']!=0) && $data['timeend'] <= $data['timestart']) {
            $errors['timeend'] = get_string('timestartenderror', 'forum');
        }
        if (empty($data['message']['text'])) {
            $errors['message'] = get_string('erroremptymessage', 'forum');
        }
        if (empty($data['subject'])) {
            $errors['subject'] = get_string('erroremptysubject', 'forum');
        }
        return $errors;
    }
}
