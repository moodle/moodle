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
 * Provides the information to backup question custom field.
 *
 * @package    qbank_customfields
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Matt Porritt <mattp@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_qbank_customfields_plugin extends \backup_qbank_plugin {

    /**
     * Returns the comment information to attach to question element.
     *
     * @return backup_plugin_element The backup plugin element
     */
    protected function define_question_plugin_structure(): backup_plugin_element {

        // Define the virtual plugin element with the condition to fulfill.
        $plugin = $this->get_plugin_element();

        // Create one standard named plugin element (the visible container).
        $pluginwrapper = new backup_nested_element($this->get_recommended_name());

        // Connect the visible container ASAP.
        $plugin->add_child($pluginwrapper);

        $customfields = new backup_nested_element('customfields');
        $customfield = new backup_nested_element('customfield', ['id'],
            ['shortname', 'type', 'value', 'valueformat', 'valuetrust']);

        $pluginwrapper->add_child($customfields);
        $customfields->add_child($customfield);

        $customfield->set_source_sql("SELECT cfd.id, cff.shortname, cff.type,  cfd.value, cfd.valueformat, cfd.valuetrust
                                        FROM {customfield_data} cfd
                                        JOIN {customfield_field} cff ON cff.id = cfd.fieldid
                                        JOIN {customfield_category} cfc ON cfc.id = cff.categoryid
                                       WHERE cfc.component = 'qbank_customfields'
                                         AND cfc.area = 'question'
                                         AND cfd.instanceid = ?",
                [
                        backup::VAR_PARENTID
                ]);

        // Don't need to annotate ids nor files.

        return $plugin;
    }
}
