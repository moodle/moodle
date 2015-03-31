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
 * This file is the main controller to do with the portfolio export wizard.
 *
 * @package core_portfolio
 * @copyright 2008 Penny Leach <penny@catalyst.net.nz>,
 *            Martin Dougiamas <http://dougiamas.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL
 */
require_once(dirname(dirname(__FILE__)) . '/config.php');

if (empty($CFG->enableportfolios)) {
    print_error('disabled', 'portfolio');
}

require_once($CFG->libdir . '/portfoliolib.php');
require_once($CFG->libdir . '/portfolio/exporter.php');
require_once($CFG->libdir . '/portfolio/caller.php');
require_once($CFG->libdir . '/portfolio/plugin.php');

$dataid = optional_param('id', 0, PARAM_INT); // The ID of partially completed export, corresponds to a record in portfolio_tempdata.
$type = optional_param('type', null, PARAM_SAFEDIR); // If we're returning from an external system (postcontrol) for a single-export only plugin.
$cancel = optional_param('cancel', 0, PARAM_RAW); // User has cancelled the request.
$cancelsure = optional_param('cancelsure', 0, PARAM_BOOL); // Make sure they confirm first.
$logreturn = optional_param('logreturn', 0, PARAM_BOOL); // When cancelling, we can also come from the log page, rather than the caller.
$instanceid = optional_param('instance', 0, PARAM_INT); // The instance of configured portfolio plugin.
$courseid = optional_param('course', 0, PARAM_INT); // The courseid the data being exported belongs to (caller object should provide this later).
$stage = optional_param('stage', PORTFOLIO_STAGE_CONFIG, PARAM_INT); // Stage of the export we're at (stored in the exporter).
$postcontrol = optional_param('postcontrol', 0, PARAM_INT); // When returning from some bounce to an external system, this gets passed.
$callbackcomponent = optional_param('callbackcomponent', null, PARAM_PATH); // Callback component eg mod_forum - the component of the exporting content.
$callbackclass = optional_param('callbackclass', null, PARAM_ALPHAEXT); // Callback class eg forum_portfolio_caller - the class to handle the exporting content.
$callerformats = optional_param('callerformats', null, PARAM_TAGLIST); // Comma separated list of formats the specific place exporting content supports.

require_login();  // this is selectively called again with $course later when we know for sure which one we're in.
$PAGE->set_context(context_system::instance());
$PAGE->set_url('/portfolio/add.php', array('id' => $dataid, 'sesskey' => sesskey()));
$PAGE->set_pagelayout('admin');
$exporter = null;

if ($postcontrol && $type && !$dataid) {
    // we're returning from an external system that can't construct dynamic return urls
    // this is a special "one export of this type only per session" case
    if (portfolio_static_function($type, 'allows_multiple_exports')) {
        throw new portfolio_exception('multiplesingleresume', 'portfolio');
    }

    if (!$dataid = portfolio_export_type_to_id($type, $USER->id)) {
        throw new portfolio_exception('invalidtempid', 'portfolio');
    }
} else {
    // we can't do this in the above case, because we're redirecting straight back from an external system
    // this is not really ideal, but since we're in a "staged" wizard, the session key is checked in other stages.
    require_sesskey(); // pretty much everything in this page is a write that could be hijacked, so just do this at the top here
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
            if ($logreturn) {
                redirect($CFG->wwwroot . '/user/portfoliologs.php');
            }
            redirect($CFG->wwwroot);
        } else {
            throw $e;
        }
    }
    // we have to wake it up first before we can cancel it
    // so temporary directories etc get cleaned up.
    if ($cancel) {
        if ($cancelsure) {
            $exporter->cancel_request($logreturn);
        } else {
            portfolio_export_pagesetup($PAGE, $exporter->get('caller'));
            $exporter->print_header(get_string('confirmcancel', 'portfolio'));
            echo $OUTPUT->box_start();
            $yesbutton = new single_button(new moodle_url('/portfolio/add.php', array('id' => $dataid, 'cancel' => 1, 'cancelsure' => 1, 'logreturn' => $logreturn)), get_string('yes'));
            if ($logreturn) {
                $nobutton  = new single_button(new moodle_url('/user/portfoliologs.php'), get_string('no'));
            } else {
                $nobutton  = new single_button(new moodle_url('/portfolio/add.php', array('id' => $dataid)), get_string('no'));
            }
            echo $OUTPUT->confirm(get_string('confirmcancel', 'portfolio'), $yesbutton, $nobutton);
            echo $OUTPUT->box_end();
            echo $OUTPUT->footer();
            exit;
        }
    }
    // verify we still belong to the correct user and permissions are still ok
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
    if (empty($callbackcomponent) || empty($callbackclass)) {
        debugging('no callback file or class');
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
                if (!$value = optional_param($key, false, PARAM_FLOAT)) {
                    $value = optional_param($key, false, PARAM_PATH);
                }
            }
            // strip off ca_ for niceness
            $callbackargs[substr($key, 3)] = $value;
        }
    }

    // Ensure that we found a file we can use, if not throw an exception.
    portfolio_include_callback_file($callbackcomponent, $callbackclass);

    $caller = new $callbackclass($callbackargs);
    $caller->set('user', $USER);
    if ($formats = explode(',', $callerformats)) {
        $caller->set_formats_from_button($formats);
    }
    $caller->load_data();
    // this must check capabilities and either throw an exception or return false.
    if (!$caller->check_permissions()) {
        throw new portfolio_caller_exception('nopermissions', 'portfolio', $caller->get_return_url());
    }

    portfolio_export_pagesetup($PAGE, $caller); // this calls require_login($course) if it can..

    // finally! set up the exporter object with the portfolio instance, and caller information elements
    $exporter = new portfolio_exporter($instance, $caller, $callbackcomponent);

    // set the export-specific variables, and save.
    $exporter->set('user', $USER);
    $exporter->save();
}

if (!$exporter->get('instance')) {
    // we've just arrived but have no instance
    // in this case the exporter object and the caller object have been set up above
    // so just make a little form to select the portfolio plugin instance,
    // which is the last thing to do before starting the export.
    //
    // first check to make sure there is actually a point
    $options = portfolio_instance_select(
        portfolio_instances(),
        $exporter->get('caller')->supported_formats(),
        get_class($exporter->get('caller')),
        $exporter->get('caller')->get_mimetype(),
        'instance',
        true,
        true
    );
    if (empty($options)) {
        throw new portfolio_export_exception($exporter, 'noavailableplugins', 'portfolio');
    } else if (count($options) == 1) {
        // no point displaying a form, just redirect.
        $optionskeys = array_keys($options);
        $instance = array_shift($optionskeys);
        redirect($CFG->wwwroot . '/portfolio/add.php?id= ' . $exporter->get('id') . '&instance=' . $instance . '&sesskey=' . sesskey());
    }
    // be very selective about not including this unless we really need to
    require_once($CFG->libdir . '/portfolio/forms.php');
    $mform = new portfolio_instance_select('', array('id' => $exporter->get('id'), 'caller' => $exporter->get('caller'), 'options' => $options));
    if ($mform->is_cancelled()) {
        $exporter->cancel_request();
    } else if ($fromform = $mform->get_data()){
        redirect($CFG->wwwroot . '/portfolio/add.php?instance=' . $fromform->instance . '&amp;id=' . $exporter->get('id'));
        exit;
    }
    else {
        $exporter->print_header(get_string('selectplugin', 'portfolio'));
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


