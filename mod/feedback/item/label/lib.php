<?php
defined('MOODLE_INTERNAL') OR die('not allowed');
require_once($CFG->dirroot.'/mod/feedback/item/feedback_item_class.php');

class feedback_item_label extends feedback_item_base {
    var $type = "label";
    function init() {

    }

    function show_edit($item, $commonparams, $positionlist, $position) {
        global $CFG;

        require_once('label_form.php');

        $item_form = new feedback_label_form('edit_item.php', array('item'=>$item, 'common'=>$commonparams, 'positionlist'=>$positionlist, 'position'=>$position));
        
        return $item_form;
    }
    function print_item($item){
    ?>
        <td colspan="2">
            <?php echo format_text($item->presentation, FORMAT_HTML);?>
        </td>
    <?php
    }

    function create_value($data) {
        return false;
    }

    //used by create_item and update_item functions,
    //when provided $data submitted from feedback_show_edit
    function get_presentation($data) {
        return $data->presentation;
    }

    function get_hasvalue() {
        return 0;
    }
}
?>