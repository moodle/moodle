<?php
$attributemap = [

    // Generated Facebook Attributes
    'facebook_user'        => 'eduPersonPrincipalName', // username OR uid @ facebook.com
    'facebook_targetedID'  => 'eduPersonTargetedID', // http://facebook.com!uid
    'facebook_cn'          => 'cn', // duplicate of displayName

    // Attributes Returned by Facebook
    'facebook.first_name'  => 'givenName',
    'facebook.last_name'   => 'sn',
    'facebook.name'        => 'displayName', // or 'cn'
    'facebook.email'       => 'mail',
    'facebook.username'    => 'uid', // facebook username (maybe blank)
    'facebook.profile_url' => 'labeledURI',
    'facebook.locale'      => 'preferredLanguage',
    'facebook.about_me'    => 'description',
];
