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
 * Moodle site analysable.
 *
 * @package   core_analytics
 * @copyright 2016 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_analytics;

defined('MOODLE_INTERNAL') || die();

/**
 * Moodle site analysable.
 *
 * @package   core_analytics
 * @copyright 2016 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class site implements \core_analytics\analysable {

    /**
     * @var int
     */
    protected $start;

    /**
     * @var int
     */
    protected $end;

    /**
     * Analysable id
     *
     * @return int
     */
    public function get_id() {
        return SYSCONTEXTID;
    }

    /**
     * Site.
     *
     * @return string
     */
    public function get_name() {
        return get_string('site');
    }

    /**
     * Analysable context.
     *
     * @return \context
     */
    public function get_context() {
        return \context_system::instance();
    }

    /**
     * Analysable start timestamp.
     *
     * @return int
     */
    public function get_start() {
        if (!empty($this->start)) {
            return $this->start;
        }

        // Much faster than reading the first log in the site.
        $admins = get_admins();
        $this->start = self::MAX_TIME;
        foreach ($admins as $admin) {
            if ($admin->firstaccess < $this->start) {
                $this->start = $admin->firstaccess;
            }
        }
        return $this->start;
    }

    /**
     * Analysable end timestamp.
     *
     * @return int
     */
    public function get_end() {
        if (!empty($this->end)) {
            return $this->end;
        }

        $this->end = time();
        return $this->end;
    }
}
