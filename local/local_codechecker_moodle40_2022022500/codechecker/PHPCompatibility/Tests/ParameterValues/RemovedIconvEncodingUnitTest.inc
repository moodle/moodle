<?php
/*
 * OK.
 */
iconv_set_encoding();

MyNS\iconv_set_encoding('internal_encoding', 'UTF-8');

$a = iconv_get_encoding('internal_encoding');

/*
 * Test iconv_set_encoding() PHP 5.6 change in accepted values.
 */
iconv_set_encoding('internal_encoding', 'UTF-8');
\iconv_set_encoding( 'input_encoding', 'ISO-8859-1' );
iconv_set_encoding("output_encoding", "ISO-8859-1");
iconv_set_encoding( $type, "ISO-8859-1");
iconv_set_encoding('all', 'UTF-8'); // Not a valid $type value.
