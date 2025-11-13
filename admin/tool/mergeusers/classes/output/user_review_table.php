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
 * User review table util file
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
use core\exception\moodle_exception;
use html_table;
use html_writer;
use renderable;
use stdClass;
use tool_mergeusers\local\selected_users_to_merge;

/**
 * Extend the html table to provide a build function inside for creating a table
 * for reviewing the users to merge.
 *
 * @package   tool_mergeusers
 * @author    John Hoopes <hoopes@wisc.edu>
 * @copyright Univeristy of Wisconsin - Madison
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_review_table extends html_table implements renderable {
    /** @var stdClass $olduser The olduser db object */
    protected stdClass $olduser;

    /** @var stdClass $newuser The newuser db object */
    protected stdClass $newuser;

    /** @var renderer Render to help showing user info. */
    protected renderer $renderer;

    /**
     * Call parent construct and then build table
     *
     * @param renderer $renderer
     * @throws coding_exception
     */
    public function __construct(renderer $renderer) {
        parent::__construct();
        $currentuserselection = selected_users_to_merge::instance();
        $this->renderer = $renderer;
        if ($currentuserselection->from_user_is_set()) {
            $this->olduser = $currentuserselection->from_user();
        }
        if ($currentuserselection->to_user_is_set()) {
            $this->newuser = $currentuserselection->to_user();
        }
        $this->build_table();
    }

    /**
     * Build the user select table using the extension of html_table
     *
     * @throws coding_exception
     * @throws moodle_exception
     */
    protected function build_table(): void {
        // Reset any existing data.
        $this->data = [];

        if (empty($this->olduser) && empty($this->newuser)) {
            return;
        }

        // Show the selected users to merge. At least, there is one selected user for merging.
        $this->id = 'merge_users_tool_user_review_table';
        $this->attributes['class'] = 'generaltable table-reboot boxaligncenter';

        if (
            (isset($this->olduser->idnumber) && !empty($this->olduser->idnumber))
            || (isset($this->newuser->idnumber) && !empty($this->newuser->idnumber))
        ) {
            $extrafield = 'idnumber';
        } else {
            $extrafield = 'description';
        }

        $columns = [
            'col_label' => '',
            'col_userid' => 'Id',
            'col_username' => get_string('user'),
            'col_email' => get_string('email'),
            'col_extra' => get_string($extrafield),
        ];
        $this->head = array_values($columns);
        $this->colclasses = array_keys($columns);

        // Always display both rows so that the end user can see what is selected/not selected.
        $users = [
            [get_string('olduser', 'tool_mergeusers'), $this->olduser ?? null],
            [get_string('newuser', 'tool_mergeusers'), $this->newuser ?? null],
        ];
        foreach ($users as $user) {
            $row = [array_shift($user)];
            $user = reset($user);
            if (empty($user)) {
                $row = array_merge($row, array_fill(0, count($columns), ''));
            } else {
                $spanclass = ($user->suspended) ? ('usersuspended') : ('');
                $row[] = html_writer::tag('span', $user->id, ['class' => $spanclass]);
                $row[] = $this->renderer->show_user($user->id, $user);
                $row[] = html_writer::tag('span', $user->email, ['class' => $spanclass]);
                $row[] = html_writer::tag('span', $user->$extrafield, ['class' => $spanclass]);
            }
            $this->data[] = $row;
        }
    }
}
