<?php // $Id$
/**
* print the single-values of anonymous completeds
*
* @version $Id$
* @author Andreas Grabs
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package feedback
*/

    require_once("../../config.php");
    require_once("lib.php");

    // $SESSION->feedback->current_tab = 'showoneentry';
    $current_tab = 'showentries';

    $id = required_param('id', PARAM_INT); 
    $userid = optional_param('userid', false, PARAM_INT);
    
    if(($formdata = data_submitted('nomatch')) AND !confirm_sesskey()) {
        error('no sesskey defined');
    }

    if ($id) {
        if (! $cm = get_coursemodule_from_id('feedback', $id)) {
            error("Course Module ID was incorrect");
        }
     
        if (! $course = get_record("course", "id", $cm->course)) {
            error("Course is misconfigured");
        }
     
        if (! $feedback = get_record("feedback", "id", $cm->instance)) {
            error("Course module is incorrect");
        }
    }
    $capabilities = feedback_load_capabilities($cm->id);

    require_login($course->id, true, $cm);
    
    if(!$capabilities->viewreports){
        error(get_string('error'));
    }


    //get the completeds
    // if a new anonymous record has not been assigned a random response number
    if ($feedbackcompleteds = get_records_select('feedback_completed','feedback='.$feedback->id.' AND random_response=0 AND anonymous_response='.FEEDBACK_ANONYMOUS_YES, 'random_response')){ //arb
        //then get all of the anonymous records and go through them  
        $feedbackcompleteds = get_records_select('feedback_completed','feedback='.$feedback->id.' AND anonymous_response='.FEEDBACK_ANONYMOUS_YES,'id'); //arb
        shuffle($feedbackcompleteds);
        $num = 1;
        foreach($feedbackcompleteds as $compl){
            $compl->random_response = $num;
            update_record('feedback_completed', $compl);
            $num++;
        }
    }
    $feedbackcompleteds = get_records_select('feedback_completed','feedback='.$feedback->id.' AND anonymous_response='.FEEDBACK_ANONYMOUS_YES, 'random_response'); //arb

    /// Print the page header
    $strfeedbacks = get_string("modulenameplural", "feedback");
    $strfeedback  = get_string("modulename", "feedback");
    $buttontext = update_module_button($cm->id, $course->id, $strfeedback);
    
    $navlinks = array();
    $navlinks[] = array('name' => $strfeedbacks, 'link' => "index.php?id=$course->id", 'type' => 'activity');
    $navlinks[] = array('name' => format_string($feedback->name), 'link' => "", 'type' => 'activityinstance');
    
    $navigation = build_navigation($navlinks);
    
    print_header_simple(format_string($feedback->name), "",
                 $navigation, "", "", true, $buttontext, navmenu($course, $cm));

    /// Print the main part of the page
    ///////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////
    include('tabs.php');
    
    print_heading(format_text($feedback->name));
    
    print_continue(htmlspecialchars('show_entries.php?id='.$id.'&do_show=showentries'));
    //print the list with anonymous completeds
    // print_simple_box_start("center");
    print_box_start('generalbox boxaligncenter boxwidthwide');
?>
    <script type="text/javascript">
        function feedbackGo2delete(form)
        {
            form.action = "<?php echo $CFG->wwwroot;?>/mod/feedback/delete_completed.php";
            form.submit();
        }
    </script>

    <div align="center">
    <form name="frm" action="<?php echo me();?>" method="post">
        <table>
            <tr>
                <td>
                    <input type="hidden" name="sesskey" value="<?php echo $USER->sesskey;?>" />
                    <select name="completedid" size="<?php echo (sizeof($feedbackcompleteds)>10)?10:5;?>">
<?php
                    if(is_array($feedbackcompleteds)) {
                        $num = 1;
                        foreach($feedbackcompleteds as $compl) {
                            $selected = (isset($formdata->completedid) AND $formdata->completedid == $compl->id)?'selected="selected"':'';
                            echo '<option value="'.$compl->id.'" '. $selected .'>'.get_string('response_nr', 'feedback').': '. $compl->random_response. '</option>';//arb
                            $num++;
                        }
                    }
?>
                    </select>
                    <input type="hidden" name="showanonym" value="<?php echo FEEDBACK_ANONYMOUS_YES;?>" />
                    <input type="hidden" name="id" value="<?php echo $id;?>" />
                </td>
                <td valign="top">
                    <button type="submit"><?php print_string('show_entry', 'feedback');?></button><br />
                    <button type="button" onclick="feedbackGo2delete(this.form);"><?php print_string('delete_entry', 'feedback');?></button>
                </td>
            </tr>
        </table>
    </form>
    </div>
<?php
    // print_simple_box_end();
    print_box_end();
    if(!isset($formdata->completedid)) {
        $formdata = null;
    }
    //print the items
    if(isset($formdata->showanonym) && $formdata->showanonym == FEEDBACK_ANONYMOUS_YES) {
        //get the feedbackitems
        $feedbackitems = get_records('feedback_item', 'feedback', $feedback->id, 'position');
        $feedbackcompleted = get_record('feedback_completed', 'id', $formdata->completedid);
        if(is_array($feedbackitems)){
            if($feedbackcompleted) {
                echo '<p align="center">'.get_string('chosen_feedback_response', 'feedback').'<br />('.get_string('anonymous', 'feedback').')</p>';//arb
            } else {
                echo '<p align="center">'.get_string('not_completed_yet','feedback').'</p>';
            }
            // print_simple_box_start("center", '50%');
            print_box_start('generalbox boxaligncenter boxwidthnormal');
            echo '<form>';
            echo '<input type="hidden" name="sesskey" value="' . $USER->sesskey . '" />';
            echo '<table width="100%">';
            $itemnr = 0;
            foreach($feedbackitems as $feedbackitem){
                //get the values
                $value = get_record_select('feedback_value','completed ='.$feedbackcompleted->id.' AND item='.$feedbackitem->id);
                echo '<tr>';
                if($feedbackitem->hasvalue == 1) {
                    $itemnr++;
                    echo '<td valign="top">' . $itemnr . '.)&nbsp;</td>';
                } else {
                    echo '<td>&nbsp;</td>';
                }
                if($feedbackitem->typ != 'pagebreak') {
                    $itemvalue = isset($value->value) ? $value->value : false;
                    feedback_print_item($feedbackitem, $itemvalue, true);
                }else {
                    echo '<td colspan="2"><hr /></td>';
                }
                echo '</tr>';
            }
            echo '<tr><td colspan="2" align="center">';
            echo '</td></tr>';
            echo '</table>';
            echo '</form>';
            // print_simple_box_end();
            print_box_end();
        }
    }
    /// Finish the page
    ///////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////

    print_footer($course);

?>
