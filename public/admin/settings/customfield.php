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
 * Custom fields related settings.
 *
 * @package   core_admin
 * @copyright 2025 David Carrillo <davidmc@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

defined('MOODLE_INTERNAL') || die;

/** @var admin_root $ADMIN */
$ADMIN->add(
    'root',
    new admin_category(
        'customfield',
        new lang_string('customfields', 'core_customfield')
    )
);

$ADMIN->add(
    'customfield',
    new admin_externalpage(
        'sharedcustomfields',
        new lang_string('sharedcustomfields', 'core_customfield'),
        new moodle_url("/customfield/customfield.php")
    )
);
