<?php
/**
 * Moodle - Modular Object-Oriented Dynamic Learning Environment
 *          http://moodle.org
 * Copyright (C) 1999 onwards Martin Dougiamas  http://dougiamas.com
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package    moodle
 * @subpackage portfolio
 * @author     Penny Leach <penny@catalyst.net.nz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 1999 onwards Martin Dougiamas  http://dougiamas.com
 *
 * This file is the handler that gets invoked when there's already an export happening.
 */
require_once(dirname(dirname(__FILE__)) . '/config.php');

if (empty($CFG->enableportfolios)) {
    print_error('disabled', 'portfolio');
}

require_once($CFG->libdir . '/portfoliolib.php');

require_login();

$dataid = 0;

// look for the export id in the request, if it's not there, try the session
if (!$dataid = optional_param('id', '', PARAM_INT) ) {
    if (isset($SESSION->portfolioexport)) {
        $dataid = $SESSION->portfolioexport;
    }
}

// all we're going to do is print a table with some information
// about the current export, with a yes/ no option to resume or cancel.
$table = new html_table();
$table->head = array(
    get_string('displayarea', 'portfolio'),   // the part of moodle exporting content
    get_string('destination', 'portfolio'),   // the portfolio plugin instance
    get_string('displayinfo', 'portfolio'),   // any extra data about what it is we're exporting from the caller
);
$table->data = array();
if ($dataid) {
    try {
        // try to reawaken it and get any information about it we can
        $exporter = portfolio_exporter::rewaken_object($dataid);
        $exporter->verify_rewaken();
        $table->data[] = array(
            $exporter->get('caller')->display_name(),
            ($exporter->get('instance') ? $exporter->get('instance')->get('name') : get_string('notyetselected', 'portfolio')),
            $exporter->get('caller')->heading_summary(),
        );
    } catch (portfolio_exception $e) { }  // maybe in this case we should just kill it and redirect to the new request anyway ?
}

$strheading = get_string('activeexport', 'portfolio');

$PAGE->set_title($strheading);
$PAGE->set_heading($strheading);
echo $OUTPUT->header();
echo $OUTPUT->confirm(get_string('alreadyexporting', 'portfolio'), $CFG->wwwroot . '/portfolio/add.php', $CFG->wwwroot . '/portfolio/add.php?cancel=1');

if (count($table->data) > 0) {
    echo $OUTPUT->table($table);
}

echo $OUTPUT->footer();


