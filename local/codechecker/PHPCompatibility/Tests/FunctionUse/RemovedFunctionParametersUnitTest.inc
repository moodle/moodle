<?php

// These are ok.
mktime(1, 2, 3, 4, 5, 6);
gmmktime(1, 2, 3, 4, 5, 6);

// These are not.
mktime(1, 2, 3, 4, 5, 6, true);
gmmktime(1, 2, 3, 4, 5, 6, true);

ldap_first_attribute( $link_identifier, $result_entry_identifier, $ber_identifier );
ldap_next_attribute( $link_identifier, $result_entry_identifier, $ber_identifier );

define('CONSTANT', 'foo'); // OK.
define( 'CONSTANT', 'foo', true, );

curl_version(); // OK.
curl_version( CURLVERSION_NOW ); // OK.
curl_version( 4 ); // OK when on Curl version 4.
curl_version( 10505678 );
curl_version( $age );
