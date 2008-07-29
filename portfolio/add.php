<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');

if (empty($CFG->portfolioenabled)) {
    print_error('disabled', 'portfolio');
}

require_once($CFG->libdir . '/portfoliolib.php');

$exporter = null;
if (isset($SESSION->portfolio) && isset($SESSION->portfolio->exporter)) {
    $exporter = unserialize(serialize($SESSION->portfolio->exporter));
    if ($exporter->instancefile) {
        require_once($CFG->dirroot . '/' . $exporter->instancefile);
    }
    require_once($CFG->dirroot . '/' . $exporter->callerfile);
    $exporter = unserialize(serialize($SESSION->portfolio->exporter));
    $SESSION->portfolio->exporter =& $exporter;
    if ($instance = optional_param('instance', 0, PARAM_INT)) {
        $instance = portfolio_instance($instance);
        if ($broken = portfolio_instance_sanity_check($instance)) {
            print_error(get_string($broken[$instance->get('id')], 'portfolio_' . $instance->get('plugin')));
        }
        $instance->set('user', $USER);
        $exporter->set('instance', $instance);

    }
} else {
    // we'e just posted here for the first time and have might the instance already
    if ($instance = optional_param('instance', 0, PARAM_INT)) {
        $instance = portfolio_instance($instance);
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
    foreach (array_keys($_POST) as $key) {
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

    if (!empty($course)) {
        $COURSE = $DB->get_record('course', array('id' => $course), 'id,shortname,fullname');
        // this is yuk but used in build_navigation
    }

    list($extranav, $cm) = $caller->get_navigation();
    $extranav[] = array('type' => 'title', 'name' => get_string('exporting', 'portfolio'));
    $navigation = build_navigation($extranav, $cm);

    $exporter = new portfolio_exporter($instance, $caller, $callbackfile, $navigation);
    $exporter->set('user', $USER);
    $SESSION->portfolio = new StdClass;
    $SESSION->portfolio->exporter =& $exporter;
}


$stage = optional_param('stage', PORTFOLIO_STAGE_CONFIG);
$alreadystolen = false;
// for places returning control to pass (rather than PORTFOLIO_STAGE_PACKAGE
// which is unstable if they can't get to the constant (eg external system)
if ($postcontrol = optional_param('postcontrol', 0, PARAM_INT)) {
    $stage = $SESSION->portfolio->stagepresteal;
    $exporter->instance()->post_control($stage, array_merge($_GET, $_POST));
    $alreadystolen = true;
}

if (!$exporter->get('instance')) {
    // we've just arrived but have no instance
    // so retrieve everything from the request,
    // add them as hidden fields in a new form
    // to select the instance and post back here again
    // for the next block to catch
    $form = '<form action="' . $CFG->wwwroot . '/portfolio/add.php" method="post">' . "\n";

    if (!$select = portfolio_instance_select(portfolio_instances(), $exporter->get('caller')->supported_formats(), get_class($exporter->get('caller')))) {
        print_error('noavailableplugins', 'portfolio');
    }
    $form .= $select;
    $form .= '<input type="submit" value="' . get_string('select') . '" />';
    $form .= '</form>' . "\n";
    $exporter->print_header();
    print_heading(get_string('selectplugin', 'portfolio'));
    print_simple_box_start();
    echo $form;
    print_simple_box_end();
    print_footer();
    exit;
}

$exporter->process_stage($stage, $alreadystolen);

?>
