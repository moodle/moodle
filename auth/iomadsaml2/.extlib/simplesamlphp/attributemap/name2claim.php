<?php // Maps AD LDAP to Claims from http://msdn.microsoft.com/en-us/library/hh159803.aspx
$attributemap = [
    'c'               => 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/country',
    'givenName'       => 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/givenname',
    'mail'            => 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress',
    'memberOf'        => 'http://schemas.microsoft.com/ws/2008/06/identity/claims/role',
    'postalcode'      => 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/postalcode',
    'uid'             => 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name',
    'sn'              => 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/surname',
    'st'              => 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/stateorprovince',
    'streetaddress'   => 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/streetaddress',
    'telephonenumber' => 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/otherphone',
];
