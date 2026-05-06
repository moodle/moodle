<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace core_admin\setting\setting;

use core_admin\admin_search;

/**
 * External services management.
 *
 * @package    core_admin
 * @copyright  2024 onwards Moodle Pty Ltd {@link https://moodle.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manageexternalservices extends \core_admin\setting {
    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        $this->nosave = true;
        parent::__construct('webservicesui', get_string('externalservices', 'webservice'), '', '');
    }

    #[\Override]
    public function get_setting() {
        return true;
    }

    #[\Override]
    public function get_defaultsetting() {
        return true;
    }

    /**
     * Always returns '', does not write anything
     *
     * @return string Always returns ''
     */
    #[\Override]
    public function write_setting($data) {
        // Do not write any setting.
        return '';
    }

    #[\Override]
    public function is_related($query) {
        global $DB;

        if (parent::is_related($query)) {
            return true;
        }

        $services = $DB->get_records('external_services', [], 'id, name');
        foreach ($services as $service) {
            if (strpos(\core_text::strtolower($service->name), $query) !== false) {
                $this->searchmatchtype = admin_search::SEARCH_MATCH_SETTING_DISPLAY_NAME;
                return true;
            }
        }
        return false;
    }

    #[\Override]
    public function output_html($data, $query = '') {
        global $CFG, $OUTPUT, $DB;

        // Display strings.
        $stradministration = get_string('administration');
        $stredit = get_string('edit');
        $strservice = get_string('externalservice', 'webservice');
        $strdelete = get_string('delete');
        $strplugin = get_string('plugin', 'admin');
        $stradd = get_string('add');
        $strfunctions = get_string('functions', 'webservice');
        $strusers = get_string('users');
        $strserviceusers = get_string('serviceusers', 'webservice');

        $esurl = "$CFG->wwwroot/$CFG->admin/webservice/service.php";
        $efurl = "$CFG->wwwroot/$CFG->admin/webservice/service_functions.php";
        $euurl = "$CFG->wwwroot/$CFG->admin/webservice/service_users.php";

        // Built in services.
         $services = $DB->get_records_select('external_services', 'component IS NOT NULL', null, 'name');
         $return = "";
        if (!empty($services)) {
            $return .= $OUTPUT->heading(get_string('servicesbuiltin', 'webservice'), 3, 'main');

            $table = new \html_table();
            $table->head  = [$strservice, $strplugin, $strfunctions, $strusers, $stredit];
            $table->colclasses = [
                'leftalign service',
                'leftalign plugin',
                'centeralign functions',
                'centeralign users',
                'centeralign ',
            ];
            $table->id = 'builtinservices';
            $table->attributes['class'] = 'admintable externalservices table generaltable table-hover';
            $table->data  = [];

            // Iterate through auth plugins and add to the display table.
            foreach ($services as $service) {
                $name = $service->name;

                // Hide/show link.
                if ($service->enabled) {
                    $displayname = "<span>$name</span>";
                } else {
                    $displayname = "<span class=\"dimmed_text\">$name</span>";
                }

                $plugin = $service->component;

                $functions = "<a href=\"$efurl?id=$service->id\">$strfunctions</a>";

                if ($service->restrictedusers) {
                    $users = "<a href=\"$euurl?id=$service->id\">$strserviceusers</a>";
                } else {
                    $users = get_string('allusers', 'webservice');
                }

                $edit = "<a href=\"$esurl?id=$service->id\">$stredit</a>";

                // Add a row to the table.
                $table->data[] = [$displayname, $plugin, $functions, $users, $edit];
            }
            $return .= \html_writer::table($table);
        }

        // Custom services.
        $return .= $OUTPUT->heading(get_string('servicescustom', 'webservice'), 3, 'main');
        $services = $DB->get_records_select('external_services', 'component IS NULL', null, 'name');

        $table = new \html_table();
        $table->head  = [$strservice, $strdelete, $strfunctions, $strusers, $stredit];
        $table->colclasses = [
            'leftalign service',
            'leftalign plugin',
            'centeralign functions',
            'centeralign users',
            'centeralign ',
        ];
        $table->id = 'customservices';
        $table->attributes['class'] = 'admintable externalservices table generaltable table-hover';
        $table->data  = [];

        // Iterate through auth plugins and add to the display table.
        foreach ($services as $service) {
            $name = $service->name;

            // Hide/show link.
            if ($service->enabled) {
                $displayname = "<span>$name</span>";
            } else {
                $displayname = "<span class=\"dimmed_text\">$name</span>";
            }

            // Delete link.
            $delete = "<a href=\"$esurl?action=delete&amp;sesskey=" . sesskey() . "&amp;id=$service->id\">$strdelete</a>";

            $functions = "<a href=\"$efurl?id=$service->id\">$strfunctions</a>";

            if ($service->restrictedusers) {
                $users = "<a href=\"$euurl?id=$service->id\">$strserviceusers</a>";
            } else {
                $users = get_string('allusers', 'webservice');
            }

            $edit = "<a href=\"$esurl?id=$service->id\">$stredit</a>";

            // Add a row to the table.
            $table->data[] = [$displayname, $delete, $functions, $users, $edit];
        }
        // Add new custom service option.
        $return .= \html_writer::table($table);

        $return .= '<br />';
        // Add a token to the table.
        $return .= "<a href=\"$esurl?id=0\">$stradd</a>";

        return highlight($query, $return);
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(manageexternalservices::class, \admin_setting_manageexternalservices::class);
