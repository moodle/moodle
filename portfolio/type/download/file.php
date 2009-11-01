<?php

// this script is a slightly more user friendly way to 'send' the file to them
// (using portfolio/file.php) but still give them the 'return to where you were' link
// to go back to their assignment, or whatever

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.php');

if (empty($CFG->enableportfolios)) {
    print_error('disabled', 'portfolio');
}

require_once($CFG->libdir.'/portfoliolib.php');
$PAGE->requires->yui_lib('dom');
$id = required_param('id', PARAM_INT);

require_login();

$exporter = portfolio_exporter::rewaken_object($id);
$exporter->verify_rewaken();

$exporter->print_header(get_string('downloading', 'portfolio_download'), false);
$returnurl = $exporter->get('caller')->get_return_url();
echo $OUTPUT->notification('<a href="' . $returnurl . '">' . get_string('returntowhereyouwere', 'portfolio') . '</a><br />');

// if they don't have javascript, they can submit the form here to get the file.
// if they do, it does it nicely for them.
echo '<div id="redirect">
    <form action="' . $exporter->get('instance')->get_base_file_url() . '" method="post" id="redirectform">
      <input type="submit" value="' . get_string('downloadfile', 'portfolio_download') . '" />
    </form>
    <script language="javascript">
        f = YAHOO.util.Dom.get("redirectform");
        YAHOO.util.Dom.addClass(f.parentNode, "hide");
        f.submit();
    </script>';

echo $OUTPUT->footer();


