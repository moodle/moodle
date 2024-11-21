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
 *
 * @package    local_intelliboard
 * @copyright  2017 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    https://intelliboard.net/
 */

const MYSQL_TYPE = 'mysqli';
const POSTGRES_TYPE = 'pgsql';

// Activating Japanish/China compatible font.
if(get_config('local_intelliboard', 'enableexportcustomfont')) {
    define('PDF_CUSTOM_FONT_PATH', $CFG->dirroot . "/local/intelliboard/assets/fonts/tcpdf/");
    define('PDF_DEFAULT_FONT', 'cid0jp');
}

function clean_raw($value, $mode = true)
{
	$params = array("'","`");
	if($mode){
		$params[] = '"';
		$params[] = '(';
		$params[] = ')';
	}
	return str_replace($params, '', $value);
}

function intelliboard_clean($content){
	return trim($content);
}

function intelliboard_compl_sql($prefix = "", $sep = true)
{
    $completions = get_config('local_intelliboard', 'completions');
    $prefix = ($sep) ? " AND ".$prefix : $prefix;
    if (!empty($completions)) {
        return $prefix . "completionstate IN($completions)";
    } else {
        return $prefix . "completionstate IN(1,2)"; //Default completed and passed
    }
}
function intelliboard_grade_sql($avg = false, $params = null, $alias = 'g.', $round = 0, $alias_gi='gi.',$percent = false)
{
    global $CFG;
    require_once($CFG->dirroot . '/local/intelliboard/classes/grade_aggregation.php');

    $scales = get_config('local_intelliboard', 'scales');
    $raw = get_config('local_intelliboard', 'scale_raw');
    $total = clean_param(get_config('local_intelliboard', 'scale_total'), PARAM_INT);
    $value = clean_param(get_config('local_intelliboard', 'scale_value'), PARAM_INT);
    $percentage = clean_param(get_config('local_intelliboard', 'scale_percentage'), PARAM_INT);
    $scale_real = get_config('local_intelliboard', 'scale_real');

    if($percent){
        if ($avg) {
            return "ROUND(AVG(CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), {$round})";
        } else {
            return "ROUND((CASE WHEN ({$alias}rawgrademax-{$alias}rawgrademin) > 0 THEN (({$alias}finalgrade-{$alias}rawgrademin)/({$alias}rawgrademax-{$alias}rawgrademin))*100 ELSE {$alias}finalgrade END), {$round})";
        }
    }elseif ((isset($params->scale_raw) and $params->scale_raw) or ($raw and !isset($params->scale_raw))) {
        if((isset($params->scale_real) and $params->scale_real) or ($scale_real and !isset($params->scale_real))){
            if ($avg) {
                return local_intelliboard_grade_aggregation::get_real_grade_avg($alias, $round, $alias_gi);
            } else {
                return local_intelliboard_grade_aggregation::get_real_grade_single($alias, $round, $alias_gi);
            }
        }else{
            if ($avg) {
                return "ROUND(AVG({$alias}finalgrade), $round)";
            } else {
                return "ROUND({$alias}finalgrade, $round)";
            }
        }
    } elseif (isset($params->scales) and $params->scales) {
        $total = $params->scale_total;
        $value = $params->scale_value;
        $percentage = $params->scale_percentage;
        $scales = true;
    } elseif (isset($params->scales) and !$params->scales) {
        $scales = false;
    }

    if ($scales and $total and $value and $percentage) {
        $dif = $total - $value;
        if ($avg) {
            return "ROUND(AVG(CASE WHEN ({$alias}finalgrade - $value) < 0 THEN ((({$alias}finalgrade / $value) * 100) / 100) * $percentage ELSE ((((({$alias}finalgrade - $value) / $dif) * 100) / 100) * $percentage) + $percentage END), $round)";
        } else {
            return "ROUND((CASE WHEN ({$alias}finalgrade - $value) < 0 THEN ((({$alias}finalgrade / $value) * 100) / 100) * $percentage ELSE ((((({$alias}finalgrade - $value) / $dif) * 100) / 100) * $percentage) + $percentage END), $round)";
        }
    }
    if ($avg) {
        return "ROUND(AVG(CASE WHEN {$alias}rawgrademax > 0 THEN ({$alias}finalgrade/{$alias}rawgrademax)*100 ELSE {$alias}finalgrade END), $round)";
    } else {
        return "ROUND((CASE WHEN {$alias}rawgrademax > 0 THEN ({$alias}finalgrade/{$alias}rawgrademax)*100 ELSE {$alias}finalgrade END), $round)";
    }
}
function intelliboard_filter_in_sql($sequence, $column, $params = array(), $prfx = 0, $sep = true, $equal = true)
{
	global $DB;

	$sql = '';
	if ($sequence){
	    if (!is_array($sequence)) {
            $items = explode(",", clean_param($sequence, PARAM_SEQUENCE));
        } else {
	        $items = $sequence;
        }

		if (!empty($items)){
			$key = clean_param($column.$prfx, PARAM_ALPHANUM);
			list($sql, $sqp) = $DB->get_in_or_equal($items, SQL_PARAMS_NAMED, $key, $equal);
			$params = array_merge($params, $sqp);
			$sql = ($sep) ? " AND $column $sql ": " $column $sql ";
		}
	}

	return array($sql, $params);
}

