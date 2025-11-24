<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Quiz Password Verification Plugin
 *
 * @package    local_quiz_password_verify
 * @copyright  2024
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'local_quiz_password_verify';
$plugin->version = 2024112400;
$plugin->requires = 2024042200; // Moodle 5.2
$plugin->maturity = MATURITY_STABLE;
$plugin->release = '1.0';
