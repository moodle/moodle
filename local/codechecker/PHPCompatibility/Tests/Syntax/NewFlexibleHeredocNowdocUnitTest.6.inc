<?php

/*
 * Test for errors.
 * Each test case using PHP 7.3 syntax has to be in its own file as otherwise they can't
 * be tested using PHP < 7.3.
 * Everything after the first test case in PHP < 7.3 will be tokenized as T_ENCAPSED_AND_WHITESPACE.
 */

// Closing marker is indented further than any lines of the body is a parse error, but not our concern.
    echo <<<END
      a
     b
    c
        END;
