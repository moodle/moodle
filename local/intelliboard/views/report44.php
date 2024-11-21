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
?>
<ul class="intelliboard-list">
	<?php foreach($report44['data'] as $row):  ?>
	<li class="intelliboard-tooltip" title="<?php echo get_string('enrolled_users_completed', 'local_intelliboard', $row); ?>">
		<?php echo format_string($row->fullname); ?>
		<span class="pull-right"><?php echo (int) $row->completed; ?>/<?php echo (int) $row->users; ?></span>
		<div class="intelliboard-progress"><span style="width:<?php echo ($row->completed) ? (($row->completed / $row->users) * 100) : 0; ?>%"></span></div>
	</li>
	<?php endforeach; ?>
	<?php if (!empty($report44['data'])): ?>
	<li class="clearfix">
		<div class="paging" style="float: right;">
			<a class="prev <?php echo ($page<=1)?'disabled':''; ?>" href="index.php?page=<?php echo $page-1; ?>&type=courses"><?php echo get_string('prev') ?></a>
			<a class="next <?php echo (count($report44['data']) < $length)?'disabled':''; ?>" href="index.php?page=<?php echo $page+1; ?>&type=courses"><?php echo get_string('next') ?></a>
		</div>
	</li>
	<?php endif; ?>
</ul>