function intelliboard_url($api = false)
{
		global $CFG;

		require($CFG->dirroot .'/local/intelliboard/config.php');


		$server = get_config('local_intelliboard', 'server');

		if ($api) {
			return $config['app_url_api'];
		} elseif ($server == 1) {
			return $config['app_url_au'];
		} elseif ($server == 2) {
			return $config['app_url_ca'];
		} elseif ($server == 5) {
			return $config['app_url_cn'];
		} elseif ($server == 3) {
			return $config['app_url_eu'];
		} elseif ($server == 4) {
			return $config['app_url_us'];
		} else {
			return $config['app_url'];
		}
}
function intelliboard($params, $function = 'sso'){
	global $CFG, $USER;

		require_once($CFG->libdir . '/filelib.php');

		$api = get_config('local_intelliboard', 'api');
		$debug = get_config('local_intelliboard', 'debug');
		$debugmode = optional_param('debug', '', PARAM_RAW);
		$url = intelliboard_url($api);

		$params['email'] = get_config('local_intelliboard', 'te1');
		$params['apikey'] = get_config('local_intelliboard', 'apikey');
		$params['useremail'] = $USER->email;
		$params['url'] = $CFG->wwwroot;
		$params['lang'] = current_language();
		$params['userid'] = $USER->id ?? 0;

		$options =[];
		if (get_config('local_intelliboard', 'verifypeer')) {
			$options['CURLOPT_SSL_VERIFYPEER'] = false;
		}
		if (get_config('local_intelliboard', 'verifyhost')) {
			$options['CURLOPT_SSL_VERIFYHOST'] = false;
		}
		$cipherlist = get_config('local_intelliboard', 'cipherlist');
		$sslversion = get_config('local_intelliboard', 'sslversion');

		if ($cipherlist) {
			$options['CURLOPT_SSL_CIPHER_LIST'] = $cipherlist;
		}
		if ($sslversion) {
			$options['CURLOPT_SSLVERSION'] = $sslversion;
		}

		if ($debug and $debugmode) {
			ob_start();
			$curl = new curl(['debug'=>true]);
			$out = fopen('php://output', 'w');

			$options['CURLOPT_VERBOSE'] = true;
			$options['CURLOPT_STDERR'] = $out;

			$json = $curl->post($url . 'moodleApi/' . $function, $params, $options);
			fclose($out);
			$output = ob_get_clean() . PHP_EOL . $json;
		} else {
			$curl = new curl;
			if ($function == 'downloadExportFile' && isset($params['filepath'])) {
                $tempdir = make_temp_directory('local_intelliboard/export');
                $downloadto = $tempdir . '/' . $params['filepath'];

                $options = ['filepath' => $downloadto, 'timeout' => 15, 'followlocation' => true, 'maxredirs' => 5];
                $success = $curl->download_one($url . 'moodleApi/' . $function, $params, $options);
                if($success){
                    $json = json_encode((object)array('filepath' => $downloadto));
                }else{
                    $json = json_encode((object)array('filepath' => null));
                }

            } else {
                $json = $curl->post($url . 'moodleApi/' . $function, $params, $options);
            }
			$output = $json;
		}

		$data = (object)json_decode($json);
		$data->status = (isset($data->status))?$data->status:'';
        $data->token = (isset($data->token))?$data->token:'';
		$data->reports = (isset($data->reports))?(array)$data->reports:null;
		$data->intellicart_reports = isset($data->intellicart_reports) ? (array) $data->intellicart_reports : null;
		$data->sets = (isset($data->reports))?(array)$data->sets:null;
		$data->alerts = (isset($data->alerts))?(array)$data->alerts:null;
		$data->alert = (isset($data->alert))?$data->alert:'';
		$data->data = (isset($data->data))? (array) $data->data : null;
		$data->shoppingcartkey = (isset($data->shoppingcartkey))? (array) $data->shoppingcartkey : null;
		$data->curlinfo = $curl->info;
		$data->debugging = $output;

		return $data;
}

