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

defined('MOODLE_INTERNAL') || die();


class backup_plagiarism_turnitin_plugin extends backup_plagiarism_plugin {

    protected function define_module_plugin_structure() {
        $plugin = $this->get_plugin_element();

        $pluginelement = new backup_nested_element($this->get_recommended_name());
        $plugin->add_child($pluginelement);

        // Add module config elements.
        $turnitinconfigs = new backup_nested_element('turnitin_configs');
        $turnitinconfig = new backup_nested_element('turnitin_config', array('id'), array('name', 'value'));
        $pluginelement->add_child($turnitinconfigs);
        $turnitinconfigs->add_child($turnitinconfig);
        $turnitinconfig->set_source_table('plagiarism_turnitin_config', array('cm' => backup::VAR_PARENTID));

        // Add file elements if required.
        if ($this->get_setting_value('userinfo')) {
            $turnitinfiles = new backup_nested_element('turnitin_files');
            $turnitinfile = new backup_nested_element('turnitin_file', array('id'),
                                array('userid', 'identifier', 'externalid', 'externalstatus',
                                    'statuscode', 'similarityscore', 'transmatch', 'lastmodified', 'grade', 'submissiontype'));
            $pluginelement->add_child($turnitinfiles);
            $turnitinfiles->add_child($turnitinfile);

            $turnitinfile->set_source_table('plagiarism_turnitin_files', array('cm' => backup::VAR_PARENTID));
        }
        return $plugin;
    }

    protected function define_course_plugin_structure() {
        $plugin = $this->get_plugin_element();

        $pluginelement = new backup_nested_element($this->get_recommended_name());
        $plugin->add_child($pluginelement);

        // Add courses from plagiarism_turnitin table.
        $turnitincourses = new backup_nested_element('turnitin_courses');
        $turnitincourse = new backup_nested_element('turnitin_course', array('id'),
            array('courseid', 'turnitin_ctl', 'turnitin_cid'));
        $pluginelement->add_child($turnitincourses);
        $turnitincourses->add_child($turnitincourse);

        $turnitincourse->set_source_table('plagiarism_turnitin_courses', array('courseid' => backup::VAR_COURSEID));
        return $plugin;
    }
}