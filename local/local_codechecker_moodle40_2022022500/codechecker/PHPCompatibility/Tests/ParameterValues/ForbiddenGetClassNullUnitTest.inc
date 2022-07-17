<?php
/*
 * Test get_class() PHP 7.2 change in accepted values.
 */

//OK.
get_class($object);
get_class();

// Not OK.
get_class(null);
