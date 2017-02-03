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
 * Search area for Users for whom I have authority to view profile.
 *
 * @package    core_user
 * @copyright  2016 Devang Gaur {@link http://www.devanggaur.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_user\search;

require_once($CFG->dirroot . '/user/lib.php');

defined('MOODLE_INTERNAL') || die();

/**
 * Search area for Users for whom I have access to view profile.
 *
 * @package    core_user
 * @copyright  2016 Devang Gaur {@link http://www.devanggaur.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user extends \core_search\base {

    /**
     * Returns recordset containing required data attributes for indexing.
     *
     * @param number $modifiedfrom
     * @return \moodle_recordset
     */
    public function get_recordset_by_timestamp($modifiedfrom = 0) {
        global $DB;
        return $DB->get_recordset_select('user', 'timemodified >= ? AND deleted = ? AND
                confirmed = ?', array($modifiedfrom, 0, 1));
    }

    /**
     * Returns document instances for each record in the recordset.
     *
     * @param StdClass $record
     * @param array $options
     * @return core_search/document
     */
    public function get_document($record, $options = array()) {

        $context = \context_system::instance();

        // Prepare associative array with data from DB.
        $doc = \core_search\document_factory::instance($record->id, $this->componentname, $this->areaname);
        // Assigning properties to our document.
        $doc->set('title', content_to_text(fullname($record), false));
        $doc->set('contextid', $context->id);
        $doc->set('courseid', SITEID);
        $doc->set('itemid', $record->id);
        $doc->set('modified', $record->timemodified);
        $doc->set('owneruserid', \core_search\manager::NO_OWNER_ID);
        $doc->set('content', content_to_text($record->description, $record->descriptionformat));

        // Check if this document should be considered new.
        if (isset($options['lastindexedtime']) && $options['lastindexedtime'] < $record->timecreated) {
            // If the document was created after the last index time, it must be new.
            $doc->set_is_new(true);
        }

        return $doc;
    }

    /**
     * Checking whether I can access a document
     *
     * @param int $id user id
     * @return int
     */
    public function check_access($id) {
        global $DB, $USER;

        $user = $DB->get_record('user', array('id' => $id));
        if (!$user || $user->deleted) {
            return \core_search\manager::ACCESS_DELETED;
        }

        if (user_can_view_profile($user)) {
            return \core_search\manager::ACCESS_GRANTED;
        }

        return \core_search\manager::ACCESS_DENIED;
    }

    /**
     * Returns a url to the profile page of user.
     *
     * @param \core_search\document $doc
     * @return moodle_url
     */
    public function get_doc_url(\core_search\document $doc) {
        return $this->get_context_url($doc);
    }

    /**
     * Returns a url to the document context.
     *
     * @param \core_search\document $doc
     * @return moodle_url
     */
    public function get_context_url(\core_search\document $doc) {
        return new \moodle_url('/user/profile.php', array('id' => $doc->get('itemid')));
    }
}
