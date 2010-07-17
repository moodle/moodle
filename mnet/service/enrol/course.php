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
 * Page to enrol our users into remote courses
 *
 * @package    plugintype
 * @subpackage pluginname
 * @copyright  2010 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/mnet/service/enrol/locallib.php');

require_sesskey();

$hostid   = required_param('host', PARAM_INT); // remote host id in our mnet_host table
$courseid = required_param('course', PARAM_INT); // id of the course in our cache table
$usecache = optional_param('usecache', true, PARAM_BOOL); // use cached list of enrolments

admin_externalpage_setup('mnetenrol', '', array('host'=>$hostid, 'course'=>$courseid, 'usecache'=>1),
                         new moodle_url('/mnet/service/enrol/course.php'));

$service = mnetservice_enrol::get_instance();

if (!$service->is_available()) {
    echo $OUTPUT->box(get_string('mnetdisabled','mnet'), 'noticebox');
    echo $OUTPUT->footer();
    die();
}

// remote hosts that may publish remote enrolment service and we are subscribed to it
$hosts = $service->get_remote_publishers();

if (empty($hosts[$hostid])) {
    print_error('wearenotsubscribedtothishost', 'mnetservice_enrol');
}
$host   = $hosts[$hostid];
$course = $DB->get_record('mnetservice_enrol_courses', array('id'=>$courseid, 'hostid'=>$host->id), '*', MUST_EXIST);

echo $OUTPUT->header();

// course name
$icon = html_writer::empty_tag('img', array('src' => $OUTPUT->pix_url('i/course'), 'alt' => get_string('category')));
echo $OUTPUT->heading($icon . s($course->fullname));

// collapsible course summary
if (!empty($course->summary)) {
    unset($options);
    $options->trusted = false;
    $options->para    = false;
    $options->filter  = false;
    $options->noclean = false;
    print_collapsible_region_start('remotecourse summary', 'remotecourse-summary', get_string('coursesummary'), false, true);
    echo format_text($course->summary, $course->summaryformat, $options,  $course->id);
    print_collapsible_region_end();
}

//$enrolments = $service->get_remote_oourse_enrolments($host->id, $course->remoteid, $usecache);

// user selectors
$currentuserselector = new mnetservice_enrol_existing_users_selector('removeselect', array('hostid'=>$host->id, 'remotecourseid'=>$course->remoteid));
$potentialuserselector = new mnetservice_enrol_potential_users_selector('addselect', array('hostid'=>$host->id, 'remotecourseid'=>$course->remoteid));

// process incoming data

// print form to enrol our students
?>
<form id="assignform" method="post" action="<?php echo $PAGE->url ?>">
<div>
  <input type="hidden" name="sesskey" value="<?php echo sesskey() ?>" />
  <input type="hidden" name="hostid" value="<?php echo $host->id ?>" />
  <input type="hidden" name="courseid" value="<?php echo $course->id ?>" />

  <table summary="" class="roleassigntable generaltable generalbox boxaligncenter" cellspacing="0">
    <tr>
      <td id="existingcell">
          <p><label for="removeselect"><?php print_string('enrolledusers', 'enrol'); ?></label></p>
          <?php $currentuserselector->display() ?>
      </td>
      <td id="buttonscell">
          <div id="addcontrols">
              <input name="add" id="add" type="submit" value="<?php echo $OUTPUT->larrow().'&nbsp;'.get_string('add'); ?>" title="<?php print_string('add'); ?>" /><br />

              <div class="enroloptions">
                  <p><?php echo get_string('assignrole', 'role') .': '. s($course->rolename); ?></p>
              </div>

          </div>

          <div id="removecontrols">
              <input name="remove" id="remove" type="submit" value="<?php echo get_string('remove').'&nbsp;'.$OUTPUT->rarrow(); ?>" title="<?php print_string('remove'); ?>" />
          </div>
      </td>
      <td id="potentialcell">
          <p><label for="addselect"><?php print_string('enrolcandidates', 'enrol'); ?></label></p>
          <?php $potentialuserselector->display() ?>
      </td>
    </tr>
  </table>
</div>
</form>
<?php

echo $OUTPUT->footer();
