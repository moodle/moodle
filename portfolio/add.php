<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');

if (empty($CFG->portfolioenabled)) {
    print_error('disabled', 'portfolio');
}

require_once($CFG->libdir . '/portfoliolib.php');
require_once($CFG->libdir . '/formslib.php');
$exporter = null;
$dataid = 0;

if (!$dataid = optional_param('id') ) {
    if (isset($SESSION->portfolioexport)) {
        $dataid = $SESSION->portfolioexport;
    }
}
if ($dataid) {
    $exporter = portfolio_exporter::rewaken_object($dataid);
    if ($cancel = optional_param('cancel', 0, PARAM_RAW)) {
        $exporter->cancel_request();
        /*
        $returnurl = $exporter->get('caller')->get_return_url();
        unset($SESSION->portfolio);
        redirect($returnurl);
        */
    }
    if (!$exporter->get('instance')) {
        if ($instance = optional_param('instance', '', PARAM_INT)) {
            if (!$instance = portfolio_instance($instance)) {
                $exporter->raise_error('invalidinstance', 'portfolio');
            }
            if ($broken = portfolio_instance_sanity_check($instance)) {
                print_error(get_string($broken[$instance->get('id')], 'portfolio_' . $instance->get('plugin')));
            }
            $instance->set('user', $USER);
            $exporter->set('instance', $instance);
            $exporter->save();
        }
    }
} else {
    // we'e just posted here for the first time and have might the instance already
    if ($instance = optional_param('instance', 0, PARAM_INT)) {
        if (!$instance = portfolio_instance($instance)) {
            portfolio_exporter::raise_error('invalidinstance', 'portfolio');
        }
        if ($broken = portfolio_instance_sanity_check($instance)) {
            print_error(get_string($broken[$instance->get('id')], 'portfolio_' . $instance->get('plugin')));
        }
        $instance->set('user', $USER);
    } else {
        $instance = null;
    }

    $callbackfile = required_param('callbackfile', PARAM_PATH);
    $callbackclass = required_param('callbackclass', PARAM_ALPHAEXT);

    $callbackargs = array();
    foreach (array_keys(array_merge($_GET, $_POST)) as $key) {
        if (strpos($key, 'ca_') === 0) {
            if (!$value =  optional_param($key, false, PARAM_ALPHAEXT)) {
                if (!$value = optional_param($key, false, PARAM_NUMBER)) {
                    $value = optional_param($key, false, PARAM_PATH);
                }
            }
            $callbackargs[substr($key, 3)] = $value;
        }
    }
    require_once($CFG->dirroot . $callbackfile);
    $caller = new $callbackclass($callbackargs);
    if (!$caller->check_permissions()) {
        print_error('nopermissions', 'portfolio', $caller->get_return_url());
    }
    $caller->set('user', $USER);

    // for build navigation
    if (!$course = $caller->get('course')) {
        $course = optional_param('course', 0, PARAM_INT);
    }

    if (!empty($course) && is_numeric($course)) {
        $course = $DB->get_record('course', array('id' => $course), 'id,shortname,fullname');
        // this is yuk but used in build_navigation
    }

    $COURSE = $course;

    list($extranav, $cm) = $caller->get_navigation();
    $extranav[] = array('type' => 'title', 'name' => get_string('exporting', 'portfolio'));
    $navigation = build_navigation($extranav, $cm);

    $exporter = new portfolio_exporter($instance, $caller, $callbackfile, $navigation);
    $exporter->set('user', $USER);
    $exporter->save();
    $SESSION->portfolioexport = $exporter->get('id');
}

if (!$exporter->get('instance')) {
    print_object($exporter);
    // we've just arrived but have no instance
    // so retrieve everything from the request,
    // add them as hidden fields in a new form
    // to select the instance and post back here again
    // for the next block to catch
    $mform = new portfolio_instance_select('', array('caller' => $exporter->get('caller')));
    if ($mform->is_cancelled()) {
        $exporter->cancel_request();
        /*
        $returnurl = $caller->get_return_url();
        unset($SESSION->portfolio);
        redirect($returnurl);
        exit;
    */
    } else if ($fromform = $mform->get_data()){
        redirect($CFG->wwwroot . '/portfolio/add.php?instance=' . $fromform->instance . '&amp;id=' . $exporter->get('id'));
        exit;
    }
    else {
        $exporter->print_header();
        print_heading(get_string('selectplugin', 'portfolio'));
        print_simple_box_start();
        $mform->display();
        print_simple_box_end();
        print_footer();
        exit;
    }
}

$stage = optional_param('stage', PORTFOLIO_STAGE_CONFIG);
$alreadystolen = false;
// for places returning control to pass (rather than PORTFOLIO_STAGE_PACKAGE
// which is unstable if they can't get to the constant (eg external system)
if ($postcontrol = optional_param('postcontrol', 0, PARAM_INT)) {
    $stage = $exporter->get('stage');
    $exporter->instance()->post_control($stage, array_merge($_GET, $_POST));
    $alreadystolen = true;
}
$exporter->process_stage($stage, $alreadystolen);

?>
