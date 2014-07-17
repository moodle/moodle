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
 * Class represents a single subscription.
 *
 * @package    tool_monitor
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_monitor;

defined('MOODLE_INTERNAL') || die();

/**
 * Class represents a single subscription instance (i.e with all the subscription info).
 *
 * @since      Moodle 2.8
 * @package    tool_monitor
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class subscription {
    use helper_trait;
    /**
     * @var \stdClass
     */
    protected $subscription;

    /**
     * Constructor.
     *
     * use {@link \tool_monitor\subscription_manager::get_subscription} to get an instance instead of directly calling this method.
     *
     * @param \stdClass $subscription
     */
    public function __construct($subscription) {
        $this->subscription = $subscription;
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
        if (property_exists($this->subscription, $prop)) {
            return $this->subscription->$prop;
        }
        throw new \coding_exception('Property "' . $prop . '" doesn\'t exist');
    }

    /**
     * Get a human readable name for instances associated with this subscription.
     *
     * @return string
     * @throws \coding_exception
     */
    public function get_instance_name() {
        if ($this->plugin === 'core') {
            $string = get_string('allevents', 'tool_monitor');
        } else {
            if ($this->cmid == 0) {
                $string = get_string('allmodules', 'tool_monitor');
            } else {
                $cms = get_fast_modinfo($this->courseid);
                $cms = $cms->get_cms();
                if (isset($cms[$this->cmid])) {
                    $string = $cms[$this->cmid]->get_formatted_name(); // Instance name.
                } else {
                    // Something is wrong, instance is not present anymore.
                    $string = get_string('invalidmodule', 'tool_monitor');
                }
            }
        }

        return $string;
    }
}
