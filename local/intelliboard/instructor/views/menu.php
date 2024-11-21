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
 * @website    https://intelliboard.net/
 */

$id = optional_param('id', 0, PARAM_RAW);
$debug = get_config('local_intelliboard', 'debug');
$debugmode = optional_param('debug', '', PARAM_RAW);

$intellicartenabled = false;
if(get_config('local_intelliboard', 'intellicart_instructor_integration') && file_exists($CFG->dirroot . '/local/intellicart/locallib.php')) {
    require_once($CFG->dirroot . '/local/intellicart/locallib.php');
    $intellicartenabled = local_intellicart_enable('', true);
}

?>

<?php if ($debug and $debugmode and isset($intelliboard->debugging)): ?>
	<pre>
		<code>[reports_status]: <?php echo (get_config('local_intelliboard', 'n9')) ? 1 : 0; ?></code>
		<code>[count_reports]: <?php echo count($intelliboard->reports); ?></code>
		<code><?php echo $intelliboard->debugging; ?></code>
	</pre>
<?php endif; ?>

<ul class="intelliboard-menu">
    <?php if(get_config('local_intelliboard', 'show_dashboard_tab')): ?>
	    <li><a href="index.php" <?php echo ($PAGE->pagetype == 'home')?'class="active"':''; ?>><i class="ion-ios-pulse"></i> <?php echo get_string('dashboard', 'local_intelliboard');?></a></li>
    <?php endif; ?>

	<?php if(get_config('local_intelliboard', 'n8')): ?>
		<li><a href="courses.php" <?php echo ($PAGE->pagetype == 'courses')?'class="active"':''; ?>><?php echo get_string('courses', 'local_intelliboard');?></a></li>
	<?php endif; ?>

    <?php if(get_config('local_intelliboard', 'coursessessionspage') && $intellicartenabled): ?>
		<li><a href="sessions.php" <?php echo ($PAGE->pagetype == 'sessions')?'class="active"':''; ?>><?php echo get_string('coursessessions', 'local_intelliboard');?></a></li>
	<?php endif; ?>


	<?php if(get_config('local_intelliboard', 'n9') and isset($intelliboard->reports) and !empty($intelliboard->reports)): ?>
	<li class="submenu"><a href="#" <?php echo ($PAGE->pagetype == 'reports')?'class="active"':''; ?>><?php echo get_string('reports', 'local_intelliboard');?> <i class="arr ion-arrow-down-b"></i></a>
		<ul>
			<?php foreach($intelliboard->reports as $key=>$val): ?>
				<li><a href="reports.php?id=<?php echo format_string($key); ?>" <?php echo ($id === $key)?'class="active"':''; ?>><?php echo format_string($val->name); ?></a></li>
			<?php endforeach; ?>
		</ul>
	</li>
	<?php endif; ?>

	<?php if(isset($intelliboard->sets) and !empty($intelliboard->sets)): ?>
    <li class="submenu"><a href="#" <?php echo ($PAGE->pagetype == 'monitors')?'class="active"':''; ?>><?php echo get_string('monitors', 'local_intelliboard');?> <i class="arr ion-arrow-down-b"></i></a>
        <ul>
            <?php foreach($intelliboard->sets as $key=>$val): ?>
                <li><a href="monitors.php?id=<?php echo format_string($key); ?>" <?php echo ($id == $key)?'class="active"':''; ?>><?php echo format_string($val); ?></a></li>
            <?php endforeach; ?>
        </ul>
    </li>
    <?php endif; ?>
</ul>
