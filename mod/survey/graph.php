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
 * This file is responsible for producing the graph for survey reports
 *
 * @package   mod_survey
 * @copyright 2021 Sujith Haridasan <sujith@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");
require_once("$CFG->libdir/graphlib.php");
require_once("lib.php");

$id = required_param('id', PARAM_INT);    // Course Module ID.
$type = required_param('type', PARAM_FILE);  // Graph Type.
$group = optional_param('group', 0, PARAM_INT);  // Group ID.
$sid = optional_param('sid', false, PARAM_INT);  // Student ID.
$qid = optional_param('qid', 0, PARAM_INT);  // Group ID.

$url = new moodle_url('/mod/survey/graph.php', array('id' => $id, 'type' => $type));
if ($group !== 0) {
    $url->param('group', $group);
}
if ($sid !== false) {
    $url->param('sid', $sid);
}
if ($qid !== 0) {
    $url->param('qid', $qid);
}
$PAGE->set_url($url);

if (!$cm = get_coursemodule_from_id('survey', $id)) {
    throw new \moodle_exception('invalidcoursemodule');
}

if (!$course = $DB->get_record("course", array("id" => $cm->course))) {
    throw new \moodle_exception('coursemisconf');
}

if ($sid) {
    if (!$user = $DB->get_record("user", array("id" => $sid))) {
        throw new \moodle_exception('invaliduserid');
    }
}

require_login($course, false, $cm);

$groupmode = groups_get_activity_groupmode($cm);   // Groups are being used.
$context = context_module::instance($cm->id);

if (!has_capability('mod/survey:readresponses', $context)) {
    if ($type != "student.png" or $sid != $USER->id) {
        throw new \moodle_exception('nopermissiontoshow');
    } else if ($groupmode and !groups_is_member($group)) {
        throw new \moodle_exception('nopermissiontoshow');
    }
}

if (!$survey = $DB->get_record("survey", array("id" => $cm->instance))) {
    throw new \moodle_exception('invalidsurveyid', 'survey');
}

// Check to see if groups are being used in this survey.
if ($group) {
    $users = get_users_by_capability($context, 'mod/survey:participate', '', '', '', '', $group, null, false);
} else if (!empty($cm->groupingid)) {
    $groups = groups_get_all_groups($courseid, 0, $cm->groupingid);
    $groups = array_keys($groups);
    $users = get_users_by_capability($context, 'mod/survey:participate', '', '', '', '', $groups, null, false);
} else {
    $users = get_users_by_capability($context, 'mod/survey:participate', '', '', '', '', '', null, false);
    $group = false;
}

$stractual = get_string("actual", "survey");
$stractualclass = get_string("actualclass", "survey");

$strpreferred = get_string("preferred", "survey");
$strpreferredclass = get_string("preferredclass", "survey");

if ($sid || isset($user)) {
    $stractualstudent = get_string("actualstudent", "survey", fullname($user));
    $strpreferredstudent = get_string("preferredstudent", "survey", fullname($user));
}

$virtualscales = false; // Set default value for case clauses.

