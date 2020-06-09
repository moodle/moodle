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
 * This file contains the core_userfeedback class
 *
 * @package    core
 * @copyright  2020 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * This Class contains helper functions for user feedback functionality.
 *
 * @copyright  2020 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_userfeedback {
    /**
     * @var int Ask user to give feedback a few days after each major upgrade.
     */
    public const REMIND_AFTER_UPGRADE = 1;

    /**
     * @var int Ask user to give feedback periodically.
     */
    public const REMIND_PERIODICALLY = 2;

    /**
     * @var int Do not ask user to give feedback.
     */
    public const REMIND_NEVER = 3;

    /**
     * Displays the feedback reminder block.
     */
    public static function print_reminder_block(): void {
        global $PAGE;

        static $jscalled = false;

        $actions = [
            [
                'title' => get_string('calltofeedback_give'),
                'url' => static::make_link()->out(false),
                'data' => [
                    'action' => 'give',
                    'record' => 1,
                    'hide' => 1,
                ],
            ],
            [
                'title' => get_string('calltofeedback_remind'),
                'url' => '#',
                'data' => [
                    'action' => 'remind',
                    'record' => 1,
                    'hide' => 1,
                ],
            ],
        ];
        $icon = [
            'pix' => 'i/bullhorn',
            'component' => 'core'
        ];

        \core\notification::add_call_to_action($icon, get_string('calltofeedback'), $actions, 'core/userfeedback');

        if (!$jscalled) {
            $jscalled = true;
            // Calling the following more than once will register event listeners twice.
            $PAGE->requires->js_call_amd('core/userfeedback', 'registerEventListeners');
        }
    }

    /**
     * Indicates whether the feedback reminder block should be shown or not.
     *
     * @return bool
     */
    public static function should_display_reminder(): bool {
        global $CFG;

        if (static::can_give_feedback()) {
            $give = get_user_preferences('core_userfeedback_give');
            $remind = get_user_preferences('core_userfeedback_remind');

            $lastactiontime = max($give ?: 0, $remind ?: 0);

            switch ($CFG->userfeedback_nextreminder) {
                case static::REMIND_AFTER_UPGRADE:
                    $lastupgrade = static::last_major_upgrade_time();
                    if ($lastupgrade >= $lastactiontime) {
                        return $lastupgrade + ($CFG->userfeedback_remindafter * DAYSECS) < time();
                    }
                    break;
                case static::REMIND_PERIODICALLY:
                    return $lastactiontime + ($CFG->userfeedback_remindafter * DAYSECS) < time();
                    break;
            }
        }
        return false;
    }

    /**
     * Prepare and return the URL of the feedback site
     *
     * @return moodle_url
     */
    public static function make_link(): moodle_url {
        global $CFG, $PAGE;

        $baseurl = $CFG->userfeedback_url ?? 'https://feedback.moodle.org/lms';
        $lang = clean_param(current_language(), PARAM_LANG); // Avoid breaking WS because of incorrect package langs.
        $moodleurl = $CFG->wwwroot;
        $moodleversion = $CFG->release;
        $theme = $PAGE->theme->name;
        $themeversion = get_config('theme_'.$theme, 'version');

        $url = new moodle_url($baseurl, [
            'lang' => $lang,
            'moodle_url' => $moodleurl,
            'moodle_version' => $moodleversion,
            'theme' => $theme,
            'theme_version' => $themeversion,
            'newtest' => 'Y', // Respondents might be using the same device/browser to fill out the survey.
                              // The newtest param resets the session.
        ]);

        return $url;
    }

    /**
     * Whether the current can give feedback.
     *
     * @return bool
     */
    public static function can_give_feedback(): bool {
        global $CFG;

        return $CFG->enableuserfeedback && isloggedin() && !isguestuser();
    }

    /**
     * Returns the last major upgrade time
     *
     * @return int
     */
    private static function last_major_upgrade_time(): int {
        global $DB;

        $targetversioncast = $DB->sql_cast_char2real('targetversion');
        $versioncast = $DB->sql_cast_char2real('version');

        // A time difference more than 3 months has to be a core upgrade.
        // Also, passing IGNORE_MULTIPLE because we are only interested in the first field and LIMIT is not cross-DB.
        $time = $DB->get_field_sql("SELECT timemodified
                                     FROM {upgrade_log}
                                    WHERE plugin = 'core' AND $targetversioncast - $versioncast > 30000
                                 ORDER BY timemodified DESC", null, IGNORE_MULTIPLE);

        return (int)$time;
    }
}
