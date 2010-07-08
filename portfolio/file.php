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
 * For portfolio plugins that are 'pull' - ie, send the request and then wait
 * for the remote system to request the file for moodle,
 * this is the script that serves up the export file to them.
 */
require_once(dirname(dirname(__FILE__)) . '/config.php');

if (empty($CFG->enableportfolios)) {
    print_error('disabled', 'portfolio');
}

require_once($CFG->libdir . '/portfoliolib.php');
require_once($CFG->libdir . '/portfolio/exporter.php');
require_once($CFG->libdir . '/filelib.php');

// exporter id
$id = required_param('id', PARAM_INT);

require_login();
$PAGE->set_url('/portfolio/add.php', array('id' => $id));

$exporter = portfolio_exporter::rewaken_object($id);
$exporter->verify_rewaken();

// push plugins don't need to access this script.
if ($exporter->get('instance')->is_push()) {
    throw new portfolio_export_exception($exporter, 'filedenied', 'portfolio');
}

// it's up to the plugin to verify the request parameters, like a token or whatever
if (!$exporter->get('instance')->verify_file_request_params(array_merge($_GET, $_POST))) {
    throw new portfolio_export_exception($exporter, 'filedenied', 'portfolio');
}

// ok, we're good, send the file and finish the export.
$exporter->get('instance')->send_file();
$exporter->process_stage_cleanup(true);
exit;

