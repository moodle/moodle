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
 * @package moodlecore
 * @subpackage backup-helper
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot.'/backup/util/xml/parser/processors/grouped_parser_processor.class.php');

/**
 * helper implementation of grouped_parser_processor that will
 * load all the categories and questions (header info only) from the questions.xml file
 * to the backup_ids table storing the whole structure there for later processing.
 * Note: only "needed" categories are loaded (must have question_categoryref record in backup_ids)
 * Note: parentitemid will contain the category->contextid for categories
 * Note: parentitemid will contain the category->id for questions
 *
 * TODO: Complete phpdocs
 */
class restore_questions_parser_processor extends grouped_parser_processor {
    /** @var string XML path in the questions.xml backup file to question categories. */
    protected const CATEGORY_PATH = '/question_categories/question_category';

    /** @var string XML path in the questions.xml to question elements within question_category (Moodle 4.0+). */
    protected const QUESTION_SUBPATH =
        '/question_bank_entries/question_bank_entry/question_version/question_versions/questions/question';

    /** @var string XML path in the questions.xml to question elements within question_category (before Moodle 4.0). */
    protected const LEGACY_QUESTION_SUBPATH = '/questions/question';

    /** @var string String for concatenating data into a string for hashing.*/
    protected const HASHDATA_SEPARATOR = '|HASHDATA|';

    /** @var string identifies the current restore. */
    protected string $restoreid;

    /** @var int during the restore, this tracks the last category we saw. Any questions we see will be in here. */
    protected int $lastcatid;

    public function __construct($restoreid) {
        global $CFG;
        $this->restoreid = $restoreid;
        $this->lastcatid = 0;
        parent::__construct();
        // Set the paths we are interested on
        $this->add_path(self::CATEGORY_PATH);
        $this->add_path(self::CATEGORY_PATH . self::QUESTION_SUBPATH, true);
        $this->add_path(self::CATEGORY_PATH . self::LEGACY_QUESTION_SUBPATH, true);

        // Add all sub-elements, including those from plugins, as grouped paths with the question tag so that
        // we can create a hash of all question data for comparison with questions in the database.
        $this->add_path(self::CATEGORY_PATH . self::QUESTION_SUBPATH . '/question_hints');
        $this->add_path(self::CATEGORY_PATH . self::LEGACY_QUESTION_SUBPATH . '/question_hints');
        $this->add_path(self::CATEGORY_PATH . self::QUESTION_SUBPATH . '/question_hints/question_hint');
        $this->add_path(self::CATEGORY_PATH . self::LEGACY_QUESTION_SUBPATH . '/question_hints/question_hint');

        $connectionpoint = new restore_path_element('question', self::CATEGORY_PATH . self::QUESTION_SUBPATH);
        foreach (\core\plugin_manager::instance()->get_plugins_of_type('qtype') as $qtype) {
            $restore = $this->get_qtype_restore($qtype->name);
            if (!$restore) {
                continue;
            }
            $structure = $restore->define_plugin_structure($connectionpoint);
            foreach ($structure as $element) {
                $subpath = str_replace(self::CATEGORY_PATH . self::QUESTION_SUBPATH . '/', '', $element->get_path());
                $pathparts = explode('/', $subpath);
                $path = self::CATEGORY_PATH . self::QUESTION_SUBPATH;
                $legacypath = self::CATEGORY_PATH . self::LEGACY_QUESTION_SUBPATH;
                foreach ($pathparts as $part) {
                    $path .= '/' . $part;
                    $legacypath .= '/' . $part;
                    if (!in_array($path, $this->paths)) {
                        $this->add_path($path);
                        $this->add_path($legacypath);
                    }
                }
            }
        }
    }

