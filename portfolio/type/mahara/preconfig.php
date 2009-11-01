<?php
require_once(dirname(dirname(dirname(dirname(__FILE__)))). '/config.php');

if (empty($CFG->enableportfolios)) {
    print_error('disabled', 'portfolio');
}

require_once($CFG->libdir . '/portfoliolib.php');
require_once($CFG->dirroot . '/mnet/lib.php');

require_login();

if (!$landed = optional_param('landed', false, PARAM_BOOL)) {
    $id = required_param('id', PARAM_INT);
    $exporter = portfolio_exporter::rewaken_object($id);
    $exporter->verify_rewaken();

    $mnetauth = get_auth_plugin('mnet');
    if (!$url = $mnetauth->start_jump_session($exporter->get('instance')->get_config('mnethostid'), '/portfolio/type/mahara/preconfig.php?landed=1', true)) {
        throw new porfolio_exception('failedtojump', 'portfolio_mahara');
    }
    redirect($url);
} else {
    // now we have the sso session set up, start sending intent stuff and then redirect back to portfolio/add.php when we're done
    $exporter = portfolio_exporter::rewaken_object($SESSION->portfolioexport);
    $exporter->verify_rewaken();

    $exporter->get('instance')->send_intent();
    redirect($CFG->wwwroot . '/portfolio/add.php?postcontrol=1&id=' . $exporter->get('id'));
}

