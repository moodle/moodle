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
 * Class represents a single rule.
 *
 * @package    tool_monitor
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_monitor;

defined('MOODLE_INTERNAL') || die();

/**
 * Class represents a single rule.
 *
 * @since      Moodle 2.8
 * @package    tool_monitor
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class rule {
    use helper_trait;

    /**
     * @var \stdClass The rule object form database.
     */
    protected $rule;

    /**
     * Constructor.
     *
     * @param \stdClass $rule A rule object from database.
     */
    public function __construct($rule) {
        $this->rule = $rule;
    }

    /**
     * Can the current user manage this rule?
     *
     * @return bool true if the current user can manage this rule, else false.
     */
    public function can_manage_rule() {
        $courseid = $this->courseid;
        $context = empty($courseid) ? \context_system::instance() : \context_course::instance($this->courseid);
        return has_capability('tool/monitor:managerules', $context);
    }

    /**
     * Api to duplicate a rule in a given courseid.
     *
     * @param int $finalcourseid Final course id.
     */
    public function duplicate_rule($finalcourseid) {
        $rule = fullclone($this->rule);
        unset($rule->id);
        $rule->courseid = $finalcourseid;
        $time = time();
        $rule->timecreated = $time;
        $rule->timemodified = $time;
        rule_manager::add_rule($rule);
    }

    /**
     * Delete this rule.
     *
     * Note: It also removes all associated subscriptions.
     */
    public function delete_rule() {
        rule_manager::delete_rule($this->id);
    }

    /**
     * Generate a select drop down with list of possible modules for a given course and rule.
     *
     * @param int $courseid course id
     *
     * @return \single_select a single select object
     * @throws \coding_exception
     */
    public function get_module_select($courseid) {
        global $CFG;
        $options = array();
        if (strpos($this->plugin, 'mod_') === 0) {
            $options[0] = get_string('allmodules', 'tool_monitor');
        } else {
            $options[0] = get_string('allevents', 'tool_monitor');
        }
        if (strpos($this->plugin, 'mod_') === 0) {
            if ($courseid == 0) {
                // They need to be in a course to select module instance.
                return get_string('selectcourse', 'tool_monitor');
            }
            // Let them select an instance.
            $cms = get_fast_modinfo($courseid);
            $instances = $cms->get_instances_of(str_replace('mod_', '',  $this->plugin));
            foreach ($instances as $cminfo) {
                // Don't list instances that are not visible or available to the user.
                if ($cminfo->uservisible && $cminfo->available) {
                    $options[$cminfo->id] = $cminfo->get_formatted_name();
                }
            }
        }
        $url = new \moodle_url($CFG->wwwroot. '/tool/monitor/index.php', array('id' => $courseid, 'ruleid' => $this->id,
                'action' => 'subscribe'));
        return new \single_select($url, 'cmid', $options, '', $nothing = array('' => 'choosedots'));
    }

    /**
     * Subscribe an user to this rule.
     *
     * @param int $courseid Course id.
     * @param int $cmid Course module id.
     * @param int $userid User id.
     *
     * @throws \coding_exception
     */
    public function subscribe_user($courseid, $cmid, $userid = 0) {
        global $USER;

        if ($this->courseid != $courseid && $this->courseid != 0) {
            // Trying to subscribe to a rule that belongs to a different course. Should never happen.
            throw new \coding_exception('Can not subscribe to rules from a different course');
        }
        if ($cmid !== 0) {
            $cms = get_fast_modinfo($courseid);
            $cminfo = $cms->get_cm($cmid);
            if (!$cminfo->uservisible || !$cminfo->available) {
                // Trying to subscribe to a hidden or restricted cm. Should never happen.
                throw new \coding_exception('You cannot do that');
            }
        }
        $userid = empty($userid) ? $USER->id : $userid;

        subscription_manager::create_subscription($this->id, $courseid, $cmid, $userid);
    }

    /**
     * Magic get method.
     *
     * @param string $prop property to get.
     *
     * @return mixed
     * @throws \coding_exception
     */
    public function __get($prop) {
        if (property_exists($this->rule, $prop)) {
            return $this->rule->$prop;
        }
        throw new \coding_exception('Property "' . $prop . '" doesn\'t exist');
    }

    /**
     * Return the rule data to be used while setting mform.
     *
     * @throws \coding_exception
     */
    public function get_mform_set_data() {
        if (!empty($this->rule)) {
            $rule = fullclone($this->rule);
            $rule->description = array('text' => $rule->description, 'format' => $rule->descriptionformat);
            $rule->template = array('text' => $rule->template, 'format' => $rule->templateformat);
            return $rule;
        }
        throw new \coding_exception('Invalid call to get_mform_set_data.');
    }
}
