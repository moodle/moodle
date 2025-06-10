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
 * This is a one-line short description of the file.
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    block_cps
 * @copyright  2014 Louisiana State University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class block_cps_renderer extends plugin_renderer_base {

    public function users_search_result_table($users, $baseurl) {
        $table = new html_table();
        $table->head = array(
            get_string('firstname'), get_string('lastname'),
            get_string('username'), get_string('idnumber'),
            get_string('alternatename'),
            get_string('action')
        );

        $editstr = get_string('edit');
        foreach ($users as $user) {
            $url = new moodle_url($baseurl, array('id' => $user->id));
            $resetlink = html_writer::link(new moodle_url($baseurl, array('id' => $user->id, 'reset' => 1)), 'reset');
            $altname = $user->alternatename ? $user->alternatename." | ".$resetlink : '';
            $line = array(
                $user->firstname,
                $user->lastname,
                $user->username,
                $user->idnumber,
                $altname,
                html_writer::link($url, $editstr)
            );

            $table->data[] = new html_table_row($line);
        }
        return $table;
    }
}