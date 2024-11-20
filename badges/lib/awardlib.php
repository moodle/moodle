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
 * Classes to manage manual badge award.
 *
 * @package    core
 * @subpackage badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/badgeslib.php');
require_once($CFG->dirroot . '/user/selector/lib.php');

abstract class badge_award_selector_base extends user_selector_base {

    /**
     * The id of the badge this selector is being used for
     * @var int
     */
    protected $badgeid = null;
    /**
     * The context of the badge this selector is being used for
     * @var object
     */
    protected $context = null;
    /**
     * The id of the role of badge issuer in current context
     * @var int
     */
    protected $issuerrole = null;
    /**
     * The id of badge issuer
     * @var int
     */
    protected $issuerid = null;

    /**
     * The return address. Accepts either a string or a moodle_url.
     * @var string $url
     */
    public $url;

    /**
     * The current group being displayed.
     * @var int $currentgroup
     */
    public $currentgroup;

    /**
     * Constructor method
     * @param string $name
     * @param array $options
     */
    public function __construct($name, array $options) {
        $options['accesscontext'] = $options['context'];
        parent::__construct($name, $options);
        if (isset($options['context'])) {
            if ($options['context'] instanceof context_system) {
                // If it is a site badge, we need to get context of frontpage.
                $this->context = context_course::instance(SITEID);
            } else {
                $this->context = $options['context'];
            }
        }
        if (isset($options['badgeid'])) {
            $this->badgeid = $options['badgeid'];
        }
        if (isset($options['issuerid'])) {
            $this->issuerid = $options['issuerid'];
        }
        if (isset($options['issuerrole'])) {
            $this->issuerrole = $options['issuerrole'];
        }
        if (isset($options['url'])) {
            $this->url = $options['url'];
        }
        if (isset($options['currentgroup'])) {
            $this->currentgroup = $options['currentgroup'];
        } else {
            // Returns group active in course, changes the group by default if 'group' page param present.
            $this->currentgroup = groups_get_course_group($COURSE, true);
        }
    }

    /**
     * Returns an array of options to seralise and store for searches
     *
     * @return array
     */
    protected function get_options() {
        global $CFG;
        $options = parent::get_options();
        $options['file'] =  'badges/lib/awardlib.php';
        $options['context'] = $this->context;
        $options['badgeid'] = $this->badgeid;
        $options['issuerid'] = $this->issuerid;
        $options['issuerrole'] = $this->issuerrole;
        // These will be used to filter potential badge recipients when searching.
        $options['currentgroup'] = $this->currentgroup;
        return $options;
    }

    /**
     * Restricts the selection of users to display, according to the groups they belong.
     *
     * @return array
     */
    protected function get_groups_sql() {
        $groupsql = '';
        $groupwheresql = '';
        $groupwheresqlparams = array();
        if ($this->currentgroup) {
            $groupsql = ' JOIN {groups_members} gm ON gm.userid = u.id ';
            $groupwheresql = ' AND gm.groupid = :gr_grpid ';
            $groupwheresqlparams = array('gr_grpid' => $this->currentgroup);
        }
        return array($groupsql, $groupwheresql, $groupwheresqlparams);
    }
}

/**
 * A user selector control for potential users to award badge
 */
class badge_potential_users_selector extends badge_award_selector_base {
    const MAX_USERS_PER_PAGE = 100;

    /**
     * Existing recipients
     */
    protected $existingrecipients = array();

    /**
     * Finds all potential badge recipients
     *
     * Potential badge recipients are all enroled users
     * who haven't got a badge from current issuer role.
     *
     * @param string $search
     * @return array
     */
    public function find_users($search) {
        global $DB;

        $whereconditions = array();
        list($wherecondition, $params) = $this->search_sql($search, 'u');
        if ($wherecondition) {
            $whereconditions[] = $wherecondition;
        }

        $existingids = array();
        foreach ($this->existingrecipients as $group) {
            foreach ($group as $user) {
                $existingids[] = $user->id;
            }
        }
        if ($existingids) {
            list($usertest, $userparams) = $DB->get_in_or_equal($existingids, SQL_PARAMS_NAMED, 'ex', false);
            $whereconditions[] = 'u.id ' . $usertest;
            $params = array_merge($params, $userparams);
        }

        if ($whereconditions) {
            $wherecondition = ' WHERE ' . implode(' AND ', $whereconditions);
        }

        list($groupsql, $groupwheresql, $groupwheresqlparams) = $this->get_groups_sql();

        list($esql, $eparams) = get_enrolled_sql($this->context, 'moodle/badges:earnbadge', 0, true);
        $params = array_merge($params, $eparams, $groupwheresqlparams);

        $fields      = 'SELECT ' . $this->required_fields_sql('u');
        $countfields = 'SELECT COUNT(u.id)';

        $params['badgeid'] = $this->badgeid;
        $params['issuerrole'] = $this->issuerrole;

        $sql = " FROM {user} u JOIN ($esql) je ON je.id = u.id
                 LEFT JOIN {badge_manual_award} bm
                     ON (bm.recipientid = u.id AND bm.badgeid = :badgeid AND bm.issuerrole = :issuerrole)
                 $groupsql
                 $wherecondition AND bm.id IS NULL
                 $groupwheresql";

        list($sort, $sortparams) = users_order_by_sql('u', $search, $this->accesscontext);
        $order = ' ORDER BY ' . $sort;

        if (!$this->is_validating()) {
            $potentialmemberscount = $DB->count_records_sql($countfields . $sql, $params);
            if ($potentialmemberscount > self::MAX_USERS_PER_PAGE) {
                return $this->too_many_results($search, $potentialmemberscount);
            }
        }

        $availableusers = $DB->get_records_sql($fields . $sql . $order, array_merge($params, $sortparams));

        if (empty($availableusers)) {
            return array();
        }

        return array(get_string('potentialrecipients', 'badges') => $availableusers);
    }

