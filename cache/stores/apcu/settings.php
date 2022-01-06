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
 * The settings for the APCu store.
 *
 * This file is part of the APCu cache store, it contains the API for interacting with an instance of the store.
 *
 * @package    cachestore_apcu
 * @copyright  2014 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$settings->add(
    new admin_setting_configcheckbox(
        'cachestore_apcu/testperformance',
        new lang_string('testperformance', 'cachestore_apcu'),
        new lang_string('testperformance_desc', 'cachestore_apcu'),
        false
    )
);
