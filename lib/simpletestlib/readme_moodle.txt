Description of Simpletest 1.1.0 library import into Moodle

Obtained from http://www.simpletest.org/en/download.html

Changes:
 * removed extensions and test directories (documentation is kept because it is not available online)
 * neutralised autorun.php - only execution via our UIs is allowed
 * test_case.php - added our global $CFG before include() MDL-10064
 * modified run() in test_case.php - skipping tests that need fake db if prefix not set
 * test_case.php - added TIME_ALLOWED_PER_UNIT_TEST constant which
   resets php time limit for each test function - MDL-24909. Marked with
   comments (replace existing per-class hack in test_case.php).

skodak, Tim, sammarshall
