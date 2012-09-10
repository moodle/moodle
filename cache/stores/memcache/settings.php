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
 * The settings for the memcache store.
 *
 * This file is part of the memcache cache store, it contains the API for interacting with an instance of the store.
 *
 * @package    cache_memcache
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    require_once($CFG->dirroot.'/cache/stores/memcache/lib.php');

    $settings->add(new admin_setting_configtextarea('cache_memcache/testservers', get_string('testservers', 'cache_memcache'), get_string('testservers_desc', 'cache_memcache'), ''));
}
