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
 * This file is the main controller to do with the portfolio export wizard.
 */
require_once(dirname(dirname(__FILE__)) . '/config.php');

if (empty($CFG->enableportfolios)) {
    print_error('disabled', 'portfolio');
}

// this will pull in all the other required libraries
require_once($CFG->libdir . '/portfoliolib.php');
// so plugins don't have to.
require_once($CFG->libdir . '/formslib.php');

$cancel        = optional_param('cancel', 0, PARAM_RAW);                      // user has cancelled the request
$dataid        = optional_param('id', 0, PARAM_INT);                          // id of partially completed export (in session, everything else in portfolio_tempdata
$instanceid    = optional_param('instance', 0, PARAM_INT);                    // instanceof of configured portfolio plugin
$courseid      = optional_param('course', 0, PARAM_INT);                      // courseid the data being exported belongs to (caller object should provide this later)
$stage         = optional_param('stage', PORTFOLIO_STAGE_CONFIG, PARAM_INT);  // stage of the export we're at (stored in the exporter)
$postcontrol   = optional_param('postcontrol', 0, PARAM_INT);                 // when returning from some bounce to an external system, this gets passed
$callbackfile  = optional_param('callbackfile', null, PARAM_PATH);            // callback file eg /mod/forum/lib.php - the location of the exporting content
$callbackclass = optional_param('callbackclass', null, PARAM_ALPHAEXT);       // callback class eg forum_portfolio_caller - the class to handle the exporting content.

require_login();  // this is selectively called again with $course later when we know for sure which one we're in.
$PAGE->set_url('/portfolio/add.php', array('id' => $dataid));
$exporter = null;

// try and find a partial export id in the session if it's not passed explicitly
if (empty($dataid)) {
    if (isset($SESSION->portfolioexport)) {
        $dataid = $SESSION->portfolioexport;
    }
}

