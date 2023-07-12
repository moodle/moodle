<?php	

include_once('lib_rapport.php');
include_once("../../config.php");
include('INC/connexion.php'); 

require_once($CFG->dirroot.'/blocks/use_stats/locallib.php');
require_once($CFG->dirroot.'/report/trainingsessions/locallib.php');
require_once($CFG->dirroot.'/report/trainingsessions/renderers/htmlrenderers.php');
include_once('../trainingsessions/htmlrenderers.php');

require_once('tcpdf/tcpdf.php');



    function training_reports_print_session_list_frank(&$str, $sessions, $courseid = 0){
                global $OUTPUT;
                setlocale(LC_ALL, 'fr_FR');

                $y = 0;
                while (isset($_POST['cal'.$y])) {
                    $cal[$y] = $_POST['cal'.$y];
                    $y++;
                }

                // effective printing of available sessions
                $str .= '<table width="100%" id="session-table">';
                $str .= '<tr valign="top">';
                $str .= '<td width="33%"><b>Début de session</b></td>';
                $str .= '<td width="33%"><b>Fin de session</b></td>';
                $str .= '<td width="33%"><b>Durée<sup>*</sup></b></td>';
                $str .= '</tr>';


                
                $totalelapsed = 0;


                foreach($sessions as $s){

                
                    $cont = 1;

                    if ($courseid && !array_key_exists($courseid, $s->courses)) continue; // omit all sessions not visiting this course

                    if (!isset($s->sessionstart)) continue;

                    if (@$s->elapsed == 0) continue;

                    if ($s->sessionstart < $_POST['date']) continue;

                    if (isset($cal[0])) {
                        foreach ($cal as $calandar) {
                            if ($s->sessionstart > ($calandar + 28800) && $s->sessionstart < ($calandar + 61200)){
                                $cont = 0;
                            }
                        }
                    }

                    if (!$cont) continue;

                    $sessionenddate = (isset($s->sessionend)) ? @$s->sessionend : '' ;
                    $str .= '<tr valign="top">';
                    $str .= '<td>'.date_fr(date("D j M Y G:i",$s->sessionstart)).'</td>';
                    $str .= '<td>'.date_fr(date("D j M Y G:i",$sessionenddate)).'</td>';
                    $str .= '<td>'.seconds_to_hours(@$s->elapsed).'</td>';
		
		echo "##";
		echo date_fr(date("D j M Y G:i",$s->sessionstart));
		echo "$$";
		echo date_fr(date("D j M Y G:i",$sessionenddate));
		echo "$$";
		echo seconds_to_hours(@$s->elapsed);
		// echo "##";
		
                    $str .= '</tr>';
                    $totalelapsed += @$s->elapsed;
                }
				
		exit;

               

                $str .= '</table>';

        }

	function report_trainingsessions_print_html_franck(&$str, $structure, &$aggregate, &$done, $indent = '', $level = 0) {
    global $OUTPUT;
    static $titled = false;

    

    $usconfig = get_config('use_stats');

    $config = get_config('report_trainingsessions');

    if (!empty($config->showseconds)) {
        $durationformat = 'htmlds';
    } else {
        $durationformat = 'htmld';
    }

    if (isset($usconfig->ignoremodules)) {
        $ignoremodulelist = explode(',', $usconfig->ignoremodules);
    } else {
        $ignoremodulelist = array();
    }

    if (empty($structure)) {
        $str .= get_string('nostructure', 'report_trainingsessions');
        return new StdClass;
    }

    if (!$titled )  {
        $titled = true;
        // $str .= $OUTPUT->heading(get_string('instructure', 'report_trainingsessions'));
        // Effective printing of available sessions.
        $str .= '<table width="140%" id="structure-table">';
        $str .= '<tr valign="top">';
        $str .= '<td class="userreport-col0"><b>'.get_string('structureitem', 'report_trainingsessions').'</b></td>';
        $label = get_string('duration', 'report_trainingsessions');
        // $label .= ' ('.get_string('hits', 'report_trainingsessions').')';
        $str .= '<td class="userreport-col1"><b>'.$label.'</b></td>';
        $str .= '</tr>';
        $str .= '</table>';
    }

    $indent = str_repeat('&nbsp;&nbsp;', $level);
    $suboutput = '';

    // Initiates a blank dataobject.
    if (!isset($dataobject)) {
        $dataobject = new StdClass;
        $dataobject->elapsed = 0;
        $dataobject->events = 0;
    }

    if (is_array($structure)) {
        // If an array of elements produce sucessively each output and collect aggregates.
        foreach ($structure as $element) {
            if (isset($element->instance) && empty($element->instance->visible)) {                    
                // Non visible items should not be displayed.                                         
                continue;
            }
            $level++;
            $res = report_trainingsessions_print_html_franck($str, $element, $aggregate, $done, $indent, $level);
            $level--;
            $dataobject->elapsed += $res->elapsed;
            $dataobject->events += (0 + @$res->events);
        }
    } 
	else {
        $nodestr = '';
        if (!isset($structure->instance) || !empty($structure->instance->visible)) {
            // Non visible items should not be displayed.
            // Name is not empty. It is a significant module (non structural).
            if (!empty($structure->name)) {
                if ($level==1) $nodestr .='<div></div>';
                $nodestr .= '<table class="sessionreport level'.$level.'">';
                $nodestr .= '<tr class="sessionlevel'.$level.' userreport-col0" valign="top">';
                $nodestr .= '<td class="sessionitem item" width="70%">';
                $nodestr .= $indent;
                if (debugging()) {
                    $nodestr .= '['.$structure->type.'] ';
                }
                $nodestr .= shorten_text(strip_tags(format_string($structure->name)), 85);
echo "##" . $structure->id . '$$';
                $nodestr .= '</td>';
                $nodestr .= '<td class="reportvalue rangedate userreport-col3">';
                if ($level > 1) $nodestr .= '       ';
                if (isset($structure->id) && !empty($aggregate[$structure->type][$structure->id])) {
                    $done++;
                    $dataobject = $aggregate[$structure->type][$structure->id];
                }
                if (!empty($structure->subs)) {
                    $res = report_trainingsessions_print_html_franck($suboutput, $structure->subs, $aggregate, $done, $indent, $level + 1);
                    $dataobject->elapsed += $res->elapsed;
                    $dataobject->events += $res->events;
                }

                if (!in_array($structure->type, $ignoremodulelist)) {
                    if (!empty($dataobject->timesource) && $dataobject->timesource == 'credit' && $dataobject->elapsed) {
                        $nodestr .= get_string('credittime', 'block_use_stats');
                    }
                    if (!empty($dataobject->timesource) && $dataobject->timesource == 'declared' && $dataobject->elapsed) {
                        $nodestr .= get_string('declaredtime', 'block_use_stats');
                    }
                    $nodestr .= report_trainingsessions_format_time($dataobject->elapsed, $durationformat);
	echo '||' . report_trainingsessions_format_time($dataobject->elapsed, $durationformat);
                    // if (is_siteadmin()) {
                        // $nodestr .= ' ('.(0 + @$dataobject->events).')';
                    // }
                } else {
                    $nodestr .= get_string('ignored', 'block_use_stats');
                }

                // Plug here specific details.
                $nodestr .= '</td>';
                $nodestr .= '</tr>';
                $nodestr .= '</table>';
            }
			else {
                // It is only a structural module that should not impact on level.
                if (isset($structure->id) && !empty($aggregate[$structure->type][$structure->id])) {
                    $dataobject = $aggregate[$structure->type][$structure->id];
                }
                if (!empty($structure->subs)) {
                    $res = report_trainingsessions_print_html_franck($suboutput, $structure->subs, $aggregate, $done, $indent, $level);
                    $dataobject->elapsed += $res->elapsed;
                    $dataobject->events += $res->events;
                }
            }
           
            $str .= $nodestr;
            
            if (!empty($structure->subs)) {
                $str .= '<table class="trainingreport subs">';
                $str .= '<tr valign="top">';
                $str .= '<td colspan="2">';
                $str .= '<br/>';
                $str .= $suboutput;
                $str .= '</td>';
                $str .= '</tr>';
                $str .= "</table>\n";
            }
        }
    }
     return $dataobject;
}
		
	
    function liste_detail ($userid, $courseid, $action) {

            ob_start();
            include_once '../../blocks/use_stats/locallib.php'; 

            $data = new StdClass;
            $data->from = 1483226000;
            $data->userid = $userid;
            $data->output = 'html';
            $coursestructure = report_trainingsessions_get_course_structure($courseid, $items);



        // get data
            $dated = $_POST['dated'];
            $datef = $_POST['datef'];
           $logs = use_stats_extract_logs($dated, $datef, $userid, $courseid);
            $aggregate = use_stats_aggregate_logs($logs, 'module', $dated, $datef);
            $weekaggregate = use_stats_aggregate_logs($logs, 'module', time() - WEEKSECS, time());


            if (empty($aggregate['sessions'])) {
				$aggregate['sessions'] = array();
            }
            
			if($action == "trainingsessions") report_trainingsessions_print_html_franck($str, $coursestructure, $aggregate, $done);
			elseif($action == "session_list")	training_reports_print_session_list_frank($str, @$aggregate['sessions'], $courseid);
// '<pre>';
// print_r( $coursestructure );
// echo '</pre>';
// echo '<pre style="background-color: lightgrey;">';
// print_r( $weekaggregate );
// echo '</pre>';
exit;
                
                ob_end_clean();
                return $donnes;


    }

	function date_fr ($date) {

            $array_date = explode(" ", $date);
            $date_fr = '';
            switch ($array_date[0]) {
                case "Mon":
                    $date_fr .= "Lundi";
                    break;
                case "Tue":
                    $date_fr .= "Mardi";
                    break;
                case "Wed":
                    $date_fr .= "Mercredi";
                    break;
                case "Thu":
                    $date_fr .= "Jeudi";
                    break;
                case "Fri":
                    $date_fr .= "Vendredi";
                    break;
                case "Sat":
                    $date_fr .= "Samedi";
                    break;
                case "Sun":
                    $date_fr .= "Dimanche";
                    break;
                default:
                    break;
            }

            $date_fr .= " " . $array_date[1] . " " ;

            switch ($array_date[2]) {
                case 'Jan':
                    $date_fr .= 'janvier';
                    break;
                case 'Feb':
                    $date_fr .= 'février';
                    break;
                case 'Mar':
                    $date_fr .= 'mars';
                    break;
                case 'Apr':
                    $date_fr .= 'avril';
                    break;
                case 'May':
                    $date_fr .= 'mai';
                    break;
                case 'Jun':
                   $date_fr .= 'juin';
                    break;
                case 'Jul':
                    $date_fr .= 'juillet';
                    break;
                case 'Aug':
                    $date_fr .= 'aout';
                    break;
                case 'Sep':
                    $date_fr .= 'septembre';
                    break;
                case 'Oct':
                    $date_fr .= 'octobre';
                    break;
                case 'Nov':
                    $date_fr .= 'novembre';
                    break;
                case 'Dec':
                    $date_fr .= 'decembre';
                    break;
                default:
                    break;
            }

            $date_fr .= " " . $array_date[3];
            $date_fr .= " " . $array_date[4];
            return $date_fr;
    }


if( isset($_POST['user_id'], $_POST['course_id']) )	{
// if( isset($_POST['user_id'], $_POST['course_id']) )	{
	// $liste_detail = json_encode( liste_detail( $_POST['user_id'], $_POST['course_id'] ) );
	liste_detail( $_POST['user_id'], $_POST['course_id'], $_POST['action'] );
	// echo '<h2> OK !! <h2>';
	
}