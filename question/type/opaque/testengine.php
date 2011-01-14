<?php // $Id$
/**
 * Page for configuring the list Opaque question engines we can connect to.
 *
 * @copyright &copy; 2006 The Open University
 * @author T.J.Hunt@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package opaquequestiontype
 *//** */

require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->libdir . '/formslib.php');
include_once($CFG->libdir . '/validateurlsyntax.php');
require_once(dirname(__FILE__) . '/locallib.php');

// Check the user is logged in.
require_login();
if (!has_capability('moodle/question:config', get_context_instance(CONTEXT_SYSTEM, SITEID))) {
    print_error('restricteduser');
}

// Load the engine definition.
$engineid = required_param('engineid', PARAM_INT);
$engine = load_engine_def($engineid);
if (is_string($engine)) {
    print_error('unknownengine', 'qtype_opaque', 'engines.php', $engine);
}

// Do the test.
$ok = true;
$strtitle = get_string('testingengine', 'qtype_opaque');
$navlinks[] = array('name' => get_string('configuredquestionengines', 'qtype_opaque'), 'link' => "$CFG->wwwroot/question/type/opaque/engines.php", 'type' => 'misc');
$navlinks[] = array('name' => $strtitle, 'link' => '', 'type' => 'title');
print_header_simple($strtitle, '', build_navigation($navlinks));
print_heading($strtitle);

foreach ($engine->questionengines as $engineurl) {
    print_box_start();
    print_heading(get_string('testconnectionto', 'qtype_opaque', $engineurl), '', 3);

    $info = get_engine_info($engineurl);
    if (is_array($info) && isset($info['engineinfo']['#'])) {
        xml_to_dl($info['engineinfo']['#']);
    } else {
        notify($info);
        $ok = false;
    }
    print_box_end();
}

if ($ok) {
    notify(get_string('testconnectionpassed', 'qtype_opaque'), 'notifysuccess');
} else {
    notify(get_string('testconnectionfailed', 'qtype_opaque'));
}

print_continue('engines.php');
print_footer();

/**
 * @param output some XML as a <dl>.
 */
function xml_to_dl($xml) {
    echo '<dl>';
    foreach ($xml as $element => $content) {
        echo "<dt>$element</dt><dd>" . $content['0']['#'] . "</dd>\n";
    }
    echo '</dl>';
}
?>