// if we have a dataid, it means we're in the middle of an export,
// so rewaken it and continue.
if (!empty($dataid)) {
    try {
        $exporter = portfolio_exporter::rewaken_object($dataid);
    } catch (portfolio_exception $e) {
        // this can happen in some cases, a cancel request is sent when something is already broken
        // so process it elegantly and move on.
        if ($cancel) {
            unset($SESSION->portfolioexport);
            redirect($CFG->wwwroot);
        } else {
            portfolio_exporter::print_expired_export();
        }
    }
    // we have to wake it up first before we can cancel it
    // so temporary directories etc get cleaned up.
    if ($cancel) {
        $exporter->cancel_request();
    }
    // verify we still belong to the correct user and session
    $exporter->verify_rewaken();
    // if we don't have an instanceid in the exporter
    // it means we've just posted from the 'choose portfolio instance' page
    // so process that and start up the portfolio plugin
    if (!$exporter->get('instance')) {
        if ($instanceid) {
            try {
                $instance = portfolio_instance($instanceid);
            } catch (portfolio_exception $e) {
                portfolio_export_rethrow_exception($exporter, $e);
            }
            // this technically shouldn't happen but make sure anyway
            if ($broken = portfolio_instance_sanity_check($instance)) {
                throw new portfolio_export_exception($exporter, $broken[$instance->get('id')], 'portfolio_' . $instance->get('plugin'));
            }
            // now we're all set up, ready to go
            $instance->set('user', $USER);
            $exporter->set('instance', $instance);
            $exporter->save();
        }
    }

    portfolio_export_pagesetup($PAGE, $exporter->get('caller')); // this calls require_login($course) if it can..

// completely new request, look to see what information we've been passed and set up the exporter object.
} else {
    // you cannot get here with no information for us, we must at least have the caller.
    if (empty($_GET) && empty($_POST)) {
        portfolio_exporter::print_expired_export();
    }
    // we'e just posted here for the first time and have might the instance already
    if ($instanceid) {
        // this can throw exceptions but there's no point catching and rethrowing here
        // as the exporter isn't created yet.
        $instance = portfolio_instance($instanceid);
        if ($broken = portfolio_instance_sanity_check($instance)) {
            throw new portfolio_exception($broken[$instance->get('id')], 'portfolio_' . $instance->get('plugin'));
        }
        $instance->set('user', $USER);
    } else {
        $instance = null;
    }

    // we must be passed this from the caller, we cannot start a new export
    // without knowing information about what part of moodle we come from.
    if (empty($callbackfile) || empty($callbackclass)) {
        portfolio_exporter::print_expired_export();
    }

    // so each place in moodle can pass callback args here
    // process the entire request looking for ca_*
    // be as lenient as possible while still being secure
    // so only accept certain parameter types.
    $callbackargs = array();
    foreach (array_keys(array_merge($_GET, $_POST)) as $key) {
        if (strpos($key, 'ca_') === 0) {
            if (!$value =  optional_param($key, false, PARAM_ALPHAEXT)) {
                if (!$value = optional_param($key, false, PARAM_NUMBER)) {
                    $value = optional_param($key, false, PARAM_PATH);
                }
            }
            // strip off ca_ for niceness
            $callbackargs[substr($key, 3)] = $value;
        }
    }
    if (!confirm_sesskey()) {
        throw new portfolio_caller_exception('confirmsesskeybad', 'error');
    }
    // righto, now we have the callback args set up
    // load up the caller file and class and tell it to set up all the data
    // it needs
    require_once($CFG->dirroot . $callbackfile);
    if (!class_exists($callbackclass) || !is_subclass_of($callbackclass, 'portfolio_caller_base')) {
        throw new portfolio_caller_exception('callbackclassinvalid', 'portfolio');
    }
    $caller = new $callbackclass($callbackargs);
    $caller->set('user', $USER);
    $caller->load_data();
    // this must check capabilities and either throw an exception or return false.
    if (!$caller->check_permissions()) {
        throw new portfolio_caller_exception('nopermissions', 'portfolio', $caller->get_return_url());
    }

    portfolio_export_pagesetup($PAGE, $caller); // this calls require_login($course) if it can..

    // finally! set up the exporter object with the portfolio instance, and caller information elements
    $exporter = new portfolio_exporter($instance, $caller, $callbackfile);

    // set the export-specific variables, and save.
    $exporter->set('user', $USER);
    $exporter->set('sesskey', sesskey());
    $exporter->save();

    // and finally, put it in the session for waking up again later.
    $SESSION->portfolioexport = $exporter->get('id');
}

if (!$exporter->get('instance')) {
    // we've just arrived but have no instance
    // in this case the exporter object and the caller object have been set up above
    // so just make a little form to select the portfolio plugin instance,
    // which is the last thing to do before starting the export.
    $mform = new portfolio_instance_select('', array('caller' => $exporter->get('caller')));
    if ($mform->is_cancelled()) {
        $exporter->cancel_request();
    } else if ($fromform = $mform->get_data()){
        redirect($CFG->wwwroot . '/portfolio/add.php?instance=' . $fromform->instance . '&amp;id=' . $exporter->get('id'));
        exit;
    }
    else {
        $exporter->print_header('selectplugin');
        echo $OUTPUT->box_start();
        $mform->display();
        echo $OUTPUT->box_end();
        echo $OUTPUT->footer();
        exit;
    }
}

// if we haven't been passed &stage= grab it from the exporter.
if (!$stage) {
    $stage = $exporter->get('stage');
}

// for places returning control to pass (rather than PORTFOLIO_STAGE_PACKAGE
// which is unstable if they can't get to the constant (eg external system)
$alreadystolen = false;
if ($postcontrol) { // the magic request variable plugins must pass on returning here
    try {
        // allow it to read whatever gets sent back in the request
        // this is useful for plugins that redirect away and back again
        // adding a token to the end of the url, for example box.net
        $exporter->instance()->post_control($stage, array_merge($_GET, $_POST));
    } catch (portfolio_plugin_exception $e) {
        portfolio_export_rethrow_exception($exporter, $e);
    }
    $alreadystolen = true; // remember this so we don't get caught in a steal control loop!
}

// actually do the work now..
$exporter->process_stage($stage, $alreadystolen);


