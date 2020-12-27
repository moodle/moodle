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
 * Prints navigation tabs
 *
 * @package    core_group
 * @copyright  2010 Petr Skoda (http://moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
    $row = array();
    $row[] = new tabobject('groups',
                           new moodle_url('/group/index.php', array('id' => $courseid)),
                           get_string('groups'));

    $row[] = new tabobject('groupings',
                           new moodle_url('/group/groupings.php', array('id' => $courseid)),
                           get_string('groupings', 'group'));

    $row[] = new tabobject('overview',
                           new moodle_url('/group/overview.php', array('id' => $courseid)),
                           get_string('overview', 'group'));
    echo '<div class="groupdisplay">';
    echo $OUTPUT->tabtree($row, $currenttab);
    echo '</div>';
