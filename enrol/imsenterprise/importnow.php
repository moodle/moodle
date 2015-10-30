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
 * Import IMS Enterprise file immediately.
 *
 * @package    enrol_imsenterprise
 * @copyright  2006 Dan Stowell
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_login(0, false);
require_capability('moodle/site:config', context_system::instance());
require_sesskey();

$site = get_site();

// Get language strings.
$PAGE->set_context(context_system::instance());

$PAGE->set_url('/enrol/imsenterprise/importnow.php');
$PAGE->set_title(get_string('importimsfile', 'enrol_imsenterprise'));
$PAGE->set_heading(get_string('importimsfile', 'enrol_imsenterprise'));
$PAGE->navbar->add(get_string('administrationsite'));
$PAGE->navbar->add(get_string('plugins', 'admin'));
$PAGE->navbar->add(get_string('enrolments', 'enrol'));
$PAGE->navbar->add(get_string('pluginname', 'enrol_imsenterprise'),
    new moodle_url('/admin/settings.php', array('section' => 'enrolsettingsimsenterprise')));
$PAGE->navbar->add(get_string('importimsfile', 'enrol_imsenterprise'));
$PAGE->navigation->clear_cache();

echo $OUTPUT->header();

require_once('lib.php');

$enrol = new enrol_imsenterprise_plugin();

?>
<p>Launching the IMS Enterprise "cron" function. The import log will appear below (giving details of any
problems that might require attention).</p>
<pre style="margin:10px; padding: 2px; border: 1px solid black; background-color: white; color: black;"><?php
$enrol->cron();
?></pre><?php
echo $OUTPUT->footer();