    /**
     * Sets the existing recipients
     * @param array $users
     */
    public function set_existing_recipients(array $users) {
        $this->existingrecipients = $users;
    }
}

/**
 * A user selector control for existing users to award badge
 */
class badge_existing_users_selector extends badge_award_selector_base {

    /**
     * Finds all users who already have been awarded a badge by current role
     *
     * @param string $search
     * @return array
     */
    public function find_users($search) {
        global $DB;
        list($wherecondition, $params) = $this->search_sql($search, 'u');
        $params['badgeid'] = $this->badgeid;
        $params['issuerrole'] = $this->issuerrole;

        list($esql, $eparams) = get_enrolled_sql($this->context, 'moodle/badges:earnbadge', 0, true);
        $fields = $this->required_fields_sql('u');
        list($sort, $sortparams) = users_order_by_sql('u', $search, $this->accesscontext);

        list($groupsql, $groupwheresql, $groupwheresqlparams) = $this->get_groups_sql();

        $params = array_merge($params, $eparams, $sortparams, $groupwheresqlparams);
        $recipients = $DB->get_records_sql("SELECT $fields
                FROM {user} u
                JOIN ($esql) je ON je.id = u.id
                JOIN {badge_manual_award} s ON s.recipientid = u.id
                $groupsql
                WHERE $wherecondition AND s.badgeid = :badgeid AND s.issuerrole = :issuerrole
                $groupwheresql
                ORDER BY $sort", $params);

        return array(get_string('existingrecipients', 'badges') => $recipients);
    }
}

function process_manual_award($recipientid, $issuerid, $issuerrole, $badgeid) {
    global $DB;
    $params = array(
                'badgeid' => $badgeid,
                'issuerid' => $issuerid,
                'issuerrole' => $issuerrole,
                'recipientid' => $recipientid
            );

    if (!$DB->record_exists('badge_manual_award', $params)) {
        $award = new stdClass();
        $award->badgeid = $badgeid;
        $award->issuerid = $issuerid;
        $award->issuerrole = $issuerrole;
        $award->recipientid = $recipientid;
        $award->datemet = time();
        if ($DB->insert_record('badge_manual_award', $award)) {
            return true;
        }
    }
    return false;
}

/**
 * Manually revoke awarded badges.
 *
 * @param int $recipientid
 * @param int $issuerid
 * @param int $issuerrole
 * @param int $badgeid
 * @return bool
 */
function process_manual_revoke($recipientid, $issuerid, $issuerrole, $badgeid) {
    global $DB;
    $params = array(
                'badgeid' => $badgeid,
                'issuerid' => $issuerid,
                'issuerrole' => $issuerrole,
                'recipientid' => $recipientid
            );
    if ($DB->record_exists('badge_manual_award', $params)) {
        if ($DB->delete_records('badge_manual_award', array('badgeid' => $badgeid,
                                                            'issuerid' => $issuerid,
                                                            'recipientid' => $recipientid))
            && $DB->delete_records('badge_issued', array('badgeid' => $badgeid,
                                                      'userid' => $recipientid))) {

            // Trigger event, badge revoked.
            $badge = new \badge($badgeid);
            $eventparams = array(
                'objectid' => $badgeid,
                'relateduserid' => $recipientid,
                'context' => $badge->get_context()
            );
            $event = \core\event\badge_revoked::create($eventparams);
            $event->trigger();

            return true;
        }
    } else {
        throw new moodle_exception('error:badgenotfound', 'badges');
    }
    return false;
}
