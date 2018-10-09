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
 * Expiry Data.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_dataprivacy;

use core_privacy\manager;

defined('MOODLE_INTERNAL') || die();

/**
 * Expiry Data.
 *
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class expiry_info {

    /** @var bool Whether this context is fully expired */
    protected $isexpired = false;

    /**
     * Constructor for the expiry_info class.
     *
     * @param   bool    $isexpired Whether the retention period for this context has expired yet.
     */
    public function __construct(bool $isexpired) {
        $this->isexpired = $isexpired;
    }

    /**
     * Whether this context has 'fully' expired.
     * That is to say that the default retention period has been reached, and that there are no unexpired roles.
     *
     * @return  bool
     */
    public function is_fully_expired() : bool {
        return $this->isexpired;
    }

    /**
     * Whether any part of this context has expired.
     *
     * @return  bool
     */
    public function is_any_expired() : bool {
        if ($this->is_fully_expired()) {
            return true;
        }

        return false;
    }

    /**
     * Merge this expiry_info object with another belonging to a child context in order to set the 'safest' heritage.
     *
     * It is not possible to delete any part of a context that is not deleted by a parent.
     * So if a course's retention policy has been reached, then only parts where the children have also expired can be
     * deleted.
     *
     * @param   expiry_info $child The child record to merge with.
     * @return  $this
     */
    public function merge_with_child(expiry_info $child) : expiry_info {
        if ($child->is_fully_expired()) {
            return $this;
        }

        // If the child is not fully expired, then none of the parents can be either.
        $this->isexpired = false;

        return $this;
    }
}
