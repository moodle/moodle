	<!--<style>
			table	{
				border: 1px solid black;
				border-collapse: collapse;
			}
			td {
				border: 1px solid black;
			}
	</style> -->

 
<?php	

include_once('lib_rapport.php');
include('INC/connexion.php'); 
include_once("../../config.php");

require_once($CFG->dirroot.'/blocks/use_stats/locallib.php');
require_once($CFG->dirroot.'/report/trainingsessions/locallib.php');
require_once($CFG->dirroot.'/report/trainingsessions/renderers/htmlrenderers.php');
include_once('../trainingsessions/htmlrenderers.php');

require_once('tcpdf/tcpdf.php');
// echo '<pre>';
// print_r($_GET);
// echo '</pre>';
// exit;
        $PreNomStagiaire = $_GET['PreNomStagiaire'];
        $NomStagiaire = $_GET['NomStagiaire'];
        $NomComplet = $PreNomStagiaire . ' ' . $NomStagiaire;
        
        $RefSession = $_GET['RefSession'];
        $tef = $_GET['tef'];
        $tabloTemp = explode(":", $tef);
        $tef = $tabloTemp[0] . ":" . $tabloTemp[1];

        


    function training_reports_print_session_list_frank(&$str, $sessions, $courseid = 0){
                global $OUTPUT;
                setlocale(LC_ALL, 'fr_FR');

                $y = 0;
                while (isset($_GET['cal'.$y])) {
                    $cal[$y] = $_GET['cal'.$y];
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

                    if ($s->sessionstart < $_GET['date']) continue;

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
                    $str .= '</tr>';
                    $totalelapsed += @$s->elapsed;
                }

               

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
    } else {
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
                $nodestr .= '</td>';
                // // $nodestr .= '<td class="sessionitem rangedate userreport-col1">';
                // if (isset($structure->id) && !empty($aggregate[$structure->type][$structure->id])) {
                //     $nodestr .= date('Y/m/d H:i', 0 + (@$aggregate[$structure->type][$structure->id]->firstaccess));
                // }
                // // $nodestr .= '</td>';
                // // $nodestr .= '<td class="sessionitem rangedate  userreport-col2">';
                // if (isset($structure->id) && !empty($aggregate[$structure->type][$structure->id])) {
                //     $nodestr .= date('Y/m/d H:i', 0 + (@$aggregate[$structure->type][$structure->id]->lastaccess));
                // }
                // // $nodestr .= '</td>';
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
            } else {
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
    

    /* -------------------------------------------------------------------------------------------------------------------------------- */


    

    function liste_detail ($userid, $courseid) {

            ob_start();
            include_once '../../blocks/use_stats/locallib.php'; 

            $data = new StdClass;
            $data->from = 1483226000;
            $data->userid = $userid;
            $data->output = 'html';
            $coursestructure = report_trainingsessions_get_course_structure($courseid, $items);

        // get data
            $dated = $_GET['date'];
            $datef = $_GET['datef'];
           $logs = use_stats_extract_logs($dated, $datef, $userid, $courseid);
            $aggregate = use_stats_aggregate_logs($logs, 'module', $dated, $datef);
            $weekaggregate = use_stats_aggregate_logs($logs, 'module', time() - WEEKSECS, time());


            if (empty($aggregate['sessions'])) {
            $aggregate['sessions'] = array();
            }
            
        // print result

                   
            if ($data->output == 'html'){
                // require_once('htmlrenderers.php');
                $str = '';

                report_trainingsessions_print_html_franck($str, $coursestructure, $aggregate, $done);

                $str.="<h2>Calendrier de connexion</h2>"; 
                

                training_reports_print_session_list_frank($str, @$aggregate['sessions'], $courseid);

                $str.="<p>* Les temps relevés dans ce calendrier correspondent à la durée de connexion de l’apprenant sur les outils de l'organisme de formation. Il peut être supérieur ou égal au temps facturé sur l’ensemble de la période concernée. Le temps facturé étant celui prévu dans le cadre du contrat de prise en charge sur cette même période.</p>" ;


                ob_end_clean();
                return $str;
            }

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

    function print_etat ($i) {
         

        
        $PreNomStagiaire = $_GET['PreNomStagiaire'.$i];
        $NomStagiaire = $_GET['NomStagiaire'.$i];
        $NomComplet = $PreNomStagiaire . ' ' . $NomStagiaire;
        
        $doss = $_GET['doss'];
        $RefSession = $_GET['RefSession'.$i];
        $tef = $_GET['tef'.$i];
        $tabloTemp = explode(":", $tef);
        $tef = $tabloTemp[0] . ":" . $tabloTemp[1];
        $tef = str_replace(':', 'h', $tef);



       $str= "
        <h1 style='text-align: center;'>Suivi de l'apprenant(e) en formation à distance</h1>
        <h3 style='font-weight: bold; font-size: 14pt;'>Stagiaire : ". $NomComplet ."</h3>
        <h3 style='font-weight: bold; font-size: 14pt;'>Session : ". $RefSession."</h3>
        <h3 style='font-weight: bold; font-size: 14pt;'>Numéro de parcours : ". $doss."</h3>";


       

        
    

        /* $str .= '<tr valign="top">';
                    $str .= '<td>Samedi</td>';
                    $str .= '<td>Dimanche</td>';
                    $str .= '<td>huit</td>';
                    $str .= '</tr>';*/


        $str.='<h1>Temps dans les activités : '. $tef .'</h1>';
        $str.=liste_detail($_GET['id'.$i], $_GET['course'.$i]);


        return $str;

   


    }

         

   $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);



// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('INFANS');
$pdf->SetTitle('Relevé '.$NomStagiaire);
$pdf->SetSubject('Relevé '.$NomStagiaire);
$pdf->SetKeywords('Relevé, PDF');

$pdf->setPrintHeader(false);

// set default header data
// $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'Relevé de test', "Session: ".$RefSession, array(0,64,255), array(0,64,128));
$pdf->setFooterData(array(0,64,0), array(0,64,128));

// set header and footer fonts
// $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_RIGHT);
// $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set default font subsetting mode
$pdf->setFontSubsetting(true);

// Set font
// dejavusans is a UTF-8 Unicode font, if you only need to
// print standard ASCII chars, you can use core fonts like
// helvetica or times to reduce file size.
$pdf->SetFont('dejavusans', '', 8, '', true);

// Add a page
// This method has several options, check the source code documentation for more information.


// set text shadow effect
//s$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));

// echo '<pre>';
// print_r( $_GET );
// echo '</pre>';
// exit;

if ($_GET['total'] == 1) {

    $i = $_GET['nb'] - 1;
// if($i<0) $i=0;
// exit(' I = ' .$i );		
    while ($i>=0) {
        // $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'Relevé de '.$_GET['NomStagiaire'.$i], "Session: ".$_GET['RefSession0'], array(0,64,255), array(0,64,128));
        // $pdf->setHeaderTemplateAutoreset(true);
        $pdf->startPageGroup();

        $pdf->AddPage();
        $html = print_etat($i);

        $pdf->writeHTML($html, true, false, false, false, '');

        $pageN = $pdf->PageNo();
        if ($pageN % 2 == 1) {
            $pdf->AddPage();
        }

        $i--;
    }
} else {

    // $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'Relevé de '.$_GET['NomStagiaire1'], "Session: ".$_GET['RefSession1'], array(0,64,255), array(0,64,128));
    // $pdf->setHeaderTemplateAutoreset(true);
    $pdf->AddPage();
    $html = print_etat(0);
    $pdf->writeHTML($html, true, false, false, false, '');

}


// Print text using writeHTMLCell()




$pdf->IncludeJS("print(true);");

ob_end_clean();

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.

$pdf->Output('Releve_heures_session_'.$_GET['RefSession0'].'.pdf', 'I');



    

    
	
	
    	
?>