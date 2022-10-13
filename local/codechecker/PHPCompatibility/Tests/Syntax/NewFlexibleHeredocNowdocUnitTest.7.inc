<?php

/*
 * Test for errors.
 * Each test case using PHP 7.3 syntax has to be in its own file as otherwise they can't
 * be tested using PHP < 7.3.
 * Everything after the first test case in PHP < 7.3 will be tokenized as T_ENCAPSED_AND_WHITESPACE.
 */

// Mixing tabs and spaces for the indentation of the body and the closing marker is a parse error, but not our concern.
	echo <<<END
	    a
    	END;
