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
 * @package mod_dataform
 * @copyright 2011 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define all the backup steps that will be used by the backup_dataform_activity_task.
 */

/**
 * Define the complete data structure for backup, with file and id annotations.
 */
class backup_dataform_activity_structure_step extends backup_activity_structure_step {

    protected function define_structure() {
        global $CFG;

        // To know if we are including userinfo.
        $userinfo = $this->get_setting_value('userinfo');

        // Define each element separated.
        $dataform = new backup_nested_element('dataform', array('id'), array(
            'name', 'intro', 'introformat', 'inlineview', 'embedded',
            'timemodified', 'timeavailable', 'timedue', 'timeinterval', 'intervalcount',
            'grade', 'gradeitems',
            'maxentries', 'entriesrequired', 'individualized', 'grouped', 'anonymous', 'timelimit',
            'css', 'cssincludes', 'js', 'jsincludes',
            'defaultview', 'defaultfilter', 'completionentries', 'completionspecificgrade'));

        $fields = new backup_nested_element('fields');
        $field = new backup_nested_element('field', array('id'), array(
            'type', 'name', 'description', 'visible', 'editable',
            'label', 'defaultcontentmode', 'defaultcontent',
            'param1', 'param2', 'param3', 'param4', 'param5',
            'param6', 'param7', 'param8', 'param9', 'param10'));

        $filters = new backup_nested_element('filters');
        $filter = new backup_nested_element('filter', array('id'), array(
            'name', 'description',
            'visible', 'perpage', 'selection',
            'search', 'customsort', 'customsearch'));

        $views = new backup_nested_element('views');
        $view = new backup_nested_element('view', array('id'), array(
            'type', 'name', 'description',
            'visible', 'perpage', 'filterid', 'patterns', 'submission', 'section',
            'param1', 'param2', 'param3', 'param4', 'param5',
            'param6', 'param7', 'param8', 'param9', 'param10'));

        $entries = new backup_nested_element('entries');
        $entry = new backup_nested_element('entry', array('id'), array(
            'userid', 'groupid', 'timecreated', 'timemodified', 'state'));

        $contents = new backup_nested_element('contents');
        $content = new backup_nested_element('content', array('id'), array(
            'fieldid', 'content', 'content1', 'content2', 'content3', 'content4'));

        $ratings = new backup_nested_element('ratings');
        $rating = new backup_nested_element('rating', array('id'), array(
            'component', 'ratingarea', 'scaleid', 'value', 'userid', 'timecreated', 'timemodified'));

        // Build the tree.
        $dataform->add_child($fields);
        $fields->add_child($field);

        $dataform->add_child($filters);
        $filters->add_child($filter);

        $dataform->add_child($views);
        $views->add_child($view);

        $dataform->add_child($entries);
        $entries->add_child($entry);

        $entry->add_child($contents);
        $contents->add_child($content);

        $entry->add_child($ratings);
        $ratings->add_child($rating);

        // Define sources.
        $dataform->set_source_table('dataform', array('id' => backup::VAR_ACTIVITYID));
        $field->set_source_table('dataform_fields', array('dataid' => backup::VAR_PARENTID));
        $filter->set_source_table('dataform_filters', array('dataid' => backup::VAR_PARENTID));
        $view->set_source_table('dataform_views', array('dataid' => backup::VAR_PARENTID));

        // All the rest of elements only happen if we are including user info.
        if ($userinfo) {
            $entry->set_source_table('dataform_entries', array('dataid' => backup::VAR_PARENTID));
            $content->set_source_table('dataform_contents', array('entryid' => backup::VAR_PARENTID));

            // Entry ratings.
            $rating->set_source_table('rating', array(
                'contextid'  => backup::VAR_CONTEXTID,
                'itemid'     => backup::VAR_PARENTID,
                'component'  => backup_helper::is_sqlparam('mod_dataform')));
            $rating->set_source_alias('rating', 'value');
        }

        // Define id annotations.
        $entry->annotate_ids('user', 'userid');
        $entry->annotate_ids('group', 'groupid');

        $rating->annotate_ids('scale', 'scaleid');
        $rating->annotate_ids('user', 'userid');

        // Define file annotations.
        $dataform->annotate_files('mod_dataform', 'intro', null); // This file area hasn't itemid.
        $dataform->annotate_files('mod_dataform', 'activityicon', null); // This file area hasn't itemid.
        $content->annotate_files('mod_dataform', 'content', 'id'); // By content->id.
        $this->annotate_dataformplugin_files('dataformfield', $field); // By field->id.
        $this->annotate_dataformplugin_files('dataformview', $view); // By view->id.

        // Return the root element (data), wrapped into standard activity structure.
        return $this->prepare_activity_structure($dataform);
    }

    protected function annotate_dataformplugin_files($plugintype, backup_nested_element $bne) {
        global $CFG;

        $plugins = core_component::get_plugin_list($plugintype);
        foreach ($plugins as $type => $path) {
            $pluginclass = $plugintype. "_$type". "_$type";
            foreach ($pluginclass::get_file_areas() as $filearea) {
                $bne->annotate_files($pluginclass, $filearea, 'id'); // By id.
            }
        }
    }
}
