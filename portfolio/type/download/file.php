<?php

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.php');
require_once($CFG->libdir . '/portfoliolib.php');
require_js(array(
    'yui_yahoo',
    'yui_dom',
));
$id = required_param('id', PARAM_INT);

$exporter = portfolio_exporter::rewaken_object($id);
$exporter->verify_rewaken();

$exporter->print_header(get_string('downloading', 'portfolio_download'), false);
$returnurl = $exporter->get('caller')->get_return_url();
notify('<a href="' . $returnurl . '">' . get_string('returntowhereyouwere', 'portfolio') . '</a><br />');

echo '<div id="redirect">
    <form action="' . $exporter->get('instance')->get_base_file_url() . '" method="post" id="redirectform">
      <input type="submit" value="' . get_string('downloadfile', 'portfolio_download') . '" />
    </form>
    <script language="javascript">
        f = YAHOO.util.Dom.get("redirectform");
        YAHOO.util.Dom.addClass(f.parentNode, "hide");
        f.submit();
    </script>';

print_footer();

?>
