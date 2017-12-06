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
 * Scheduled task class.
 *
 * @package    core
 * @copyright  2013 onwards Martin Dougiamas  http://dougiamas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core\task;

/**
 * Simple task to run cron for all plugins.
 * Note - this is only for plugins using the legacy cron method,
 * plugins can also now just add their own scheduled tasks which is the preferred method.
 */
class legacy_plugin_cron_task extends scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('tasklegacycron', 'admin');
    }

    /**
     * Do the job.
     * Throw exceptions on errors (the job will be retried).
     */
    public function execute() {
        global $CFG, $DB;

        $timenow = time();
        // Run the auth cron, if any before enrolments
        // because it might add users that will be needed in enrol plugins.
        $auths = get_enabled_auth_plugins();
        mtrace("Running auth crons if required...");
        foreach ($auths as $auth) {
            $authplugin = get_auth_plugin($auth);
            if (method_exists($authplugin, 'cron')) {
                mtrace("Running cron for auth/$auth...");
                $authplugin->cron();
                if (!empty($authplugin->log)) {
                    mtrace($authplugin->log);
                }
            }
            unset($authplugin);
        }

        // It is very important to run enrol early
        // because other plugins depend on correct enrolment info.
        mtrace("Running enrol crons if required...");
        $enrols = enrol_get_plugins(true);
        foreach ($enrols as $ename => $enrol) {
            // Do this for all plugins, disabled plugins might want to cleanup stuff such as roles.
            if (!$enrol->is_cron_required()) {
                continue;
            }
            mtrace("Running cron for enrol_$ename...");
            $enrol->cron();
            $enrol->set_config('lastcron', time());
        }

        // Run all cron jobs for each module.
        mtrace("Starting activity modules");
        if ($mods = $DB->get_records_select("modules", "cron > 0 AND ((? - lastcron) > cron) AND visible = 1", array($timenow))) {
            foreach ($mods as $mod) {
                $libfile = "$CFG->dirroot/mod/$mod->name/lib.php";
                if (file_exists($libfile)) {
                    include_once($libfile);
                    $cronfunction = $mod->name."_cron";
                    if (function_exists($cronfunction)) {
                        mtrace("Processing module function $cronfunction ...\n", '');
                        $predbqueries = null;
                        $predbqueries = $DB->perf_get_queries();
                        $pretime      = microtime(1);
                        if ($cronfunction()) {
                            $DB->set_field("modules", "lastcron", $timenow, array("id" => $mod->id));
                        }
                        if (isset($predbqueries)) {
                            mtrace("... used " . ($DB->perf_get_queries() - $predbqueries) . " dbqueries");
                            mtrace("... used " . (microtime(1) - $pretime) . " seconds");
                        }
                        // Reset possible changes by modules to time_limit. MDL-11597.
                        \core_php_time_limit::raise();
                        mtrace("done.");
                    }
                }
            }
        }
        mtrace("Finished activity modules");

        mtrace("Starting blocks");
        if ($blocks = $DB->get_records_select("block", "cron > 0 AND ((? - lastcron) > cron) AND visible = 1", array($timenow))) {
            // We will need the base class.
            require_once($CFG->dirroot.'/blocks/moodleblock.class.php');
            foreach ($blocks as $block) {
                $blockfile = $CFG->dirroot.'/blocks/'.$block->name.'/block_'.$block->name.'.php';
                if (file_exists($blockfile)) {
                    require_once($blockfile);
                    $classname = '\\block_'.$block->name;
                    $blockobj = new $classname;
                    if (method_exists($blockobj, 'cron')) {
                        mtrace("Processing cron function for ".$block->name.'....', '');
                        if ($blockobj->cron()) {
                            $DB->set_field('block', 'lastcron', $timenow, array('id' => $block->id));
                        }
                        // Reset possible changes by blocks to time_limit. MDL-11597.
                        \core_php_time_limit::raise();
                        mtrace('done.');
                    }
                }

            }
        }
        mtrace('Finished blocks');

        mtrace('Starting admin reports');
        cron_execute_plugin_type('report');
        mtrace('Finished admin reports');

        mtrace('Starting course reports');
        cron_execute_plugin_type('coursereport');
        mtrace('Finished course reports');

        // Run gradebook import/export/report cron.
        mtrace('Starting gradebook plugins');
        cron_execute_plugin_type('gradeimport');
        cron_execute_plugin_type('gradeexport');
        cron_execute_plugin_type('gradereport');
        mtrace('Finished gradebook plugins');

        // All other plugins.
        cron_execute_plugin_type('message', 'message plugins');
        cron_execute_plugin_type('filter', 'filters');
        cron_execute_plugin_type('editor', 'editors');
        cron_execute_plugin_type('format', 'course formats');
        cron_execute_plugin_type('profilefield', 'profile fields');
        cron_execute_plugin_type('webservice', 'webservices');
        cron_execute_plugin_type('repository', 'repository plugins');
        cron_execute_plugin_type('qbehaviour', 'question behaviours');
        cron_execute_plugin_type('qformat', 'question import/export formats');
        cron_execute_plugin_type('qtype', 'question types');
        cron_execute_plugin_type('plagiarism', 'plagiarism plugins');
        cron_execute_plugin_type('theme', 'themes');
        cron_execute_plugin_type('tool', 'admin tools');
        cron_execute_plugin_type('local', 'local plugins');
    }

}
