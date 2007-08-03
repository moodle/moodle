<?php  // $Id$

require_once('../config.php');
require_once('../tag/lib.php');

$action = optional_param('action', '', PARAM_ALPHA);

require_login();

if (empty($CFG->usetags)) {
    error('Tags are disabled!');
}

switch ($action) {
    case 'addinterest':
        $id  = optional_param('id', 0, PARAM_INT);
        $name = optional_param('name', '', PARAM_TEXT);
        
        if (empty($name) && $id) {
            $name = tag_name($id);
        }

        tag_an_item('user',$USER->id, $name);

        if (!empty($name) && !$id) {
            $id = tag_id(tag_normalize($name));
        }
                
        redirect($CFG->wwwroot.'/tag/index.php?id='.$id);
        break;

    case 'flaginappropriate':
        
        $id  = required_param('id', PARAM_INT);
        
        tag_flag_inappropriate($id);
        
        redirect($CFG->wwwroot.'/tag/index.php?id='.$id, get_string('responsiblewillbenotified','tag'));
        break;
}

?>
