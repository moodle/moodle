<?php
      /// Create "blog" forums in each course and copy blog entries from these courses' participants in these forums

define('NO_OUTPUT_BUFFERING', true);

require_once('../config.php');
require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->dirroot.'/blog/lib.php');
require_once($CFG->dirroot.'/mod/forum/lib.php');
require_once($CFG->libdir.'/adminlib.php');

admin_externalpage_setup('bloglevelupgrade');
$PAGE->set_pagelayout('maintenance');

$go = optional_param('go', 0, PARAM_BOOL);

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('bloglevelupgrade', 'admin'));

$strbloglevelupgrade = get_string('bloglevelupgradeinfo', 'admin');

if (!$go or !data_submitted() or !confirm_sesskey()) {   /// Print a form
    $optionsyes = array('go'=>1, 'sesskey'=>sesskey());
    echo $OUTPUT->confirm($strbloglevelupgrade, new moodle_url('bloglevelupgrade.php', $optionsyes), 'index.php');
    echo $OUTPUT->footer();
    die;
}

echo $OUTPUT->box_start();

/// Turn off time limits, sometimes upgrades can be slow.

set_time_limit(0);

$i = 0;

// If $CFG->bloglevel is set to BLOG_GROUP_LEVEL or BLOG_COURSE_LEVEL, create a new "blog" forum in each course
// whose enrolled students have written blog entries, copy these entries in that forum and switch off blogs at site level

if ($CFG->bloglevel == BLOG_COURSE_LEVEL || $CFG->bloglevel == BLOG_GROUP_LEVEL) {
    $pbar = new progress_bar('bloglevelupgrade', 500, true);

    $bloggers = $DB->get_records_sql("SELECT userid FROM {post} WHERE module = 'blog' GROUP BY userid");
    require_once($CFG->dirroot.'/mod/forum/lib.php');

    $a = new stdClass();
    $a->userscount = 0;
    $a->blogcount = 0;

    foreach ($bloggers as $blogger) {
        $courses = enrol_get_users_courses($blogger->userid, true, 'groupmode,groupmodeforce');
        $blogentries = $DB->get_records('post', array('module' => 'blog', 'userid' => $blogger->userid));

        foreach ($courses as $course) {
            $forum = forum_get_course_forum($course->id, 'blog');
            $cm = get_coursemodule_from_instance('forum', $forum->id);

            if ($CFG->bloglevel == BLOG_GROUP_LEVEL && $course->groupmode != NOGROUPS) {
                // Unless the course is set to separate groups forced, force the forum to Separate groups
                if (!($course->groupmode == SEPARATEGROUPS && $course->groupmodeforce)) {
                    $cm->groupmode = SEPARATEGROUPS;
                    $DB->update_record('course_modules', $cm);
                }

                $groups = groups_get_user_groups($course->id, $blogger->userid);
                foreach ($groups[0] as $groupid) { // [0] is for all groupings combined
                    $a->blogcount += bloglevelupgrade_entries($blogentries, $forum, $cm, $groupid);
                }
            } else {
                $a->blogcount += bloglevelupgrade_entries($blogentries, $forum, $cm);
            }
        }

        $a->userscount = $i . '/' .  count($bloggers);
        $pbar->update($i, count($bloggers), get_string('bloglevelupgradeprogress', 'admin', $a));
        $i++;
    }
}

function bloglevelupgrade_entries($blogentries, $forum, $cm, $groupid=-1) {
    $count = 0;

    $forumcontext = get_context_instance(CONTEXT_MODULE, $cm->id);
    $sitecontext = get_context_instance(CONTEXT_SYSTEM);

    foreach ($blogentries as $blogentry) {
        $discussion = new stdClass();
        $discussion->course = $forum->course;
        $discussion->forum = $forum->id;
        $discussion->name = $blogentry->subject;
        $discussion->assessed = $forum->assessed;
        $discussion->message = $blogentry->summary;
        $discussion->messageformat = $blogentry->summaryformat;
        $discussion->messagetrust = 0;
        $discussion->attachments = 0;
        $discussion->mailnow = false;
        $discussion->timemodified = $blogentry->created;
        $discussion->itemid = null;
        $discussion->groupid = $groupid;
        $message = '';

        $discussionid = forum_add_discussion($discussion, null, $message, $blogentry->userid);

        // Copy file attachment records
        $fs = get_file_storage();
        $files = $fs->get_area_files($sitecontext->id, 'blog', 'attachment', $blogentry->id);

        if (!empty($files)) {
            foreach ($files as $storedfile) {
                $newfile = new stdClass();
                $newfile->component = 'mod_forum';
                $newfile->filearea = 'attachment';
                $newfile->itemid = $discussion->firstpost;
                $newfile->contextid = $forumcontext->id;
                $fs->create_file_from_storedfile($newfile, $storedfile->get_id());
            }
        }

        $files = $fs->get_area_files($sitecontext->id, 'blog', 'post', $blogentry->id);

        if (!empty($files)) {
            foreach ($files as $storedfile) {
                $newfile = new stdClass();
                $newfile->component = 'mod_forum';
                $newfile->filearea = 'post';
                $newfile->itemid = $discussion->firstpost;
                $newfile->contextid = $forumcontext->id;
                $fs->create_file_from_storedfile($newfile, $storedfile->get_id());
            }
        }
        $count++;
    }
    return $count;
}
// END OF LOOP

// set conversion flag - switches to new plugin automatically
set_config('bloglevel_upgrade_complete', 1);
// Finally switch bloglevel to 0 (disabled)
set_config('bloglevel', 0);

echo $OUTPUT->box_end();

/// Rebuild course cache which might be incorrect now
echo $OUTPUT->notification('Rebuilding course cache...', 'notifysuccess');
rebuild_course_cache();
echo $OUTPUT->notification('...finished', 'notifysuccess');

echo $OUTPUT->continue_button('index.php');

echo $OUTPUT->footer();
die;
