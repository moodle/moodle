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
 * Question bank settings page class.
 *
 * @package    qbank_columnsortorder
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Ghaly Marc-Alexandre <marc-alexandreghaly@catalyst-ca.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');

admin_externalpage_setup('qbank_columnsortorder', '', ['section' => 'columnsortorder'],
    '/question/bank/columnsortorder/sortcolumns.php');

$renderer = $PAGE->get_renderer('qbank_columnsortorder');

echo $OUTPUT->header();
echo $OUTPUT->heading(new lang_string('qbankcolumnsortorder', 'qbank_columnsortorder'));
echo $renderer->render_column_sort_ui();
echo $OUTPUT->footer();
