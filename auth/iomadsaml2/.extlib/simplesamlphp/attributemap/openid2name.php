<?php
$attributemap = [
    // Simple Registration + AX Schema
    'http://axschema.org/namePerson/friendly'     => 'displayName', // Alias/Username -> displayName
    'openid.sreg.nickname'                        => 'displayName',
    'http://axschema.org/contact/email'           => 'mail', // Email
    'openid.sreg.email'                           => 'mail',
    'http://axschema.org/namePerson'              => 'displayName', // Full name -> displayName
    'openid.sreg.fullname'                        => 'displayName',
    'http://axschema.org/contact/postalCode/home' => 'postalCode', // Postal code
    'openid.sreg.postcode'                        => 'postalCode',
    'http://axschema.org/contact/country/home'    => 'countryName', // Country
    'openid.sreg.country'                         => 'countryName',
    'http://axschema.org/pref/language'           => 'preferredLanguage', // Language
    'openid.sreg.language'                        => 'preferredLanguage',
    // Name
    'http://axschema.org/namePerson/prefix'       => 'personalTitle', // Name prefix
    'http://axschema.org/namePerson/first'        => 'givenName', // First name
    'http://axschema.org/namePerson/last'         => 'sn', // Last name

    // Work
    'http://axschema.org/company/name'            => 'o', // Company name
    'http://axschema.org/company/title'           => 'title', // Job title

    // Telephone
    'http://axschema.org/contact/phone/default'   => 'telephoneNumber', // Phone (preferred)
    'http://axschema.org/contact/phone/home'      => 'homePhone', // Phone (home)
    'http://axschema.org/contact/phone/business'  => 'telephoneNumber', // Phone (work)
    'http://axschema.org/contact/phone/cell'      => 'mobile', // Phone (mobile)
    'http://axschema.org/contact/phone/fax'       => 'facsimileTelephoneNumber', // Phone (fax)

    // Further attributes can be found at http://www.axschema.org/types/
];
