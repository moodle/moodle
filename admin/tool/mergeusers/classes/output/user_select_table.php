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
 * User select table.
 *
 * @package   tool_mergeusers
 * @author    Nicolas Dunand <Nicolas.Dunand@unil.ch>
 * @author    Mike Holzer
 * @author    Forrest Gaston
 * @author    Juan Pablo Torres Herrera
 * @author    Jordi Pujol-Ahulló, Sred, Universitat Rovira i Virgili
 * @author    John Hoopes <hoopes@wisc.edu>, Univeristy of Wisconsin - Madison
 * @copyright Universitat Rovira i Virgili (https://www.urv.cat)
 * @copyright Univeristy of Wisconsin - Madison
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_mergeusers\output;

use coding_exception;
use html_table;
use html_writer;
use renderable;

/**
 * Extend the html_table to provide a user selection.
 *
 * @package tool_mergeusers
 * @author  John Hoopes <hoopes@wisc.edu>
 * @copyright University of Wisconsin - Madison
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_select_table extends html_table implements renderable {
    /** @var renderer Renderer to show user info. */
    protected renderer $renderer;

    /**
     * Call parent construct
     *
     * @param array $users
     * @param renderer $renderer
     *
     * @throws coding_exception
     */
    public function __construct(array $users, renderer $renderer) {
        parent::__construct();
        $this->renderer = $renderer;
        $this->build_table_for($users);
    }

    /**
     * Build the user select table using the extension of html_table
     *
     * @param array $users array of user results
     * @throws coding_exception
     */
    protected function build_table_for(array $users): void {
        // Reset any existing data.
        $this->data = [];

        $this->id = 'merge_users_tool_user_select_table';
        $this->attributes['class'] = 'generaltable table-reboot boxaligncenter';

        $columns = [
            'col_select_olduser' => get_string('olduser', 'tool_mergeusers'),
            'col_master_newuser' => get_string('newuser', 'tool_mergeusers'),
            'col_userid' => 'Id',
            'col_username' => get_string('user'),
            'col_email' => get_string('email'),
            'col_idnumber' => get_string('idnumber'),
        ];

        $this->head = array_values($columns);
        $this->colclasses = array_keys($columns);
        $reset = get_string('reset');

        foreach ($users as $userid => $user) {
            $row = [];
            $spanclass = ($user->suspended) ? ('usersuspended') : ('');
            $row[] = html_writer::empty_tag(
                'input',
                [
                    'type' => 'radio',
                    'name' => 'olduser',
                    'value' => $userid,
                    'id' => 'olduser' . $userid,
                    'onClick' => "document.getElementsByName('selectedolduser')[0].value = this.value;",
                ],
            );
            $row[] = html_writer::empty_tag(
                'input',
                [
                    'type' => 'radio',
                    'name' => 'newuser',
                    'value' => $userid,
                    'id' => 'newuser' . $userid,
                    'onClick' => "document.getElementsByName('selectednewuser')[0].value = this.value;",
                ],
            );
            $row[] = html_writer::tag('span', $user->id, ['class' => $spanclass]);
            $row[] = $this->renderer->show_user($user->id, $user);
            $row[] = html_writer::tag('span', $user->email, ['class' => $spanclass]);
            $row[] = html_writer::tag('span', $user->idnumber, ['class' => $spanclass]);
            $this->data[] = $row;
        }
    }
}
