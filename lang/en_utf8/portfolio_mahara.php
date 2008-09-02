<?php

$string['err_nomnethosts'] = 'This plugin relies on Moodle Networking peers with SSO IDP published, and portfolio and SSO SP subscribed, as well as the mnet authentication plugin.  Any instances of this plugin has been set to not visible until this is fixed - you will need to set them to visible again manually. They cannot be used before this happens.';
$string['err_networkingoff'] = 'Moodle Networking is off entirely. Please enable it before trying to configure this pugin.  Any instances of this plugin have been set to not visible until this is fixed - you will need to set them to visible again manully.  They cannot be used until this happens';
$string['err_invalidhost'] = 'This plugin is misconfigured to point to an invalid (or deleted) mnet host.  This plugin relies on Moodle Networking peers with SSO IDP published, and portfolio and SSO_SP subscribed.';
$string['err_nomnetauth'] = 'The mnet authentication plugin is disabled, but is required for this service';
$string['failedtojump'] = 'Failed to start communication with remote server';
$string['failedtoping'] = 'Failed to start communication with remote server: $a';
$string['mnethost'] = 'Moodle Networking Host';
$string['senddisallowed'] = 'You cannot transfer files to Mahara at this time';
$string['url'] = 'URL';
$string['pf_name'] = 'Portfolio services';
$string['pf_description'] = 'Allow users to push Moodle content to this host<br />'.
                                 'Subscribe to this service to allow authenticated users in your site to push content to $a<br />' .
                                 '<ul><li><em>Dependency</em>: You must also <strong>publish</strong> the SSO (Identify Provider) service to $a.</li>' .
                                 '<li><em>Dependency</em>: You must also <strong>subscribe</strong> to the SSO (Service Provider) service on $a</li>' .
                                 '<li><em>Dependency</em>: You must also enable the mnet authentication plugin.</li></ul><br />';
?>