function intelliboard_auth($params, $function) {
    global $CFG;

    require_once($CFG->libdir . '/filelib.php');

    $api = get_config('local_intelliboard', 'api');
    $url = intelliboard_url($api);
    $options =[];

    if (get_config('local_intelliboard', 'verifypeer')) {
        $options['CURLOPT_SSL_VERIFYPEER'] = false;
    }
    if (get_config('local_intelliboard', 'verifyhost')) {
        $options['CURLOPT_SSL_VERIFYHOST'] = false;
    }
    $cipherlist = get_config('local_intelliboard', 'cipherlist');
    $sslversion = get_config('local_intelliboard', 'sslversion');

    if ($cipherlist) {
        $options['CURLOPT_SSL_CIPHER_LIST'] = $cipherlist;
    }
    if ($sslversion) {
        $options['CURLOPT_SSLVERSION'] = $sslversion;
    }

    $curl = new curl;
    $json = $curl->post($url . 'moodleApi/' . $function, $params, $options);

    if (get_config('local_intelliboard', 'debug')) {
        echo "<pre>";var_dump($json);
    }

    return json_decode($json, true);
}

function chart_options()
{
		$timespent = get_string('timespent', 'local_intelliboard');
		$grade = get_string('grade', 'local_intelliboard');
    $res = array();

    $res['CourseProgressCalculation'] = json_encode([
        "factor"    => md5("#FGS$%FGH245$".rand(0,1000)),
        "title"     => '',
        "legend"    => ["position" => "none"],
        "vAxis"     => ["title" => $grade],
        "hAxis"     => ["title" => ''],
        "seriesType" => "bars",
        "series"    => ['1' => ["type" => "line"]],
        "chartArea" => ["width" => '92%', "height" => '76%', "right" => "10","top" => 10],
        "colors"    => ['#1d7fb3', '#1db34f'],
        "backgroundColor" => ["fill" => 'transparent']
    ]);

    $res['ActivityProgressCalculation'] = json_encode([
        "factor" => md5("#FGS$%FGH245$".rand(0,1000)),
        "chartArea" => [ "width" => '95%', "height" => '76%', "right" => 10, "top" => 10],
        "height" => 250,
        "hAxis" => [
            "format" => 'dd MMM',
            "gridlines" => new \stdClass(),
            "baselineColor" => '#ccc',
            "gridlineColor" => '#ccc',
        ],
        "vAxis" => [
            "baselineColor" => '#CCCCCC',
            "gridlines" => ["count" => 5, "color" => 'transparent',],
            "minValue" => 0
        ],
        "pointSize" => 6,
        "lineWidth" => 2,
        "colors" => ['#1db34f', '#1d7fb3'],
        "backgroundColor" => ["fill" => 'transparent'],
        "tooltip" => ["isHtml" =>  true],
        "legend" => ["position" => 'none']
    ]);

    $res['LearningProgressCalculation'] = json_encode([
        "factor" => md5("#FGS$%FGH245$".rand(0,1000)),
        "legend" => ["position" => 'bottom', "alignment" => 'center'],
        "title"=> '',
        "height" => '350',
        "pieHole" => 0.4,
        "pieSliceText" => 'value',
        "chartArea" => ["width" => '95%', "height" => '85%', "right" => 10, "top" => 10],
        "backgroundColor" => ["fill" => 'transparent']
    ]);

    $res['ActivityParticipationCalculation'] = json_encode([
        "factor" => md5("#FGS$%FGH245$".rand(0,1000)),
        "legend" => ["position" => 'bottom', "alignment" => 'center'],
        "title" => '',
        "height" => '350',
        "chartArea" => ["width" => '85%', "height" => '85%', "right" => 10, "top" => 10],
        "backgroundColor" => ["fill" => 'transparent']
    ]);

    $res['CorrelationsCalculation'] = json_encode([
        "factor" => md5("#FGS$%FGH245$".rand(0,1000)),
        "legend" => 'none',
        "colors" => ['#1d7fb3', '#1db34f'],
        "pointSize" => 16,
        "tooltip" => ["isHtml" => true],
        "title" => '',
        "height" => '350',
        "chartArea" => ["width" => '85%', "height" => '70%', "right" => 10, "top" => 10],
        "backgroundColor" => ["fill" => 'transparent'],
        "hAxis" => ["ticks" => [], "baselineColor" => 'none', "title" => $timespent],
        "baselineColor" => 'none',
        "title" => $timespent,
        "vAxis" => ["title" => $grade],
    ]);

    $res['CourseSuccessCalculation'] = json_encode([
        "factor"    => md5("#FGS$%FGH245$".rand(0,1000)),
        "legend"    => ["position" => 'bottom', "alignment" => 'center'],
        "title"     => '',
        "height"    => '350',
        "chartArea" => ["width" => '95%', "height" => '85%', "right" => 10, "top" => 10],
        "backgroundColor" => ["fill" => 'transparent']
    ]);

    $res['GradesCalculation'] = json_encode([
        "factor"    => md5("#FGS$%FGH245$".rand(0,1000)),
        "animate"   => true,
        "diameter"  => 40,
        "guage"     => 1,
        "coverBg"   => '#fff',
        "bgColor"   => '#efefef',
        "fillColor" => '#5c93c8',
        "percentSize"   => '11px',
        "percentWeight" => 'normal'
    ]);

    $res['GradesCalculationJSON'] = json_encode([
        "factor" => md5("#FGS$%FGH245$".rand(0,1000)),
        "animate" => true,
        "diameter" => 40,
        "guage" => 1,
        "coverBg" => "#fff",
        "bgColor" => "#efefef",
        "fillColor" => "#5c93c8",
        "percentSize" => "11px",
        "percentWeight" => "normal",
    ]);

    $res['GradesFCalculation'] = json_encode([
        "factor"    => md5("#FGS$%FGH245$".rand(0,1000)),
        "animate"   => true,
        "diameter"  => 80,
        "guage"     => 2,
        "coverBg"   => '#fff',
        "bgColor"   => '#efefef',
        "fillColor" => '#5c93c8',
        "percentSize"   => '15px',
        "percentWeight" => 'normal'
    ]);

    $res['GradesXCalculation'] = json_encode([
        "factor"    => md5("#FGS$%FGH245$".rand(0,1000)),
        "animate"   => true,
        "diameter"  => 40,
        "guage"     => 1,
        "coverBg"   => '#fff',
        "bgColor"   => '#efefef',
        "fillColor" => '#5c93c8',
        "percentSize"   => '11px',
        "percentWeight" => 'normal'
    ]);

    $res['GradesXCalculationJSON'] = json_encode([
        'factor' => md5("#FGS$%FGH245$".rand(0,1000)),
        'animate' => true,
        'diameter' => 40,
        'guage' => 1,
        'coverBg' => '#fff',
        'bgColor' => '#efefef',
        'fillColor' => '#5c93c8',
        'percentSize' => '11px',
        'percentWeight' => 'normal',
    ]);

    $res['GradesZCalculation'] = json_encode([
        "factor"    => md5("#FGS$%FGH245$".rand(0,1000)),
        "animate"   => true,
        "diameter"  => 80,
        "guage"     => 2,
        "coverBg"   => '#fff',
        "bgColor"   => '#efefef',
        "fillColor" => '#5c93c8',
        "percentSize"   => '15px',
        "percentWeight" => 'normal'
    ]);

    $res['CoursesCalculation'] = json_encode([
        "factor"    => md5("#FGS$%FGH245$".rand(0,1000)),
        "chartArea" => ["width" => '90%', "height" => '76%', "right" => 20, "top" => 10],
        "height"    => 200,
        "hAxis"     => ["format" => 'dd MMM', "gridlines" => new \stdClass(), "baselineColor" => '#ccc', "gridlineColor" => '#ccc'],
        "vAxis"     => ["baselineColor" => '#CCCCCC', "gridlines" => ["count" => 5, "color" => 'transparent'], "minValue" => 0],
        "pointSize" => 6,
        "lineWidth" => 2,
        "colors"    => ['#1db34f','#1d7fb3'],
        "backgroundColor"   => ["fill" => 'transparent'],
        "tooltip"   => ["isHtml" => true],
        "legend"    => ["position" => 'bottom']
    ]);

    $res['GradeProgression'] = json_encode([
        "factor"    => md5("#FGS$%FGH245$".rand(0,1000)),
        "chartArea" => ["width" => '90%', "height" => '70%', "top" => 10],
        "hAxis"     => ["format" => 'dd MMM', "gridlines" => new \stdClass(), "baselineColor" => '#ccc', "gridlineColor" => '#ccc'],
        "vAxis"     => ["baselineColor" => '#CCCCCC', "gridlines" => ["count" => 5, "color" => 'transparent'], "minValue" => 0],
        "pointSize" => 6,
        "lineWidth" => 2,
        "colors"    => ['#1db34f','#1d7fb3'],
        "backgroundColor" => ["fill" => 'transparent'],
        "tooltip" => ["isHtml" => true],
        "legend" => ["position" => 'bottom']
    ]);

    $res['GradeActivitiesOverview'] = json_encode([
        "factor" => md5("#FGS$%FGH245$".rand(0,1000)),
        "chart" => new \stdClass(),
        "chartArea" => ["width" => '90%', "height" => '70%', "top" => 10],
        "legend" => ["position" => 'none']
    ]);

    return (object) $res;
}
function seconds_to_time($t,$f=':'){
	if($t < 0){
		return "00:00:00";
	}
	return sprintf("%02d%s%02d%s%02d", (int) floor($t/3600), $f, (int) ($t/60)%60, $f, (int) $t%60);
}


