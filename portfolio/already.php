<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');

if (empty($CFG->enableportfolios)) {
    print_error('disabled', 'portfolio');
}
require_once($CFG->libdir . '/portfoliolib.php');

$dataid = 0;
$currentinfo = null;
if (!$dataid = optional_param('id', '', PARAM_INT) ) {
    if (isset($SESSION->portfolioexport)) {
        $dataid = $SESSION->portfolioexport;
    }
}

$table = new StdClass;
$table->head = array(
    get_string('displayarea', 'portfolio'),
    get_string('plugin', 'portfolio'),
    get_string('displayinfo', 'portfolio'),
);
$table->data = array();
if ($dataid) {
    try {
        $exporter = portfolio_exporter::rewaken_object($dataid);
        $exporter->verify_rewaken();
        $table->data[] = array(
            $exporter->get('caller')->display_name(),
            $exporter->get('instance')->get('name'),
            $exporter->get('caller')->heading_summary(),
        );
    } catch (portfolio_exception $e) { }
}

$strheading = get_string('activeexport', 'portfolio');
print_header($strheading, $strheading);

notice_yesno(get_string('alreadyexporting', 'portfolio'), $CFG->wwwroot . '/portfolio/add.php', $CFG->wwwroot . '/portfolio/add.php?cancel=1');

if (count($table->data) > 0) {
    print_table($table);
}

print_footer();

?>
