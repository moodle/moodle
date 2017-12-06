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
 * Search area base class for messages.
 *
 * @package    core_message
 * @copyright  2016 Devang Gaur
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_message\search;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/message/lib.php');

/**
 * Search area base class for messages.
 *
 * @package    core_message
 * @copyright  2016 Devang Gaur
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class base_message extends \core_search\base {

    /**
     * The context levels the search area is working on.
     * @var array
     */
    protected static $levels = [CONTEXT_USER];

    /**
     * Returns the document associated with this message record.
     *
     * @param stdClass $record
     * @param array    $options
     * @return \core_search\document
     */
    public function get_document($record, $options = array()) {

        // Check if user still exists, before proceeding.
        $user = \core_user::get_user($options['user1id'], 'deleted');
        if ($user->deleted == 1) {
            return false;
        }

        // Get user context.
        try {
            $usercontext = \context_user::instance($options['user1id']);
        } catch (\moodle_exception $ex) {
            // Notify it as we run here as admin, we should see everything.
            debugging('Error retrieving ' . $this->areaid . ' ' . $record->id . ' document, not all required data is available: ' .
                    $ex->getMessage(), DEBUG_DEVELOPER);
            return false;
        }
        // Prepare associative array with data from DB.
        $doc = \core_search\document_factory::instance($record->id, $this->componentname, $this->areaname);
        $doc->set('title', content_to_text($record->subject, false));
        $doc->set('itemid', $record->id);
        $doc->set('content', content_to_text($record->smallmessage, false));
        $doc->set('contextid', $usercontext->id);
        $doc->set('courseid', SITEID);
        $doc->set('owneruserid', $options['user1id']);
        $doc->set('userid', $options['user2id']);
        $doc->set('modified', $record->timecreated);

        // Check if this document should be considered new.
        if (isset($options['lastindexedtime']) && $options['lastindexedtime'] < $record->timecreated) {
            // If the document was created after the last index time, it must be new.
            $doc->set_is_new(true);
        }

        return $doc;
    }

    /**
     * Link to the message.
     *
     * @param \core_search\document $doc
     * @return \moodle_url
     */
    public function get_doc_url(\core_search\document $doc) {
        $users = $this->get_current_other_users($doc);
        $position = 'm'.$doc->get('itemid');
        return new \moodle_url('/message/index.php', array('history' => MESSAGE_HISTORY_ALL,
                'user1' => $users['currentuserid'], 'user2' => $users['otheruserid']), $position);
    }

    /**
     * Link to the conversation.
     *
     * @param \core_search\document $doc
     * @return \moodle_url
     */
    public function get_context_url(\core_search\document $doc) {
        $users = $this->get_current_other_users($doc);
        return new \moodle_url('/message/index.php', array('user1' => $users['currentuserid'], 'user2' => $users['otheruserid']));
    }

    /**
     * Sorting the current(user1) and other(user2) user in the conversation.
     *
     * @param \core_search\document $doc
     * @return array()
     */
    protected function get_current_other_users($doc) {
        global $USER;

        $users = array();
        if (($USER->id == $doc->get('owneruserid')) || (get_class($this) === 'message_sent')) {
            $users['currentuserid'] = $doc->get('owneruserid');
            $users['otheruserid'] = $doc->get('userid');
        } else {
            $users['currentuserid'] = $doc->get('userid');
            $users['otheruserid'] = $doc->get('owneruserid');
        }

        return $users;
    }

}
