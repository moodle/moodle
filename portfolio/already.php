<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');

if (empty($CFG->enableportfolios)) {
    print_error('disabled', 'portfolio');
}

$strheading = get_string('activeexport', 'portfolio');
print_header($strheading, $strheading);

notice_yesno(get_string('alreadyexporting', 'portfolio'), $CFG->wwwroot . '/portfolio/add.php', $CFG->wwwroot . '/portfolio/add.php?cancel=1');

print_footer();

?>
