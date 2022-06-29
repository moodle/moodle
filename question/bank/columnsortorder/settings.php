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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Adds admin settings for the plugin.
 *
 * @package    qbank_columnsortorder
 * @category   admin
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Ghaly Marc-Alexandre <marc-alexandreghaly@catalyst-ca.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Column sort order link in manageqbanks page.
$url = new moodle_url('/question/bank/columnsortorder/sortcolumns.php', ['section' => 'columnsortorder']);

if ($ADMIN->fulltree) {
    $page = $adminroot->locate('manageqbanks');
    if (isset($page)) {
        $page->add(new admin_setting_description(
            'manageqbanksgotocolumnsort',
            '',
            new lang_string('qbankgotocolumnsort', 'qbank_columnsortorder',
                html_writer::link($url, get_string('qbankcolumnsortorder', 'qbank_columnsortorder')))
        ));
    }
}
// Column sort order link in admin page.
$settings = new admin_externalpage('qbank_columnsortorder', get_string('qbankcolumnsortorder', 'qbank_columnsortorder'), $url);
