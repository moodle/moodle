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

$courses = enrol_get_my_courses();

$options = array();
$options['courses'] = $options['quizes'] = array();
foreach($courses as $course){
    $options['courses'][$course->id] = $course->fullname;
}

$quizes = array();
if($courseid != 0){
    $vars = (object)array('courseid'=>$courseid);
    $quizes = $plugin->get_quizes($vars);

    foreach($quizes['data'] as $quiz){
        $options['quizes'][$quiz->id] = $quiz->name;
    }
}

echo html_writer::start_div('intelliboard-content');
echo html_writer::start_div('reports-header clearfix');
echo html_writer::tag('h3',get_string('analityc_3_name','local_intelliboard'));

if($courseid == 0){
    echo html_writer::start_tag('form',array('action'=>$PAGE->url,'class'=>'intelliboard-report-filter'));
    echo html_writer::empty_tag('input',array('type'=>'hidden','value'=>$id,'name'=>'id'));
    echo html_writer::select($options['courses'], 'courseid', '', $nothing = array('' => get_string('select_course','local_intelliboard')), array('onchange'=>'this.form.submit()'));
    echo html_writer::end_tag('form');
    echo html_writer::end_div();

    echo $OUTPUT->box(get_string('enter_course_and_quiz','local_intelliboard'), 'alert alert-info alert-block');
}elseif($courseid != 0 && empty($options['quizes'])){
    echo html_writer::start_tag('form',array('action'=>$PAGE->url,'class'=>'intelliboard-report-filter'));
    echo html_writer::empty_tag('input',array('type'=>'hidden','value'=>$id,'name'=>'id'));
    echo html_writer::select($options['courses'], 'courseid', $courseid, $nothing = array('' => get_string('select_course','local_intelliboard')), array('onchange'=>'this.form.submit()'));
    echo html_writer::end_tag('form');
    echo html_writer::end_div();

    echo $OUTPUT->box(get_string('not_quiz','local_intelliboard'), 'alert alert-warning alert-block');
}elseif($courseid != 0 && !isset($options['quizes'][$custom])){
    echo html_writer::start_tag('form',array('action'=>$PAGE->url,'class'=>'intelliboard-report-filter'));
    echo html_writer::empty_tag('input',array('type'=>'hidden','value'=>$id,'name'=>'id'));
    echo html_writer::select($options['courses'], 'courseid', $courseid, $nothing = array('' => get_string('select_course','local_intelliboard')), array('onchange'=>'this.form.submit()'));
    echo html_writer::select($options['quizes'], 'custom', '', $nothing = array('' => get_string('select_quiz','local_intelliboard')), array('onchange'=>'this.form.submit()'));
    echo html_writer::end_tag('form');
    echo html_writer::end_div();

    echo $OUTPUT->box(get_string('enter_quiz','local_intelliboard'), 'alert alert-info alert-block');
}else{
    $url = clone $PAGE->url;
    $url->param('custom',$custom);
    $url->param('courseid', $courseid);
    $url->param('id', $id);

    $vars = (object)array('start'=> 0,'length'=> -1,'order_column'=> 0,'order_dir'=> "asc", 'custom'=>$custom, 'courseid'=>$courseid);
    $data = (object)$plugin->analytic3($vars);

    $correct = $incorrect = 0;
    foreach($data->data as $item){
        $correct += $item->rightanswer;
        $incorrect += $item->allanswer-$item->rightanswer;
    }

    $correct_str = get_string('correct','local_intelliboard');
    $incorrect_str = get_string('incorrect','local_intelliboard');

    $barchart_data = array();
    $count = 0;
    for($i=0;$i<=6;$i++){
        for($j=1;$j<=4;$j++){
            foreach($data->time as $item){
                if($item->day == $i && $item->time_of_day == $j){
                    $count = $item->count;
                    break;
                }
            }
            $barchart_data[$i][$j] = $count;
            //$barchart_data .= "d$j.push([". $t .",".$count."]);";
            $count = 0;
        }
    }
    $data_str = "['Day'";
    for($j=1;$j<=4;$j++){
        $data_str .= ",'".get_string('time_'.$j,'local_intelliboard')."'";
    }
    $data_str .= '],';

    for($i=-1;$i<6;$i++){
        $t = ($i == -1)?6:$i;
        $data_str .= "['".get_string('weekday_'.$t,'local_intelliboard')."'";
        for($j=1;$j<=4;$j++){
            $data_str .= ",".$barchart_data[$t][$j];
        }
        $data_str .= '],';
    }

    $grades = array();
    $max_users = 0;
    foreach($data->grades as $grade){
        $grades[$grade->quiz_id]['quiz_name'] = $grade->quiz_name;
        $grades[$grade->quiz_id]['gradepass'] = $grade->gradepass;
        $grades[$grade->quiz_id]['data'][] = array('users'=>$grade->users,'grade'=>$grade->grade);
        $max_users = ($max_users < $grade->users)?$grade->users:$max_users;
    }
    $data_grades_str = "['Grade','users',{type: 'string', role: 'tooltip', 'p': {'html': true}}],";
    $data_grades_str_pass = '';
    $data_grades_quiz = '';
    foreach($grades as $id_notused=>$item){
        foreach($item['data'] as $value){
            $title = "<strong>".$item['quiz_name']."</strong><br>".$value['users']." ".get_string('users','local_intelliboard').": ".(int)$value['grade']."%";
            $data_grades_str .= "[".(int)$value['grade'].','.$value['users'].',"'.$title.'"],';
        }
        $data_grades_quiz = $item['quiz_name'];
        /*if($item['gradepass']>0){
            $data_grades_str_pass = "[1,".$item['gradepass']."],[".$max_users.",".$item['gradepass']."]";
        }*/
    }

    $header = array(get_string('name','local_intelliboard'),get_string('answers','local_intelliboard'),get_string('correct','local_intelliboard'),get_string('incorrect','local_intelliboard'));
    $body = array();

    foreach($data->data as $item){
        $row = array();
        $row[] = $item->name;
        $row[] = $item->allanswer;
        $row[] = array('class'=> 'rightanswer', 'value'=>$item->rightanswer,'width'=>($item->rightanswer/$item->allanswer)*100);
        $row[] = array('class'=> 'incorrectanswer', 'value'=>$item->allanswer - $item->rightanswer,'width'=>(($item->allanswer - $item->rightanswer)/$item->allanswer)*100);
        $body[] = $row;
    }

    $data_table = (object)array('header'=>$header,'body'=>$body);


    echo html_writer::start_tag('form',array('action'=>$PAGE->url,'class'=>'intelliboard-report-filter'));
    echo html_writer::empty_tag('input',array('type'=>'hidden','value'=>$id,'name'=>'id'));
    echo html_writer::select($options['courses'], 'courseid', $courseid, $nothing = array('' => get_string('select_course','local_intelliboard')), array('onchange'=>'this.form.submit()'));
    echo html_writer::select($options['quizes'], 'custom', $custom, $nothing = array('' => get_string('select_quiz','local_intelliboard')), array('onchange'=>'this.form.submit()'));
    echo html_writer::end_tag('form');
    echo html_writer::end_div();

    echo html_writer::start_div('clearfix');
    echo html_writer::start_div('head-box clearfix');
    echo html_writer::start_div('title-box');
    echo html_writer::tag('h4',get_string('course_name_a','local_intelliboard',$options['courses'][$courseid]));
    echo html_writer::tag('h4',get_string('quiz_name_a','local_intelliboard',$options['quizes'][$custom]));
    echo html_writer::end_div();
    echo html_writer::start_div('export-box');
    $url->param('action','export_pdf');
    echo html_writer::link($url,get_string('pdf','local_intelliboard'),array('class'=>'btn export'));
    $url->param('action','export_csv');
    echo html_writer::link($url,get_string('csv','local_intelliboard'),array('class'=>'btn'));
    $url->param('action','export_excel');
    echo html_writer::link($url,get_string('excel','local_intelliboard'),array('class'=>'btn export'));
    echo html_writer::end_div();
    echo html_writer::end_div();

    echo "<div class='pie-box clearfix'>
            <div class='pie-chart-box clearfix'>
                <div class='chart-box'>
                    <h4>".get_string('cor_incor_answers','local_intelliboard')."</h4>
                    <div id='pie-3' class='chart'></div>
                </div>
                <div class='chart-box'>
                    <h4>".get_string('quiz_finished','local_intelliboard')."</h4>
                    <div id='barchart-3-1' class='chart'></div>
                </div>
            </div>
            <div class='chart-box'>
                <h4>".get_string('quiz_grades','local_intelliboard')."</h4>
                <div id='linechart-3-2' class='chart'></div>
            </div>            
        </div>";

    echo "<div class='info-box'>
			<h4>".get_string('ques_breakdown','local_intelliboard')."</h4>";
    echo "<table id='questionTable' class='generaltable'><thead><tr>";
    foreach($data_table->header as $item){
        echo "<th>$item</th>";
    }
    echo "</tr></thead><tbody>";
    foreach($data_table->body as $row){
        echo '<tr>';
        foreach($row as $item){
            if(is_array($item)){
                echo "<td class='" . $item['class'] . "'  data-width='" . $item['width'] . "'>" . $item['value'] . "</td>";
            }else{
                echo "<td>$item</td>";
            }
        }
        echo '</tr>';
    }
    echo "</tbody></table></div>";

    echo html_writer::end_div();
}

