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
 * Log storage manager interface.
 *
 * @package    core
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\log;

defined('MOODLE_INTERNAL') || die();

/**
 * Interface describing log readers.
 *
 * This is intended for reports, use get_log_manager() to get
 * the configured instance.
 *
 * @package core\log
 */
interface manager {
    /**
     * Return list of available log readers in given
     * context for current user.
     *
     * @param \context $context
     * @return \core\log\reader[]
     */
    public function get_readers(\context $context);

    /**
     * Dispose all initialised stores.
     * @return void
     */
    public function dispose();
}
