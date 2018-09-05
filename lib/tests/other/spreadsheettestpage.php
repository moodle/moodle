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
 * This script produces sample Excel and ODF spreadsheets.
 *
 * @package    core
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/excellib.class.php');
require_once($CFG->libdir . '/odslib.class.php');

$context = context_system::instance();
$PAGE->set_url('/lib/tests/other/spreadsheettestpage.php');
$PAGE->set_context($context);

require_login();
require_capability('moodle/site:config', $context);

$type = optional_param('type', '', PARAM_ALPHANUM);

if (!in_array($type, array('excel2007', 'ods'))) {
    $type = '';
}

if (!$type) {
    $PAGE->set_title('Moodle spreadsheet export test');
    $PAGE->set_heading('Moodle spreadsheet export test');

    echo $OUTPUT->header();
    echo $OUTPUT->box_start();

    $notes = '
Tested with:

* MS Excel Viewer 2003 (with Compatibility Pack), 2010
* LibreOffice 3.5, 3.6
* NeoOffice 3.3
* Apple Numbers \'09 (2.3) and Preview
* Google Drive spreadsheet import
* IBM Lotus Symphony 3.0.1
* Gnumeric 1.11
* Calligra Suite 2.4, 2.5

Known problems:

* Excel 2007 borders appear too thick in LibreOffice
* Excel 2007 can not be opened in Calligra Suite
';

    echo markdown_to_html($notes);
    echo $OUTPUT->box_end();
    echo $OUTPUT->single_button(new moodle_url($PAGE->url, array('type' => 'excel2007')), 'Test Excel 2007 format');
    echo $OUTPUT->single_button(new moodle_url($PAGE->url, array('type' => 'ods')), 'Test ODS format');
    echo $OUTPUT->footer();
    die;
}

if ($type === 'excel2007') {
    $workbook = new MoodleExcelWorkbook('moodletest.xlsx', 'Excel2007');
} else if ($type === 'ods') {
    $workbook = new MoodleODSWorkbook('moodletest.ods');
}

$worksheet = array();

$worksheet = $workbook->add_worksheet('Supported');

$worksheet->hide_screen_gridlines();

$worksheet->write_string(0, 0, 'Moodle worksheet export test', $workbook->add_format(array('color'=>'red', 'size'=>20, 'bold'=>1, 'italic'=>1)));
$worksheet->set_row(0, 25);
$worksheet->write(1, 0, 'Moodle release: '.$CFG->release, $workbook->add_format(array('size'=>8, 'italic'=>1)));

$worksheet->set_column(0, 0, 20);
$worksheet->set_column(1, 1, 30);
$worksheet->set_column(2, 2, 5);
$worksheet->set_column(3, 3, 30);
$worksheet->set_column(4, 4, 20);

$miniheading = $workbook->add_format(array('size'=>15, 'bold'=>1, 'italic'=>1, 'underline'=>1));


$worksheet->write(2, 0, 'Cell types', $miniheading);
$worksheet->set_row(2, 20);
$worksheet->set_row(3, 5);

$worksheet->write(4, 0, 'String');
$worksheet->write_string(4, 1, 'Žluťoučký koníček');

$worksheet->write(5, 0, 'Number as string');
$worksheet->write_string(5, 1, 3.14159);

$worksheet->write(6, 0, 'Integer');
$worksheet->write_number(6, 1, 666);

$worksheet->write(7, 0, 'Float');
$worksheet->write_number(7, 1, 3.14159);

$worksheet->write(8, 0, 'URL');
$worksheet->write_url(8, 1, 'http://moodle.org');

$worksheet->write(9, 0, 'Date (now)');
$worksheet->write_date(9, 1, time());

$worksheet->write(10, 0, 'Formula');
$worksheet->write(10, 1, '=1+2');

$worksheet->write(11, 0, 'Blank');
$worksheet->write_blank(11, 1, $workbook->add_format(array('bg_color'=>'silver')));


$worksheet->write(14, 0, 'Text formats', $miniheading);
$worksheet->set_row(14, 20);
$worksheet->set_row(15, 5);

