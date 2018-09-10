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

/**
 * Displays and processes the messaging form
 *
 * @package    block_messageteacher
 * @author      Mark Johnson <mark@barrenfrozenwasteland.com>
 * @copyright   2013 Mark Johnson
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

@$ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH']);

if ($ajax) {
    define('AJAX_SCRIPT', true);
}

require_once(__DIR__.'/../../config.php');

$courseid = required_param('courseid', PARAM_INT);
$recipientid = required_param('recipientid', PARAM_INT);
$referurl = required_param('referurl', PARAM_URL);

$coursecontext = context_course::instance($courseid);
$PAGE->set_context($coursecontext);

require_login();
require_capability('moodle/site:sendmessage', $coursecontext);

$url = '/blocks/messageteacher/message.php';
$PAGE->set_url($url);

$recipient = $DB->get_record('user', array('id' => $recipientid));

$customdata = array(
    'recipient' => $recipient,
    'referurl' => $referurl,
    'courseid' => $courseid
);
$mform = new block_messageteacher\message_form(null, $customdata);

if ($mform->is_cancelled()) {
    // Form cancelled, redirect.
    redirect($referurl);
    exit();
} else if (($data = $mform->get_data())) {
    try {
        $mform->process($data);
    } catch (messageteacher_no_recipient_exception $e) {
        if ($ajax) {
            header('HTTP/1.1 400 Bad Request');
            die($e->getMessage());
        } else {
            throw $e;
        }
    } catch (messageteacher_message_failed_exception $e) {
        if ($ajax) {
            header('HTTP/1.1 500 Internal Server Error');
            die($e->getMessage());
        } else {
            throw $e;
        }
    }
    if ($ajax) {
        $output = html_writer::tag('p',
                                    get_string('messagesent', 'block_messageteacher'),
                                    array('class' => 'messageteacher_confirm'));
        echo json_encode(array('state' => 1, 'output' => $output));
    } else {
        redirect($data->referurl);
    }
    exit();
} else {

    // Form has not been submitted, just display it.
    if ($ajax) {
        ob_start();
        $mform->display();
        $form = ob_get_clean();
        if (strpos($form, '</script>') !== false) {
            $outputparts = explode('</script>', $form);
            $output = $outputparts[1];
            $script = str_replace('<script type="text/javascript">', '', $outputparts[0]);
        } else {
            $output = $form;
        }

        // Now it gets a bit tricky, we need to get the libraries and init calls for any Javascript used
        // by the form element plugins.
        $headcode = $PAGE->requires->get_head_code($PAGE, $OUTPUT);
        $loadpos = strpos($headcode, 'M.yui.loader');
        $cfgpos = strpos($headcode, 'M.cfg');
        $script .= substr($headcode, $loadpos, $cfgpos - $loadpos);
        $endcode = $PAGE->requires->get_end_code();
        $script .= preg_replace('/<\/?(script|link)[^>]*>/', '', $endcode);

        $output = html_writer::tag('div', $form, array('id' => 'messageteacher_form'));

        echo json_encode(array('state' => 0, 'output' => $output, 'script' => $script));

    } else {
        echo $OUTPUT->header();
        $mform->display();
        echo $OUTPUT->footer();
    }
}
