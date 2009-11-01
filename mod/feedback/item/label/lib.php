<?php
defined('FEEDBACK_INCLUDE_TEST') OR die('not allowed');
require_once($CFG->dirroot.'/mod/feedback/item/feedback_item_class.php');

class feedback_item_label extends feedback_item_base {
    var $type = "label";
    function init() {

    }

    function show_edit($item) {
        global $CFG;

        require_once('label_form.php');

        $item_form = new feedback_label_form();

        $item->presentation = isset($item->presentation) ? $item->presentation : '';

        $item_form->area->setValue($item->presentation);
        return $item_form;
    }
    function print_item($item){
    ?>
        <td colspan="2">
            <?php echo format_text($item->presentation);?>
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