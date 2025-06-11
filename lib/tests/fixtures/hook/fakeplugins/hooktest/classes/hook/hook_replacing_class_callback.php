<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace fake_hooktest\hook;

use core\attribute;

/**
 * Fixture for testing of hooks.
 *
 * @package core
 * @author Mark Johnson <mark.johnson@catalyst-eu.net>
 * @copyright 2024 Catalyst IT Europe Ltd.
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[attribute\label('Test hook replacing a class callback.')]
#[attribute\tags('test')]
#[attribute\hook\replaces_callbacks('callbacks::old_class_callback')]
final class hook_replacing_class_callback {
}
