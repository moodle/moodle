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

require('../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot .'/local/intelliboard/locallib.php');

require_login();
require_capability('local/intelliboard:view', context_system::instance());

$intelliboardReport = optional_param('id', '', PARAM_ALPHANUM);
$alias = optional_param('alias', '', PARAM_RAW);
$intelliboard = intelliboard(['task'=>'reports']);

if ($alias and !empty($intelliboard->reports)) {
	foreach($intelliboard->reports as $key=>$val) {
		if ($val->alias == $alias) {
			redirect(new moodle_url("/local/intelliboard/reports.php", array('id'=>$key)));
			break;
		}
	}
}

$report_type = isset($intelliboard->reports[$intelliboardReport]->type)?$intelliboard->reports[$intelliboardReport]->type:'';
$params = '';

$PAGE->set_url(new moodle_url("/local/intelliboard/reports.php", array('id'=>$intelliboardReport)));
$PAGE->set_pagelayout('report');
$PAGE->set_pagetype('reports');
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('intelliboardroot', 'local_intelliboard'));
$PAGE->set_heading(get_string('intelliboardroot', 'local_intelliboard'));
$PAGE->requires->css('/local/intelliboard/assets/css/style.css');
echo $OUTPUT->header();
?>
<div class="intelliboard-page">
	<?php include("views/menu.php"); ?>
	<div class="intelliboard-content">
		<?php if ($intelliboard->alerts): ?>
			<?php foreach ($intelliboard->alerts as $text => $alert): ?>
				<div class="alert alert-block alert-<?php echo format_string($alert); ?>" role="alert"><?php echo format_text($text); ?></div>
			<?php endforeach; ?>
		<?php endif; ?>
		<?php if ($intelliboardReport): ?>
		<div id="iframe">
			<iframe id="iframe" src="<?php echo intelliboard_url().$report_type; ?>/share/<?php echo $intelliboard->db . '/' . $intelliboardReport; ?>/<?php echo format_string($intelliboard->token); ?>?header=0&frame=1&<?php echo $params; ?>" width="100%" height="800" frameborder="0"></iframe>
			<span id="iframe-loading"><?php echo get_string('loading2', 'local_intelliboard'); ?></span>
		</div>
		<?php elseif(isset($intelliboard->reports) and !empty($intelliboard->reports)): ?>
			<div id="adminsettings">
				<?php foreach($intelliboard->reports as $key=>$val): ?>
					<div><h3><a href="reports.php?id=<?php echo format_string($key); ?>" <?php echo ($id == $key)?'class="active"':''; ?>><?php echo format_string($val->name); ?></a></h3></div>
				<?php endforeach; ?>
			</div>
		<?php else: ?>
			<div class="alert alert-block alert-info" role="alert"><?php echo get_string('reportselect', 'local_intelliboard'); ?></div>
		<?php endif; ?>
	</div>
	<?php include("views/footer.php"); ?>
</div>
<?php
echo $OUTPUT->footer();
