<?php  //$Id$

$settings->add(new admin_setting_configcheckbox('hotpot_showtimes', get_string('showtimes', 'hotpot'),
                    get_string('configshowtimes', 'hotpot'), 0) );

$settings->add(new admin_setting_configtext('hotpot_excelencodings', get_string('excelencodings', 'hotpot'),
                   get_string('configexcelencodings', 'hotpot'), '') );

?>
