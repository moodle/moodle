<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 * @package    local_intelliboard
 * @copyright  2019 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    http://intelliboard.net/
 */

$plugin = new local_intelliboard_external();

$vars = (object)array('start'=> 0,'length'=> -1,'order_column'=> 0,'order_dir'=> "asc", 'custom'=>$custom, 'courseid'=>$courseid);
$data = (object)$plugin->analytic3($vars);
$course = get_course($courseid)->fullname;
$quiz = $DB->get_field('quiz', 'name', array('id' => $custom));

if($action == 'export_pdf'){
    require_once($CFG->libdir."/pdflib.php");
    $images = optional_param_array('images', '', PARAM_TEXT);

    $exportsubdir = "local_intelliboard/export_images";
    make_temp_directory($exportsubdir);
    $exportdir = "$CFG->tempdir/$exportsubdir";

    foreach($images as $key=>$image){
        if(empty($image)){continue;}
        $file = fopen($exportdir . "/analytic3_$key.png", 'w');
        list($type, $image) = explode(';', $image);
        list(, $image)      = explode(',', $image);
        $image = base64_decode($image);
        fwrite($file, $image);
        fclose($file);
    }

    $header = array(get_string('name','local_intelliboard'),get_string('answers','local_intelliboard'),get_string('correct','local_intelliboard'),get_string('incorrect','local_intelliboard'));
    $body = array();

    foreach($data->data as $item){
        $row = array();
        $row[] = $item->name;
        $row[] = $item->allanswer;
        $row[] = $item->rightanswer;
        $row[] = $item->allanswer - $item->rightanswer;
        $body[] = $row;
    }

    $data_table = (object)array('header'=>$header,'body'=>$body);

    $title = get_string('analityc_3_name','local_intelliboard');
    $filename = "$title by IntelliBoard.pdf";
    $header = '<html><body><head><link rel="Stylesheet" href="'.$CFG->wwwroot.'/local/intelliboard/assets/css/pdf.css"></head>';
    $header .= '<table border="0" width="770px" class="header"><tr>';
    $header .= '<td width="20%" align="left" valign="middle"><img src="'.$CFG->wwwroot.'/local/intelliboard/assets/img/logo.png" /> </td><td align="right">';
    $header .= '<h2>'.$title.'</h2>';
    $header .= '<p><a href="www.intelliboard.net">www.intelliboard.net</a></p>';
    $header .= '</td></tr></table><br><hr><br>';

    $table = '<style>
                table{width:100%;border-collapse: collapse; border:none;  margin-top:20pt;}
                table tr td{border:0.5pt solid #999;padding:2pt; font-size:9pt;font-family:arial; text-align:center} 
                table tr th{border:none;padding:2pt; font-family:arial;font-weight:bold; font-size:9pt; text-align:center}
              </style><table width="100%"><tr>';
    foreach($data_table->header as $value){
        $table .= "<th>$value</th>";
    }
    $table .= "</tr>";

    foreach($data_table->body as $key=>$value){
        $class = (($key%2) == 0) ? 'odd' : 'even';
        $table .= "<tr class='$class'>";
        foreach($value as $line){
            $table .= "<td>$line</td>";
        }
        $table .= "</tr>";
    }
    $table .= "</table></body></html>";


    $pdf = new PDF('P', 'mm', 'A4', true, 'UTF-8', false);
    $pdf->SetTitle($title);
    $pdf->SetProtection(array('modify'));
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->SetAutoPageBreak(false, 0);
    $pdf->AddPage();

    $pdf->WriteHTML($header);

    $pdf->setFont('freeserif', 'B', 16);
    $pdf->writeHTMLCell(190, 10, 10, 35, get_string('course_name_a','local_intelliboard',$course));
    $pdf->writeHTMLCell(190, 10, 10, 45, get_string('quiz_name_a','local_intelliboard',$quiz));

    $pdf->SetFont('freesans', '', 0);
    $pdf->writeHTMLCell(190,1,10,55,html_writer::tag('div','',array('style'=>'height:1px;width:100%;background-color:#444; ')));

    $pdf->Image($exportdir . "/analytic3_pie.png", 10, 60, 0, 60);
    $pdf->Image($exportdir . "/analytic3_barchart.png", 110, 60, 0, 60);
    $pdf->Image($exportdir . "/analytic3_linechart.png", 10, 130, 190, 76);

    $pdf->writeHTMLCell(190,1,10,210,html_writer::tag('div','',array('style'=>'height:1px;width:100%;background-color:#444; ')));
    $pdf->setFont('freeserif', 'B', 14);
    $pdf->writeHTMLCell(190, 10, 10, 214, get_string('ques_breakdown','local_intelliboard'));

    $pdf->setFont('freeserif', '', 12);
    $pdf->SetXY(13, 223);
    $pdf->WriteHTML($table);

    $pdf->Output($filename, 'D');

    echo $html;
    exit;
}elseif($action == 'export_excel'){
    require_once($CFG->libdir . '/excellib.class.php');
    $images = optional_param_array('images', '', PARAM_TEXT);

    $exportsubdir = "local_cailms/export_images";
    make_temp_directory($exportsubdir);
    $exportdir = "$CFG->tempdir/$exportsubdir";

    foreach($images as $key=>$image){
        if(empty($image)){continue;}
        $file = fopen($exportdir . "/analytic3_$key.png", 'w');
        list($type, $image) = explode(';', $image);
        list(, $image)      = explode(',', $image);
        $image = base64_decode($image);
        fwrite($file, $image);
        fclose($file);
    }
    $header = array(get_string('name','local_intelliboard'),get_string('answers','local_intelliboard'),get_string('correct','local_intelliboard'),get_string('incorrect','local_intelliboard'));
    $body = array();

    foreach($data->data as $item){
        $row = array();
        $row[] = $item->name;
        $row[] = $item->allanswer;
        $row[] = $item->rightanswer;
        $row[] = $item->allanswer - $item->rightanswer;
        $body[] = $row;
    }

    $title = get_string('analityc_3_name','local_intelliboard');
    $filename = "$title by IntelliBoard.xlsx";

    $workbook = new MoodleExcelWorkbook($filename, 'Excel2007');
    $worksheet = $workbook->add_worksheet('');

    $rowID = 0;
    $columnID = 0;
    $params = array('size'=>12, 'bold'=>1);
    $worksheet->write_string($rowID, $columnID, $title, $params);
    $rowID++;
    $params = array('size'=>10, 'bold'=>1);
    $worksheet->write_string($rowID, $columnID, get_string('course_name_a','local_intelliboard',$course), $params);
    $rowID++;
    $params = array('size'=>10, 'bold'=>1);
    $worksheet->write_string($rowID, $columnID, get_string('quiz_name_a','local_intelliboard',$quiz), $params);


    $rowID++;
    $worksheet->insert_bitmap($rowID, $columnID, $exportdir . '/analytic3_pie.png');
    $worksheet->merge_cells($rowID, $columnID, $rowID, 9);
    $worksheet->set_row($rowID, 300);
    $worksheet->insert_bitmap($rowID, 10, $exportdir . '/analytic3_barchart.png');
    $worksheet->merge_cells($rowID, 10, $rowID, 19);
    $worksheet->set_row($rowID, 300);
    $rowID++;
    $worksheet->insert_bitmap($rowID, 1, $exportdir . '/analytic3_linechart.png');
    $worksheet->merge_cells($rowID, 0, $rowID, 19);
    $worksheet->set_row($rowID, 300);
    $rowID++;
    $params = array('size'=>10, 'bold'=>1);
    $worksheet->write_string($rowID, $columnID, get_string('ques_breakdown','local_intelliboard'), $params);

    $rowID++;
    $columnID=1;
    $params = array('size'=>9, 'bold'=>1);
    foreach($header as $item){
        $worksheet->write_string($rowID, $columnID, $item, $params);
        $columnID++;
    }
    $rowID++;
    $params = array('size'=>9);
    foreach($body as $row){
        $columnID=1;
        foreach($row as $item){
            $worksheet->write_string($rowID, $columnID, $item, $params);
            $columnID++;
        }
        $rowID++;
    }


    $workbook->close();
    exit;
}elseif($action == 'export_csv'){
    $sep=",";
    $br="\n";
    $csv = "";
    $header = array(get_string('name','local_intelliboard'),get_string('answers','local_intelliboard'),get_string('correct','local_intelliboard'),get_string('incorrect','local_intelliboard'));
    $body = array();
    $filename = get_string('analityc_3_name','local_intelliboard')." by IntelliBoard.csv";

    foreach($data->data as $item){
        $row = array();
        $row[] = '"'.str_replace('"',"'",$item->name).'"';
        $row[] = '"'.str_replace('"',"'",$item->allanswer).'"';
        $row[] = '"'.str_replace('"',"'",$item->rightanswer).'"';
        $row[] = '"'.str_replace('"',"'",$item->allanswer - $item->rightanswer).'"';
        $body[] = $row;
    }

    foreach($header as $key => $value){
        if($key > 0){
            $csv .= $sep;
        }
        $csv .= '"'.str_replace('"',"'",$value).'"';
    }
    $csv .= $br;
    if(!empty($data->data)){
        foreach($body as $row){
            foreach($row as $key => $item){
                if($key > 0){
                    $csv .= $sep;
                }
                $csv .= $item;
            }
            $csv .= $br;
        }
    }else{
        $csv .= "No data";
    }

    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: private",false);
    header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename=\"$filename\";" );
    header("Content-Transfer-Encoding: binary");

    die($csv);
}

