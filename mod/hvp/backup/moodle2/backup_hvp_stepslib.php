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
 * Defines backup structure steps for both hvp content and hvp libraries.
 *
 * @package     mod_hvp
 * @category    backup
 * @copyright   2016 Joubel AS <contact@joubel.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Define the complete hvp structure for backup, with file and id annotations
 *
 * @copyright   2018 Joubel AS <contact@joubel.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_hvp_activity_structure_step extends backup_activity_structure_step {

    /**
     * Defines backup element's structure
     *
     * @return backup_nested_element
     * @throws base_element_struct_exception
     * @throws base_step_exception
     */
    protected function define_structure() {

        // To know if we are including user info.
        $userinfo = $this->get_setting_value('userinfo');

        // Define each element separated.
        $hvp = new backup_nested_element('hvp', array('id'), array(
            'name',
            'machine_name',
            'major_version',
            'minor_version',
            'intro',
            'introformat',
            'json_content',
            'embed_type',
            'disable',
            'content_type',
            'source',
            'year_from',
            'year_to',
            'license_version',
            'changes',
            'license_extras',
            'author_comments',
            'slug',
            'timecreated',
            'timemodified',
            'authors',
            'license',
            'completionpass'
        ));

        // User data.
        $entries = new backup_nested_element('content_user_data');
        $contentuserdata = new backup_nested_element('entry', array(
            'user_id', // Annotated.
            'sub_content_id'
            ), array(
            'data_id',
            'data',
            'preloaded',
            'delete_on_content_change',
        ));

        // Build the tree.

        $hvp->add_child($entries);
        $entries->add_child($contentuserdata);

        // Define sources.

        // Uses library name and version instead of main_library_id.
        $hvp->set_source_sql('
          SELECT h.id,
                 hl.machine_name,
                 hl.major_version,
                 hl.minor_version,
                 h.name,
                 h.intro,
                 h.introformat,
                 h.json_content,
                 h.embed_type,
                 h.disable,
                 h.content_type,
                 h.slug,
                 h.timecreated,
                 h.timemodified,
                 h.authors,
                 h.source,
                 h.year_from,
                 h.year_to,
                 h.license_version,
                 h.changes,
                 h.license_extras,
                 h.author_comments,
                 h.license,
                 h.completionpass
          FROM {hvp} h
              JOIN {hvp_libraries} hl ON hl.id = h.main_library_id
              WHERE h.id = ?', array(backup::VAR_ACTIVITYID));

        // All the rest of elements only happen if we are including user info.
        if ($userinfo) {
            $contentuserdata->set_source_table('hvp_content_user_data', array('hvp_id' => backup::VAR_PARENTID));
        }

        // Define id annotations.
        $contentuserdata->annotate_ids('user', 'user_id');
        // In an ideal world we would use the main_library_id and annotate that
        // but since we cannot know the required dependencies of the content
        // without parsing json_content and crawling the libraries_libraries
        // (library dependencies) table it's much easier to just include all
        // installed libraries.

        // Define file annotations.
        $hvp->annotate_files('mod_hvp', 'intro', null, null);
        $hvp->annotate_files('mod_hvp', 'content', null, null);

        // Return the root element (hvp), wrapped into standard activity structure.
        return $this->prepare_activity_structure($hvp);
    }
}

/**
 * Backup h5p libraries.
 *
 * Structure step in charge of constructing the hvp_libraries.xml file for
 * all the H5P libraries.
 *
 * @copyright   2018 Joubel AS <contact@joubel.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_hvp_libraries_structure_step extends backup_structure_step {

    /**
     * Determines if backup step should be executed
     *
     * @return bool
     * @throws backup_step_exception
     */
    protected function execute_condition() {
        $fullpath = $this->task->get_taskbasepath();
        if (empty($fullpath)) {
            throw new backup_step_exception('backup_structure_step_undefined_fullpath');
        }

        // Modify filename to use a globally shared file for all libraries.
        $this->filename = "../{$this->filename}";

        // Append the filename to the full path.
        $fullpath = rtrim($fullpath, '/') . '/' . $this->filename;

        // Determine if already generated.
        return !file_exists($fullpath);
    }

    /**
     * Defines the structure to be executed by this backup step
     *
     * @return backup_nested_element
     * @throws base_element_struct_exception
     * @throws dml_exception
     */
    protected function define_structure() {
        // Libraries.
        $libraries = new backup_nested_element('hvp_libraries');
        $library = new backup_nested_element('library', array('id'), array(
            'title',
            'machine_name',
            'major_version',
            'minor_version',
            'patch_version',
            'runnable',
            'fullscreen',
            'embed_types',
            'preloaded_js',
            'preloaded_css',
            'drop_library_css',
            'semantics',
            'restricted',
            'tutorial_url',
            'add_to',
            'metadata'
        ));

        // Library translations.
        $translations = new backup_nested_element('translations');
        $translation = new backup_nested_element('translation', array(
            'language_code'
        ), array(
            'language_json'
        ));

        // Library dependencies.
        $dependencies = new backup_nested_element('dependencies');
        $dependency = new backup_nested_element('dependency', array(
            'required_library_id'
        ), array(
            'dependency_type'
        ));

        // Build the tree.
        $libraries->add_child($library);

        $library->add_child($translations);
        $translations->add_child($translation);

        $library->add_child($dependencies);
        $dependencies->add_child($dependency);

        // Define sources.

        $library->set_source_table('hvp_libraries', array());

        $translation->set_source_table('hvp_libraries_languages', array('library_id' => backup::VAR_PARENTID));

        $dependency->set_source_table('hvp_libraries_libraries', array('library_id' => backup::VAR_PARENTID));

        // Define file annotations.
        $context = \context_system::instance();
        $library->annotate_files('mod_hvp', 'libraries', null, $context->id);

        // Return root element.
        return $libraries;
    }
}
