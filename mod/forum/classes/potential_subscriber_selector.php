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
 * A type of forum.
 *
 * @package    mod_forum
 * @copyright  2014 Andrew Robert Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/user/selector/lib.php');

/**
 * A user selector control for potential subscribers to the selected forum
 * @package   mod_forum
 * @copyright 2009 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_forum_potential_subscriber_selector extends mod_forum_subscriber_selector_base {
    /**
     * If set to true EVERYONE in this course is force subscribed to this forum
     * @var bool
     */
    protected $forcesubscribed = false;
    /**
     * Can be used to store existing subscribers so that they can be removed from
     * the potential subscribers list
     */
    protected $existingsubscribers = array();

    /**
     * Constructor method
     * @param string $name
     * @param array $options
     */
    public function __construct($name, $options) {
        parent::__construct($name, $options);
        if (isset($options['forcesubscribed'])) {
            $this->forcesubscribed=true;
        }
    }

    /**
     * Returns an arary of options for this control
     * @return array
     */
    protected function get_options() {
        $options = parent::get_options();
        if ($this->forcesubscribed===true) {
            $options['forcesubscribed']=1;
        }
        return $options;
    }

    /**
     * Finds all potential users
     *
     * Potential subscribers are all enroled users who are not already subscribed.
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

        if (!$this->forcesubscribed) {
            $existingids = array();
            foreach ($this->existingsubscribers as $group) {
                foreach ($group as $user) {
                    $existingids[$user->id] = 1;
                }
            }
            if ($existingids) {
                list($usertest, $userparams) = $DB->get_in_or_equal(
                        array_keys($existingids), SQL_PARAMS_NAMED, 'existing', false);
                $whereconditions[] = 'u.id ' . $usertest;
                $params = array_merge($params, $userparams);
            }
        }

        if ($whereconditions) {
            $wherecondition = 'WHERE ' . implode(' AND ', $whereconditions);
        }

        list($esql, $eparams) = get_enrolled_sql($this->context, '', $this->currentgroup, true);
        $params = array_merge($params, $eparams);

        $fields      = 'SELECT ' . $this->required_fields_sql('u');

        $sql = " FROM {user} u
                 JOIN ($esql) je ON je.id = u.id
                      $wherecondition";

        list($sort, $sortparams) = users_order_by_sql('u', $search, $this->accesscontext);
        $order = ' ORDER BY ' . $sort;

        $availableusers = $DB->get_records_sql($fields . $sql . $order, array_merge($params, $sortparams));

        $cm = get_coursemodule_from_instance('forum', $this->forumid);
        $modinfo = get_fast_modinfo($cm->course);
        $info = new \core_availability\info_module($modinfo->get_cm($cm->id));
        $availableusers = $info->filter_user_list($availableusers);

        if (empty($availableusers)) {
            return array();
        }

        // Check to see if there are too many to show sensibly.
        if (!$this->is_validating()) {
            $potentialmemberscount = count($availableusers);
            if ($potentialmemberscount > $this->maxusersperpage) {
                return $this->too_many_results($search, $potentialmemberscount);
            }
        }

        if ($this->forcesubscribed) {
            return array(get_string("existingsubscribers", 'forum') => $availableusers);
        } else {
            return array(get_string("potentialsubscribers", 'forum') => $availableusers);
        }
    }

    /**
     * Sets the existing subscribers
     * @param array $users
     */
    public function set_existing_subscribers(array $users) {
        $this->existingsubscribers = $users;
    }

    /**
     * Sets this forum as force subscribed or not
     */
    public function set_force_subscribed($setting=true) {
        $this->forcesubscribed = true;
    }
}