function intelliboard_csv_quote($value) {
	return '"'.str_replace('"',"'",$value).'"';
}
function intelliboard_export_report($json, $itemname, $format = 'csv', $output_type = 1)
{
    global $CFG;

    $name =  clean_filename($itemname . '-' . gmdate("Y-m-d"));

	if($format == 'csv'){
		return intelliboard_export_csv($json, $name, $output_type);
	}elseif($format == 'xlsx' || $format == 'xls'){
        return intelliboard_export_xls($json, $name, $output_type);
	}elseif($format == 'pdf'){
        return intelliboard_export_pdf($json, $name, $output_type);
	}else{
        return intelliboard_export_html($json, $name, $output_type);
	}
}

function intelliboard_export_html($json, $filename, $type = 1)
{
    $html = '<h2>'.$filename.'</h2>';
    $html .= '<table width="100%">';
    $html .= '<tr>';
    foreach ($json->header as $col) {
        $html .= '<th>'. $col->name.'</th>';
    }
    $html .= '</tr>';
    foreach ($json->body as $row) {
    	$html .= '<tr>';
        foreach($row as $col) {
        	$value = str_replace('"', '', $col);
			$value = strip_tags($value);
            $html .= '<td>'. $value.'</td>';
        }
    	$html .= '</tr>';
    }
    $html .= '</table>';
    $html .= '<style>table{border-collapse: collapse; width: 100%;} table tr th {font-weight: bold;} table th, table td {border:1px solid #aaaaaa; padding: 7px 10px; font: 13px/13px Arial;} table tr:nth-child(odd) td {background-color: #f5f5f5;}</style>';

    switch ($type) {
        case 2:
            return $html;
        default:
            die($html);
    }
}
function intelliboard_export_csv($json, $filename, $type = 1)
{
	global $CFG;
    require_once($CFG->libdir . '/csvlib.class.php');

    $data = array(); $line = 0;
	foreach($json->header as $col){
		$value = str_replace('"', '', $col->name);
		$value = strip_tags($value);
		$data[$line][] = intelliboard_csv_quote($value);
	}
	$line++;
	foreach($json->body as $row){
		foreach($row as $col){
			$value = str_replace('"', '', $col);
			$value = strip_tags($value);
			$data[$line][] = intelliboard_csv_quote($value);
		}
		$line++;
	}
    $delimiters = array('comma'=>',', 'semicolon'=>';', 'colon'=>':', 'tab'=>'\\t');

    switch ($type) {
        case 1:
            return csv_export_writer::download_array($filename, $data, $delimiters['tab']);
        case 2:
            return csv_export_writer::print_array($data, $delimiters['tab'], '"', true);
        default:
            return csv_export_writer::print_array($data, $delimiters['tab']);
    }
}

