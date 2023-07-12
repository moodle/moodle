<?php
$attributemap = [

    // Generated Windows Live ID Attributes
    'windowslive_user'       => 'eduPersonPrincipalName', // uid @ windowslive.com
    'windowslive_targetedID' => 'eduPersonTargetedID', // http://windowslive.com!uid
    'windowslive_uid'        => 'uid', // windows live id
    'windowslive_mail'       => 'mail',
    // Attributes Returned by Windows Live ID
    'windowslive.FirstName'  => 'givenName',
    'windowslive.LastName'   => 'sn',
    'windowslive.Location'   => 'l',
    // Attributes returned by Microsoft Graph - http://graph.microsoft.io/en-us/docs/api-reference/v1.0/resources/user
    'windowslive.givenName' => 'givenName',
    'windowslive.surname' => 'sn',
    'windowslive.displayName' => 'displayName',
    'windowslive.id' => 'uid',
    'windowslive.userPrincipalName' => 'eduPersonPrincipalName',
    'windowslive.mail' => 'mail',
    'windowslive.preferredLanguage' => 'preferredLanguage',

];
