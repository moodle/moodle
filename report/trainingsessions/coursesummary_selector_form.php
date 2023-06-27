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
 * Course trainingsessions report
 *
 * @package    report_trainingsessions
 * @category   report
 * @version    moodle 2.x
 * @author     Valery Fremaux (valery.fremaux@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/lib/formslib.php');
require_once($CFG->dirroot.'/report/trainingsessions/__other/elementgrid.php');

class CourseSummarySelectorForm extends moodleform {

    public function definition() {

        $mform = $this->_form;

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'output');
        $mform->setType('output', PARAM_ALPHA);

        $mform->addElement('hidden', 'asxls');
        $mform->setType('asxls', PARAM_BOOL);

        $mform->addElement('hidden', 'view', 'coursesummary');
        $mform->setType('asxls', PARAM_ALPHA);

        $grid = &$mform->addElement('elementgrid', 'grid', '', '');

        $titles = array();
        $row = array();
        $row2 = array();

        $dateparms = array(
            'startyear' => 2008,
            'stopyear'  => 2020,
            'timezone'  => 99,
            'applydst'  => true,
            'optional'  => false
        );
        $titles[] = get_string('from');
        $row[] = & $mform->createElement('date_selector', 'from', '', $dateparms);

        $titles[] = get_string('to');
        $row[] = & $mform->createElement('date_selector', 'to', '', $dateparms);

    }
}
?>

<center>
<tr valign="top">
    <td align="right">
<?php
print_string('chooseagroup', 'report_trainingsessions');
echo " :&nbsp;";
?>
    </td>
    <td>
<?php
if (has_capability('moodle/site:accessallgroups', $context)) {
    $groups = groups_get_all_groups($course->id);
} else {
    $groups = groups_get_all_groups($course->id, $USER->id);
}
$groupoptions[0] = get_string('allgroups', 'report_trainingsessions');
if ($groupid === false) {
    $groupid = 0;
}
foreach ($groups as $group) {
    $groupoptions[$group->id] = $group->name;
}
echo html_writer::select($groupoptions, 'groupid', $groupid);
?>
    </td>
</tr>
<tr valign="top">
    <td align="right">
<?php
print_string('to');
echo ' :&nbsp;</td><td align="left">';
print_date_selector('endday', 'endmonth', 'endyear', $to);
?>
    </td>
    <td/><td align="right">
        <input type="submit" id="go_btn" name="go_btn" value="<?php print_string('update') ?>"/>
    </td>
</tr>
</table>
</form>
</center>