// Following writes use alternative format array.
$worksheet->write(16, 0, 'Bold', array('bold'=>1));
$worksheet->write(17, 0, 'Italic', array('italic'=>1));
$worksheet->write(18, 0, 'Single underline', array('underline'=>1));
$worksheet->write(19, 0, 'Double underline', array('underline'=>2));
$worksheet->write(20, 0, 'Strikeout', array('strikeout'=>1));
$worksheet->write(21, 0, 'Superscript', array('script'=>1));
$worksheet->write(22, 0, 'Subscript', array('script'=>2));
$worksheet->write(23, 0, 'Red', array('color'=>'red'));


$worksheet->write(25, 0, 'Text align', $miniheading);
$worksheet->set_row(25, 20);
$worksheet->set_row(26, 5);

$worksheet->write(27, 0, 'Wrapped text - Žloťoučký koníček', $workbook->add_format(array('text_wrap'=>true, 'border'=>1)));
$worksheet->set_row(27, 30);
$worksheet->write(27, 1, 'All centered', $workbook->add_format(array('v_align'=>'center', 'h_align'=>'center', 'border'=>1)));
$worksheet->write(28, 0, 'Top', $workbook->add_format(array('align'=>'top', 'border'=>1)));
$worksheet->set_row(28, 25);
$worksheet->write(29, 0, 'Vcenter', $workbook->add_format(array('align'=>'vcenter', 'border'=>1)));
$worksheet->set_row(29, 25);
$worksheet->write(30, 0, 'Bottom', $workbook->add_format(array('align'=>'bottom', 'border'=>1)));
$worksheet->set_row(30, 25);
$worksheet->write(28, 1, 'Left', $workbook->add_format(array('align'=>'left', 'border'=>1)));
$worksheet->write(29, 1, 'Center', $workbook->add_format(array('align'=>'center', 'border'=>1)));
$worksheet->write(30, 1, 'Right', $workbook->add_format(array('align'=>'right', 'border'=>1)));

$worksheet->write(32, 0, 'Number formats', $miniheading);
$worksheet->set_row(32, 20);
$worksheet->set_row(33, 5);

$numbers[1] = '0';
$numbers[2] = '0.00';
$numbers[3] = '#,##0';
$numbers[4] = '#,##0.00';
$numbers[11] = '0.00E+00';
$numbers[12] = '# ?/?';
$numbers[13] = '# ??/??';
$numbers[14] = 'mm-dd-yy';
$numbers[15] = 'd-mmm-yy';
$numbers[16] = 'd-mmm';
$numbers[17] = 'mmm-yy';
$numbers[22] = 'm/d/yy h:mm';
$numbers[49] = '@';


$worksheet->write_string(34, 0, '1: 0');
$worksheet->write_number(34, 1, 1003.14159, array('num_format'=>1));
$worksheet->write_string(35, 0, '2: 0.00');
$worksheet->write_number(35, 1, 1003.14159, array('num_format'=>2));
$worksheet->write_string(36, 0, '3: #,##0');
$worksheet->write_number(36, 1, 1003.14159, array('num_format'=>3));
$worksheet->write_string(37, 0, '3: #,##0.00');
$worksheet->write_number(37, 1, 1003.14159, array('num_format'=>4));
$worksheet->write_string(38, 0, '11: 0.00E+00');
$worksheet->write_number(38, 1, 3.14159, array('num_format'=>11));
$worksheet->write_string(39, 0, '12: # ?/?');
$worksheet->write_number(39, 1, 3.14, array('num_format'=>12));
$worksheet->write_string(40, 0, '13: # ??/??');
$worksheet->write_number(40, 1, 3.14, array('num_format'=>13));
$worksheet->write_string(41, 0, '15: d-mmm-yy');
$worksheet->write_date(41, 1, time(), array('num_format'=>15));
$worksheet->write_string(42, 0, '22: m/d/yy h:mm');
$worksheet->write_date(42, 1, time(), array('num_format'=>22));



$worksheet->write(2, 3, 'Borders', $miniheading);

