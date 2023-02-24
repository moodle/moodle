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
 * Base class for the table used by a {@link quiz_attempts_report}.
 *
 * @package   local_report_user_license_allocations
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_email\tables;

use \table_sql;
use \moodle_url;
use \html_writer;
use \iomad;
use \context_system;
use \context_course;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/tablelib.php');

class templatesets_table extends table_sql {

    /**
     * Generate the display of the user's firstname
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_templatesetname($row) {

        if (empty($row->isdefault)) {
            return format_string($row->templatesetname);
        } else {
            return format_string($row->templatesetname . ' (' . get_string('default') .')');
        }
    }

    /**
     * Generate the display of the user's license allocated timestamp
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_actions($row) {
        global $DB, $USER;

            $deletelink = new moodle_url('/local/email/template_list.php',
                                          ['templatesetid' => $row->id,
                                           'action' => 'delete',
                                           'sesskey' => sesskey()]);
            $editlink = new moodle_url('/local/email/template_list.php',
                                        ['templatesetid' => $row->id,
                                         'action' => 'edit']);
            $applylink = new moodle_url('/local/email/template_apply_form.php',
                                        ['templatesetid' => $row->id,
                                         'action' => 'apply']);
            $defaultlink = new moodle_url('/local/email/template_list.php',
                                          ['templatesetid' => $row->id,
                                           'action' => 'setdefault',
                                           'sesskey' => sesskey()]);
            $unsetdefaultlink = new moodle_url('/local/email/template_list.php',
                                              ['templatesetid' => $row->id,
                                               'action' => 'unsetdefault',
                                               'sesskey' => sesskey()]);

            $return = html_writer::start_tag('a', ['href' => $deletelink]);
            $return .= html_writer::tag('i',
                                        '',
                                        ['class' => 'icon fa fa-trash fa-fw ',
                                         'title' => get_string('deletetemplateset', 'local_email'),
                                         'role' => 'img',
                                         'aria-label' => get_string('deletetemplateset', 'local_email')]);
            $return .= html_writer::end_tag('a'); 
            $return .= html_writer::start_tag('a', ['href' => $editlink]);
            $return .= html_writer::tag('i',
                                        '',
                                        ['class' => 'icon fa fa-cog fa-fw ',
                                         'title' => get_string('edittemplateset', 'local_email'),
                                         'role' => 'img',
                                         'aria-label' => get_string('edittemplateset', 'local_email')]);
            $return .= html_writer::end_tag('a'); 
            if (empty($row->isdefault)) {
                $return .= html_writer::start_tag('a', ['href' => $defaultlink]);
                $return .= html_writer::tag('i',
                                            '',
                                            ['class' => 'icon fa fa-toggle-off fa-fw ',
                                             'title' => get_string('setdefault', 'local_email'),
                                             'role' => 'img',
                                             'aria-label' => get_string('setdefault', 'local_email')]);
                $return .= html_writer::end_tag('a'); 
            } else {
                $return .= html_writer::start_tag('a', ['href' => $unsetdefaultlink]);
                $return .= html_writer::tag('i',
                                            '',
                                            ['class' => 'icon fa fa-toggle-on fa-fw ',
                                             'title' => get_string('unsetdefault', 'local_email'),
                                             'role' => 'img',
                                             'aria-label' => get_string('unsetdefault', 'local_email')]);
                $return .= html_writer::end_tag('a'); 
            }

            $return .= html_writer::start_tag('a', ['href' => $applylink]);
            $return .= html_writer::tag('i',
                                        '',
                                        ['class' => 'icon fa fa-share fa-fw ',
                                         'title' => get_string('applytemplateset', 'local_email', $row->templatesetname),
                                         'role' => 'img',
                                         'aria-label' => get_string('applytemplateset', 'local_email', $row->templatesetname)]);
            $return .= html_writer::end_tag('a'); 

        return $return;
    }
}
