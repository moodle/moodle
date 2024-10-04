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

namespace mod_bigbluebuttonbn\search;

use stdClass;

/**
 * Search area for mod_bigbluebuttonbn tags.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2010 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tags extends \core_search\base_activity {

    /**
     * Returns true if this area uses file indexing.
     *
     * @return bool
     */
    public function uses_file_indexing() {
        return false;
    }

    /**
     * Overwritting get_document_recordset()
     * In this search implementation, we need to re-index all instances (and not only the last modified) because we
     * are working with core tags and these can be removed from "manage tags" without change the timemodified in
     * BBB instances.
     * @param int $modifiedfrom
     * @param \context|null $context
     * @return \moodle_recordset|null
     */
    public function get_document_recordset($modifiedfrom = 0, ?\context $context = null) {
        global $DB;
        [$contextjoin, $contextparams] = $this->get_context_restriction_sql($context, $this->get_module_name(), 'modtable');
        if ($contextjoin === null) {
            return null;
        }

        $result = $DB->get_recordset_sql(
            'SELECT modtable.* FROM {' . $this->get_module_name() .  '} modtable ' . $contextjoin,
            array_merge($contextparams)
        );

        return($result);
    }

    /**
     * Overriding method to index tags of module as string separated by comma.
     *
     * @param stdClass $record
     * @param array    $options
     * @return \core_search\document|bool
     */
    public function get_document($record, $options = []) {

        try {
            $cm = $this->get_cm($this->get_module_name(), $record->id, $record->course);
            $context = \context_module::instance($cm->id);

            $tags = \core_tag_tag::get_tags_by_area_in_contexts("core", "course_modules", [$context]);
            $tagsstring = "";
            if (!empty($tags)) {
                $res = [];
                foreach ($tags as $t) {
                    $res[] = $t->name;
                }
                $tagsstring = implode(", ", $res);
            }

        } catch (\dml_missing_record_exception $ex) {
            // Notify it as we run here as admin, we should see everything.
            debugging('Error retrieving ' . $this->areaid . ' ' . $record->id . ' document, not all required data is available: ' .
                $ex->getMessage(), DEBUG_DEVELOPER);
            return false;
        } catch (\dml_exception $ex) {
            // Notify it as we run here as admin, we should see everything.
            debugging('Error retrieving ' . $this->areaid . ' ' . $record->id . ' document: ' . $ex->getMessage(), DEBUG_DEVELOPER);
            return false;
        }

        // Prepare array with data from DB.
        $doc = \core_search\document_factory::instance($record->id, $this->componentname, $this->areaname);
        $doc->set('title', content_to_text($record->name, false));
        $doc->set('content', $tagsstring);
        $doc->set('contextid', $context->id);
        $doc->set('courseid', $record->course);
        $doc->set('owneruserid', \core_search\manager::NO_OWNER_ID);
        $doc->set('modified', $record->{static::MODIFIED_FIELD_NAME});

        // Check if this document should be considered new.
        if (isset($options['lastindexedtime'])) {
            $createdfield = static::CREATED_FIELD_NAME;
            if (!empty($createdfield) && ($options['lastindexedtime'] < $record->{$createdfield})) {
                // If the document was created after the last index time, it must be new.
                $doc->set_is_new(true);
            }
        }

        return $doc;
    }
}
