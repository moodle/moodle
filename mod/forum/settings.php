<?php  //$Id$

require_once($CFG->dirroot.'/mod/forum/lib.php');

$settings->add(new admin_setting_configselect('forum_displaymode', get_string('displaymode', 'forum'),
                   get_string('configdisplaymode', 'forum'), FORUM_MODE_NESTED, forum_get_layout_modes()));

$settings->add(new admin_setting_configcheckbox('forum_replytouser', get_string('replytouser', 'forum'),
                   get_string('configreplytouser', 'forum'), 1));

// Less non-HTML characters than this is short
$settings->add(new admin_setting_configtext('forum_shortpost', get_string('shortpost', 'forum'),
                   get_string('configshortpost', 'forum'), 300, PARAM_INT));

// More non-HTML characters than this is long
$settings->add(new admin_setting_configtext('forum_longpost', get_string('longpost', 'forum'),
                   get_string('configlongpost', 'forum'), 600, PARAM_INT));

// Number of discussions on a page
$settings->add(new admin_setting_configtext('forum_manydiscussions', get_string('manydiscussions', 'forum'),
                   get_string('configmanydiscussions', 'forum'), 100, PARAM_INT));

$settings->add(new admin_setting_configselect('forum_maxbytes', get_string('maxattachmentsize', 'forum'),
                   get_string('configmaxbytes', 'forum'), 512000, get_max_upload_sizes($CFG->maxbytes)));

// Default whether user needs to mark a post as read
$settings->add(new admin_setting_configcheckbox('forum_trackreadposts', get_string('trackforum', 'forum'),
                   get_string('configtrackreadposts', 'forum'), 1));

// Default number of days that a post is considered old
$settings->add(new admin_setting_configtext('forum_oldpostdays', get_string('oldpostdays', 'forum'),
                   get_string('configoldpostdays', 'forum'), 14, PARAM_INT));

// Default whether user needs to mark a post as read
$settings->add(new admin_setting_configcheckbox('forum_usermarksread', get_string('usermarksread', 'forum'),
                   get_string('configusermarksread', 'forum'), 0));

// Default time (hour) to execute 'clean_read_records' cron
$options = array();
for ($i=0; $i<24; $i++) {
    $options[$i] = $i;
}
$settings->add(new admin_setting_configselect('forum_cleanreadtime', get_string('cleanreadtime', 'forum'),
                   get_string('configcleanreadtime', 'forum'), 2, $options));


if (empty($CFG->enablerssfeeds)) {
    $options = array(0 => get_string('rssglobaldisabled', 'admin'));
    $str = get_string('configenablerssfeeds', 'forum').'<br />'.get_string('configenablerssfeedsdisabled2', 'admin');

} else {
    $options = array(0=>get_string('no'), 1=>get_string('yes'));
    $str = get_string('configenablerssfeeds', 'forum');
}
$settings->add(new admin_setting_configselect('forum_enablerssfeeds', get_string('enablerssfeeds', 'admin'),
                   $str, 0, $options));

$settings->add(new admin_setting_configcheckbox('forum_enabletimedposts', get_string('timedposts', 'forum'),
                   get_string('configenabletimedposts', 'forum'), 0));

$settings->add(new admin_setting_configcheckbox('forum_logblocked', get_string('logblocked', 'forum'),
                   get_string('configlogblocked', 'forum'), 1));

$settings->add(new admin_setting_configcheckbox('forum_ajaxrating', get_string('ajaxrating', 'forum'),
                   get_string('configajaxrating', 'forum'), 0));

?>
