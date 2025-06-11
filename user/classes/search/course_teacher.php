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
 * Index teachers in a course
 *
 * @package core_user
 * @author  Nathan Nguyen <nathannguyen@catalyst-au.net>
 * @copyright  Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_user\search;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/user/lib.php');

/**
 * Search for user role assignment in a course
 *
 * @package core_user
 * @author  Nathan Nguyen <nathannguyen@catalyst-au.net>
 * @copyright  Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_teacher extends \core_search\base {

    /**
     * The context levels the search implementation is working on.
     *
     * @var array
     */
    protected static $levels = [CONTEXT_COURSE];

    /**
     * Returns the moodle component name.
     *
     * It might be the plugin name (whole frankenstyle name) or the core subsystem name.
     *
     * @return string
     */
    public function get_component_name() {
        return 'course_teacher';
    }

    /**
     * Returns recordset containing required data attributes for indexing.
     *
     * @param number $modifiedfrom
     * @param \context|null $context Optional context to restrict scope of returned results
     * @return \moodle_recordset|null Recordset (or null if no results)
     */
    public function get_document_recordset($modifiedfrom = 0, ?\context $context = null) {
        global $DB;
        $teacherroleids = get_config('core', 'searchteacherroles');

        // Only index teacher roles.
        if (!empty($teacherroleids)) {
            $teacherroleids = explode(',', $teacherroleids);
            list($insql, $inparams) = $DB->get_in_or_equal($teacherroleids, SQL_PARAMS_NAMED);
        } else {
            // Do not index at all.
            list($insql, $inparams) = [' = :roleid', ['roleid' => 0]];
        }

        $params = [
            'coursecontext' => CONTEXT_COURSE,
            'modifiedfrom' => $modifiedfrom
        ];

        $params = array_merge($params, $inparams);

        $recordset = $DB->get_recordset_sql("
            SELECT u.*, ra.contextid, r.shortname as roleshortname, ra.id as itemid, ra.timemodified as timeassigned
              FROM {role_assignments} ra
              JOIN {context} ctx
                ON ctx.id = ra.contextid
               AND ctx.contextlevel = :coursecontext
              JOIN {user} u
                ON u.id = ra.userid
              JOIN {role} r
                ON r.id = ra.roleid
             WHERE ra.timemodified >= :modifiedfrom AND r.id $insql
          ORDER BY ra.timemodified ASC", $params);
        return $recordset;
    }

    /**
     * Returns document instances for each record in the recordset.
     *
     * @param \stdClass $record
     * @param array $options
     * @return \core_search\document
     */
    public function get_document($record, $options = array()) {
        $context = \context::instance_by_id($record->contextid);

        // Content.
        if ($context->contextlevel == CONTEXT_COURSE) {
            $course = get_course($context->instanceid);
            $contentdata = new \stdClass();
            $contentdata->role = ucfirst($record->roleshortname);
            $contentdata->course = $course->fullname;
            $content = get_string('content:courserole', 'core_search', $contentdata);
        } else {
            return false;
        }

        $doc = \core_search\document_factory::instance($record->itemid, $this->componentname, $this->areaname);
        // Assigning properties to our document.
        $doc->set('title', content_to_text(fullname($record), false));
        $doc->set('contextid', $context->id);
        $doc->set('courseid', $context->instanceid);
        $doc->set('itemid', $record->itemid);
        $doc->set('modified', $record->timeassigned);
        $doc->set('owneruserid', \core_search\manager::NO_OWNER_ID);
        $doc->set('userid', $record->id);
        $doc->set('content', $content);

        // Check if this document should be considered new.
        if (isset($options['lastindexedtime']) && $options['lastindexedtime'] < $record->timeassigned) {
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
        $user = $this->get_user($id);
        if (!$user || $user->deleted) {
            return \core_search\manager::ACCESS_DELETED;
        }

        if (user_can_view_profile($user)) {
            return \core_search\manager::ACCESS_GRANTED;
        }

        return \core_search\manager::ACCESS_DENIED;
    }

    /**
     * Returns a url to the document context.
     *
     * @param \core_search\document $doc
     * @return \moodle_url
     */
    public function get_context_url(\core_search\document $doc) {
        $user = $this->get_user($doc->get('itemid'));
        $courseid = $doc->get('courseid');
        return new \moodle_url('/user/view.php', array('id' => $user->id, 'course' => $courseid));
    }

    /**
     * Returns the user fullname to display as document title
     *
     * @param \core_search\document $doc
     * @return string User fullname
     */
    public function get_document_display_title(\core_search\document $doc) {
        $user = $this->get_user($doc->get('itemid'));
        return fullname($user);
    }

    /**
     * Get user based on role assignment id
     *
     * @param int $itemid role assignment id
     * @return mixed
     */
    private function get_user($itemid) {
        global $DB;
        $sql = "SELECT u.*
                  FROM {user} u
                  JOIN {role_assignments} ra
                    ON ra.userid = u.id
                 WHERE ra.id = :raid";
        return $DB->get_record_sql($sql, array('raid' => $itemid));
    }

    /**
     * Returns a list of category names associated with the area.
     *
     * @return array
     */
    public function get_category_names() {
        return [\core_search\manager::SEARCH_AREA_CATEGORY_ALL, \core_search\manager::SEARCH_AREA_CATEGORY_USERS];
    }

    /**
     * Link to the teacher in the course
     *
     * @param \core_search\document $doc the document
     * @return \moodle_url
     */
    public function get_doc_url(\core_search\document $doc) {
        return $this->get_context_url($doc);
    }
}
