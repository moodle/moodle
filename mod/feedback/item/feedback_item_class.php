<?php

abstract class feedback_item_base {
    var $type;

    /**
     * constructor
     *
     */
    function __construct() {
        $this->init();
    }

    //this function only can used after the call of build_editform()
    function show_editform() {
        $this->item_form->display();
    }
    
    function is_cancelled() {
        return $this->item_form->is_cancelled();
    }

    function get_data() {
        if($this->item = $this->item_form->get_data()) {
            return true;
        }
        return false;
    }
    
    abstract function init();
    abstract function build_editform($item, $feedback, $cm);
    abstract function save_item();
    abstract function check_value($value, $item);
    abstract function create_value($data);
    abstract function compare_value($item, $dbvalue, $dependvalue);
    abstract function get_presentation($data);
    abstract function get_hasvalue();
    abstract function can_switch_require();

    /**
     * @param object $worksheet a reference to the pear_spreadsheet-object
     * @param integer $rowOffset
     * @param object $item the db-object from feedback_item
     * @param integer $groupid
     * @param integer $courseid
     * @return integer the new rowOffset
    */
    abstract function excelprint_item(&$worksheet, $rowOffset, $xlsFormats, $item, $groupid, $courseid = false);

    /**
     * @param $item the db-object from feedback_item
     * @param string $itemnr
     * @param integer $groupid
     * @param integer $courseid
     * @return integer the new itemnr
    */
    abstract function print_analysed($item, $itemnr = '', $groupid = false, $courseid = false);
    
    /**
     * @param object $item the db-object from feedback_item
     * @param string $value a item-related value from feedback_values
     * @return string
    */
    abstract function get_printval($item, $value);
    
    /**
     * returns an Array with three values(typ, name, XXX)
     * XXX is also an Array (count of responses on type $this->type)
     * each element is a structure (answertext, answercount)
     * @param $item the db-object from feedback_item
     * @param $groupid if given
     * @param $courseid if given
     * @return array
    */
    abstract function get_analysed($item, $groupid = false, $courseid = false);
  
    /**     
     * print the item at the edit-page of feedback
     *
     * @global object
     * @param object $item
     * @return void
     */
    abstract function print_item_preview($item);
    
    /**     
     * print the item at the complete-page of feedback
     *
     * @global object
     * @param object $item
     * @param string $value
     * @param bool $highlightrequire
     * @return void
     */
    abstract function print_item_complete($item, $value = '', $highlightrequire = false);

    /**     
     * print the item at the complete-page of feedback
     *
     * @global object
     * @param object $item
     * @param string $value
     * @return void
     */
    abstract function print_item_show_value($item, $value = '');

}

//a dummy class to realize pagebreaks
class feedback_item_pagebreak extends feedback_item_base {
    var $type = "pagebreak";

    function show_editform() {}
    function is_cancelled() {}
    function get_data() {}
    function init() {}
    function build_editform($item, $feedback, $cm) {}
    function save_item() {}
    function check_value($value, $item) {}
    function create_value($data) {}
    function compare_value($item, $dbvalue, $dependvalue) {}
    function get_presentation($data) {}
    function get_hasvalue() {}
    function excelprint_item(&$worksheet, $rowOffset, $xlsFormats, $item, $groupid, $courseid = false) {}
    function print_analysed($item, $itemnr = '', $groupid = false, $courseid = false) {}
    function get_printval($item, $value) {}
    function get_analysed($item, $groupid = false, $courseid = false) {}
    function print_item_preview($item) {}
    function print_item_complete($item, $value = '', $highlightrequire = false) {}
    function print_item_show_value($item, $value = '') {}
    function can_switch_require(){}

}


