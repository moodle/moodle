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
 * Class to display collection select for the refresh interval.
 *
 * @package core_calendar
 * @copyright 2021 Huong Nguyen <huongnv13@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_calendar\output;

use core\output\inplace_editable;

class refreshintervalcollection extends inplace_editable {

    /**
     * Constructor.
     *
     * @param \stdClass $subscription Subscription object
     */
    public function __construct(\stdClass $subscription) {
        $collection = calendar_get_pollinterval_choices();
        parent::__construct('core_calendar', 'refreshinterval', $subscription->id, true, null, $subscription->pollinterval, null,
                get_string('pollinterval', 'calendar'));
        $this->set_type_select($collection);
    }

    public static function update(int $subscriptionid, int $pollinterval) {
        if (calendar_can_edit_subscription($subscriptionid)) {
            $subscription = calendar_get_subscription($subscriptionid);
            $subscription->pollinterval = $pollinterval;
            calendar_update_subscription($subscription);
            $tmpl = new self($subscription);
            return $tmpl;
        } else {
            throw new \moodle_exception('nopermissions', 'error', '', get_string('managesubscriptions', 'calendar'));
        }
    }
}
