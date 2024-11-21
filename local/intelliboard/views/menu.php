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
?>
<?php
	$id = optional_param('id', 0, PARAM_RAW);
	$debug = get_config('local_intelliboard', 'debug');
	$debugmode = optional_param('debug', '', PARAM_RAW);

	echo (!isset($USER->noalert) and $intelliboard->alert) ? $intelliboard->alert : '';
?>

<?php if ($debug and $debugmode and isset($intelliboard->debugging)): ?>
	<pre>
		<code>
			<?php echo $intelliboard->debugging; ?>
		</code>
	</pre>
<?php endif; ?>


<?php if ($intelliboard->alerts && $PAGE->pagetype !== 'initial-report'): ?>
	<?php foreach($intelliboard->alerts as $key => $value): ?>
			<div class="alert alert-<?php echo format_string($value); ?>"><?php echo $key; ?></div>
	<?php endforeach; ?>
<?php endif; ?>


<ul class="intelliboard-menu admin-menu">
	<li><a href="index.php" <?php echo ($PAGE->pagetype == 'home')?'class="active"':''; ?>><i class="ion-ios-pulse"></i> <?php echo get_string('dashboard', 'local_intelliboard');?></a></li>

    <?php if(isset($intelliboard->sets) and !empty($intelliboard->sets)): ?>
    <li class="submenu"><a href="#" <?php echo ($PAGE->pagetype == 'monitors')?'class="active"':''; ?>><?php echo get_string('monitors', 'local_intelliboard');?> <i class="arr ion-arrow-down-b"></i></a>
        <ul>
            <?php foreach($intelliboard->sets as $key=>$val): ?>
                <li><a href="monitors.php?id=<?php echo format_string($key); ?>" <?php echo ($id == $key)?'class="active"':''; ?>><?php echo format_string($val); ?></a></li>
            <?php endforeach; ?>
        </ul>
    </li>
    <?php endif; ?>

    <?php $intelli_initial_reports = intelli_initial_reports();
    if ($intelli_initial_reports || $intelliboard->reports): ?>
    <li class="submenu">
        <a href="#" <?php echo (in_array($PAGE->pagetype, ['reports', 'initial-report']))?'class="active"':''; ?>>
            <?php echo get_string('reports', 'local_intelliboard');?> <i class="arr ion-arrow-down-b"></i>
        </a>
        <ul class="reports-l">
            <?php foreach($intelli_initial_reports as $ireport): ?>
                <li>
                    <a href="<?php echo $ireport["url"]; ?>">
                        <?php echo $ireport["name"]; ?>
                    </a>
                </li>
            <?php endforeach; ?>

            <?php if(isset($intelliboard->reports) and !empty($intelliboard->reports)): ?>
                <?php foreach($intelliboard->reports as $key=>$val): ?>
                    <li>
                        <a href="reports.php?id=<?php echo format_string($key); ?>" <?php echo ($id === $key)?'class="active"':''; ?>>
                            <?php echo format_string($val->name); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </li>
    <?php endif; ?>

    <li><a target="_blank" href="https://intelliboard.atlassian.net/servicedesk/customer/portal/6" <?php echo ($PAGE->pagetype == 'help')?'class="active"':''; ?>><?php echo get_string('help', 'local_intelliboard');?></a></li>
    <?php if($intelliboard->token and get_config('local_intelliboard', 'sso')): ?>
        <li class="sso" >
            <a target="_blank" href="<?php echo intelliboard_url(); ?>auth/sso/<?php echo format_string($intelliboard->db); ?>/<?php echo format_string($intelliboard->token); ?>" class="ion-log-in"> <?php echo get_string('intelliboardnet', 'local_intelliboard');?></a>
        </li>
    <?php endif; ?>
</ul>
