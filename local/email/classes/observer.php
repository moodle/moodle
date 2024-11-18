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
 * @package   local_email
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_email;

defined('MOODLE_INTERNAL') || die();

use local_email;

class observer {

    /**
     * Consume company created event
     * @param object $event the event object
     */
    public static function company_created($event) {
        global $DB;

        $companyid = $event->objectid;

        // Get the list of template languages.
        $langs = array_keys(get_string_manager()->get_list_of_translations(true));

        // Get all of the templates.
        $templates = array_keys(local_email::get_templates());

        foreach ($langs as $lang) {
            if ($DB->count_records('email_template', ['companyid' => $companyid, 'lang' => $lang]) != count($templates)) {
                foreach ($templates as $template) {
                    $templaterec = (object) [];
                    $templaterec->companyid = $companyid;
                    $templaterec->name = $template;
                    $templaterec->lang = $lang;
                    $DB->execute("INSERT INTO {email_template} (companyid,name,lang)
                                  SELECT :companyid, :name, :lang
                                  WHERE NOT EXISTS (
                                    SELECT * FROM {email_template}
                                    WHERE companyid = :companyid2
                                    AND name = :name2
                                    AND lang = :lang2)",
                                  ['companyid' => $companyid,
                                   'companyid2' => $companyid,
                                   'name' => $template,
                                   'name2' => $template,
                                   'lang' => $lang,
                                   'lang2' => $lang]);
                }
            }
        }

        return true;
    }

    /**
     * Consume langpack imported event
     * @param object $event the event object
     */
    public static function langpack_imported($event) {

        $newlang = $event->other['langcode'];

        // Moved to an adhoc task as it may take a while.
        $importtask = new \local_email\task\importlangpack();
        $importtask->set_custom_data_as_string($newlang);

        // Queue the task.
        \core\task\manager::queue_adhoc_task($importtask);

        return true;
    }

    /**
     * Consume langpack removed event
     * @param object $event the event object
     */
    public static function langpack_removed($event) {
        global $DB, $CFG;

        $oldlang = $event->other['langcode'];

        // Delete for templatesets
        $DB->delete_records('email_templateset_templates', ['lang' => $oldlang]);

        // Delete for companies
        $DB->delete_records('email_template', ['lang' => $oldlang]);

        return true;
    }
}