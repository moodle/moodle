<?php

/*
 * Test for "identifier found in body" errors.
 */

// This was ok pre-PHP 7.3, but will be a parse error in PHP 7.3+ because the
// Heredoc body contains text which can be confused with the closing marker.
$values = [<<<'END'
a
b
END ING
c
END
, 'd e f'];

echo <<<END
END{$var}
END;

echo <<<"FOOBAR"
	something
	FOOBAR
	something
FOOBAR;

// Test detecting the correct end in PHP 7.3+.
echo <<<END
    something
    END
    something END
END
. 'more text';

// Test throwing an error for each detected "END" at the start of a line in PHP < 7.3.
echo <<<END
something
END something
something
END something
something
END something
something
END
. 'more text';
