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
 * Course category proxy.
 *
 * @package    core_calendar
 * @copyright  2017 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_calendar\local\event\proxies;

defined('MOODLE_INTERNAL') || die();

/**
 * Course category proxy.
 *
 * This returns an instance of a coursecat rather than a stdClass.
 *
 * @copyright  2017 Andrew Nicols <andrew@nicols.co.uk>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class coursecat_proxy implements proxy_interface {
    /**
     * @var int $id The ID of the database record.
     */
    protected $id;

    /**
     * @var \stdClass $base Base class to get members from.
     */
    protected $base;

    /**
     * @var \core_course_category $category The proxied instance.
     */
    protected $category;

    /**
     * coursecat_proxy constructor.
     *
     * @param int       $id       The ID of the record in the database.
     */
    public function __construct($id) {
        $this->id = $id;
        $this->base = (object) [
            'id' => $id,
        ];
    }

    /**
     * Retrieve a member of the proxied class.
     *
     * @param string $member The name of the member to retrieve
     * @return mixed The member.
     */
    public function get($member) {
        if ($this->base && property_exists($this->base, $member)) {
            return $this->base->{$member};
        }

        return $this->get_proxied_instance()->{$member};
    }

    /**
     * Get the full instance of the proxied class.
     *
     * @return \core_course_category
     */
    public function get_proxied_instance() : \core_course_category {
        if (!$this->category) {
            $this->category = \core_course_category::get($this->id, IGNORE_MISSING, true);
        }
        return $this->category;
    }
}
