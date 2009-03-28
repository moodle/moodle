<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');

if (empty($CFG->enableportfolios)) {
    print_error('disabled', 'portfolio');
}

require_once($CFG->libdir . '/portfoliolib.php');
require_once($CFG->libdir . '/file/stored_file.php');
require_once($CFG->libdir . '/filelib.php');

$id = required_param('id', PARAM_INT);

require_login();

$exporter = portfolio_exporter::rewaken_object($id);
$exporter->verify_rewaken();

if ($exporter->get('instance')->is_push()) {
    throw new portfolio_export_exception($exporter, 'filedenied', 'portfolio');
}

if (!$exporter->get('instance')->verify_file_request_params(array_merge($_GET, $_POST))) {
    throw new portfolio_export_exception($exporter, 'filedenied', 'portfolio');
}

$exporter->get('instance')->send_file();
$exporter->process_stage_cleanup(true);
exit;
?>
