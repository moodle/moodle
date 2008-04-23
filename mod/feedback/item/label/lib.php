<?PHP  // $Id$
defined('FEEDBACK_INCLUDE_TEST') OR die('not allowed');
require_once($CFG->dirroot.'/mod/feedback/item/feedback_item_class.php');

class feedback_item_label extends feedback_item_base {
    var $type = "label";
    function init() {
    
    }
    
    function show_edit($item, $usehtmleditor = false) {
        $item->presentation=isset($item->presentation)?$item->presentation:'';
        print_string('label', 'feedback');
        echo '<br />';
        print_textarea($usehtmleditor, 20, 0, 0, 0, "presentation", $item->presentation);
        echo '<input type="hidden" id="itemname" name="itemname" value="label" />';

        if ($usehtmleditor) {
            use_html_editor();
        }
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
        return stripslashes($data->presentation);
    }

    function get_hasvalue() {
        return 0;
    }
}
?>