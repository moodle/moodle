<?php
defined('MOODLE_INTERNAL') OR die('not allowed');
require_once($CFG->dirroot.'/mod/feedback/item/feedback_item_class.php');
require_once($CFG->libdir.'/formslib.php');

class feedback_item_label extends feedback_item_base {
    var $type = "label";
    var $presentationoptions = null;
    var $commonparams;
    var $item_form;
    var $context;
    var $item;

    function init() {
        global $CFG;
        $this->presentationoptions = array('maxfiles' => EDITOR_UNLIMITED_FILES, 'trusttext'=>true);

    }

    function build_editform($item, $feedback, $cm) {
        global $DB, $CFG;
        require_once('label_form.php');

        //get the lastposition number of the feedback_items
        $position = $item->position;
        $lastposition = $DB->count_records('feedback_item', array('feedback'=>$feedback->id));
        if($position == -1){
            $i_formselect_last = $lastposition + 1;
            $i_formselect_value = $lastposition + 1;
            $item->position = $lastposition + 1;
        }else {
            $i_formselect_last = $lastposition;
            $i_formselect_value = $item->position;
        }
        //the elements for position dropdownlist
        $positionlist = array_slice(range(0,$i_formselect_last),1,$i_formselect_last,true);

        //all items for dependitem
        $feedbackitems = feedback_get_depend_candidates_for_item($feedback, $item);
        $commonparams = array('cmid'=>$cm->id,
                             'id'=>isset($item->id) ? $item->id : NULL,
                             'typ'=>$item->typ,
                             'items'=>$feedbackitems,
                             'feedback'=>$feedback->id);

        $this->context = get_context_instance(CONTEXT_MODULE, $cm->id);


        //preparing the editor for new file-api
        $item->presentationformat = FORMAT_HTML;
        $item->presentationtrust = 1;

        // Append editor context to presentation options, giving preference to existing context.
        $this->presentationoptions = array_merge(array('context' => $this->context), $this->presentationoptions);

        $item = file_prepare_standard_editor($item,
                                            'presentation', //name of the form element
                                            $this->presentationoptions,
                                            $this->context,
                                            'mod_feedback',
                                            'item', //the filearea
                                            $item->id);

        //build the form
        $this->item_form = new feedback_label_form('edit_item.php', array('item'=>$item, 'common'=>$commonparams, 'positionlist'=>$positionlist, 'position'=>$position, 'presentationoptions'=>$this->presentationoptions));
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

    function save_item() {
        global $DB;

        if(!$item = $this->item_form->get_data()) {
            return false;
        }

        if(isset($item->clone_item) AND $item->clone_item) {
            $item->id = ''; //to clone this item
            $item->position++;
        }

        $item->presentation = '';

        $item->hasvalue = $this->get_hasvalue();
        if(!$item->id) {
            $item->id = $DB->insert_record('feedback_item', $item);
        }else {
            $DB->update_record('feedback_item', $item);
        }

        $item = file_postupdate_standard_editor($item,
                                                'presentation',
                                                $this->presentationoptions,
                                                $this->context,
                                                'mod_feedback',
                                                'item',
                                                $item->id);

        $DB->update_record('feedback_item', $item);

        return $DB->get_record('feedback_item', array('id'=>$item->id));
    }

    function print_item($item){
        global $DB, $CFG;

        require_once($CFG->libdir . '/filelib.php');

        //is the item a template?
        if(!$item->feedback AND $item->template) {
            $template = $DB->get_record('feedback_template', array('id'=>$item->template));
            $context = get_context_instance(CONTEXT_COURSE, $template->course);
            $filearea = 'template';
        }else {
            $cm = get_coursemodule_from_instance('feedback', $item->feedback);
            $context = get_context_instance(CONTEXT_MODULE, $cm->id);
            $filearea = 'item';
        }

        $item->presentationformat = FORMAT_HTML;
        $item->presentationtrust = 1;

        $output = file_rewrite_pluginfile_urls($item->presentation, 'pluginfile.php', $context->id, 'mod_feedback', $filearea, $item->id);

        $formatoptions = array('overflowdiv'=>true, 'trusted'=>$CFG->enabletrusttext);
        echo format_text($output, FORMAT_HTML, $formatoptions);
    }

    /**
     * print the item at the edit-page of feedback
     *
     * @global object
     * @param object $item
     * @return void
     */
    function print_item_preview($item) {
        global $OUTPUT, $DB;

        if($item->dependitem) {
            if($dependitem = $DB->get_record('feedback_item', array('id'=>$item->dependitem))) {
                echo ' <span class="feedback_depend">('.$dependitem->label.'-&gt;'.$item->dependvalue.')</span>';
            }
        }
        $this->print_item($item);
    }

    /**
     * print the item at the complete-page of feedback
     *
     * @global object
     * @param object $item
     * @param string $value
     * @param bool $highlightrequire
     * @return void
     */
    function print_item_complete($item, $value = '', $highlightrequire = false) {
        $this->print_item($item);
    }

    /**
     * print the item at the complete-page of feedback
     *
     * @global object
     * @param object $item
     * @param string $value
     * @return void
     */
    function print_item_show_value($item, $value = '') {
        $this->print_item($item);
    }

    function create_value($data) {
        return false;
    }

    function compare_value($item, $dbvalue, $dependvalue) {
        return false;
    }

    //used by create_item and update_item functions,
    //when provided $data submitted from feedback_show_edit
    function get_presentation($data) {
        // $context = get_context_instance(CONTEXT_MODULE, $data->cmid);

        // $presentation = new stdClass();
        // $presentation->id = null;
        // $presentation->definition = '';
        // $presentation->format = FORMAT_HTML;

        // $draftid_editor = file_get_submitted_draft_itemid('presentation');
        // $currenttext = file_prepare_draft_area($draftid_editor, $context->id, 'mod_feedback', 'item_label', $presentation->id, array('subdirs'=>true), $presentation->definition);
        // $presentation->entry = array('text'=>$currenttext, 'format'=>$presentation->format, 'itemid'=>$draftid_editor);

        // return $data->presentation;
    }

    function postupdate($item) {
        global $DB;

        $context = get_context_instance(CONTEXT_MODULE, $item->cmid);
        $item = file_postupdate_standard_editor($item, 'presentation', $this->presentationoptions, $context, 'mod_feedback', 'item', $item->id);

        // $item = new stdClass();
        // $item->id = $data->id
        $DB->update_record('feedback_item', $item);
        return $item->id;
    }

    function get_hasvalue() {
        return 0;
    }

    function can_switch_require() {
        return false;
    }

    function check_value($value, $item) {}
    function excelprint_item(&$worksheet, $rowOffset, $xlsFormats, $item, $groupid, $courseid = false) {}
    function print_analysed($item, $itemnr = '', $groupid = false, $courseid = false) {}
    function get_printval($item, $value) {}
    function get_analysed($item, $groupid = false, $courseid = false) {}

    function clean_input_value($value) {
        return '';
    }
}
