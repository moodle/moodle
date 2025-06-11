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
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_quickmail\persistents\interfaces;

defined('MOODLE_INTERNAL') || die();

interface schedulable_interface {

    /*
     * PROPS
     * last_run_at
     * next_run_at
     * is_running
     */

    public function get_schedule();
    public function get_last_run_time();
    public function get_next_run_time();
    public function set_next_run_time();
    public function is_running();
    public function toggle_running_status($isrunning);
    public function run_scheduled();
    public function handle_schedule_pre_run_actions();
    public function handle_schedule_post_run_actions();

}
