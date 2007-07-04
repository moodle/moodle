<?php // $Id$

/// Add settings for this module to the $settings object (it's already defined)

    $settings->add(new admin_setting_configcheckbox('gradereport_grader_enableajax', 'Enable AJAX in gradebook', 'This setting will enable the AJAX interface in the gradebooks, depending on the site setting and the individual user profile choice.', 1));

?>
