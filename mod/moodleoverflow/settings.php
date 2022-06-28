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
 * File for the settings of moodleoverflow.
 *
 * @package   mod_moodleoverflow
 * @copyright 2017 Kennet Winter <k_wint10@uni-muenster.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    require_once($CFG->dirroot . '/mod/moodleoverflow/lib.php');

    // Number of discussions per page.
    $settings->add(new admin_setting_configtext('moodleoverflow/manydiscussions', get_string('manydiscussions', 'moodleoverflow'),
        get_string('configmanydiscussions', 'moodleoverflow'), 10, PARAM_INT));

    if (isset($CFG->maxbytes)) {
        $maxbytes = 0;
        if (get_config('moodleoverflow', 'maxbytes')) {
            $maxbytes = get_config('moodleoverflow', 'maxbytes');
        }
        $settings->add(new admin_setting_configselect('moodleoverflow/maxbytes', get_string('maxattachmentsize', 'moodleoverflow'),
            get_string('configmaxbytes', 'moodleoverflow'), 512000, get_max_upload_sizes($CFG->maxbytes, 0, 0, $maxbytes)));
    }

    // Default number of attachments allowed per post in all moodlevoerflows.
    $settings->add(new admin_setting_configtext('moodleoverflow/maxattachments', get_string('maxattachments', 'moodleoverflow'),
        get_string('configmaxattachments', 'moodleoverflow'), 9, PARAM_INT));

    $settings->add(new admin_setting_configtext('moodleoverflow/maxeditingtime', get_string('maxeditingtime', 'moodleoverflow'),
        get_string('configmaxeditingtime', 'moodleoverflow'), 3600, PARAM_INT));


    // Default read tracking settings.
    $options                                   = array();
    $options[MOODLEOVERFLOW_TRACKING_OPTIONAL] = get_string('trackingoptional', 'moodleoverflow');
    $options[MOODLEOVERFLOW_TRACKING_OFF]      = get_string('trackingoff', 'moodleoverflow');
    $options[MOODLEOVERFLOW_TRACKING_FORCED]   = get_string('trackingon', 'moodleoverflow');
    $settings->add(new admin_setting_configselect('moodleoverflow/trackingtype', get_string('trackingtype', 'moodleoverflow'),
        get_string('configtrackingtype', 'moodleoverflow'), MOODLEOVERFLOW_TRACKING_OPTIONAL, $options));

    // Should unread posts be tracked for each user?
    $settings->add(new admin_setting_configcheckbox('moodleoverflow/trackreadposts',
        get_string('trackmoodleoverflow', 'moodleoverflow'), get_string('configtrackmoodleoverflow', 'moodleoverflow'), 1));

    // Allow moodleoverflows to be set to forced read tracking.
    $settings->add(new admin_setting_configcheckbox('moodleoverflow/allowforcedreadtracking',
        get_string('forcedreadtracking', 'moodleoverflow'), get_string('configforcedreadtracking', 'moodleoverflow'), 0));

    // Default number of days that a post is considered old.
    $settings->add(new admin_setting_configtext('moodleoverflow/oldpostdays', get_string('oldpostdays', 'moodleoverflow'),
        get_string('configoldpostdays', 'moodleoverflow'), 14, PARAM_INT));

    // Default time (hour) to execute 'clean_read_records' cron.
    $options = array();
    for ($i = 0; $i < 24; $i++) {
        $options[$i] = sprintf("%02d", $i);
    }
    $settings->add(new admin_setting_configselect('moodleoverflow/cleanreadtime', get_string('cleanreadtime', 'moodleoverflow'),
        get_string('configcleanreadtime', 'moodleoverflow'), 2, $options));

    $settings->add(new admin_setting_configcheckbox('moodleoverflow/allowanonymous',
        get_string('allowanonymous', 'moodleoverflow'),
        get_string('allowanonymous_desc', 'moodleoverflow'),
        1
    ));

    // Allow teachers to disable ratings/reputation.
    $settings->add(new admin_setting_configcheckbox('moodleoverflow/allowdisablerating',
        get_string('allowdisablerating', 'moodleoverflow'), get_string('configallowdisablerating', 'moodleoverflow'), 1));

    // Allow users to change their votes?
    $settings->add(new admin_setting_configcheckbox('moodleoverflow/allowratingchange',
        get_string('allowratingchange', 'moodleoverflow'), get_string('configallowratingchange', 'moodleoverflow'), 1));

    // Set scales for the reputation.
    $votesettings = [];

    // Votescale: How much reputation gives a vote for another post?
    $settings->add($votesettings[] = new admin_setting_configtext('moodleoverflow/votescalevote',
        get_string('votescalevote', 'moodleoverflow'),
        get_string('configvotescalevote', 'moodleoverflow'), 1, PARAM_INT));

    // Votescale: How much reputation gives a post that has been downvoted?
    $settings->add($votesettings[] = new admin_setting_configtext('moodleoverflow/votescaledownvote',
        get_string('votescaledownvote', 'moodleoverflow'), get_string('configvotescaledownvote', 'moodleoverflow'), -5, PARAM_INT));

    // Votescale: How much reputation gives a post that has been upvoted?
    $settings->add($votesettings[] = new admin_setting_configtext('moodleoverflow/votescaleupvote',
        get_string('votescaleupvote', 'moodleoverflow'),
        get_string('configvotescaleupvote', 'moodleoverflow'), 5, PARAM_INT));

    // Votescale: How much reputation gives a post that is marked as solved.
    $settings->add($votesettings[] = new admin_setting_configtext('moodleoverflow/votescalesolved',
        get_string('votescalesolved', 'moodleoverflow'),
        get_string('configvotescalesolved', 'moodleoverflow'), 30, PARAM_INT));

    // Votescale: How much reputation gives a post that is marked as helpful.
    $settings->add($votesettings[] = new admin_setting_configtext('moodleoverflow/votescalehelpful',
        get_string('votescalehelpful', 'moodleoverflow'),
        get_string('configvotescalehelpful', 'moodleoverflow'), 15, PARAM_INT));

    // Number of discussions per page.
    $settings->add($votesettings[] = new admin_setting_configtext('moodleoverflow/maxmailingtime',
        get_string('maxmailingtime', 'moodleoverflow'),
        get_string('configmaxmailingtime', 'moodleoverflow'), 48, PARAM_INT));

    foreach ($votesettings as $setting) {
        $setting->set_updatedcallback('moodleoverflow_update_all_grades');
    }

}
