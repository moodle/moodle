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
 * An adhoc task for local Iomad track
 *
 * @package    local_iomad_track
 * @copyright  2020 E-Learn Design https://www.e-learndesign.co.uk
 * @author     Derick Turner
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_email\task;

defined('MOODLE_INTERNAL') || die();

use core\task\adhoc_task;
use \local_email;

class importlangpack extends adhoc_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('importlangpackaddhoc', 'local_email');
    }

    /**
     * Run importlangpack
     */
    public function execute() {
        global $DB, $CFG;

        // Mark that something is happening.
        set_config('local_email_templates_migrating', 1);

        // Get the languages.
        $newlang = $this->get_custom_data_as_string();
        $sitelang = $CFG->lang;

        // Get all of the templates.
        $templates = array_keys(local_email::get_templates());

        // Deal with templatesets.
        $templatesets = $DB->get_records('email_templateset', [], 'id', 'id');
        foreach ($templatesets as $templateset) {
            foreach ($templates as $template) {
                if ($templaterec = $DB->get_record('email_templateset_templates', ['templateset' => $templateset->id, 'name' => $template, 'lang' => $sitelang])) {
                    unset($templaterec->id);
                    $templaterec->lang = $newlang;
                    $DB->insert_record('email_templateset_templates', $templaterec);
                }
            }
        }

        // Deal with companies.
        $companies = $DB->get_records('company', [], 'id', 'id');
        foreach ($companies as $company) {
            foreach ($templates as $template) {
                if ($templaterec = $DB->get_record('email_template', ['companyid' => $company->id, 'name' => $template, 'lang' => $sitelang])) {
                    unset($templaterec->id);
                    $templaterec->lang = $newlang;
                    $DB->insert_record('email_template', $templaterec);
                }
            }
        }

        // Mark that we are done.
        unset_config('local_email_templates_migrating', '');
    }

    /**
     * Queues the task.
     *
     */
    public static function queue_task() {

        // Let's set up the adhoc task.
        $task = new \local_email\task\importlangpack();
        \core\task\manager::queue_adhoc_task($task, true);
    }
}
