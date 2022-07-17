<?php

// These should all be ignored as we're passing in a variable.
mb_ereg_replace( $pattern, $replace, $subject, $options );
mb_eregi_replace( $pattern, $replace, $subject, $options );
mb_regex_set_options( $options );

// These should be ignored as they contain valid options.
mb_ereg_replace( $pattern, $replace, $subject, 'msi' );
mb_eregi_replace( $pattern, $replace, $subject, 'sim' );
mb_regex_set_options( 'ims' );

// These should all be flagged.
mb_ereg_replace( $pattern, $replace, $subject, 'e' );
MB_eregi_replace( $pattern, $replace, $subject, "seim" );
mb_regex_set_options( 'im' . 'se' );

// Interpolated strings: These should NOT be flagged.
mb_ereg_replace( $pattern, $replace, $subject, "$e" );
mb_eregi_replace( $pattern, $replace, $subject, "s$eim" );
mb_regex_set_options( 'im' . "$se" );

// Interpolated strings: These should all be flagged.
mb_ereg_replace( $pattern, $replace, $subject, "e$m" );
mb_eregi_replace( $pattern, $replace, $subject, "me$i" );
Mb_Regex_Set_Options( 'im' . "{$se}e" );

// Verify the sniff also pick up on the function aliases.
mbereg_replace( $pattern, $replace, $subject, 'e' );
mberegi_replace( $pattern, $replace, $subject, "me$i" );
