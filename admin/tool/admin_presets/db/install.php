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
 * Install code for Admin tool presets plugin.
 *
 * @package    tool_admin_presets
 * @copyright  2021 Sara Arjona (sara@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

use tool_admin_presets\helper;

/**
 * Perform the post-install procedures.
 */
function xmldb_tool_admin_presets_install() {

    // Create the "Starter" site admin preset.
    $data = [
        'name' => get_string('starterpreset', 'tool_admin_presets'),
        'comments' => get_string('starterpresetdescription', 'tool_admin_presets'),
        'iscore' => 1,
    ];
    $presetid = helper::create_preset($data);

    // Add settings to the "Starter" site admin preset.
    helper::add_item($presetid, 'usecomments', '0');
    helper::add_item($presetid, 'usetags', '0');
    helper::add_item($presetid, 'enablenotes', '0');
    helper::add_item($presetid, 'enableblogs', '0');
    helper::add_item($presetid, 'enablebadges', '0');
    helper::add_item($presetid, 'enableanalytics', '0');
    helper::add_item($presetid, 'enabled', '0', 'core_competency');
    helper::add_item($presetid, 'pushcourseratingstouserplans', '0', 'core_competency');

    helper::add_item($presetid, 'showdataretentionsummary', '0', 'tool_dataprivacy');
    helper::add_item($presetid, 'forum_maxattachments', '3');
    helper::add_item($presetid, 'customusermenuitems', 'profile,moodle|/user/profile.php
grades,grades|/grade/report/mygrades.php
calendar,core_calendar|/calendar/view.php?view=month
privatefiles,moodle|/user/files.php');

    // Modules: Hide chat, database, external tool (lti), IMS content package (imscp), lesson, SCORM, survey, wiki, workshop.
    helper::add_plugin($presetid, 'mod', 'chat', false);
    helper::add_plugin($presetid, 'mod', 'data', false);
    helper::add_plugin($presetid, 'mod', 'lti', false);
    helper::add_plugin($presetid, 'mod', 'imscp', false);
    helper::add_plugin($presetid, 'mod', 'lesson', false);
    helper::add_plugin($presetid, 'mod', 'scorm', false);
    helper::add_plugin($presetid, 'mod', 'survey', false);
    helper::add_plugin($presetid, 'mod', 'wiki', false);
    helper::add_plugin($presetid, 'mod', 'workshop', false);

    // Availability restrictions: Hide Grouping, User profile.
    helper::add_plugin($presetid, 'availability', 'grouping', false);
    helper::add_plugin($presetid, 'availability', 'profile', false);

    // Blocks: Disable Activities, Blog menu, Blog tags, Comments, Course completion status, Course/site summary, Courses, Flickr,
    // Global search, Latest badges, Learning plans, Logged in user, Login, Main menu, Mentees, Network servers, Private files,
    // Recent blog entries, RSS feeds, Search forums, Section links,Self completion, Social activities, Tags, YouTube.
    helper::add_plugin($presetid, 'block', 'activity_modules', false);
    helper::add_plugin($presetid, 'block', 'blog_menu', false);
    helper::add_plugin($presetid, 'block', 'blog_tags', false);
    helper::add_plugin($presetid, 'block', 'comments', false);
    helper::add_plugin($presetid, 'block', 'completionstatus', false);
    helper::add_plugin($presetid, 'block', 'course_summary', false);
    helper::add_plugin($presetid, 'block', 'course_list', false);
    helper::add_plugin($presetid, 'block', 'tag_flickr', false);
    helper::add_plugin($presetid, 'block', 'globalsearch', false);
    helper::add_plugin($presetid, 'block', 'badges', false);
    helper::add_plugin($presetid, 'block', 'lp', false);
    helper::add_plugin($presetid, 'block', 'myprofile', false);
    helper::add_plugin($presetid, 'block', 'login', false);
    helper::add_plugin($presetid, 'block', 'site_main_menu', false);
    helper::add_plugin($presetid, 'block', 'mentees', false);
    helper::add_plugin($presetid, 'block', 'mnet_hosts', false);
    helper::add_plugin($presetid, 'block', 'private_files', false);
    helper::add_plugin($presetid, 'block', 'blog_recent', false);
    helper::add_plugin($presetid, 'block', 'rss_client', false);
    helper::add_plugin($presetid, 'block', 'search_forums', false);
    helper::add_plugin($presetid, 'block', 'section_links', false);
    helper::add_plugin($presetid, 'block', 'selfcompletion', false);
    helper::add_plugin($presetid, 'block', 'social_activities', false);
    helper::add_plugin($presetid, 'block', 'tags', false);
    helper::add_plugin($presetid, 'block', 'tag_youtube', false);
    helper::add_plugin($presetid, 'block', 'feedback', false);

    // Course formats: Disable Social format.
    helper::add_plugin($presetid, 'format', 'social', false);

    // Data formats: Disable Javascript Object Notation (.json).
    helper::add_plugin($presetid, 'dataformat', 'json', false);

    // Enrolments: Disable Cohort sync.
    helper::add_plugin($presetid, 'enrol', 'cohort', false);

    // Filter: Disable MathJax, Activity names auto-linking.
    helper::add_plugin($presetid, 'filter', 'mathjaxloader', TEXTFILTER_DISABLED);
    helper::add_plugin($presetid, 'filter', 'activitynames', TEXTFILTER_DISABLED);

    // Question behaviours: Disable Adaptive mode (no penalties), Deferred feedback with CBM, Immediate feedback with CBM.
    helper::add_plugin($presetid, 'qbehaviour', 'adaptivenopenalty', false);
    helper::add_plugin($presetid, 'qbehaviour', 'deferredcbm', false);
    helper::add_plugin($presetid, 'qbehaviour', 'immediatecbm', false);

    // Question types: Disable Calculated, Calculated multichoice, Calculated simple, Description, Drag and drop markers,
    // Drag and drop onto image, Embedded answers (Cloze), Essay, Numerical, Random short-answer matching.
    helper::add_plugin($presetid, 'qtype', 'calculated', false);
    helper::add_plugin($presetid, 'qtype', 'calculatedmulti', false);
    helper::add_plugin($presetid, 'qtype', 'calculatedsimple', false);
    helper::add_plugin($presetid, 'qtype', 'description', false);
    helper::add_plugin($presetid, 'qtype', 'ddmarker', false);
    helper::add_plugin($presetid, 'qtype', 'ddimageortext', false);
    helper::add_plugin($presetid, 'qtype', 'multianswer', false);
    helper::add_plugin($presetid, 'qtype', 'essay', false);
    helper::add_plugin($presetid, 'qtype', 'numerical', false);
    helper::add_plugin($presetid, 'qtype', 'randomsamatch', false);

    // Repositories: Disable Server files, URL downloader, Wikimedia.
    helper::add_plugin($presetid, 'repository', 'local', false);
    helper::add_plugin($presetid, 'repository', 'url', false);
    helper::add_plugin($presetid, 'repository', 'wikimedia', false);

    // Text editors: Disable TinyMCE HTML editor.
    helper::add_plugin($presetid, 'editor', 'tinymce', false);

    // Create the "Full" site admin preset.
    $data = [
        'name' => get_string('fullpreset', 'tool_admin_presets'),
        'comments' => get_string('fullpresetdescription', 'tool_admin_presets'),
        'iscore' => 1,
    ];
    $presetid = helper::create_preset($data);

    // Add settings to the "Full" site admin preset.
    helper::add_item($presetid, 'usecomments', '1');
    helper::add_item($presetid, 'usetags', '1');
    helper::add_item($presetid, 'enablenotes', '1');
    helper::add_item($presetid, 'enableblogs', '1');
    helper::add_item($presetid, 'enablebadges', '1');
    helper::add_item($presetid, 'enableanalytics', '1');
    helper::add_item($presetid, 'enabled', '1', 'core_competency');
    helper::add_item($presetid, 'pushcourseratingstouserplans', '1', 'core_competency');

    helper::add_item($presetid, 'showdataretentionsummary', '1', 'tool_dataprivacy');
    helper::add_item($presetid, 'forum_maxattachments', '9');
    // In that case, the indentation coding style can't follow the rules to guarantee the setting value is created properly.
    helper::add_item($presetid, 'customusermenuitems', 'profile,moodle|/user/profile.php
grades,grades|/grade/report/mygrades.php
calendar,core_calendar|/calendar/view.php?view=month
privatefiles,moodle|/user/files.php'
    );

    // Modules: Enable chat, database, external tool (lti), IMS content package (imscp), lesson, SCORM, survey, wiki, workshop.
    helper::add_plugin($presetid, 'mod', 'chat', true);
    helper::add_plugin($presetid, 'mod', 'data', true);
    helper::add_plugin($presetid, 'mod', 'lti', true);
    helper::add_plugin($presetid, 'mod', 'imscp', true);
    helper::add_plugin($presetid, 'mod', 'lesson', true);
    helper::add_plugin($presetid, 'mod', 'scorm', true);
    helper::add_plugin($presetid, 'mod', 'survey', true);
    helper::add_plugin($presetid, 'mod', 'wiki', true);
    helper::add_plugin($presetid, 'mod', 'workshop', true);

    // Availability restrictions: Enable Grouping, User profile.
    helper::add_plugin($presetid, 'availability', 'grouping', true);
    helper::add_plugin($presetid, 'availability', 'profile', true);

    // Blocks: Enable Activities, Blog menu, Blog tags, Comments, Course completion status, Course/site summary, Courses, Flickr,
    // Global search, Latest badges, Learning plans, Logged in user, Login, Main menu, Mentees, Network servers, Private files,
    // Recent blog entries, RSS feeds, Search forums, Section links,Self completion, Social activities, Tags, YouTube.
    helper::add_plugin($presetid, 'block', 'activity_modules', true);
    helper::add_plugin($presetid, 'block', 'blog_menu', true);
    helper::add_plugin($presetid, 'block', 'blog_tags', true);
    helper::add_plugin($presetid, 'block', 'comments', true);
    helper::add_plugin($presetid, 'block', 'completionstatus', true);
    helper::add_plugin($presetid, 'block', 'course_summary', true);
    helper::add_plugin($presetid, 'block', 'course_list', true);
    helper::add_plugin($presetid, 'block', 'tag_flickr', true);
    helper::add_plugin($presetid, 'block', 'globalsearch', true);
    helper::add_plugin($presetid, 'block', 'badges', true);
    helper::add_plugin($presetid, 'block', 'lp', true);
    helper::add_plugin($presetid, 'block', 'myprofile', true);
    helper::add_plugin($presetid, 'block', 'login', true);
    helper::add_plugin($presetid, 'block', 'site_main_menu', true);
    helper::add_plugin($presetid, 'block', 'mentees', true);
    helper::add_plugin($presetid, 'block', 'mnet_hosts', true);
    helper::add_plugin($presetid, 'block', 'private_files', true);
    helper::add_plugin($presetid, 'block', 'blog_recent', true);
    helper::add_plugin($presetid, 'block', 'rss_client', true);
    helper::add_plugin($presetid, 'block', 'search_forums', true);
    helper::add_plugin($presetid, 'block', 'section_links', true);
    helper::add_plugin($presetid, 'block', 'selfcompletion', true);
    helper::add_plugin($presetid, 'block', 'social_activities', true);
    helper::add_plugin($presetid, 'block', 'tags', true);
    helper::add_plugin($presetid, 'block', 'feedback', true);

    // Course formats: Enable Social format.
    helper::add_plugin($presetid, 'format', 'social', true);

    // Data formats: Enable Javascript Object Notation (.json).
    helper::add_plugin($presetid, 'dataformat', 'json', true);

    // Enrolments: Enable Cohort sync.
    helper::add_plugin($presetid, 'enrol', 'cohort', true);

    // Filter: Enable MathJax, Activity names auto-linking.
    helper::add_plugin($presetid, 'filter', 'mathjaxloader', TEXTFILTER_ON);
    helper::add_plugin($presetid, 'filter', 'activitynames', TEXTFILTER_ON);

    // Question behaviours: Enable Adaptive mode (no penalties), Deferred feedback with CBM, Immediate feedback with CBM.
    helper::add_plugin($presetid, 'qbehaviour', 'adaptivenopenalty', true);
    helper::add_plugin($presetid, 'qbehaviour', 'deferredcbm', true);
    helper::add_plugin($presetid, 'qbehaviour', 'immediatecbm', true);

    // Question types: Enable Calculated, Calculated multichoice, Calculated simple, Description, Drag and drop markers,
    // Drag and drop onto image, Embedded answers (Cloze), Essay, Numerical, Random short-answer matching.
    helper::add_plugin($presetid, 'qtype', 'calculated', true);
    helper::add_plugin($presetid, 'qtype', 'calculatedmulti', true);
    helper::add_plugin($presetid, 'qtype', 'calculatedsimple', true);
    helper::add_plugin($presetid, 'qtype', 'description', true);
    helper::add_plugin($presetid, 'qtype', 'ddmarker', true);
    helper::add_plugin($presetid, 'qtype', 'ddimageortext', true);
    helper::add_plugin($presetid, 'qtype', 'multianswer', true);
    helper::add_plugin($presetid, 'qtype', 'essay', true);
    helper::add_plugin($presetid, 'qtype', 'numerical', true);
    helper::add_plugin($presetid, 'qtype', 'randomsamatch', true);

    // Repositories: Enable Server files, URL downloader, Wikimedia.
    helper::add_plugin($presetid, 'repository', 'local', true);
    helper::add_plugin($presetid, 'repository', 'url', true);
    helper::add_plugin($presetid, 'repository', 'wikimedia', true);

    // Text editors: Enable TinyMCE HTML editor.
    helper::add_plugin($presetid, 'editor', 'tinymce', true);

}
