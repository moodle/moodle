<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/*
 * @package    block_use_stats
 * @category   blocks
 * @author     Valery Fremaux <valery.fremaux@gmail.com>
 * @copyright  Valery Fremaux <valery.fremaux@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 */
require_once('../../../config.php');
header("Content-type: text/javascript; charset=utf-8");

$id = optional_param('id', SITEID, PARAM_INT); // Course id.
$cmid = optional_param('cmid', 0, PARAM_INT); // Course module id.

if (!$course = $DB->get_record('course', array('id' => "$id"))) {
    print_error('invalidcourseid');
}

require_login($course);

$config = get_config('block_use_stats');

if (empty($config->keepalive_delay)) {
    set_config('keepalive_delay', 600, 'block_use_stats'); // In seconds.
    $config->keepalive_delay = 600;
}

if (is_siteadmin()) {
    // Do not install tracking for administrators.
    return;
}

?>

$(document).ready(function() {
    $([window, document]).focusin(function() {
        }).focusout(function(){
        // Your logic when the page gets inactive.
    });

    // Periodic update.

    function send_keepalive() {
        url = '<?php echo $CFG->wwwroot; ?>/blocks/use_stats/pro/ajax/services.php?course=<?php echo $id; ?>&cmid=<?php echo $cmid; ?>';
        $.post(url, function(data) {
            // Just send, do anything else.
            return;
        });
    }

    setInterval(send_keepalive, <?php echo $config->keepalive_delay * 1000; ?>);

});