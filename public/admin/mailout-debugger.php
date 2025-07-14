#!/usr/bin/php
<?php
  /*
   * Utility to debug mailouts - will save the content of emails to a
   * logfile instead of sending them out. Use it as a sendmail
   * "stand-in" when testing mailouts.
   *
   * It is not Moodle specific - use it anywhere by setting the php
   * "sendmail_path" setting to this file with a logfile parameter.
   *
   * - Set in php.ini (not settable in config.php):
   *     sendmail_path=/path-to-moodle/admin/mailout-debugger.php');
   *   Or from the commandline
   *     php -d sendmail_path='/path-to-moodle/admin/mailout-debugger.php' /path/to/cron.php
   *
   * - Create a file in admin called mailout-debugger.enable
   *   (this is a security check to prevent execution in prod environments)
   *   touch /path/to/moodle/admin/mailout-debugger.enable
   *
   * - Mark as executable: chmod ugo+rx mailout-debugger.php
   *
   * - Run your admin/cron.php
   *
   * - Read /tmp/moodle-mailout.log
   *
   *
   * This script will create logfiles in /tmp/ or in $TMPDIR if set.
   * On windows, use php -r 'print sys_get_temp_dir()' to see where the file is saved.
   */

// Security check.
if (!file_exists(__DIR__.'/mailout-debugger.enable')) {
    mdie("Disabled.");
}
$tmpdir=sys_get_temp_dir(); // default

if (isset($_SERVER['REMOTE_ADDR'])) {
    mdie("should not be called from web server!");
}

if (isset($_ENV['TMPDIR']) && is_dir($_ENV['TMPDIR'])) {
    $tmpdir = $_ENV['TMPDIR'];
}

$tmpfile = $tmpdir . '/moodle-mailout.log';
$fh = fopen($tmpfile, 'a+', false)
    or mdie("Error openning $tmpfile on append\n");
fwrite($fh, "==== ".date("D M d H:i:s Y", time())." ====\n");
fwrite($fh, "==== Commandline: " . implode(' ',$argv) . "\n");

$stdin = fopen('php://stdin', 'r');

while ($line = fgets($stdin)) {
    fwrite($fh, $line);
}
fwrite($fh, "\n");
fclose($fh);
fclose($stdin);

/**
 * Print an error to STDOUT and exit with a non-zero code. For commandline scripts.
 * Default errorcode is 1.
 *
 * Very useful for perl-like error-handling:
 *
 * do_something() or mdie("Something went wrong");
 *
 * @param string  $msg       Error message
 * @param integer $errorcode Error code to emit
 *
 */
function mdie($msg='', $errorcode=1) {
    trigger_error($msg);
    exit($errorcode);
}