function intelliboard_export_xls($json, $filename, $type = 1)
{
    global $CFG;
    require_once("$CFG->libdir/excellib.class.php");

    $filename .= '.xls';
    $filearg = '-';
    $workbook = new MoodleExcelWorkbook($filearg);
    $workbook->send($filename);
    $worksheet = array();
    $worksheet[0] = $workbook->add_worksheet('');
    $rowno = 0; $colno = 0;

    foreach ($json->header as $col) {
        $worksheet[0]->write($rowno, $colno, $col->name);
        $colno++;
    }
    $rowno++;
    foreach ($json->body as $row) {
        $colno = 0;
        foreach($row as $col) {
        	$value = str_replace('"', '', $col);
			$value = strip_tags($value);
            $worksheet[0]->write($rowno, $colno, $value);
            $colno++;
        }
        $rowno++;
    }

    switch ($type) {
        case 1:
            $workbook->close();
            exit;
        case 2:
            ob_start();
            $workbook->close();
            $data = ob_get_contents();
            ob_end_clean();
            return $data;
        default:
            return $workbook->close();
    }

}
function intelliboard_export_pdf($json, $name, $type = 1)
{
    global $CFG, $SITE;

	require_once($CFG->libdir . '/pdflib.php');

    $fontfamily = PDF_FONT_NAME_MAIN;

    raise_memory_limit(MEMORY_EXTRA);

    $doc = new pdf();
    $doc->SetTitle($name);
    $doc->SetAuthor('Moodle ' . $CFG->release);
    $doc->SetCreator('local/intelliboard/reports.php');
    $doc->SetKeywords($name);
    $doc->SetSubject($name);
    $doc->SetMargins(15, 30);
    $doc->setPrintHeader(true);
    $doc->setHeaderMargin(10);
    $doc->setHeaderFont(array($fontfamily, 'b', 10));
    $doc->setHeaderData('', 0, $SITE->fullname, $name);
    $doc->setPrintFooter(true);
    $doc->setFooterMargin(10);
    $doc->setFooterFont(array($fontfamily, '', 8));
    $doc->AddPage();
    $doc->SetFont($fontfamily, '', 8);
    $doc->SetTextColor(0,0,0);
    $name .= '.pdf';
    $html = '<table width="100%">';
    $html .= '<tr>';
    foreach ($json->header as $col) {
        $html .= '<th>'. $col->name.'</th>';
    }
    $html .= '</tr>';
    foreach ($json->body as $row) {
    	$html .= '<tr>';
        foreach($row as $col) {
        	$value = $col ? str_replace('"', '', $col) : '';
			$value = strip_tags($value);
            $html .= '<td>'. $value.'</td>';
        }
    	$html .= '</tr>';
    }
    $html .= '</table>';
    $html .= '<style>';
    $html .= 'td{border:0.1px solid #000; padding:10px;}';
    $html .= '</style>';
    $doc->writeHTML($html);

    switch ($type) {
        case 1:
            $doc->Output($name);
            die();
        case 2:
            return $doc->Output($name, 'S');
        default:
            die($doc->Output($name, 'S'));
    }


}
function get_modules_names() {
    global $DB;

    $modules = $DB->get_records_sql("SELECT m.id, m.name FROM {modules} m WHERE m.visible = 1");
    $nameColumn = array_reduce($modules, function($carry, $module) {
        return $carry . " WHEN m.name='{$module->name}' THEN (SELECT name FROM {".$module->name."} WHERE id = cm.instance)";
    }, '');

    return $nameColumn?  "CASE $nameColumn ELSE 'NONE' END" : "''";
}

