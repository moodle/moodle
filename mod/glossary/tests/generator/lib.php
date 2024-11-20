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
 * mod_glossary data generator.
 *
 * @package    mod_glossary
 * @category   test
 * @copyright  2013 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * mod_glossary data generator class.
 *
 * @package    mod_glossary
 * @category   test
 * @copyright  2013 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_glossary_generator extends testing_module_generator {

    /**
     * @var int keep track of how many entries have been created.
     */
    protected $entrycount = 0;

    /**
     * @var int keep track of how many entries have been created.
     */
    protected $categorycount = 0;

    /**
     * To be called from data reset code only,
     * do not use in tests.
     * @return void
     */
    public function reset() {
        $this->entrycount = 0;
        $this->categorycount = 0;
        parent::reset();
    }

    public function create_instance($record = null, ?array $options = null) {
        global $CFG;

        // Add default values for glossary.
        $record = (array)$record + array(
            'globalglossary' => 0,
            'mainglossary' => 0,
            'defaultapproval' => $CFG->glossary_defaultapproval,
            'allowduplicatedentries' => $CFG->glossary_dupentries,
            'allowcomments' => $CFG->glossary_allowcomments,
            'usedynalink' => $CFG->glossary_linkbydefault,
            'displayformat' => 'dictionary',
            'approvaldisplayformat' => 'default',
            'entbypage' => !empty($CFG->glossary_entbypage) ? $CFG->glossary_entbypage : 10,
            'showalphabet' => 1,
            'showall' => 1,
            'showspecial' => 1,
            'allowprintview' => 1,
            'rsstype' => 0,
            'rssarticles' => 0,
            'grade' => 100,
            'assessed' => 0,
        );

        return parent::create_instance($record, (array)$options);
    }

    public function create_category($glossary, $record = array(), $entries = array()) {
        global $CFG, $DB;
        $this->categorycount++;
        $record = (array)$record + array(
            'name' => 'Glossary category '.$this->categorycount,
            'usedynalink' => $CFG->glossary_linkbydefault,
        );
        $record['glossaryid'] = $glossary->id;

        $id = $DB->insert_record('glossary_categories', $record);

        if ($entries) {
            foreach ($entries as $entry) {
                $ce = new stdClass();
                $ce->categoryid = $id;
                $ce->entryid = $entry->id;
                $DB->insert_record('glossary_entries_categories', $ce);
            }
        }

        return $DB->get_record('glossary_categories', array('id' => $id), '*', MUST_EXIST);
    }

    public function create_content($glossary, $record = array(), $aliases = array()) {
        global $DB;

        $entry = $this->create_entry((array)$record + ['glossaryid' => $glossary->id]);

        if ($aliases) {
            foreach ($aliases as $alias) {
                $ar = new stdClass();
                $ar->entryid = $entry->id;
                $ar->alias = $alias;
                $DB->insert_record('glossary_alias', $ar);
            }
        }

        if (array_key_exists('tags', $record)) {
            $tags = is_array($record['tags']) ? $record['tags'] : preg_split('/,/', $record['tags']);

            core_tag_tag::set_item_tags('mod_glossary', 'glossary_entries', $entry->id,
                context_module::instance($glossary->cmid), $tags);
        }

        return $entry;
    }

    /**
     * Create an entry.
     *
     * @param array $data Data to create the entry record.
     *        In addition to columns in the entry table, the following attributes are supported:
     *         - glossaryid (required): Id of the glossary where the entry will be created.
     *         - categoryids: Array of ids for the categories this entry belongs to.
     *
     * @return stdClass Entry record.
     */
    public function create_entry(array $data): stdClass {
        global $DB, $USER, $CFG;

        // Prepare glossary.
        $coursemodule = get_coursemodule_from_instance('glossary', $data['glossaryid']);
        $glossary = $DB->get_record('glossary', ['id' => $data['glossaryid']], '*', MUST_EXIST);
        $glossary->cmid = $coursemodule->id;

        unset($data['glossaryid']);

        // Prepare category ids.
        $categoryids = $data['categoryids'] ?? [];

        unset($data['categoryids']);

        // Create entry.
        $this->entrycount++;
        $now = time();
        $record = $data + [
            'glossaryid' => $glossary->id,
            'timecreated' => $now,
            'timemodified' => $now,
            'userid' => $USER->id,
            'concept' => 'Glossary entry '.$this->entrycount,
            'definition' => 'Definition of glossary entry '.$this->entrycount,
            'definitionformat' => FORMAT_MOODLE,
            'definitiontrust' => 0,
            'usedynalink' => $CFG->glossary_linkentries,
            'casesensitive' => $CFG->glossary_casesensitive,
            'fullmatch' => $CFG->glossary_fullmatch
        ];
        if (!isset($record['teacherentry']) || !isset($record['approved'])) {
            $context = context_module::instance($glossary->cmid);
            if (!isset($record['teacherentry'])) {
                $record['teacherentry'] = has_capability('mod/glossary:manageentries', $context, $record['userid']);
            }
            if (!isset($record['approved'])) {
                $defaultapproval = $glossary->defaultapproval;
                $record['approved'] = ($defaultapproval || has_capability('mod/glossary:approve', $context));
            }
        }

        $id = $DB->insert_record('glossary_entries', $record);

        foreach ($categoryids as $categoryid) {
            $DB->insert_record('glossary_entries_categories', ['entryid' => $id, 'categoryid' => $categoryid]);
        }

        $entries =  $DB->get_record('glossary_entries', ['id' => $id], '*', MUST_EXIST);

        if (isset($record['tags'])) {
            $cm = get_coursemodule_from_instance('glossary', $glossary->id);
            $tags = is_array($record['tags']) ? $record['tags'] : explode(',', $record['tags']);

            core_tag_tag::set_item_tags('mod_glossary', 'glossary_entries', $id,
                context_module::instance($cm->id), $tags);
        }

        return $entries;
    }
}