    protected function dispatch_chunk($data) {
        // Prepare question_category record
        if ($data['path'] == self::CATEGORY_PATH) {
            $info     = (object)$data['tags'];
            $itemname = 'question_category';
            $itemid   = $info->id;
            $parentitemid = $info->contextid;
            $this->lastcatid = $itemid;

        // Prepare question record
        } else if ($data['path'] == self::CATEGORY_PATH . self::QUESTION_SUBPATH ||
                $data['path'] == self::CATEGORY_PATH . self::LEGACY_QUESTION_SUBPATH) {
            // Remove sub-elements from the question info we're going to save.
            $info = (object) array_filter($data['tags'], fn($tag) => !is_array($tag));
            $itemname = 'question';
            $itemid   = $info->id;
            $parentitemid = $this->lastcatid;
            $restore = $this->get_qtype_restore($data['tags']['qtype']);
            if ($restore) {
                $questiondata = $restore->convert_backup_to_questiondata($data['tags']);
            } else {
                $questiondata = restore_qtype_plugin::convert_backup_to_questiondata($data['tags']);
            }
            // Store a hash of question fields for comparison with existing questions.
            $info->questionhash = $this->generate_question_identity_hash($questiondata);

        // Not question_category nor question, impossible. Throw exception.
        } else {
            throw new progressive_parser_exception('restore_questions_parser_processor_unexpected_path', $data['path']);
        }

        // Only load it if needed (exist same question_categoryref itemid in table)
        if (restore_dbops::get_backup_ids_record($this->restoreid, 'question_categoryref', $this->lastcatid)) {
            restore_dbops::set_backup_ids_record($this->restoreid, $itemname, $itemid, 0, $parentitemid, $info);
        }
    }

    protected function notify_path_start($path) {
        // nothing to do
    }

    protected function notify_path_end($path) {
        // nothing to do
    }

    /**
     * Provide NULL decoding
     */
    public function process_cdata($cdata) {
        if ($cdata === '$@NULL@$') {
            return null;
        }
        return $cdata;
    }

    /**
     * Load and instantiate the restore class for the given question type.
     *
     * If there is no restore class, null is returned.
     *
     * @param string $qtype The question type name (no qtype_ prefix)
     * @return ?restore_qtype_plugin
     */
    protected static function get_qtype_restore(string $qtype): ?restore_qtype_plugin {
        global $CFG;
        $step = new restore_quiz_activity_structure_step('questions', 'question.xml');
        $filepath = "{$CFG->dirroot}/question/type/{$qtype}/backup/moodle2/restore_qtype_{$qtype}_plugin.class.php";
        if (!file_exists($filepath)) {
            return null;
        }
        require_once($filepath);
        $restoreclass = "restore_qtype_{$qtype}_plugin";
        if (!class_exists($restoreclass)) {
            return null;
        }
        return new $restoreclass('qtype', $qtype, $step);
    }

    /**
     * Given a data structure containing the data for a question, reduce it to a flat array and return a sha1 hash of the data.
     *
     * @param stdClass $questiondata An array containing all the data for a question, including hints and qtype plugin data.
     * @param ?backup_xml_transformer $transformer If provided, run the backup transformer process on all text fields. This ensures
     *     that values from the database are compared like-for-like with encoded values from the backup.
     * @return string A sha1 hash of all question data, normalised and concatenated together.
     */
    public static function generate_question_identity_hash(
        stdClass $questiondata,
        ?backup_xml_transformer $transformer = null,
    ): string {
        $questiondata = clone($questiondata);
        $restore = self::get_qtype_restore($questiondata->qtype);
        if ($restore) {
            $restore->define_plugin_structure(new restore_path_element('question', self::CATEGORY_PATH . self::QUESTION_SUBPATH));
            // Combine default exclusions with those specified by the plugin.
            $questiondata = $restore->remove_excluded_question_data($questiondata, $restore->get_excluded_identity_hash_fields());
        } else {
            // The qtype has no restore class, use the default reduction method.
            $questiondata = restore_qtype_plugin::remove_excluded_question_data($questiondata);
        }

        // Convert questiondata to a flat array of values.
        $hashdata = [];
        // Convert the object to a multi-dimensional array for compatibility with array_walk_recursive.
        $questiondata = json_decode(json_encode($questiondata), true);
        array_walk_recursive($questiondata, function($value) use (&$hashdata) {
            // Normalise data types. Depending on where the data comes from, it may be a mixture of nulls, strings,
            // ints and floats. Convert everything to strings, then all numbers to floats to ensure we are doing
            // like-for-like comparisons without losing accuracy.
            $value = (string) $value;
            if (is_numeric($value)) {
                $value = (float) ($value);
            } else if (str_contains($value, "\r\n")) {
                // Normalise line breaks.
                $value = str_replace("\r\n", "\n", $value);
            }
            $hashdata[] = $value;
        });

        sort($hashdata, SORT_STRING);
        $hashstring = implode(self::HASHDATA_SEPARATOR, $hashdata);
        if ($transformer) {
            $hashstring = $transformer->process($hashstring);
            // Need to re-sort the hashdata with the transformed strings.
            $hashdata = explode(self::HASHDATA_SEPARATOR, $hashstring);
            sort($hashdata, SORT_STRING);
            $hashstring = implode(self::HASHDATA_SEPARATOR, $hashdata);
        }
        return sha1($hashstring);
    }
}
