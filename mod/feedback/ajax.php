<?php

if (!defined('AJAX_SCRIPT')) {
    define('AJAX_SCRIPT', true);
}

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');

$id = required_param('id', PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);
$sesskey = optional_param('sesskey', false, PARAM_TEXT);
$itemorder = optional_param('itemorder', false, PARAM_SEQUENCE);

$cm = get_coursemodule_from_id('feedback', $id, 0, false, MUST_EXIST);
$course = $DB->get_record("course", array("id"=>$cm->course), '*', MUST_EXIST);
$feedback = $DB->get_record("feedback", array("id"=>$cm->instance), '*', MUST_EXIST);

confirm_sesskey();

$context = context_module::instance($cm->id);
require_login($course, true, $cm);
require_capability('mod/feedback:edititems', $context);

$return = false;

switch ($action) {
    case 'saveitemorder':
        $itemlist = explode(',', trim($itemorder, ','));
        if (count($itemlist) > 0) {
            $return = feedback_ajax_saveitemorder($itemlist, $feedback);
        }
        break;
}

echo json_encode($return);
die;

////////////////////////////////////////

function feedback_ajax_saveitemorder($itemlist, $feedback) {
    global $DB;

    $result = true;
    $position = 0;
    foreach ($itemlist as $itemid) {
        $position++;
        $result = $result && $DB->set_field('feedback_item',
                                            'position',
                                            $position,
                                            array('id'=>$itemid, 'feedback'=>$feedback->id));
    }
    return $result;
}
