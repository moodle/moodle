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
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 *
 * @package    local_intelliboard
 * @copyright  2017 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    http://intelliboard.net/
 */

require_once($CFG->dirroot .'/local/intelliboard/instructor/lib.php');


$id = optional_param('id', 0, PARAM_RAW);
$alt_name = get_config('local_intelliboard', 'grades_alt_text');
$def_name = get_string('grades', 'local_intelliboard');
$grade_name = ($alt_name) ? $alt_name : $def_name;
$scale_real = get_config('local_intelliboard', 'scale_real');
$intellicart = get_config('local_intelliboard', 'intellicart_student_integration');
$other_user = optional_param('user', 0, PARAM_INT);


if($intellicart && file_exists($CFG->dirroot . '/local/intellicart/locallib.php')) {
    require_once($CFG->dirroot . '/local/intellicart/locallib.php');
}

$mentor_role = get_config('local_intelliboard', 't09');
$show_students = false;
if ($mentor_role>0){
    $show_students = intelliboard_instructor_have_access($USER->id);

    if($show_students){
        $students = $DB->get_records_sql("SELECT u.*
                                          FROM {role_assignments} ra
                                            JOIN {context} c ON c.id=ra.contextid
                                            JOIN {user} u ON u.id=c.instanceid
                                          WHERE ra.roleid=:role AND ra.userid=:userid",array('role'=>$mentor_role, 'userid'=>$USER->id));
        $users_list = array(0=>fullname($USER));
        foreach($students as $student){
            $users_list[$student->id] = fullname($student);
        }
    }
}
$user_courses = enrol_get_users_courses($showing_user->id);
$sum_courses = get_user_preferences('enabeled_sum_courses_'.$showing_user->id, '');
$sum_courses = (!empty($sum_courses))?explode(',', $sum_courses):array();

if (!$intellicart) {
  $intellicartenabled = false;
} else {
  $intellicartenabled = (
      file_exists($CFG->dirroot . '/local/intellicart/locallib.php') &&
      local_intellicart_enable('', true)
  );
  $showwaitlist = get_config('local_intellicart', 'enablewaitlist');
  $showseats = get_config('local_intellicart', 'enableseatsvendors');
  $showsubscriptions = get_config('local_intellicart', 'enablesubscription');
}
$ordersurl = (new moodle_url('/local/intelliboard/student/orders.php'))->out();
$seatsurl = (new moodle_url('/local/intelliboard/student/seats.php'))->out();
$waitlisturl = (new moodle_url('/local/intelliboard/student/waitlist.php'))->out();
$subscriptionsurl = (new moodle_url('/local/intelliboard/student/subscriptions.php'))->out();

?>

<div class="sheader clearfix">
	<div class="avatar">
		<?php echo $OUTPUT->user_picture($showing_user, array('size'=>75)); ?>
	</div>
    <?php if($show_students && !empty($students)):?>
        <div class="info">
            <div class="intelliboard-dropdown students">
                <?php foreach($users_list as $key=>$value): ?>
                    <?php if($key == $other_user): ?>
                        <button><span value="<?php echo $key; ?>"><?php echo $value; ?></span> <i class="ion-android-arrow-dropdown"></i></button>
                    <?php endif; ?>
                <?php endforeach; ?>
                <ul>
                    <?php foreach($users_list as $key=>$value): ?>
                        <?php if($key != $other_user): ?>
                            <li value="<?php echo $key; ?>"><?php echo $value; ?></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="clear"></div>
            <p><?php echo format_string($showing_user->email); ?></p>
        </div>
    <?php else:?>
        <div class="info">
            <h2><?php echo fullname($showing_user); ?> <i class="ion-checkmark-circled"></i></h2>
            <p><?php echo format_string($showing_user->email); ?></p>
        </div>
    <?php endif;?>
	<div class="stats">
		<ul>
			<?php if(get_config('local_intelliboard', 't04')): ?>
			<li><?php echo (int)$totals->completed; ?><span><?php echo get_string('completed_courses', 'local_intelliboard');?></span></li>
			<?php endif; ?>

			<?php if(get_config('local_intelliboard', 't05')): ?>
			<li><?php echo (int)$totals->inprogress; ?><span><?php echo get_string('courses_in_progress', 'local_intelliboard');?></span></li>
			<?php endif; ?>

			<?php if(get_config('local_intelliboard', 't06')): ?>
			<li><?php echo ($scale_real)?$totals->grade:(int)$totals->grade; ?><span><?php echo get_string('courses_avg_grade', 'local_intelliboard');?></span></li>
			<?php endif; ?>

			<?php if(get_config('local_intelliboard', 't08')): ?>
			<li class="dropdown">
                <a href="javascript:;" data-toggle="dropdown" ><?php echo $totals->sum_grade; ?></a>
                <span><?php echo get_string('courses_sum_grade', 'local_intelliboard');?></span>
                <div class="dropdown-menu">
                    <ul class="sum-courses-list">
                        <?php foreach($user_courses as $course):?>
                            <li class="course-item clearfix">
                                <label>
                                    <input type="checkbox" value="<?php echo $course->id?>" <?php echo (empty($sum_courses) || in_array($course->id, $sum_courses))?'checked="checked"':''; ?>>
                                    <?php echo $course->fullname;?>
                                </label>
                            </li>
                        <?php endforeach;?>
                    </ul>
                    <button class="btn btn-primary"><?php echo get_string('save');?></button>
                </div>
            </li>
			<?php endif; ?>

			<?php if(get_config('local_intelliboard', 't07')): ?>
			<li><a href="<?php echo $CFG->wwwroot; ?>/message/index.php?viewing=unread&id=<?php echo $showing_user->id; ?>">
				<?php echo (int)$totals->messages; ?></a>
			<span><?php echo get_string('messages', 'local_intelliboard');?></span></li>
			<?php endif; ?>
		</ul>
	</div>
</div>
<ul class="intelliboard-menu">
	<?php if(get_config('local_intelliboard', 't2')): ?>
		<li><a href="index.php<?php echo ($other_user>0)?"?user=".$other_user:"";?>" <?php echo ($PAGE->pagetype == 'home')?'class="active"':''; ?>><i class="ion-ios-pulse"></i> <?php echo get_string('dashboard', 'local_intelliboard');?></a></li>
	<?php endif; ?>
	<?php if(get_config('local_intelliboard', 't3')): ?>
		<li><a href="courses.php<?php echo ($other_user>0)?"?user=".$other_user:"";?>" <?php echo ($PAGE->pagetype == 'courses')?'class="active"':''; ?>><?php echo get_string('courses', 'local_intelliboard');?></a></li>
	<?php endif; ?>
	<?php if(get_config('local_intelliboard', 't4')): ?>
		<li><a href="grades.php<?php echo ($other_user>0)?"?user=".$other_user:"";?>" <?php echo ($PAGE->pagetype == 'grades')?'class="active"':''; ?>><?php echo $grade_name;?></a></li>
	<?php endif; ?>

	<?php if(get_config('local_intelliboard', 't48') and isset($intelliboard->reports) and !empty($intelliboard->reports)): ?>
	<li class="submenu"><a href="#" <?php echo ($PAGE->pagetype == 'reports')?'class="active"':''; ?>><?php echo get_string('reports', 'local_intelliboard');?> <i class="arr ion-arrow-down-b"></i></a>
		<ul>
			<?php foreach($intelliboard->reports as $key=>$val): ?>
				<li><a href="reports.php?id=<?php echo $key; ?>" <?php echo ($id === $key)?'class="active"':''; ?>><?php echo format_string($val->name); ?></a></li>
			<?php endforeach; ?>
		</ul>
	</li>
	<?php endif; ?>
    <?php if(get_config('local_intelliboard', 'enable_badges_report')):?>
        <li>
            <a href="<?php echo $CFG->wwwroot ?>/local/intelliboard/student/badges.php">
                <?php echo get_string('badges');?>
            </a>
        </li>
    <?php endif; ?>
    <?php if($intellicartenabled): ?>
        <!-- Orders -->
        <li>
            <a href="<?php echo $ordersurl; ?>" <?php echo ($PAGE->pagetype == 'myorders')?'class="active"':''; ?>>
                <?php echo get_string('myorders', 'local_intelliboard');?>
            </a>
        </li>
        <!-- Seats -->
        <?php if($showseats): ?>
            <li>
                <a href="<?php echo $seatsurl; ?>" <?php echo ($PAGE->pagetype == 'myseats')?'class="active"':''; ?>>
                    <?php echo get_string('myseats', 'local_intelliboard');?>
                </a>
            </li>
        <?php endif; ?>
        <!-- Waitlist -->
        <?php if($showwaitlist): ?>
            <li>
                <a href="<?php echo $waitlisturl; ?>" <?php echo ($PAGE->pagetype == 'mywaitlist')?'class="active"':''; ?>>
                    <?php echo get_string('mywaitlist', 'local_intelliboard');?>
                </a>
            </li>
        <?php endif; ?>
        <!-- Subscriptions -->
        <?php if($showsubscriptions): ?>
            <li>
                <a href="<?php echo $subscriptionsurl; ?>" <?php echo ($PAGE->pagetype == 'mysubscriptions')?'class="active"':''; ?>>
                    <?php echo get_string('mysubscriptions', 'local_intelliboard');?>
                </a>
            </li>
        <?php endif; ?>
    <?php endif; ?>
</ul>
<?php if($show_students && !empty($students)): ?>
    <script>
        jQuery(document).ready(function() {
            jQuery('.sheader .info .intelliboard-dropdown ul li').click(function (e) {
                var stext = jQuery(this).parent().parent().find('span').text();
                var svalue = jQuery(this).parent().parent().find('span').attr('value');
                var ctext = jQuery(this).text();
                var cvalue = jQuery(this).attr('value');

                jQuery(this).text(stext);
                jQuery(this).attr('value', svalue);
                jQuery(this).parent().parent().find('span').text(ctext);
                jQuery(this).parent().parent().find('span').attr('value', cvalue);
                jQuery(this).parent().hide();
                location = "<?php echo $PAGE->url->get_path(); ?>?user=" + cvalue;
            });

            jQuery('.sheader .info .intelliboard-dropdown button').click(function (e) {
                e.stopPropagation();
                if (jQuery(this).parent().hasClass('disabled')) {
                    return false;
                }
                jQuery(this).parent().find('ul').toggle();
            });

            jQuery('.sum-courses-list label').click(function (e) {
                e.stopPropagation();
            });
            jQuery('.sheader .stats .dropdown-menu button').click(function (e) {
                e.stopPropagation();
                var checkedVals = jQuery('.sum-courses-list input:checkbox:checked').map(function() {
                    return this.value;
                }).get().join(',');
                M.cfg.developerdebug = false;
                require(['core_user/repository'], function(UserRepository) {
                    UserRepository.setUserPreference('enabeled_sum_courses_<?php echo $showing_user->id;?>', checkedVals);
                });
                setTimeout(function () {
                    window.location.reload();
                },600);
            });
        });
    </script>
<?php endif; ?>
