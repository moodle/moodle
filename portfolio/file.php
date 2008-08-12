<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once($CFG->libdir . '/portfoliolib.php');
require_once($CFG->libdir . '/file/stored_file.php');
require_once($CFG->libdir . '/filelib.php');

$id = required_param('id', PARAM_INT);

$exporter = portfolio_exporter::rewaken_object($id);

if ($exporter->get('instance')->is_push()) {
    $exporter->raise_error('filedenied', 'portfolio');
}

if (!$exporter->get('instance')->verify_file_request_params(array_merge($_GET, $_POST))) {
    $exporter->raise_error('filedenied', 'portfolio');
}

$file = $exporter->get('instance')->get('file');
if (!($file instanceof stored_file)) {
    $exporter->raise_error('filenotfound', 'portfolio');
}

send_stored_file($file, 0, 0, true, null, true);
$exporter->process_stage_cleanup(true);

?>
