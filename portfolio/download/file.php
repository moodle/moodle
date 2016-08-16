<?php

// this script is a slightly more user friendly way to 'send' the file to them
// (using portfolio/file.php) but still give them the 'return to where you were' link
// to go back to their assignment, or whatever

require(__DIR__.'/../../config.php');

if (empty($CFG->enableportfolios)) {
    print_error('disabled', 'portfolio');
}

require_once($CFG->libdir.'/portfoliolib.php');
require_once($CFG->libdir.'/portfolio/exporter.php');

$id = required_param('id', PARAM_INT);

$PAGE->set_url('/portfolio/download/file.php', array('id' => $id));

$exporter = portfolio_exporter::rewaken_object($id);
portfolio_export_pagesetup($PAGE, $exporter->get('caller'));
$exporter->verify_rewaken();

$exporter->print_header(get_string('downloading', 'portfolio_download'), false);
$returnurl = $exporter->get('caller')->get_return_url();
echo $OUTPUT->notification('<a href="' . $returnurl . '">' . get_string('returntowhereyouwere', 'portfolio') . '</a><br />');

$PAGE->requires->js('/portfolio/download/helper.js');
$PAGE->requires->js_function_call('submit_download_form', null, true);

// if they don't have javascript, they can submit the form here to get the file.
// if they do, it does it nicely for them.
echo '<div id="redirect">
    <form action="' . $exporter->get('instance')->get_base_file_url() . '" method="post" id="redirectform">
      <input type="submit" value="' . get_string('downloadfile', 'portfolio_download') . '" />
    </form>
';
echo $OUTPUT->footer();