$worksheet->write(4, 3, 'Left', $workbook->add_format(array('left'=>'1')));
$worksheet->write(6, 3, 'Bottom', $workbook->add_format(array('bottom'=>'1')));
$worksheet->write(8, 3, 'Right', $workbook->add_format(array('right'=>'1')));
$worksheet->write(10, 3, 'Top', $workbook->add_format(array('top'=>'1')));
$worksheet->write(12, 3, 'Thick borders', $workbook->add_format(array('border'=>'2')));


$worksheet->write(14, 3, 'Background colours', $miniheading);

$worksheet->write(16, 3, 'Yellow', $workbook->add_format(array('bg_color'=>'yellow')));
$worksheet->write(17, 3, 'Red', $workbook->add_format(array('bg_color'=>'red')));
$worksheet->write(18, 3, 'Green', $workbook->add_format(array('bg_color'=>'green')));
$worksheet->write(19, 3, 'Blue', $workbook->add_format(array('bg_color'=>12)));
$worksheet->write(20, 3, 'Cyan', $workbook->add_format(array('bg_color'=>'#00FFFF')));


$worksheet->write(25, 3, 'Cell merging', $miniheading);

$worksheet->merge_cells(27, 3, 28, 3);
$worksheet->write(27, 3, 'Vertical merging of cells', $workbook->add_format(array('bg_color'=>'silver')));

$worksheet->merge_cells(30, 3, 30, 4);
$worksheet->write(30, 3, 'Horizontal merging of cells', $workbook->add_format(array('pattern'=>1, 'bg_color'=>'silver')));
$worksheet->set_column(4, 4, 5);

$worksheet->set_row(44, null, null, true);
$worksheet->write(44, 0, 'Hidden row', array('bg_color'=>'yellow'));

$worksheet->set_column(5, 5, null, null, true);
$worksheet->write(0, 5, 'Hidden column', array('bg_color'=>'yellow'));


$worksheet->write(45, 0, 'Outline row 1');
$worksheet->set_row(45, null, null, false, 1);
$worksheet->write(46, 0, 'Outline row 2');
$worksheet->set_row(46, null, null, false, 2);

$worksheet->write(0, 6, 'Outline column 1');
$worksheet->set_column(6, 6, 20, null, false, 1);
$worksheet->write(0, 7, 'Outline column 2');
$worksheet->set_column(7, 7, 20, null, false, 2);



// Some unfinished stuff.

$worksheet2 = $workbook->add_worksheet('Unsupported');
$worksheet2->write(0, 0, 'Incomplete and missing features', $workbook->add_format(array('size'=>20, 'bold'=>1, 'italic'=>1)));
$worksheet2->set_row(0, 25);
$worksheet2->set_column(1, 1, 25);

$worksheet2->write(3, 1, 'Gray row - buggy');
$worksheet2->set_row(3, null, array('bg_color'=>'silver'));
$worksheet2->write(2, 6, 'Gray column - buggy');
$worksheet2->set_column(6, 6, 20, array('bg_color'=>'silver'));

$worksheet2->hide_gridlines();

$worksheet2->write(5, 0, 'Outline text - not implemented', array('outline'=>1));
$worksheet2->write(6, 0, 'Shadow text - not implemented', array('outline'=>1));

$worksheet2->write(8, 0, 'Pattern 1');
$worksheet2->write_blank(8, 1, array('pattern'=>1));
$worksheet2->write(9, 0, 'Pattern 2');
$worksheet2->write_blank(9, 1, array('pattern'=>2));
$worksheet2->write(10, 0, 'Pattern 3');
$worksheet2->write_blank(10, 1, array('pattern'=>3));


// Other worksheet tests follow.

$worksheet3 = $workbook->add_worksheet('Žlutý:koníček?přeskočil mrňavoučký potůček');
$worksheet3->write(1, 0, 'Test long Unicode worksheet name.');


$worksheet4 = $workbook->add_worksheet('');
$worksheet4->write(1, 0, 'Test missing worksheet name.');

$workbook->close();
die;
