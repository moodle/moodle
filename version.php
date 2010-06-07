<?php

// MOODLE VERSION INFORMATION

// This file defines the current version of the core Moodle code being used.
// This is compared against the values stored in the database to determine
// whether upgrades should be performed (see lib/db/*.php)

   $version = 2007021599.17;  // YYYYMMDD      = date of the 1.8 branch (don't change)
                              //         99    = we reached a .10 release! (don't change)
                              //          .XX  = incrementing number.

                              // Do not use more than two decimal points as we have
                              // hit the float limit

   $release = '1.8.12+ (Build: 20100607)';     // Human-friendly version name

?>
