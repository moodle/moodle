<?PHP // $Id$

    require("../../config.php");
    require("$CFG->libdir/graphlib.php");
    require("lib.php");

    require_variable($id);    // Course Module ID

    if (! $cm = get_record("course_modules", "id", $id)) {
        error("Course Module ID was incorrect");
    }

    if (! $course = get_record("course", "id", $cm->course)) {
        error("Course is misconfigured");
    }

    require_login($course->id);

    if (!isteacher($course->id)) {
        error("Sorry, only teachers can see this.");
    }

    if (! $survey = get_record("survey", "id", $cm->instance)) {
        error("Survey ID was incorrect");
    }

    switch ($type) {

     case "question.png":

       $question = get_record("survey_questions", "id", $qid);
  
       $options = explode(",",$question->options);

       while (list($key,) = each($options)) {
           $buckets1[$key] = 0;
           $buckets2[$key] = 0;
       }

       $aa = $db->Execute("SELECT * FROM survey_answers WHERE survey = $cm->instance AND question = $qid");

       while (!$aa->EOF) {
           if ($a1 = $aa->fields["answer1"]) {
               $buckets1[$a1 - 1]++;
           }
           if ($a2 = $aa->fields["answer2"]) {
               $buckets2[$a2 - 1]++;
           }
           $aa->MoveNext();
       }
       
       $maxbuckets1 = max($buckets1);
       $maxbuckets2 = max($buckets2);
       $maxbuckets = ($maxbuckets1 > $maxbuckets2) ? $maxbuckets1 : $maxbuckets2;

       $graph = new graph($GWIDTH,$GHEIGHT);
       $graph->parameter['title'] = "$question->text";

       $graph->x_data               = $options;

       $graph->y_data['answers1']   = $buckets1;
       $graph->y_format['answers1'] = array('colour' => 'blue','bar' => 'fill','legend' =>'actual','bar_size' => 0.4);
       $graph->y_data['answers2']   = $buckets2;
       $graph->y_format['answers2'] = array('colour'=>'green','bar' => 'fill','legend' =>'preferred','bar_size' => 0.2);

       $graph->parameter['legend']        = 'outside-top';
       $graph->parameter['legend_border'] = 'black';
       $graph->parameter['legend_offset'] = 4;

       if (($maxbuckets1 > 0.0) && ($maxbuckets2 > 0.0)) {
           $graph->y_order = array('answers1', 'answers2');
       } else if ($maxbuckets1 > 0.0) {
           $graph->y_order = array('answers1');
       } else {
           $graph->y_order = array('answers2');
       }
       
       $graph->parameter['y_axis_gridlines']= $maxbuckets + 1;
       $graph->parameter['y_resolution_left']= 1;
       $graph->parameter['y_decimal_left']  = 0;
       $graph->parameter['x_axis_angle']    = 0;
       $graph->parameter['shadow']          = 'none';

       $graph->draw_stack();

       break;



     case "multiquestion.png":

       $question  = get_record("survey_questions", "id", $qid);

       $options = explode(",",$question->options);
       $questionorder = explode( ",", $question->multi);

       $qqq = get_records_sql("SELECT * FROM survey_questions WHERE id in ($question->multi)");

       foreach ($questionorder as $i => $val) {
           $names[$i] = shorten_name($qqq["$val"]->text, 4);
           $buckets1[$i] = 0;
           $buckets2[$i] = 0;
           $count1[$i] = 0;
           $count2[$i] = 0;
           $indexof[$val] = $i;
       }

       $aaa = get_records_sql("SELECT * FROM survey_answers WHERE ((survey = $cm->instance) AND (question in ($question->multi)))");

       foreach ($aaa as $a) {
           $index = $indexof[$a->question];
           if ($a->answer1) {
               $buckets1[$index] += $a->answer1;
               $count1[$index]++;
           }
           if ($a->answer2) {
               $buckets2[$index] += $a->answer2;
               $count2[$index]++;
           }
       }

       foreach ($questionorder as $i => $val) {
           if ($count1[$i]) {
               $buckets1[$i] = (float)$buckets1[$i] / (float)$count1[$i];
           }
           if ($count2[$i]) {
               $buckets2[$i] = (float)$buckets2[$i] / (float)$count2[$i];
           }
       }

       foreach ($aaa as $a) {
           $index = $indexof[$a->question];
           if ($a->answer1) {
               $difference = (float) ($a->answer1 - $buckets1[$index]);
               $stdev1[$index] += ($difference * $difference);
           }
           if ($a->answer2) {
               $difference = (float) ($a->answer2 - $buckets2[$index]);
               $stdev2[$index] += ($difference * $difference);
           }
       }

       foreach ($questionorder as $i => $val) {
           if ($count1[$i]) {
               $stdev1[$i] = sqrt( (float)$stdev1[$i] / ((float)$count1[$i]));
           }
           if ($count2[$i]) {
               $stdev2[$i] = sqrt( (float)$stdev2[$i] / ((float)$count2[$i]));
           }
       }

       

       $maxbuckets1 = max($buckets1);
       $maxbuckets2 = max($buckets2);


       $graph = new graph($GWIDTH,$GHEIGHT);
       $graph->parameter['title'] = "$question->text";

       $graph->x_data               = $names;
       $graph->y_data['answers1']   = $buckets1;
       $graph->y_format['answers1'] = array('colour' => 'ltblue', 'line' => 'line',  'point' => 'square', 
                                            'shadow_offset' => 4, 'legend' => 'actual');
       $graph->y_data['answers2']   = $buckets2;
       $graph->y_format['answers2'] = array('colour' => 'ltgreen', 'line' => 'line', 'point' => 'square', 
                                                'shadow_offset' => 4, 'legend' => 'preferred');
       $graph->y_data['stdev1']   = $stdev1;
       $graph->y_format['stdev1'] = array('colour' => 'ltltblue', 'bar' => 'fill', 
                                            'shadow_offset' => '4', 'legend' => 'none');
       $graph->y_data['stdev2']   = $stdev2;
       $graph->y_format['stdev2'] = array('colour' => 'ltltgreen', 'bar' => 'fill', 
                                            'shadow_offset' => '4', 'legend' => 'none');
       $graph->offset_relation['stdev1'] = 'answers1';
       $graph->offset_relation['stdev2'] = 'answers2';

       $graph->parameter['bar_size']    = 0.15;

       $graph->parameter['legend']        = 'outside-top';
       $graph->parameter['legend_border'] = 'black';
       $graph->parameter['legend_offset'] = 4;

       if (($maxbuckets1 > 0.0) && ($maxbuckets2 > 0.0)) {
              $graph->y_order = array('stdev1', 'answers1', 'stdev2', 'answers2');
       } else if ($maxbuckets1 > 0.0) {
           $graph->y_order = array('stdev1', 'answers1');
       } else {
           $graph->y_order = array('stdev2', 'answers2');
       }
       
       $graph->parameter['y_max_left']= count($options);
       $graph->parameter['y_axis_gridlines']= count($options) + 1;
       $graph->parameter['y_resolution_left']= 1;
       $graph->parameter['y_decimal_left']= 1;
       $graph->parameter['x_axis_angle']  = 0;

       $graph->draw();

       break;


    
     case "overall.png":

       $qqq = get_records_sql("SELECT * FROM survey_questions WHERE id in ($survey->questions) AND multi <> ''");

       foreach ($qqq as $qq) {
           if ($qq->type < 0) {
               $virtualscales = true;
           }
       }
       foreach ($qqq as $qq) {         // if any virtual, then use JUST virtual, else use JUST nonvirtual
           if ($virtualscales && $qq->type < 0) {
               $question[] = $qq;
           } else if (!$virtualscales && $qq->type > 0) {
               $question[] = $qq;
           }
       }
       $numquestions = count($question);

       $options = explode(",",$question[0]->options);
       $numoptions = count($options);

       for ($i=0; $i<$numquestions; $i++) {
           $names[$i] = $question[$i]->text;
           $buckets1[$i] = 0.0;
           $buckets2[$i] = 0.0;
           $stdev1[$i] = 0.0;
           $stdev2[$i] = 0.0;
           $count1[$i] = 0;
           $count2[$i] = 0;
           $subquestions = $question[$i]->multi;   // otherwise next line doesn't work
           $aaa = get_records_sql("SELECT * FROM survey_answers WHERE ((survey = $cm->instance) AND (question in ($subquestions)))");

           foreach ($aaa as $a) {
               if ($a->answer1) {
                   $buckets1[$i] += $a->answer1;
                   $count1[$i]++;
               }
               if ($a->answer2) {
                   $buckets2[$i] += $a->answer2;
                   $count2[$i]++;
               }
           }

           if ($count1[$i]) {
               $buckets1[$i] = (float)$buckets1[$i] / (float)$count1[$i];
           }
           if ($count2[$i]) {
               $buckets2[$i] = (float)$buckets2[$i] / (float)$count2[$i];
           }

           // Calculate the standard devaiations
           foreach ($aaa as $a) {
               if ($a->answer1) {
                   $difference = (float) ($a->answer1 - $buckets1[$i]);
                   $stdev1[$i] += ($difference * $difference);
               }
               if ($a->answer2) {
                   $difference = (float) ($a->answer2 - $buckets2[$i]);
                   $stdev2[$i] += ($difference * $difference);
               }
           }

           if ($count1[$i]) {
               $stdev1[$i] = sqrt( (float)$stdev1[$i] / ((float)$count1[$i]));
           }
           if ($count2[$i]) {
               $stdev2[$i] = sqrt( (float)$stdev2[$i] / ((float)$count2[$i]));
           }

           
       }

       $maxbuckets1 = max($buckets1);
       $maxbuckets2 = max($buckets2);


       $graph = new graph($GWIDTH,$GHEIGHT);
       $graph->parameter['title'] = "$survey->name";

       $graph->x_data               = $names;

       $graph->y_data['answers1']   = $buckets1;
       $graph->y_format['answers1'] = array('colour' => 'ltblue', 'line' => 'line',  'point' => 'square', 
                                            'shadow_offset' => 4, 'legend' => 'actual');
       $graph->y_data['answers2']   = $buckets2;
       $graph->y_format['answers2'] = array('colour' => 'ltgreen', 'line' => 'line', 'point' => 'square', 
                                                'shadow_offset' => 4, 'legend' => 'preferred');

       $graph->y_data['stdev1']   = $stdev1;
       $graph->y_format['stdev1'] = array('colour' => 'ltltblue', 'bar' => 'fill', 
                                            'shadow_offset' => '4', 'legend' => 'none');
       $graph->y_data['stdev2']   = $stdev2;
       $graph->y_format['stdev2'] = array('colour' => 'ltltgreen', 'bar' => 'fill', 
                                            'shadow_offset' => '4', 'legend' => 'none');
       $graph->offset_relation['stdev1'] = 'answers1';
       $graph->offset_relation['stdev2'] = 'answers2';

       $graph->parameter['bar_size']    = 0.15;
       $graph->parameter['legend']        = 'outside-top';
       $graph->parameter['legend_border'] = 'black';
       $graph->parameter['legend_offset'] = 4;

       if (($maxbuckets1 > 0.0) && ($maxbuckets2 > 0.0)) {
              $graph->y_order = array('stdev1', 'answers1', 'stdev2', 'answers2');
       } else if ($maxbuckets1 > 0.0) {
           $graph->y_order = array('stdev1', 'answers1');
       } else {
           $graph->y_order = array('stdev2', 'answers2');
       }
       
       $graph->parameter['y_max_left']= $numoptions;
       $graph->parameter['y_axis_gridlines']= $numoptions + 1;
       $graph->parameter['y_resolution_left']= 1;
       $graph->parameter['y_decimal_left']= 1;
       $graph->parameter['x_axis_angle']  = 0;

       $graph->draw();

       break;



     case "student.png":

       $qqq = get_records_sql("SELECT * FROM survey_questions WHERE id in ($survey->questions) AND multi <> ''");

       foreach ($qqq as $qq) {
           if ($qq->type < 0) {
               $virtualscales = true;
           }
       }
       foreach ($qqq as $qq) {         // if any virtual, then use JUST virtual, else use JUST nonvirtual
           if ($virtualscales && $qq->type < 0) {
               $question[] = $qq;
           } else if (!$virtualscales && $qq->type > 0) {
               $question[] = $qq;
           }
       }
       $numquestions= count($question);

       $options = explode(",",$question[0]->options);
       $numoptions = count($options);

       for ($i=0; $i<$numquestions; $i++) {
           $names[$i] = $question[$i]->text;
           $buckets1[$i] = 0.0;
           $buckets2[$i] = 0.0;
           $count1[$i] = 0;
           $count2[$i] = 0;
           $studbuckets1[$i] = 0.0;
           $studbuckets2[$i] = 0.0;
           $studcount1[$i] = 0;
           $studcount2[$i] = 0;
           $subquestions = $question[$i]->multi;   // otherwise next line doesn't work
           $aaa = get_records_sql("SELECT * FROM survey_answers WHERE ((survey = $cm->instance) AND (question in ($subquestions)))");

           foreach ($aaa as $a) {
               if ($a->user == $sid) {
                   if ($a->answer1) {
                       $studbuckets1[$i] += $a->answer1;
                       $studcount1[$i]++;
                   }
                   if ($a->answer2) {
                       $studbuckets2[$i] += $a->answer2;
                       $studcount2[$i]++;
                   }
               }
               if ($a->answer1) {
                   $buckets1[$i] += $a->answer1;
                   $count1[$i]++;
               }
               if ($a->answer2) {
                   $buckets2[$i] += $a->answer2;
                   $count2[$i]++;
               }
           }

           if ($count1[$i]) {
               $buckets1[$i] = (float)$buckets1[$i] / (float)$count1[$i];
           }
           if ($count2[$i]) {
               $buckets2[$i] = (float)$buckets2[$i] / (float)$count2[$i];
           }
           if ($studcount1[$i]) {
               $studbuckets1[$i] = (float)$studbuckets1[$i] / (float)$studcount1[$i];
           }
           if ($studcount2[$i]) {
               $studbuckets2[$i] = (float)$studbuckets2[$i] / (float)$studcount2[$i];
           }

           // Calculate the standard devaiations
           foreach ($aaa as $a) {
               if ($a->answer1) {
                   $difference = (float) ($a->answer1 - $buckets1[$i]);
                   $stdev1[$i] += ($difference * $difference);
               }
               if ($a->answer2) {
                   $difference = (float) ($a->answer2 - $buckets2[$i]);
                   $stdev2[$i] += ($difference * $difference);
               }
           }

           if ($count1[$i]) {
               $stdev1[$i] = sqrt( (float)$stdev1[$i] / ((float)$count1[$i]));
           }
           if ($count2[$i]) {
               $stdev2[$i] = sqrt( (float)$stdev2[$i] / ((float)$count2[$i]));
           }

       }

       $maxbuckets1 = max($buckets1);
       $maxbuckets2 = max($buckets2);


       $graph = new graph($GWIDTH,$GHEIGHT);
       $graph->parameter['title'] = "$survey->name";

       $graph->x_data               = $names;

       $graph->y_data['answers1']   = $buckets1;
       $graph->y_format['answers1'] = array('colour' => 'ltblue', 'line' => 'line',  'point' => 'square', 
                                            'shadow_offset' => 0.1, 'legend' => 'class actual');
       $graph->y_data['answers2']   = $buckets2;
       $graph->y_format['answers2'] = array('colour' => 'ltgreen', 'line' => 'line', 'point' => 'square', 
                                                'shadow_offset' => 0.1, 'legend' => 'class preferred');
       $graph->y_data['studanswers1']   = $studbuckets1;
       $graph->y_format['studanswers1'] = array('colour' => 'blue', 'line' => 'line',  'point' => 'square', 
                                            'shadow_offset' => 4, 'legend' => 'student actual');
       $graph->y_data['studanswers2']   = $studbuckets2;
       $graph->y_format['studanswers2'] = array('colour' => 'green', 'line' => 'line', 'point' => 'square', 
                                                'shadow_offset' => 4, 'legend' => 'student preferred');
       $graph->y_data['stdev1']   = $stdev1;
       $graph->y_format['stdev1'] = array('colour' => 'ltltblue', 'bar' => 'fill', 
                                            'shadow_offset' => 0.1, 'legend' => 'none');
       $graph->y_data['stdev2']   = $stdev2;
       $graph->y_format['stdev2'] = array('colour' => 'ltltgreen', 'bar' => 'fill', 
                                            'shadow_offset' => 0.1, 'legend' => 'none');
       $graph->offset_relation['stdev1'] = 'answers1';
       $graph->offset_relation['stdev2'] = 'answers2';

       $graph->parameter['bar_size']    = 0.15;

       $graph->parameter['legend']        = 'outside-top';
       $graph->parameter['legend_border'] = 'black';
       $graph->parameter['legend_offset'] = 4;

       if (($maxbuckets1 > 0.0) && ($maxbuckets2 > 0.0)) {
              $graph->y_order = array('stdev1', 'stdev2', 'answers1', 'answers2', 'studanswers1', 'studanswers2');
       } else if ($maxbuckets1 > 0.0) {
           $graph->y_order = array('stdev1', 'answers1', 'studanswers1');
       } else {
           $graph->y_order = array('stdev2', 'answers2', 'studanswers2');
       }
       
       $graph->parameter['y_max_left']= $numoptions;
       $graph->parameter['y_axis_gridlines']= $numoptions + 1;
       $graph->parameter['y_resolution_left']= 1;
       $graph->parameter['y_decimal_left']= 1;
       $graph->parameter['x_axis_angle']  = 0;

       $graph->draw();
       break;



     case "studentmultiquestion.png":

       $question  = get_record("survey_questions", "id", $qid);

       $options = explode(",",$question->options);
       $questionorder = explode( ",", $question->multi);

       $qqq = get_records_sql("SELECT * FROM survey_questions WHERE id in ($question->multi)");

       foreach ($questionorder as $i => $val) {
           $names[$i] = shorten_name($qqq["$val"]->text, 4);
           $buckets1[$i] = 0;
           $buckets2[$i] = 0;
           $count1[$i] = 0;
           $count2[$i] = 0;
           $indexof[$val] = $i;
           $studbuckets1[$i] = 0.0;
           $studbuckets2[$i] = 0.0;
           $studcount1[$i] = 0;
           $studcount2[$i] = 0;
       }

       $aaa = get_records_sql("SELECT * FROM survey_answers WHERE ((survey = $cm->instance) AND (question in ($question->multi)))");

       foreach ($aaa as $a) {
           $index = $indexof[$a->question];
               if ($a->user == $sid) {
                   if ($a->answer1) {
                       $studbuckets1[$index] += $a->answer1;
                       $studcount1[$index]++;
                   }
                   if ($a->answer2) {
                       $studbuckets2[$index] += $a->answer2;
                       $studcount2[$index]++;
                   }
               }
           if ($a->answer1) {
               $buckets1[$index] += $a->answer1;
               $count1[$index]++;
           }
           if ($a->answer2) {
               $buckets2[$index] += $a->answer2;
               $count2[$index]++;
           }
       }

       foreach ($questionorder as $i => $val) {
           if ($count1[$i]) {
               $buckets1[$i] = (float)$buckets1[$i] / (float)$count1[$i];
           }
           if ($count2[$i]) {
               $buckets2[$i] = (float)$buckets2[$i] / (float)$count2[$i];
           }
           if ($studcount1[$i]) {
               $studbuckets1[$i] = (float)$studbuckets1[$i] / (float)$studcount1[$i];
           }
           if ($studcount2[$i]) {
               $studbuckets2[$i] = (float)$studbuckets2[$i] / (float)$studcount2[$i];
           }
       }

       foreach ($aaa as $a) {
           $index = $indexof[$a->question];
           if ($a->answer1) {
               $difference = (float) ($a->answer1 - $buckets1[$index]);
               $stdev1[$index] += ($difference * $difference);
           }
           if ($a->answer2) {
               $difference = (float) ($a->answer2 - $buckets2[$index]);
               $stdev2[$index] += ($difference * $difference);
           }
       }

       foreach ($questionorder as $i => $val) {
           if ($count1[$i]) {
               $stdev1[$i] = sqrt( (float)$stdev1[$i] / ((float)$count1[$i]));
           }
           if ($count2[$i]) {
               $stdev2[$i] = sqrt( (float)$stdev2[$i] / ((float)$count2[$i]));
           }
       }

       

       $maxbuckets1 = max($buckets1);
       $maxbuckets2 = max($buckets2);


       $graph = new graph($GWIDTH,$GHEIGHT);
       $graph->parameter['title'] = "$question->text";

       $graph->x_data               = $names;
       $graph->y_data['answers1']   = $buckets1;
       $graph->y_format['answers1'] = array('colour' => 'ltblue', 'line' => 'line',  'point' => 'square', 
                                            'shadow_offset' => 0.1, 'legend' => 'class actual');
       $graph->y_data['answers2']   = $buckets2;
       $graph->y_format['answers2'] = array('colour' => 'ltgreen', 'line' => 'line', 'point' => 'square', 
                                                'shadow_offset' => 0.1, 'legend' => 'class preferred');
       $graph->y_data['studanswers1']   = $studbuckets1;
       $graph->y_format['studanswers1'] = array('colour' => 'blue', 'line' => 'line',  'point' => 'square', 
                                            'shadow_offset' => 4, 'legend' => 'student actual');
       $graph->y_data['studanswers2']   = $studbuckets2;
       $graph->y_format['studanswers2'] = array('colour' => 'green', 'line' => 'line', 'point' => 'square', 
                                                'shadow_offset' => 4, 'legend' => 'student preferred');
       $graph->y_data['stdev1']   = $stdev1;
       $graph->y_format['stdev1'] = array('colour' => 'ltltblue', 'bar' => 'fill', 
                                            'shadow_offset' => 0.1, 'legend' => 'none');
       $graph->y_data['stdev2']   = $stdev2;
       $graph->y_format['stdev2'] = array('colour' => 'ltltgreen', 'bar' => 'fill', 
                                            'shadow_offset' => 0.1, 'legend' => 'none');
       $graph->offset_relation['stdev1'] = 'answers1';
       $graph->offset_relation['stdev2'] = 'answers2';

       $graph->parameter['bar_size']    = 0.15;

       $graph->parameter['legend']        = 'outside-top';
       $graph->parameter['legend_border'] = 'black';
       $graph->parameter['legend_offset'] = 4;

       if (($maxbuckets1 > 0.0) && ($maxbuckets2 > 0.0)) {
              $graph->y_order = array('stdev1', 'stdev2', 'answers1', 'answers2', 'studanswers1', 'studanswers2');
       } else if ($maxbuckets1 > 0.0) {
           $graph->y_order = array('stdev1', 'answers1', 'studanswers1');
       } else {
           $graph->y_order = array('stdev2', 'answers2', 'studanswers2');
       }
       
       $graph->parameter['y_max_left']= count($options);
       $graph->parameter['y_axis_gridlines']= count($options) + 1;
       $graph->parameter['y_resolution_left']= 1;
       $graph->parameter['y_decimal_left']= 1;
       $graph->parameter['x_axis_angle']  = 0;

       $graph->draw();

       break;

     default:
       break;
   }

   exit;

function shorten_name ($name, $numwords) {
    $words = explode(" ", $name);
    for ($i=0; $i < $numwords; $i++) {
        $output .= $words[$i]." ";
    }
    return $output;
}
         
?>
