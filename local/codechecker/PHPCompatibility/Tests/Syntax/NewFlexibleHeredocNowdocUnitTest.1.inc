<?php

/*
 * Prevent false positives.
 */
// OK pre-PHP 7.3 as well as in PHP 7.3+: closing marker on line by itself followed by semi-colon.
        $foo = <<<EOT
foo
EOT;

        $bar = <<<'EOT'
bar
EOT;

echo <<<END
      a
     b
    c
END;

// OK pre-PHP 7.3 as well as in PHP 7.3+: closing marker on line by itself, follow on code on next line.
stringManipulator(<<<"END"
   a
  b
 c
END
);

$values = [<<<'END'
a
b
c
END
, 'd e f'];

// OK pre-PHP 7.3 as well as in PHP 7.3+: text body contains a line starting with text resembling the closing marker.
$values = [<<<END
a
b
ENDING
END
, 'd e f'];

// OK pre-PHP 7.3 as well as in PHP 7.3+: text body contains the closing marker not at the start of the line.
$values = [<<<END
a
b
some more text END
END
, 'd e f'];
