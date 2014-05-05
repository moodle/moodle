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
 * @package mod_glossary
 * @subpackage backup-moodle2
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define all the backup steps that will be used by the backup_glossary_activity_task
 */

/**
 * Define the complete glossary structure for backup, with file and id annotations
 */
class backup_glossary_activity_structure_step extends backup_activity_structure_step {

    protected function define_structure() {

        // To know if we are including userinfo
        $userinfo = $this->get_setting_value('userinfo');

        // Define each element separated
        $glossary = new backup_nested_element('glossary', array('id'), array(
            'name', 'intro', 'introformat', 'allowduplicatedentries', 'displayformat',
            'mainglossary', 'showspecial', 'showalphabet', 'showall',
            'allowcomments', 'allowprintview', 'usedynalink', 'defaultapproval',
            'globalglossary', 'entbypage', 'editalways', 'rsstype',
            'rssarticles', 'assessed', 'assesstimestart', 'assesstimefinish',
            'scale', 'timecreated', 'timemodified', 'completionentries'));

        $entries = new backup_nested_element('entries');

        $entry = new backup_nested_element('entry', array('id'), array(
            'userid', 'concept', 'definition', 'definitionformat',
            'definitiontrust', 'attachment', 'timecreated', 'timemodified',
            'teacherentry', 'sourceglossaryid', 'usedynalink', 'casesensitive',
            'fullmatch', 'approved'));

        $aliases = new backup_nested_element('aliases');

        $alias = new backup_nested_element('alias', array('id'), array(
            'alias_text'));

        $ratings = new backup_nested_element('ratings');

        $rating = new backup_nested_element('rating', array('id'), array(
            'component', 'ratingarea', 'scaleid', 'value', 'userid', 'timecreated', 'timemodified'));

        $categories = new backup_nested_element('categories');

        $category = new backup_nested_element('category', array('id'), array(
            'name', 'usedynalink'));

        $categoryentries = new backup_nested_element('category_entries');

        $categoryentry = new backup_nested_element('category_entry', array('id'), array(
            'entryid'));

        // Build the tree
        $glossary->add_child($entries);
        $entries->add_child($entry);

        $entry->add_child($aliases);
        $aliases->add_child($alias);

        $entry->add_child($ratings);
        $ratings->add_child($rating);

        $glossary->add_child($categories);
        $categories->add_child($category);

        $category->add_child($categoryentries);
        $categoryentries->add_child($categoryentry);

        // Define sources
        $glossary->set_source_table('glossary', array('id' => backup::VAR_ACTIVITYID));

        $category->set_source_table('glossary_categories', array('glossaryid' => backup::VAR_PARENTID));

        // All the rest of elements only happen if we are including user info
        if ($userinfo) {
            $entry->set_source_table('glossary_entries', array('glossaryid' => backup::VAR_PARENTID));

            $alias->set_source_table('glossary_alias', array('entryid' => backup::VAR_PARENTID));
            $alias->set_source_alias('alias', 'alias_text');

            $rating->set_source_table('rating', array('contextid'  => backup::VAR_CONTEXTID,
                                                      'itemid'     => backup::VAR_PARENTID,
                                                      'component'  => backup_helper::is_sqlparam('mod_glossary'),
                                                      'ratingarea' => backup_helper::is_sqlparam('entry')));
            $rating->set_source_alias('rating', 'value');

            $categoryentry->set_source_table('glossary_entries_categories', array('categoryid' => backup::VAR_PARENTID));
        }

        // Define id annotations
        $glossary->annotate_ids('scale', 'scale');

        $entry->annotate_ids('user', 'userid');

        $rating->annotate_ids('scale', 'scaleid');

        $rating->annotate_ids('user', 'userid');

        // Define file annotations
        $glossary->annotate_files('mod_glossary', 'intro', null); // This file area hasn't itemid

        $entry->annotate_files('mod_glossary', 'entry', 'id');
        $entry->annotate_files('mod_glossary', 'attachment', 'id');

        // Return the root element (glossary), wrapped into standard activity structure
        return $this->prepare_activity_structure($glossary);
    }
}
