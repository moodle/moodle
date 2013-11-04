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
     * To be called from data reset code only,
     * do not use in tests.
     * @return void
     */
    public function reset() {
        $this->entrycount = 0;
        parent::reset();
    }

    public function create_instance($record = null, array $options = null) {
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

    public function create_content($glossary, $record = array()) {
        global $DB, $USER, $CFG;
        $this->entrycount++;
        $now = time();
        $record = (array)$record + array(
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
        );
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
        return $DB->get_record('glossary_entries', array('id' => $id), '*', MUST_EXIST);
    }
}
