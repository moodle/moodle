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
 * This file is the landing point for returning to moodle after authenticating at mahara
 *
 * @since Moodle 2.0
 * @package moodlecore
 * @subpackage portfolio
 * @copyright 2009 Penny Leach
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(dirname(dirname(__FILE__))). '/config.php');

if (empty($CFG->enableportfolios)) {
    print_error('disabled', 'portfolio');
}

require_once($CFG->libdir . '/portfoliolib.php');
require_once($CFG->libdir . '/portfolio/plugin.php');
require_once($CFG->libdir . '/portfolio/exporter.php');
require_once($CFG->dirroot . '/mnet/lib.php');

require_login();

$id     = required_param('id', PARAM_INT);              // id of current export
$landed = optional_param('landed', false, PARAM_BOOL);  // this is the parameter we get back after we've jumped to mahara

if (!$landed) {
    $exporter = portfolio_exporter::rewaken_object($id);
    $exporter->verify_rewaken();

    $mnetauth = get_auth_plugin('mnet');
    if (!$url = $mnetauth->start_jump_session($exporter->get('instance')->get_config('mnethostid'), '/portfolio/mahara/preconfig.php?landed=1&id=' . $id, true)) {
        throw new porfolio_exception('failedtojump', 'portfolio_mahara');
    }
    redirect($url);
} else {
    // now we have the sso session set up, start sending intent stuff and then redirect back to portfolio/add.php when we're done
    $exporter = portfolio_exporter::rewaken_object($id);
    $exporter->verify_rewaken();

    $exporter->get('instance')->send_intent();
    redirect($CFG->wwwroot . '/portfolio/add.php?postcontrol=1&sesskey=' . sesskey() . '&id=' . $id);
}

