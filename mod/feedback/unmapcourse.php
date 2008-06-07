<?php // $Id$
/**
* drops records from feedback_sitecourse_map
*
* @version $Id$
* @author Andreas Grabs
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package feedback
*/

    require_once("../../config.php");
    require_once($CFG->dirroot.'/mod/feedback/lib.php');

    $id = required_param('id', PARAM_INT);
    $cmapid = required_param('cmapid', PARAM_INT);
    
    if ($id) {
        if (! $cm = get_coursemodule_from_id('feedback', $id)) {
            error("Course Module ID was incorrect");
        }

        if (! $course = $DB->get_record("course", array("id"=>$cm->course))) {
            error("Course is misconfigured");
        }
        
        if (! $feedback = $DB->get_record("feedback", array("id"=>$cm->instance))) {
            error("Course module is incorrect");
        }
    }
    $capabilities = feedback_load_capabilities($cm->id);
    
    if (!$capabilities->mapcourse) {
        error ('access not allowed');
    }


    // cleanup all lost entries after deleting courses or feedbacks
    feedback_clean_up_sitecourse_map();

    if ($DB->delete_records('feedback_sitecourse_map', array('id'=>$cmapid))) {
        redirect (htmlspecialchars('mapcourse.php?id='.$id));
    } else {
        error('Database problem, unable to unmap');
    }

?>