switch ($type) {

    case "question.png":

        $question = $DB->get_record("survey_questions", array("id" => $qid));
        $question->text = wordwrap(get_string($question->text, "survey"), SURVEY_QLENGTH_WRAP);
        $question->options = get_string($question->options, "survey");

        $options = explode(",", $question->options);

        foreach ($options as $key => $unused) {
            $buckets1[$key] = 0;
            $buckets2[$key] = 0;
        }

        if ($aaa = $DB->get_records('survey_answers', array('survey' => $cm->instance, 'question' => $qid))) {
            foreach ($aaa as $aa) {
                if (!$group or isset($users[$aa->userid])) {
                    if ($a1 = $aa->answer1) {
                        $buckets1[$a1 - 1]++;
                    }
                    if ($a2 = $aa->answer2) {
                        $buckets2[$a2 - 1]++;
                    }
                }
            }
        }

        $maxbuckets1 = max($buckets1);
        $maxbuckets2 = max($buckets2);
        $maxbuckets = ($maxbuckets1 > $maxbuckets2) ? $maxbuckets1 : $maxbuckets2;

        $graph = new graph($SURVEY_GWIDTH, $SURVEY_GHEIGHT);
        $graph->parameter['title'] = "$question->text";

        $graph->x_data = $options;

        $graph->y_data['answers1'] = $buckets1;
        $graph->y_format['answers1'] = array('colour' => 'ltblue', 'bar' => 'fill', 'legend' => $stractual, 'bar_size' => 0.4);
        $graph->y_data['answers2'] = $buckets2;
        $graph->y_format['answers2'] = array('colour' => 'ltorange', 'bar' => 'fill', 'legend' => $strpreferred, 'bar_size' => 0.2);

        $graph->parameter['legend'] = 'outside-top';
        $graph->parameter['legend_border'] = 'black';
        $graph->parameter['legend_offset'] = 4;

        if (($maxbuckets1 > 0.0) && ($maxbuckets2 > 0.0)) {
            $graph->y_order = array('answers1', 'answers2');
        } else if ($maxbuckets1 > 0.0) {
            $graph->y_order = array('answers1');
        } else {
            $graph->y_order = array('answers2');
        }

        $graph->parameter['y_axis_gridlines'] = $maxbuckets + 1;
        $graph->parameter['y_resolution_left'] = 1;
        $graph->parameter['y_decimal_left'] = 0;
        $graph->parameter['x_axis_angle'] = 20;
        $graph->parameter['shadow'] = 'none';

        $graph->y_tick_labels = null;
        $graph->offset_relation = null;

        $graph->draw_stack();

        break;

    case "multiquestion.png":

        $question = $DB->get_record("survey_questions", array("id" => $qid));
        $question->text = get_string($question->text, "survey");
        $question->options = get_string($question->options, "survey");

        $options = explode(",", $question->options);
        $questionorder = explode(",", $question->multi);

        $qqq = $DB->get_records_list("survey_questions", "id", explode(',', $question->multi));

        foreach ($questionorder as $i => $val) {
            $names[$i] = get_string($qqq["$val"]->shorttext, "survey");
            $buckets1[$i] = 0;
            $buckets2[$i] = 0;
            $count1[$i] = 0;
            $count2[$i] = 0;
            $indexof[$val] = $i;
            $stdev1[$i] = 0;
            $stdev2[$i] = 0;
        }

        $aaa = $DB->get_records_select("survey_answers", "((survey = ?) AND (question in ($question->multi)))",
                array($cm->instance));

        if ($aaa) {
            foreach ($aaa as $a) {
                if (!$group or isset($users[$a->userid])) {
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
            }
        }

        foreach ($questionorder as $i => $val) {
            if ($count1[$i]) {
                $buckets1[$i] = (float) $buckets1[$i] / (float) $count1[$i];
            }
            if ($count2[$i]) {
                $buckets2[$i] = (float) $buckets2[$i] / (float) $count2[$i];
            }
        }

        if ($aaa) {
            foreach ($aaa as $a) {
                if (!$group or isset($users[$a->userid])) {
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
            }
        }

        foreach ($questionorder as $i => $val) {
            if ($count1[$i]) {
                $stdev1[$i] = sqrt((float) $stdev1[$i] / ((float) $count1[$i]));
            }
            if ($count2[$i]) {
                $stdev2[$i] = sqrt((float) $stdev2[$i] / ((float) $count2[$i]));
            }
            $buckets1[$i] = $buckets1[$i] - 1;
            $buckets2[$i] = $buckets2[$i] - 1;
        }

        $maxbuckets1 = max($buckets1);
        $maxbuckets2 = max($buckets2);

        $graph = new graph($SURVEY_GWIDTH, $SURVEY_GHEIGHT);
        $graph->parameter['title'] = "$question->text";

        $graph->x_data = $names;
        $graph->y_data['answers1'] = $buckets1;
        $graph->y_format['answers1'] = array('colour' => 'ltblue', 'line' => 'line', 'point' => 'square',
                'shadow_offset' => 4, 'legend' => $stractual);
        $graph->y_data['answers2'] = $buckets2;
        $graph->y_format['answers2'] = array('colour' => 'ltorange', 'line' => 'line', 'point' => 'square',
                'shadow_offset' => 4, 'legend' => $strpreferred);
        $graph->y_data['stdev1'] = $stdev1;
        $graph->y_format['stdev1'] = array('colour' => 'ltltblue', 'bar' => 'fill',
                'shadow_offset' => '4', 'legend' => 'none', 'bar_size' => 0.3);
        $graph->y_data['stdev2'] = $stdev2;
        $graph->y_format['stdev2'] = array('colour' => 'ltltorange', 'bar' => 'fill',
                'shadow_offset' => '4', 'legend' => 'none', 'bar_size' => 0.2);
        $graph->offset_relation['stdev1'] = 'answers1';
        $graph->offset_relation['stdev2'] = 'answers2';

        $graph->parameter['bar_size'] = 0.15;

        $graph->parameter['legend'] = 'outside-top';
        $graph->parameter['legend_border'] = 'black';
        $graph->parameter['legend_offset'] = 4;

        $graph->y_tick_labels = $options;

        if (($maxbuckets1 > 0.0) && ($maxbuckets2 > 0.0)) {
            $graph->y_order = array('stdev1', 'answers1', 'stdev2', 'answers2');
        } else if ($maxbuckets1 > 0.0) {
            $graph->y_order = array('stdev1', 'answers1');
        } else {
            $graph->y_order = array('stdev2', 'answers2');
        }

        $graph->parameter['y_max_left'] = count($options) - 1;
        $graph->parameter['y_axis_gridlines'] = count($options);
        $graph->parameter['y_resolution_left'] = 1;
        $graph->parameter['y_decimal_left'] = 1;
        $graph->parameter['x_axis_angle'] = 20;

        $graph->draw();

        break;

    case "overall.png":

        $qqq = $DB->get_records_list("survey_questions", "id", explode(',', $survey->questions));

        foreach ($qqq as $key => $qq) {
            if ($qq->multi) {
                $qqq[$key]->text = get_string($qq->text, "survey");
                $qqq[$key]->options = get_string($qq->options, "survey");
                if ($qq->type < 0) {
                    $virtualscales = true;
                }
            }
        }
        foreach ($qqq as $qq) {         // If any virtual, then use JUST virtual, else use JUST nonvirtual.
            if ($qq->multi) {
                if ($virtualscales && $qq->type < 0) {
                    $question[] = $qq;
                } else if (!$virtualscales && $qq->type > 0) {
                    $question[] = $qq;
                }
            }
        }
        $numquestions = count($question);

        $options = explode(",", $question[0]->options);
        $numoptions = count($options);

        for ($i = 0; $i < $numquestions; $i++) {
            $names[$i] = $question[$i]->text;
            $buckets1[$i] = 0.0;
            $buckets2[$i] = 0.0;
            $stdev1[$i] = 0.0;
            $stdev2[$i] = 0.0;
            $count1[$i] = 0;
            $count2[$i] = 0;
            $subquestions = $question[$i]->multi;   // Otherwise next line doesn't work.
            $aaa = $DB->get_records_select("survey_answers", "((survey = ?) AND (question in ($subquestions)))",
                    array($cm->instance));

            if ($aaa) {
                foreach ($aaa as $a) {
                    if (!$group or isset($users[$a->userid])) {
                        if ($a->answer1) {
                            $buckets1[$i] += $a->answer1;
                            $count1[$i]++;
                        }
                        if ($a->answer2) {
                            $buckets2[$i] += $a->answer2;
                            $count2[$i]++;
                        }
                    }
                }
            }

            if ($count1[$i]) {
                $buckets1[$i] = (float) $buckets1[$i] / (float) $count1[$i];
            }
            if ($count2[$i]) {
                $buckets2[$i] = (float) $buckets2[$i] / (float) $count2[$i];
            }

            // Calculate the standard devaiations.
            if ($aaa) {
                foreach ($aaa as $a) {
                    if (!$group or isset($users[$a->userid])) {
                        if ($a->answer1) {
                            $difference = (float) ($a->answer1 - $buckets1[$i]);
                            $stdev1[$i] += ($difference * $difference);
                        }
                        if ($a->answer2) {
                            $difference = (float) ($a->answer2 - $buckets2[$i]);
                            $stdev2[$i] += ($difference * $difference);
                        }
                    }
                }
            }

            if ($count1[$i]) {
                $stdev1[$i] = sqrt((float) $stdev1[$i] / ((float) $count1[$i]));
            }
            if ($count2[$i]) {
                $stdev2[$i] = sqrt((float) $stdev2[$i] / ((float) $count2[$i]));
            }

            $buckets1[$i] = $buckets1[$i] - 1;         // Hack because there should not be ANY 0 values in the data.
            $buckets2[$i] = $buckets2[$i] - 1;

        }

        $maxbuckets1 = max($buckets1);
        $maxbuckets2 = max($buckets2);

        $graph = new graph($SURVEY_GWIDTH, $SURVEY_GHEIGHT);
        $graph->parameter['title'] = strip_tags(format_string($survey->name, true));

        $graph->x_data = $names;

        $graph->y_data['answers1'] = $buckets1;
        $graph->y_format['answers1'] = array('colour' => 'ltblue', 'line' => 'line', 'point' => 'square',
                'shadow_offset' => 4, 'legend' => $stractual);
        $graph->y_data['answers2'] = $buckets2;
        $graph->y_format['answers2'] = array('colour' => 'ltorange', 'line' => 'line', 'point' => 'square',
                'shadow_offset' => 4, 'legend' => $strpreferred);

        $graph->y_data['stdev1'] = $stdev1;
        $graph->y_format['stdev1'] = array('colour' => 'ltltblue', 'bar' => 'fill',
                'shadow_offset' => '4', 'legend' => 'none', 'bar_size' => 0.3);
        $graph->y_data['stdev2'] = $stdev2;
        $graph->y_format['stdev2'] = array('colour' => 'ltltorange', 'bar' => 'fill',
                'shadow_offset' => '4', 'legend' => 'none', 'bar_size' => 0.2);
        $graph->offset_relation['stdev1'] = 'answers1';
        $graph->offset_relation['stdev2'] = 'answers2';

        $graph->parameter['legend'] = 'outside-top';
        $graph->parameter['legend_border'] = 'black';
        $graph->parameter['legend_offset'] = 4;

        $graph->y_tick_labels = $options;

        if (($maxbuckets1 > 0.0) && ($maxbuckets2 > 0.0)) {
            $graph->y_order = array('stdev1', 'answers1', 'stdev2', 'answers2');
        } else if ($maxbuckets1 > 0.0) {
            $graph->y_order = array('stdev1', 'answers1');
        } else {
            $graph->y_order = array('stdev2', 'answers2');
        }

        $graph->parameter['y_max_left'] = $numoptions - 1;
        $graph->parameter['y_axis_gridlines'] = $numoptions;
        $graph->parameter['y_resolution_left'] = 1;
        $graph->parameter['y_decimal_left'] = 1;
        $graph->parameter['x_axis_angle'] = 0;
        $graph->parameter['x_inner_padding'] = 6;

        $graph->draw();

        break;

    case "student.png":

        $qqq = $DB->get_records_list("survey_questions", "id", explode(',', $survey->questions));

        foreach ($qqq as $key => $qq) {
            if ($qq->multi) {
                $qqq[$key]->text = get_string($qq->text, "survey");
                $qqq[$key]->options = get_string($qq->options, "survey");
                if ($qq->type < 0) {
                    $virtualscales = true;
                }
            }
        }
        foreach ($qqq as $qq) {         // If any virtual, then use JUST virtual, else use JUST nonvirtual.
            if ($qq->multi) {
                if ($virtualscales && $qq->type < 0) {
                    $question[] = $qq;
                } else if (!$virtualscales && $qq->type > 0) {
                    $question[] = $qq;
                }
            }
        }
        $numquestions = count($question);

        $options = explode(",", $question[0]->options);
        $numoptions = count($options);

        for ($i = 0; $i < $numquestions; $i++) {
            $names[$i] = $question[$i]->text;
            $buckets1[$i] = 0.0;
            $buckets2[$i] = 0.0;
            $count1[$i] = 0;
            $count2[$i] = 0;
            $studbuckets1[$i] = 0.0;
            $studbuckets2[$i] = 0.0;
            $studcount1[$i] = 0;
            $studcount2[$i] = 0;
            $stdev1[$i] = 0.0;
            $stdev2[$i] = 0.0;

            $subquestions = $question[$i]->multi;   // Otherwise next line doesn't work.
            $aaa = $DB->get_records_select("survey_answers", "((survey = ?) AND (question in ($subquestions)))",
                    array($cm->instance));

            if ($aaa) {
                foreach ($aaa as $a) {
                    if (!$group or isset($users[$a->userid])) {
                        if ($a->userid == $sid) {
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
                }
            }

            if ($count1[$i]) {
                $buckets1[$i] = (float) $buckets1[$i] / (float) $count1[$i];
            }
            if ($count2[$i]) {
                $buckets2[$i] = (float) $buckets2[$i] / (float) $count2[$i];
            }
            if ($studcount1[$i]) {
                $studbuckets1[$i] = (float) $studbuckets1[$i] / (float) $studcount1[$i];
            }
            if ($studcount2[$i]) {
                $studbuckets2[$i] = (float) $studbuckets2[$i] / (float) $studcount2[$i];
            }

            // Calculate the standard devaiations.
            foreach ($aaa as $a) {
                if (!$group or isset($users[$a->userid])) {
                    if ($a->answer1) {
                        $difference = (float) ($a->answer1 - $buckets1[$i]);
                        $stdev1[$i] += ($difference * $difference);
                    }
                    if ($a->answer2) {
                        $difference = (float) ($a->answer2 - $buckets2[$i]);
                        $stdev2[$i] += ($difference * $difference);
                    }
                }
            }

            if ($count1[$i]) {
                $stdev1[$i] = sqrt((float) $stdev1[$i] / ((float) $count1[$i]));
            }
            if ($count2[$i]) {
                $stdev2[$i] = sqrt((float) $stdev2[$i] / ((float) $count2[$i]));
            }

            $buckets1[$i] = $buckets1[$i] - 1;         // Hack because there should not be ANY 0 values in the data.
            $buckets2[$i] = $buckets2[$i] - 1;
            $studbuckets1[$i] = $studbuckets1[$i] - 1;
            $studbuckets2[$i] = $studbuckets2[$i] - 1;

        }

        $maxbuckets1 = max($buckets1);
        $maxbuckets2 = max($buckets2);

        $graph = new graph($SURVEY_GWIDTH, $SURVEY_GHEIGHT);
        $graph->parameter['title'] = strip_tags(format_string($survey->name, true));

        $graph->x_data = $names;

        $graph->y_data['answers1'] = $buckets1;
        $graph->y_format['answers1'] = array('colour' => 'ltblue', 'line' => 'line', 'point' => 'square',
                'shadow_offset' => 0.1, 'legend' => $stractualclass);
        $graph->y_data['answers2'] = $buckets2;
        $graph->y_format['answers2'] = array('colour' => 'ltorange', 'line' => 'line', 'point' => 'square',
                'shadow_offset' => 0.1, 'legend' => $strpreferredclass);
        $graph->y_data['studanswers1'] = $studbuckets1;
        $graph->y_format['studanswers1'] = array('colour' => 'blue', 'line' => 'line', 'point' => 'square',
                'shadow_offset' => 4, 'legend' => $stractualstudent);
        $graph->y_data['studanswers2'] = $studbuckets2;
        $graph->y_format['studanswers2'] = array('colour' => 'orange', 'line' => 'line', 'point' => 'square',
                'shadow_offset' => 4, 'legend' => $strpreferredstudent);
        $graph->y_data['stdev1'] = $stdev1;
        $graph->y_format['stdev1'] = array('colour' => 'ltltblue', 'bar' => 'fill',
                'shadow_offset' => 0.1, 'legend' => 'none', 'bar_size' => 0.3);
        $graph->y_data['stdev2'] = $stdev2;
        $graph->y_format['stdev2'] = array('colour' => 'ltltorange', 'bar' => 'fill',
                'shadow_offset' => 0.1, 'legend' => 'none', 'bar_size' => 0.2);
        $graph->offset_relation['stdev1'] = 'answers1';
        $graph->offset_relation['stdev2'] = 'answers2';

        $graph->y_tick_labels = $options;

        $graph->parameter['bar_size'] = 0.15;

        $graph->parameter['legend'] = 'outside-top';
        $graph->parameter['legend_border'] = 'black';
        $graph->parameter['legend_offset'] = 4;

        if (($maxbuckets1 > 0.0) && ($maxbuckets2 > 0.0)) {
            $graph->y_order = array('stdev1', 'stdev2', 'answers1', 'answers2', 'studanswers1', 'studanswers2');
        } else if ($maxbuckets1 > 0.0) {
            $graph->y_order = array('stdev1', 'answers1', 'studanswers1');
        } else {
            $graph->y_order = array('stdev2', 'answers2', 'studanswers2');
        }

        $graph->parameter['y_max_left'] = $numoptions - 1;
        $graph->parameter['y_axis_gridlines'] = $numoptions;
        $graph->parameter['y_resolution_left'] = 1;
        $graph->parameter['y_decimal_left'] = 1;
        $graph->parameter['x_axis_angle'] = 20;

        $graph->draw();
        break;

    case "studentmultiquestion.png":

        $question = $DB->get_record("survey_questions", array("id" => $qid));
        $question->text = get_string($question->text, "survey");
        $question->options = get_string($question->options, "survey");

        $options = explode(",", $question->options);
        $questionorder = explode(",", $question->multi);

        $qqq = $DB->get_records_list("survey_questions", "id", explode(',', $question->multi));

        foreach ($questionorder as $i => $val) {
            $names[$i] = get_string($qqq[$val]->shorttext, "survey");
            $buckets1[$i] = 0;
            $buckets2[$i] = 0;
            $count1[$i] = 0;
            $count2[$i] = 0;
            $indexof[$val] = $i;
            $studbuckets1[$i] = 0.0;
            $studbuckets2[$i] = 0.0;
            $studcount1[$i] = 0;
            $studcount2[$i] = 0;
            $stdev1[$i] = 0.0;
            $stdev2[$i] = 0.0;
        }

        $aaa = $DB->get_records_select("survey_answers", "((survey = ?) AND (question in ($question->multi)))",
                array($cm->instance));

        if ($aaa) {
            foreach ($aaa as $a) {
                if (!$group or isset($users[$a->userid])) {
                    $index = $indexof[$a->question];
                    if ($a->userid == $sid) {
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
            }
        }

        foreach ($questionorder as $i => $val) {
            if ($count1[$i]) {
                $buckets1[$i] = (float) $buckets1[$i] / (float) $count1[$i];
            }
            if ($count2[$i]) {
                $buckets2[$i] = (float) $buckets2[$i] / (float) $count2[$i];
            }
            if ($studcount1[$i]) {
                $studbuckets1[$i] = (float) $studbuckets1[$i] / (float) $studcount1[$i];
            }
            if ($studcount2[$i]) {
                $studbuckets2[$i] = (float) $studbuckets2[$i] / (float) $studcount2[$i];
            }
        }

        foreach ($aaa as $a) {
            if (!$group or isset($users[$a->userid])) {
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
        }

        foreach ($questionorder as $i => $val) {
            if ($count1[$i]) {
                $stdev1[$i] = sqrt((float) $stdev1[$i] / ((float) $count1[$i]));
            }
            if ($count2[$i]) {
                $stdev2[$i] = sqrt((float) $stdev2[$i] / ((float) $count2[$i]));
            }
            $buckets1[$i] = $buckets1[$i] - 1;         // Hack because there should not be ANY 0 values in the data.
            $buckets2[$i] = $buckets2[$i] - 1;
            $studbuckets1[$i] = $studbuckets1[$i] - 1;
            $studbuckets2[$i] = $studbuckets2[$i] - 1;
        }

        $maxbuckets1 = max($buckets1);
        $maxbuckets2 = max($buckets2);

        $graph = new graph($SURVEY_GWIDTH, $SURVEY_GHEIGHT);
        $graph->parameter['title'] = "$question->text";

        $graph->x_data = $names;
        $graph->y_data['answers1'] = $buckets1;
        $graph->y_format['answers1'] = array('colour' => 'ltblue', 'line' => 'line', 'point' => 'square',
                'shadow_offset' => 0.1, 'legend' => $stractualclass);
        $graph->y_data['answers2'] = $buckets2;
        $graph->y_format['answers2'] = array('colour' => 'ltorange', 'line' => 'line', 'point' => 'square',
                'shadow_offset' => 0.1, 'legend' => $strpreferredclass);
        $graph->y_data['studanswers1'] = $studbuckets1;
        $graph->y_format['studanswers1'] = array('colour' => 'blue', 'line' => 'line', 'point' => 'square',
                'shadow_offset' => 4, 'legend' => $stractualstudent);
        $graph->y_data['studanswers2'] = $studbuckets2;
        $graph->y_format['studanswers2'] = array('colour' => 'orange', 'line' => 'line', 'point' => 'square',
                'shadow_offset' => 4, 'legend' => $strpreferredstudent);
        $graph->y_data['stdev1'] = $stdev1;
        $graph->y_format['stdev1'] = array('colour' => 'ltltblue', 'bar' => 'fill',
                'shadow_offset' => 0.1, 'legend' => 'none', 'bar_size' => 0.3);
        $graph->y_data['stdev2'] = $stdev2;
        $graph->y_format['stdev2'] = array('colour' => 'ltltorange', 'bar' => 'fill',
                'shadow_offset' => 0.1, 'legend' => 'none', 'bar_size' => 0.2);
        $graph->offset_relation['stdev1'] = 'answers1';
        $graph->offset_relation['stdev2'] = 'answers2';

        $graph->parameter['bar_size'] = 0.15;

        $graph->parameter['legend'] = 'outside-top';
        $graph->parameter['legend_border'] = 'black';
        $graph->parameter['legend_offset'] = 4;

        $graph->y_tick_labels = $options;

        if (($maxbuckets1 > 0.0) && ($maxbuckets2 > 0.0)) {
            $graph->y_order = array('stdev1', 'stdev2', 'answers1', 'answers2', 'studanswers1', 'studanswers2');
        } else if ($maxbuckets1 > 0.0) {
            $graph->y_order = array('stdev1', 'answers1', 'studanswers1');
        } else {
            $graph->y_order = array('stdev2', 'answers2', 'studanswers2');
        }

        $graph->parameter['y_max_left'] = count($options) - 1;
        $graph->parameter['y_axis_gridlines'] = count($options);
        $graph->parameter['y_resolution_left'] = 1;
        $graph->parameter['y_decimal_left'] = 1;
        $graph->parameter['x_axis_angle'] = 20;

        $graph->draw();

        break;

    default:
        break;
}

exit;