echo html_writer::end_div();
?>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <script>
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);
        var charts = [];
        function drawChart() {

            var data = google.visualization.arrayToDataTable([
                ['<?php echo get_string('name');?>', '<?php echo get_string('answers','local_intelliboard');?>'],
                ['<?php echo $correct_str;?>', <?php echo $correct;?>],
                ['<?php echo $incorrect_str;?>', <?php echo $incorrect;?>]
            ]);

            var options = {
                legend: { position: 'none'},
                chartArea: {width:'80%',height:'80%'}
            };

            charts['pie'] = new google.visualization.PieChart(document.getElementById('pie-3'));

            charts['pie'].draw(data, options);

            var data = google.visualization.arrayToDataTable([<?php echo $data_str?>]);

            var options = {
                legend: { position: 'top'},
                bar: { groupWidth: '75%' },
                isStacked: true,
                chartArea: {width:'95%',height:'85%'},
                vAxis:{textPosition:'in'}
            };

            charts['barchart'] = new google.visualization.ColumnChart(document.getElementById('barchart-3-1'));

            charts['barchart'].draw(data, options);

            var data = google.visualization.arrayToDataTable([<?php echo $data_grades_str?>]);

            var options = {
                legend: { position: 'none'},
                hAxis:{maxValue:100, minValue:0, format: '#\'%\'',baselineColor: '#ccc'},
                vAxis:{minValue:1,textPosition:'in',viewWindow: { min: 1},baselineColor: '#ccc'},
                chartArea: {width:'95%',height:'80%'},
                tooltip:{isHtml:true,textStyle:{fontSize: 12}},
            };

            charts['linechart'] = new google.visualization.LineChart(document.getElementById('linechart-3-2'));

            charts['linechart'].draw(data, options);
        }


        function correct_incorrect_table_color(){
            jQuery('#questionTable td.rightanswer').each(function(){
                var width = ($(this).innerWidth()*$(this).attr('data-width'))/100;
                $(this).css('box-shadow',width+'px 0 0 0 #4caf50 inset');
            });
            jQuery('#questionTable td.incorrectanswer').each(function(){
                var width = ($(this).innerWidth()*$(this).attr('data-width'))/100;
                $(this).css('box-shadow',width+'px 0 0 0 #ef5350 inset');
            });
        }
        correct_incorrect_table_color();
        jQuery(window).resize(function() {
            correct_incorrect_table_color();
        });

        jQuery('.export-box .btn.export').click(function (e) {
            e.preventDefault();
            var example = Y.one(this);
            var spinner = M.util.add_spinner(Y, example);
            spinner.show();

            var action = jQuery(this).attr('href');
            jQuery('<form action="'+action+'" method="POST" style="display:none;"><textarea name="images[pie]">'+charts['pie'].getImageURI()+'</textarea><textarea name="images[barchart]">'+charts['barchart'].getImageURI()+'</textarea><textarea name="images[linechart]">'+charts['linechart'].getImageURI()+'</textarea></form>').appendTo('body').submit().remove();
            spinner.hide();
        });
    </script>
<?php