function exclude_not_owners($columns) {

    global $DB, $CFG;
    $owners_users = array();
    $owners_courses = array();
    $owners_cohorts = array();

    $typeChange = $CFG->dbtype == 'pgsql'? '::text' : '';

    foreach ($columns as $type => $value) {
        if ($type == "users") {
            $owners_users = array_merge($owners_users, $DB->get_fieldset_sql("SELECT userid FROM {local_intelliboard_assign} WHERE rel = 'external' AND type = 'users' AND instance = :userid", array('userid' => $value)));
            $owners_users = array_merge($owners_users, $DB->get_fieldset_sql("SELECT lia.userid FROM {local_intelliboard_assign} lia
              INNER JOIN {context} ctx ON  ctx.instanceid" . $typeChange . " = lia.instance AND ctx.contextlevel = 50
              INNER JOIN {role_assignments} ra ON ctx.id = ra.contextid
              WHERE ra.userid = ? AND lia.type = 'courses' AND lia.rel = 'external'
            ", array('userid' => $value)));
            $owners_users = array_merge($owners_users, $DB->get_fieldset_sql("SELECT lia.userid FROM {local_intelliboard_assign}  lia
              INNER JOIN {cohort_members} cm ON cm.cohortid" . $typeChange . " = lia.instance
              WHERE cm.userid = ? AND lia.type = 'cohorts' AND lia.rel = 'external'
            ", array('userid' => $value)));

        } elseif ($type == 'courses') {
            $owners_courses = array_merge($owners_courses, $DB->get_fieldset_sql(" SELECT userid FROM {local_intelliboard_assign} WHERE rel = 'external' AND type = 'courses' AND instance = :courseid", array('courseid' => $value)));
        } elseif ($type == 'cohorts') {
            $owners_cohorts = array_merge($owners_cohorts, $DB->get_fieldset_sql(" SELECT userid FROM {local_intelliboard_assign} WHERE rel = 'external' AND type = 'cohorts' AND instance = :cohortid", array('cohortid' => $value)));
        }
    }

    $owners = array_merge($owners_users, $owners_courses, $owners_cohorts);
    $sql = "SELECT userid FROM {local_intelliboard_assign}";

    if ($owners) {
        $sql .= " WHERE rel = 'external' AND userid NOT IN (" . implode(",", $owners) . ")";
    }

    return $DB->get_fieldset_sql($sql);

}

function user_cohorts($userid) {
    global $DB;

    $user = $DB->get_record("user", ["id" => $userid], "*", MUST_EXIST);

    if (has_capability("local/intelliboard:browseallcohorts", context_system::instance(), $user)) {
        return $DB->get_records("cohort", ["visible" => 1], "name");
    }

    return $DB->get_records_sql(
        "SELECT DISTINCT ch.*
           FROM {cohort_members} chm
           JOIN {cohort} ch ON ch.id = chm.cohortid
          WHERE chm.userid = :userid AND visible = 1
       ORDER BY ch.name",
        ["userid" => $userid]
    );
}

function get_intelliboard_filter($id, $dbtype = null)
{
    global $CFG;

    $filters = [
        1 => [
            MYSQL_TYPE => 'REGEXP',
            POSTGRES_TYPE => '~*'
        ]
    ];

    if ($dbtype === null) {
        $dbtype = $CFG->dbtype;
    }

    if (empty($filters[$id])) {
        return null;
    }

    $operator = $filters[$id];

    if (is_array($filters[$id])) {
        if (empty($filters[$id][$dbtype])) {
            $operator = $filters[$id][MYSQL_TYPE];
        } else {
            $operator = $filters[$id][$dbtype];
        }
    }

    if (is_string($operator)) {
        return $operator;
    } else {
       return $operator();
    }
}

function get_operator($id, $value, $params = array(), $dbtype = null)
{
    global $CFG;

    $operators = [
        'TIME_TO_SEC' => array(
            MYSQL_TYPE => 'TIME_TO_SEC',
            POSTGRES_TYPE => function($value, $params) {
                return "extract ('epoch' from TO_TIMESTAMP($value, 'HH24:MI:SS')::TIME)";
            }),
        'SEC_TO_TIME' => array(
            MYSQL_TYPE => 'SEC_TO_TIME',
            POSTGRES_TYPE => ''
        ),
        'GROUP_CONCAT' => array(
            MYSQL_TYPE => function($value, $params = array('separator' => ', ')) {

                if (empty($params['order'])) {
                    $params['order'] = '';
                }

                return "GROUP_CONCAT($value SEPARATOR '" . $params['separator'] . "')";
            },
            POSTGRES_TYPE => function($value, $params = array('separator' => ', ')) {

                if (empty($params['order'])) {
                    $params['order'] = '';
                }

                return "string_agg($value::character varying, '" . $params['separator'] . "')";
            }
        ),
        'WEEKDAY' => array(
            MYSQL_TYPE => 'WEEKDAY',
            POSTGRES_TYPE => function($value, $params) {
                return "extract(dow from $value::timestamp)";
            }
        ),
        'DAYOFWEEK' => array(
            MYSQL_TYPE => function($value, $params) {
                return "(DAYOFWEEK($value) - 1)";
            },
            POSTGRES_TYPE => function($value, $params) {
                return "EXTRACT(DOW FROM $value)";
            }
        ),
        'DATE_FORMAT_A' => array(
            MYSQL_TYPE => function($value, $params) {
                return "DATE_FORMAT($value, '%a')";
            },
            POSTGRES_TYPE => function($value, $params) {
                return "to_char($value, 'Day')";
            }
        ),
        'FROM_UNIXTIME' => array(
            MYSQL_TYPE => function($value, $params = array()) {

                $format = isset($params['format'])? $params['format'] : '%Y-%m-%d %T';
                return "FROM_UNIXTIME($value, '$format')";
            },
            POSTGRES_TYPE => "to_timestamp"
        ),
        'MONTH' => array(
            MYSQL_TYPE => "MONTH",
            POSTGRES_TYPE => function($value, $params) {
                return "date_part('month', $value)";
            }
        ),
        'INSERT' => array(
            MYSQL_TYPE => function($value, $params) {
                $sentence = $params['sentence'];
                $position = isset($params['position'])? $params['position'] : 1;
                $length   = isset($params['length'])? $params['length'] : "CHAR_LENGTH($value)";

                return "INSERT($sentence, $position, $length, $value)";
            },
            POSTGRES_TYPE => function($value, $params) {
                $sentence = $params['sentence'];
                $position = isset($params['position'])? $params['position'] : 1;
                $length   = isset($params['length'])? $params['length'] : "CHAR_LENGTH($value)";

                return "OVERLAY($sentence placing $value from $position for $length)";
            }
        ),
        'DAY' => array(
            MYSQL_TYPE => 'DAY',
            POSTGRES_TYPE => function($value, $params) {
                return "date_part('day', $value)";
            }
        ),
        'YEAR' => array(
            MYSQL_TYPE => 'YEAR',
            POSTGRES_TYPE => function($value, $params) {
                return "date_part('year', $value)";
            }
        )
    ];

    if ($dbtype === null) {
        $dbtype = $CFG->dbtype;
    }

    if (empty($operators[$id])) {
        return null;
    }

    $operator = $operators[$id];

    if (is_array($operators[$id])) {
        if (empty($operators[$id][$dbtype])) {
            $operator = $operators[$id][MYSQL_TYPE];
        } else {
            $operator = $operators[$id][$dbtype];
        }
    }

    if (is_string($operator)) {
        $value = $operator . '(' . $value . ')';
    } else {
        $value = $operator($value, $params);
    }

    return $value;
}

function intellitext($val = '') {
    return addslashes(
        preg_replace('~[\r\n]+~', '', $val)
    );
}

/**
 * Get date format for plugin
 *
 * @return mixed|string
 * @throws dml_exception
 */
function intelli_date_format() {
    $format = get_config('local_intelliboard', 'date_format');

    return $format ? $format : 'm/d/Y';
}

/**
 * Format timestamp
 *
 * @param int $date timestamp
 * @return false|string
 * @throws dml_exception
 */
function intelli_date($date) {
    if (is_null($date) OR intval($date) == 0) {
        return '';
    }
    return date(intelli_date_format(), $date);
}

function intelli_lms_admins() {
    global $CFG, $DB;

    $adminsids = explode(',', $CFG->siteadmins);

    if (!$adminsids) {
        $adminsids = ["-1"];
    }

    list($insql, $inparams) = $DB->get_in_or_equal($adminsids);

    return $DB->get_records_sql(
        "SELECT id, firstname, lastname, email
           FROM {user}
          WHERE id {$insql}",
        $inparams
    );
}

function intelli_initial_reports() {
    $reports = [];
    if (get_config('local_intelliboard', 'adm_dshb_report_user_status')) {
        $reports[] = ["id" => 1, "class" => \local_intelliboard\output\tables\initial_reports\report1::class];
    }
    if (get_config('local_intelliboard', 'adm_dshb_report_activity_stats_summary')) {
        $reports[] = ["id" => 3, "class" => \local_intelliboard\output\tables\initial_reports\report3::class];
    }
    if (get_config('local_intelliboard', 'adm_dshb_report_quiz_activity_detail')) {
        $reports[] = ["id" => 45, "class" => \local_intelliboard\output\tables\initial_reports\report45::class];
    }

    foreach ($reports as &$report) {
        $url = new \moodle_url("/local/intelliboard/initial_report.php", ["id" => $report["id"]]);
        $report["url"] = $url->out();
        $report["name"] = get_string("report{$report["id"]}_name", "local_intelliboard");
    }

    return $reports;
}

function intelli_additional_query_params() {
    return ['lang' => current_language()];
}
