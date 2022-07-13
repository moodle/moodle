<?php
/**
 * Print this server's public key and exit
 *
 * @author  Donal McMullan  donal@catalyst.net.nz
 * @version 0.0.1
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package mnet
 */

require_once(__DIR__ . '/../config.php');
require_once $CFG->dirroot.'/mnet/lib.php';

if ($CFG->mnet_dispatcher_mode === 'off') {
    throw new \moodle_exception('mnetdisabled', 'mnet');
}

header("Content-type: text/plain; charset=utf-8");
$keypair = mnet_get_keypair();
echo $keypair['certificate'];
