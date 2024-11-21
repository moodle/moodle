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
 * @copyright  2018 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    http://intelliboard.net/
 */

require('../../../config.php');
require_once($CFG->dirroot .'/local/intelliboard/locallib.php');
require_once($CFG->dirroot .'/local/intelliboard/student/lib.php');
require_once($CFG->dirroot .'/local/intelliboard/student/tables.php');
if(file_exists($CFG->dirroot . '/local/intellicart/locallib.php')) {
    require_once($CFG->dirroot . '/local/intellicart/locallib.php');
}

require_login();
$id = required_param('id', PARAM_INT);
$search = optional_param('search', '', PARAM_TEXT);

if ($search) {
    require_sesskey();
}

if(!get_config('local_intellicart', 'enabled')){
    throw new moodle_exception('invalidaccess', 'error');
}
$showing_user = $USER;
$totals = intelliboard_learner_totals($showing_user->id);

$PAGE->set_url(new moodle_url(
    "/local/intelliboard/student/seatsdetails.php",
    [
        'search'  => $search,
        'sesskey' => sesskey(),
        'id' => $id,
    ]
));

$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('intelliboardroot', 'local_intelliboard'));
$PAGE->set_heading(get_string('intelliboardroot', 'local_intelliboard'));
$PAGE->navbar->add(get_string('ts1', 'local_intelliboard'), new moodle_url('/local/intelliboard/student/index.php'));
$PAGE->navbar->add(get_string('myseats', 'local_intelliboard'));
$PAGE->requires->jquery();
$PAGE->requires->css('/local/intelliboard/assets/css/style.css');
$PAGE->set_pagetype('myseats');
$params = array(
    'do'=>'learner',
    'mode'=> 1
);
$intelliboard = intelliboard($params);
$table = new intelliboard_used_seats_table(
    'seats_details', ['id' => $id, 'search' => $search]
);
$intellicartenabled = (
    file_exists($CFG->dirroot . '/local/intellicart/locallib.php') &&
    local_intellicart_enable('', true)
);
$showseats = get_config('local_intellicart', 'enableseatsvendors');

echo $OUTPUT->header();?>

<?php if(!isset($intelliboard) || !$intelliboard->token || !$intellicartenabled || !$showseats): ?>
    <div class="alert alert-error alert-block" role="alert"><?php echo get_string('intelliboardaccess', 'local_intelliboard'); ?></div>
<?php else: ?>
<div class="intelliboard-page intelliboard-student">
    <?php include("views/menu.php"); ?>
        <div class="intelliboard-search clearfix">
            <form action="<?php echo $PAGE->url; ?>" method="GET">
                <input type="hidden" name="sesskey" value="<?php p(sesskey()); ?>" />
                <input type="hidden" name="id" value="<?php echo $id; ?>" />

                <span class="pull-left"><input class="form-control" name="search" type="text" value="<?php echo format_string($search); ?>" placeholder="<?php echo get_string('type_here', 'local_intelliboard');?>" /></span>
                <button class="btn btn-default"><?php echo get_string('search');?></button>
            </form>
        </div>
        <div class="intelliboard-overflow grades-table">
            <?php $table->out(10, true); ?>
        </div>
    <?php include("../views/footer.php"); ?>
</div>
<?php endif; ?>
<?php echo $OUTPUT->footer();
