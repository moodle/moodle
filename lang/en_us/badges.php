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

/**
 * Strings for component 'badges', language 'en_us', version '4.1'.
 *
 * @package     badges
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['status_help'] = 'Status of a badge determines its behavior in the system:

* **AVAILABLE** – Means that this badge can be earned by users. While a badge is available to users, its criteria cannot be modified.

* **NOT AVAILABLE** – Means that this badge is not available to users and cannot be earned or manually issued. If such badge has never been issued before, its criteria can be changed.

Once a badge has been issued to at least one user, it automatically becomes **LOCKED**. Locked badges can still be earned by users, but their criteria can no longer be changed. If you need to modify details or criteria of a locked badge, you can duplicate this badge and make all the required changes.

*Why do we lock badges?*

We want to make sure that all users complete the same requirements to earn a badge. Currently, it is not possible to revoke badges. If we allowed badges requirements to be modified all the time, we would most likely end up with users having the same badge for meeting completely different requirements.';
