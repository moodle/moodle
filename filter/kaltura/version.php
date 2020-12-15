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
 * Kaltura version script.
 *
 * @package    filter_kaltura
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  (C) 2014 Remote-Learner.net Inc (http://www.remote-learner.net)
 */
defined('MOODLE_INTERNAL') || die();

$plugin->version = 20201215310; //version date YYYYMMDDXX 10 represent 3.0 for future option to moodle use 2 digit version
$plugin->component  = 'filter_kaltura';
$plugin->release = 'Kaltura release 4.2.9';
$plugin->requires = 2018120300;
$plugin->maturity = MATURITY_STABLE;
$plugin->dependencies = array(
    'local_kaltura' => 20201215310
);
