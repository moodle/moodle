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
 * Mobile app support.
 *
 * @package    tool_mobile
 * @copyright  2019 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Upgrade the plugin.
 *
 * @param int $oldversion
 * @return bool always true
 */
function xmldb_tool_mobile_upgrade($oldversion) {
    // Automatically generated Moodle v4.4.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v4.5.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v5.0.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v5.1.0 release upgrade line.
    // Put any upgrade step following this.
    if ($oldversion < 2026031000) {
        $config = get_config('tool_mobile', 'disabledfeatures');
        if (!empty($config)) {
            $replacements = [
                '$mmLoginEmailSignup' => 'CoreLoginEmailSignup',
                '$mmSideMenuDelegate' => 'CoreMainMenuDelegate',
                '$mmCoursesDelegate' => 'CoreCourseOptionsDelegate',
                '$mmUserDelegate' => 'CoreUserDelegate',
                '$mmCourseDelegate' => 'CoreCourseModuleDelegate',
                '_mmCourses' => '_CoreCourses',
                '_mmaFrontpage' => '_CoreSiteHome',
                '_mmaGrades' => '_CoreGrades',
                '_mmaCompetency' => '_AddonCompetency',
                '_mmaNotifications' => '_AddonNotifications',
                '_mmaMessages' => '_AddonMessages',
                '_mmaCalendar' => '_AddonCalendar',
                '_mmaFiles' => '_AddonPrivateFiles',
                '_mmaParticipants' => '_CoreUserParticipants',
                '_mmaCourseCompletion' => '_AddonCourseCompletion',
                '_mmaNotes' => '_AddonNotes',
                '_mmaBadges' => '_AddonBadges',
                'files_privatefiles' => 'AddonPrivateFilesPrivateFiles',
                'files_sitefiles' => 'AddonPrivateFilesSiteFiles',
                'files_upload' => 'AddonPrivateFilesUpload',
                '_mmaModAssign' => '_AddonModAssign',
                '_mmaModBigbluebuttonbn' => '_AddonModBBB',
                '_mmaModBook' => '_AddonModBook',
                '_mmaModChat' => '_AddonModChat',
                '_mmaModChoice' => '_AddonModChoice',
                '_mmaModData' => '_AddonModData',
                '_mmaModFeedback' => '_AddonModFeedback',
                '_mmaModFolder' => '_AddonModFolder',
                '_mmaModForum' => '_AddonModForum',
                '_mmaModGlossary' => '_AddonModGlossary',
                '_mmaModH5pactivity' => '_AddonModH5PActivity',
                '_mmaModImscp' => '_AddonModImscp',
                '_mmaModLabel' => '_AddonModLabel',
                '_mmaModLesson' => '_AddonModLesson',
                '_mmaModLti' => '_AddonModLti',
                '_mmaModPage' => '_AddonModPage',
                '_mmaModQuiz' => '_AddonModQuiz',
                '_mmaModResource' => '_AddonModResource',
                '_mmaModScorm' => '_AddonModScorm',
                '_mmaModSurvey' => '_AddonModSurvey',
                '_mmaModUrl' => '_AddonModUrl',
                '_mmaModWiki' => '_AddonModWiki',
                '_mmaModWorkshop' => '_AddonModWorkshop',
                'AddonNotes:addNote' => 'AddonNotes:notes',
                'CoreMainMenuDelegate_AddonCompetency' => 'CoreUserDelegate_AddonCompetency',
                'CoreMainMenuDelegate_AddonPrivateFiles' => 'CoreUserDelegate_AddonPrivateFiles',
                'CoreMainMenuDelegate_CoreGrades' => 'CoreUserDelegate_CoreGrades',
            ];

            foreach ($replacements as $old => $new) {
                $config = str_replace($old, $new, $config);
            }
            set_config('disabledfeatures', $config, 'tool_mobile');
        }

        upgrade_plugin_savepoint(true, 2026031000, 'tool', 'mobile');
    }

    if ($oldversion < 2026031100) {
        // Run the subscription cache refresh task as soon as possible after upgrade by queueing an adhoc task.
        $task = new \tool_mobile\task\refresh_subscription_cache_adhoc();
        \core\task\manager::queue_adhoc_task($task, true);
        upgrade_plugin_savepoint(true, 2026031100, 'tool', 'mobile');
    }
    // Automatically generated Moodle v5.2.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v5.3.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
