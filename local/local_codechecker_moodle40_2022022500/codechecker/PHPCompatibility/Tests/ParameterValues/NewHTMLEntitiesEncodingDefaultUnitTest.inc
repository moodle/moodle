<?php

// OK.
echo htmlentities( $string, ENT_QUOTES, 'UTF-8' );
echo htmlspecialchars( $string, ENT_COMPAT, $encoding, false );
echo html_entity_decode( $string, ENT_COMPAT, $encoding );

// Not OK - error.
echo htmlentities( $string, $flags );
echo htmlspecialchars($string);
echo HTML_entity_decode( $string, ENT_COMPAT );
