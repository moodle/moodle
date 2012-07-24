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
 * Script to set up cron to complete the upgrade automatically.
 *
 * @package    tool
 * @subpackage qeupgradehelper
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(__FILE__) . '/../../../config.php');
require_once(dirname(__FILE__) . '/locallib.php');
require_once(dirname(__FILE__) . '/cronsetup_form.php');
require_once($CFG->libdir . '/adminlib.php');

require_login();
require_capability('moodle/site:config', context_system::instance());

admin_externalpage_setup('qeupgradehelper', '', array(),
        tool_qeupgradehelper_url('cronsetup'));
$PAGE->navbar->add(get_string('cronsetup', 'tool_qeupgradehelper'));

$renderer = $PAGE->get_renderer('tool_qeupgradehelper');

$form = new tool_qeupgradehelper_cron_setup_form(
        new moodle_url('/admin/tool/qeupgradehelper/cronsetup.php'));
$form->set_data(get_config('tool_qeupgradehelper'));

if ($form->is_cancelled()) {
    redirect(tool_qeupgradehelper_url('index'));

} else if ($fromform = $form->get_data()) {
    if ($fromform->cronenabled) {
        set_config('cronenabled', $fromform->cronenabled, 'tool_qeupgradehelper');
        set_config('starthour', $fromform->starthour, 'tool_qeupgradehelper');
        set_config('stophour', $fromform->stophour, 'tool_qeupgradehelper');
        set_config('procesingtime', $fromform->procesingtime, 'tool_qeupgradehelper');

    } else {
        unset_config('cronenabled', 'tool_qeupgradehelper');
        unset_config('starthour', 'tool_qeupgradehelper');
        unset_config('stophour', 'tool_qeupgradehelper');
        unset_config('procesingtime', 'tool_qeupgradehelper');
    }
    redirect(tool_qeupgradehelper_url('index'));

}

echo $renderer->header();
echo $renderer->heading(get_string('cronsetup', 'tool_qeupgradehelper'));
echo $renderer->box(get_string('croninstructions', 'tool_qeupgradehelper'));
$form->display();
echo $renderer->footer();
