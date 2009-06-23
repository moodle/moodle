Description of Spike PHPCoverage 0.8.2 library import into Moodle

Removed:
 * build.xml
 * samples
 * screenshots
 * xdebug_bin
 * src/PEAR.php | => Already in lib/pear
 * src/XML      |

Added:
 * index.html - prevent directory browsing on misconfigured servers
 * readme_moodle.txt - this file ;-)

Our changes: /// Look for "moodle" in code
 * src/parser/PHPParser.php - comment some debug lines causing some notices in moodle

20090621 - Eloy Lafuente (stronk7): Original import of 0.8.2 release
