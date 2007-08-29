<?php // $Id$
/**
 * Print this server's public key and exit
 *
 * @author  Donal McMullan  donal@catalyst.net.nz
 * @version 0.0.1
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package mnet
 */

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once $CFG->dirroot.'/mnet/lib.php';
header("Content-type: text/plain");
$keypair = mnet_get_keypair();
echo $keypair['certificate'];
?>
