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
 * @package    local
 * @subpackage qeupgradehelper
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(__FILE__) . '/../../config.php');
require_once(dirname(__FILE__) . '/locallib.php');
require_once(dirname(__FILE__) . '/cronsetup_form.php');
require_once($CFG->libdir . '/adminlib.php');

require_login();
require_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM));

admin_externalpage_setup('qeupgradehelper', '', array(),
        local_qeupgradehelper_url('cronsetup'));
$PAGE->navbar->add(get_string('cronsetup', 'local_qeupgradehelper'));

$renderer = $PAGE->get_renderer('local_qeupgradehelper');

$form = new local_qeupgradehelper_cron_setup_form(
        new moodle_url('/local/qeupgradehelper/cronsetup.php'));
$form->set_data(get_config('local_qeupgradehelper'));

if ($form->is_cancelled()) {
    redirect(local_qeupgradehelper_url('index'));

} else if ($fromform = $form->get_data()) {
    if ($fromform->cronenabled) {
        set_config('cronenabled', $fromform->cronenabled, 'local_qeupgradehelper');
        set_config('starthour', $fromform->starthour, 'local_qeupgradehelper');
        set_config('stophour', $fromform->stophour, 'local_qeupgradehelper');
        set_config('procesingtime', $fromform->procesingtime, 'local_qeupgradehelper');

    } else {
        unset_config('cronenabled', 'local_qeupgradehelper');
        unset_config('starthour', 'local_qeupgradehelper');
        unset_config('stophour', 'local_qeupgradehelper');
        unset_config('procesingtime', 'local_qeupgradehelper');
    }
    redirect(local_qeupgradehelper_url('index'));

}

echo $renderer->header();
echo $renderer->heading(get_string('cronsetup', 'local_qeupgradehelper'));
echo $renderer->box(get_string('croninstructions', 'local_qeupgradehelper'));
$form->display();
echo $renderer->footer();
