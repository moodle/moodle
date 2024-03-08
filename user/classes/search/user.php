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
     * @param \context|null $context Optional context to restrict scope of returned results
     * @return \moodle_recordset|null Recordset (or null if no results)
     */
    public function get_document_recordset($modifiedfrom = 0, \context $context = null) {
        global $DB;

        // Prepare query conditions.
        $where = 'timemodified >= ? AND deleted = ? AND confirmed = ?';
        $params = [$modifiedfrom, 0, 1];

        // Handle context types.
        if (!$context) {
            $context = \context_system::instance();
        }
        switch ($context->contextlevel) {
            case CONTEXT_MODULE:
            case CONTEXT_BLOCK:
            case CONTEXT_COURSE:
            case CONTEXT_COURSECAT:
                // These contexts cannot contain any users.
                return null;

            case CONTEXT_USER:
                // Restrict to specific user.
                $where .= ' AND id = ?';
                $params[] = $context->instanceid;
                break;

            case CONTEXT_SYSTEM:
                break;

            default:
                throw new \coding_exception('Unexpected contextlevel: ' . $context->contextlevel);
        }

        return $DB->get_recordset_select('user', $where, $params);
    }

    /**
     * Returns document instances for each record in the recordset.
     *
     * @param \stdClass $record
     * @param array $options
     * @return \core_search\document
     */
    public function get_document($record, $options = array()) {

        $context = \context_system::instance();

        // Prepare associative array with data from DB.
        $doc = \core_search\document_factory::instance($record->id, $this->componentname, $this->areaname);
        // Include all alternate names in title.
        $array = [];
        foreach (\core_user\fields::get_name_fields(true) as $field) {
            $array[$field] = $record->$field;
        }
        $fullusername = join(' ', $array);
        // Assigning properties to our document.
        $doc->set('title', content_to_text($fullusername, false));
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
     * Returns the user fullname to display as document title
     *
     * @param \core_search\document $doc
     * @return string User fullname
     */
    public function get_document_display_title(\core_search\document $doc) {

        $user = \core_user::get_user($doc->get('itemid'));
        return fullname($user);
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
     * @return \moodle_url
     */
    public function get_doc_url(\core_search\document $doc) {
        return $this->get_context_url($doc);
    }

    /**
     * Returns a url to the document context.
     *
     * @param \core_search\document $doc
     * @return \moodle_url
     */
    public function get_context_url(\core_search\document $doc) {
        return new \moodle_url('/user/profile.php', array('id' => $doc->get('itemid')));
    }

    /**
     * Returns true if this area uses file indexing.
     *
     * @return bool
     */
    public function uses_file_indexing() {
        return true;
    }

    /**
     * Return the context info required to index files for
     * this search area.
     *
     * Should be onerridden by each search area.
     *
     * @return array
     */
    public function get_search_fileareas() {
        $fileareas = array(
                'profile' // Fileareas.
        );

        return $fileareas;
    }

    /**
     * Returns the moodle component name.
     *
     * It might be the plugin name (whole frankenstyle name) or the core subsystem name.
     *
     * @return string
     */
    public function get_component_name() {
        return 'user';
    }

    /**
     * Returns an icon instance for the document.
     *
     * @param \core_search\document $doc
     *
     * @return \core_search\document_icon
     */
    public function get_doc_icon(\core_search\document $doc): \core_search\document_icon {
        return new \core_search\document_icon('i/user');
    }

    /**
     * Returns a list of category names associated with the area.
     *
     * @return array
     */
    public function get_category_names() {
        return [\core_search\manager::SEARCH_AREA_CATEGORY_USERS];
    }

}
