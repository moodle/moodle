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
 * View responses to a quiz
 *
 * @copyright Davo Smith <moodle@davosmith.co.uk>
 * @package mod_realtimequiz
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

require_once("../../config.php");
global $CFG, $DB, $OUTPUT, $PAGE;
require_once($CFG->dirroot.'/mod/realtimequiz/lib.php');

define('REALTIMEQUIZ_DEFAULT_PERPAGE', 30);

$id = required_param('id', PARAM_INT); // Course Module ID, or.
$showsession = optional_param('showsession', 0, PARAM_INT);
$questionid = optional_param('questionid', 0, PARAM_INT);
$nextquestion = optional_param('nextquestion', false, PARAM_TEXT);
$prevquestion = optional_param('prevquestion', false, PARAM_TEXT);
$allquestions = optional_param('allquestions', false, PARAM_TEXT);
$showusers = optional_param('showusers', false, PARAM_BOOL);

$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', REALTIMEQUIZ_DEFAULT_PERPAGE, PARAM_INT);

$cm = get_coursemodule_from_id('realtimequiz', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
$realtimequiz = $DB->get_record('realtimequiz', ['id' => $cm->instance], '*', MUST_EXIST);

$url = new moodle_url('/mod/realtimequiz/responses.php', ['id' => $cm->id]);
if ($showsession) {
    $url->param('showsession', $showsession);
}
if ($questionid) {
    $url->param('questionid', $questionid);
}
if ($showusers) {
    $url->param('showusers', $showusers);
}
if ($page) {
    $url->param('page', $page);
}
if ($perpage != REALTIMEQUIZ_DEFAULT_PERPAGE) {
    $url->param('perpage', $perpage);
}
$PAGE->set_url($url);

require_login($course->id, false, $cm);
$PAGE->set_pagelayout('incourse');
if ($CFG->version < 2011120100) {
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
} else {
    $context = context_module::instance($cm->id);
}
require_capability('mod/realtimequiz:seeresponses', $context);

if ($questionid != 0) {
    if ($allquestions) {
        $questionid = 0;
    } else if ($nextquestion) {
        $question = $DB->get_record('realtimequiz_question', ['id' => $questionid]);
        $newquestion = $DB->get_record('realtimequiz_question', [
            'quizid' => $question->quizid, 'questionnum' => ($question->questionnum + 1),
        ]);

        if ($newquestion) {
            $questionid = $newquestion->id;
        } else {
            $questionid = 0;
        }
    } else if ($prevquestion) {
        $question = $DB->get_record('realtimequiz_question', ['id' => $questionid]);
        $newquestion = $DB->get_record('realtimequiz_question', [
            'quizid' => $question->quizid, 'questionnum' => ($question->questionnum - 1),
        ]);

        if ($newquestion) {
            $questionid = $newquestion->id;
        } else {
            $questionid = 0;
        }
    }

    if ($questionid == 0) {
        $redir = new moodle_url('/mod/realtimequiz/responses.php', ['id' => $cm->id, 'showsession' => $showsession]);
        if ($showusers) {
            $redir->param('showusers', 1);
        }
        redirect($redir);
    }
}

// Log that the responses were viewed.
if ($CFG->version > 2014051200) { // Moodle 2.7+.
    $params = [
        'context' => $context,
        'other' => [
            'quizid' => $realtimequiz->id,
        ],
    ];
    $event = \mod_realtimequiz\event\responses_viewed::create($params);
    $event->add_record_snapshot('course', $course);
    $event->add_record_snapshot('realtimequiz', $realtimequiz);
    $event->trigger();
} else {
    add_to_log($course->id, "realtimequiz", "seeresponses", "responses.php?id=$cm->id", "$realtimequiz->id");
}

// Print the page header.

$strrealtimequizzes = get_string("modulenameplural", "realtimequiz");
$strrealtimequiz = get_string("modulename", "realtimequiz");

$PAGE->set_title(strip_tags($course->shortname.': '.$strrealtimequiz.': '.format_string($realtimequiz->name, true)));
$PAGE->set_heading($course->fullname);
echo $OUTPUT->header();

if ($CFG->branch < 400) {
    echo $OUTPUT->heading(format_string($realtimequiz->name));

    if (class_exists('\core_completion\activity_custom_completion')) {
        // Render the activity information.
        $modinfo = get_fast_modinfo($course);
        $cminfo = $modinfo->get_cm($cm->id);
        $completiondetails = \core_completion\cm_completion_details::get_instance($cminfo, $USER->id);
        $activitydates = \core\activity_dates::get_dates_for_module($cminfo, $USER->id);
        echo $OUTPUT->activity_information($cminfo, $completiondetails, $activitydates);
    }

    realtimequiz_view_tabs('responses', $cm->id, $context);
}

$select = "quizid = ? AND id IN (SELECT sessionid FROM {realtimequiz_submitted})";
$params = [$realtimequiz->id];
$sessions = $DB->get_records_select('realtimequiz_session', $select, $params, 'timestamp');
if (empty($sessions)) {
    echo $OUTPUT->box_start('generalbox boxwidthwide boxaligncenter realtimequizbox');
    print_string('nosessions', 'realtimequiz');
    echo $OUTPUT->box_end();
    echo $OUTPUT->footer();
    die();
}
$sessions = array_reverse($sessions);

echo '<center><form method="get" action="'.$CFG->wwwroot.'/mod/realtimequiz/responses.php?id='.$cm->id.'">';
echo '<b>'.get_string('choosesession', 'realtimequiz').'</b>';
echo '<input type="hidden" name="id" value="'.$cm->id.'" />';
echo '<input type="hidden" name="questionid" value="'.$questionid.'" />';
if ($showusers) {
    echo '<input type="hidden" name="showusers" value="1" />';
}
echo '<select name="showsession" size="1" >';
if ($showsession == 0) {
    echo '<option value="0" selected="selected">'.get_string('allsessions', 'realtimequiz').'</option>';
} else {
    echo '<option value="0">'.get_string('allsessions', 'realtimequiz').'</option>';
}
foreach ($sessions as $session) {
    $sesstext = '';
    if ($session->name) {
        $sesstext = $session->name.' '; // Session name (if it exits) + date.
    }
    $sesstext .= date('j/m/Y H:i', $session->timestamp);

    if ($showsession == $session->id) {
        echo "<option value='$session->id' selected='selected'>$sesstext</option>";
    } else {
        echo "<option value='$session->id'>$sesstext</option>";
    }
}
echo '</select> <input type="submit" value="'.get_string('showsession', 'realtimequiz').'" /></form></center>';

if ($CFG->version < 2013111800) {
    $tickimg = '<img src="'.$OUTPUT->pix_url('i/tick_green_big').'" alt="'.get_string('tick', 'realtimequiz').'" />';
    $crossimg = '<img src="'.$OUTPUT->pix_url('i/cross_red_big').'" alt="'.get_string('cross', 'realtimequiz').'" />';
} else {
    $tickimg = $OUTPUT->pix_icon('i/grade_correct', get_string('tick', 'realtimequiz'));
    $crossimg = $OUTPUT->pix_icon('i/grade_incorrect', get_string('cross', 'realtimequiz'));
}

if ($questionid == 0) { // Show all of the questions.
    if ($CFG->version < 2013111800) {
        $isff = check_browser_version('Gecko');
    } else {
        $isff = core_useragent::check_browser_version('Gecko');
    }
    if ($isff) {
        $blankcolspan = 'colspan="999" ';
    } else {
        $blankcolspan = '';
    }

    $questions = $DB->get_records('realtimequiz_question', ['quizid' => $realtimequiz->id], 'questionnum');
    $linkurl = new moodle_url('/mod/realtimequiz/responses.php', ['id' => $cm->id, 'showsession' => $showsession]);

    if ($showusers) {
        $linkurl->param('showusers', 1);
        if ($CFG->version < 2013111800) {
            $usernames = 'u.firstname, u.lastname';
        } else if (class_exists('\core_user\fields')) {
            $namesql = \core_user\fields::for_name()->get_sql('u', true);
        } else {
            $namesql = (object)[
                'selects' => ','.get_all_user_name_fields(true, 'u'),
                'joins' => '',
                'params' => [],
                'mappings' => [],
            ];
        }

        $sql = "SELECT DISTINCT u.id {$namesql->selects}
                  FROM {user} u
                  JOIN {realtimequiz_submitted} s ON s.userid = u.id
                  JOIN {realtimequiz_question} q ON s.questionid = q.id
                       {$namesql->joins}
                 WHERE q.quizid = :quizid";
        $params = array_merge(['quizid' => $realtimequiz->id], $namesql->params);
        if ($showsession) {
            $sql .= ' AND s.sessionid = :sessionid';
            $params['sessionid'] = $showsession;
        }
        $sql .= ' ORDER BY u.firstname ASC';
        $users = $DB->get_records_sql($sql, $params);

        if ($page * $perpage > count($users)) {
            $page = 0;
        }

        $url = new moodle_url($PAGE->url);
        $url->remove_params(['page']);
        echo $OUTPUT->paging_bar(count($users), $page, $perpage, $url, 'page');
        $users = array_slice($users, $page * $perpage, $perpage, true);

        foreach ($users as $user) {
            $user->fullname = fullname($user);
            $user->score = 0;
        }

        $strtotals = get_string('totals', 'realtimequiz');
        [$usql, $uparams] = $DB->get_in_or_equal(array_keys($users), SQL_PARAMS_NAMED);

        $nousersurl = new moodle_url($PAGE->url);
        $nousersurl->remove_params(['showusers']);
        $userlink = html_writer::link($nousersurl, get_string('hideusers', 'realtimequiz'));
    } else {
        $usersurl = new moodle_url($PAGE->url);
        $usersurl->param('showusers', 1);
        $userlink = html_writer::link($usersurl, get_string('showusers', 'realtimequiz'));
    }

    echo html_writer::tag('p', $userlink);

    echo '<br /><table border="1" style="border-style: none;">';
    if (!empty($questions)) {
        foreach ($questions as $question) {
            echo '<tr class="realtimequiz_report_question"><td width="30%">'.$question->questionnum.'</td>';
            $answers = $DB->get_records('realtimequiz_answer', ['questionid' => $question->id], 'id');
            if (!empty($answers)) {
                $iscorrectanswer = false;
                foreach ($answers as $answer) {
                    if ($answer->correct == 1) {
                        echo '<td width="10%" class="realtimequiz_report_question_correct"><b>'.s($answer->answertext).'</b></td>';
                        $iscorrectanswer = true;
                    } else {
                        echo '<td width="10%">'.s($answer->answertext).'</td>';
                    }
                }

                echo '<td width="10%">';
                if ($showusers) {
                    echo $strtotals;
                } else {
                    echo '&nbsp;';
                }
                echo '</td>';
                $questiontext = format_string($question->questiontext);
                if (empty($questiontext)) {
                    $questiontext = get_string('question', 'mod_realtimequiz').$question->questionnum;
                }
                echo '</tr><tr class="realtimequiz_report_answer"><td><a href="'.
                    $linkurl->out(true, ['questionid' => $question->id]).'">'.format_string($questiontext).'</a></td>';

                $total = 0;
                $gotanswerright = 0;
                foreach ($answers as $answer) {
                    if ($showsession == 0) {
                        $count = $DB->count_records('realtimequiz_submitted', ['answerid' => $answer->id]);
                    } else {
                        $count = $DB->count_records('realtimequiz_submitted', [
                            'answerid' => $answer->id, 'sessionid' => $showsession,
                        ]);
                    }

                    $total += $count;
                    if ($iscorrectanswer) {
                        if ($answer->correct == 1) {
                            echo '<td align="center" class="realtimequiz_report_answer_correct" ><b>'.$count.'</b>&nbsp;';
                            if (!$showusers) {
                                echo $tickimg;
                            }
                            echo '</td>';
                            $gotanswerright = $count;

                        } else {
                            echo '<td align="center">'.$count.'&nbsp;';
                            if (!$showusers) {
                                echo $crossimg;
                            }
                            echo '</td>';
                        }
                    } else {
                        echo '<td align="center">'.$count.'</td>';
                    }
                }
            }

            echo '<td width="10%"><center>';
            if ($total != 0) {
                echo @round($gotanswerright / ($total / 100), 2).'%';
            } else {
                echo '0%';
            }
            echo '</td></center>';
            echo '</tr>';

            if ($showusers) {
                $select = "questionid = :questionid AND userid $usql";
                $params = ['questionid' => $question->id];
                $params = array_merge($params, $uparams);
                if ($showsession) {
                    $select .= ' AND sessionid = :sessionid';
                    $params['sessionid'] = $showsession;
                }

                $submitted = $DB->get_records_select('realtimequiz_submitted', $select, $params, 'userid');

                if (!$submitted) {
                    echo '<tr><td colspan="99">'.get_string('noanswers', 'realtimequiz').'</td></tr>';
                } else {
                    $sub = 0;
                    foreach ($submitted as $submission) {
                        // List each student name for each question.
                        $userid = $submission->userid;
                        $fullname = $users[$userid]->fullname;
                        echo '<tr><td>'.$fullname.'</td>';
                        foreach ($answers as $answer) {
                            echo '<td align="center">';
                            if ($answer->id == $submission->answerid) {
                                if ($answer->correct == 1) {
                                    echo $tickimg;
                                    $users[$userid]->score += 1;
                                } else {
                                    echo $crossimg;
                                }
                            } else {
                                echo '&nbsp;';
                            }
                            echo '</td>';
                        }
                        // This section shows the running score of each student.
                        echo '<td width="10%"><center>';
                        echo $users[$userid]->score;
                        echo '</center></td>';
                        echo '</tr>';
                    }
                }
            }

            echo '</tr>';
            // Draw blank line between questions results.
            echo '<tr style="border-style: none;"><td style="border-style: none;" '.$blankcolspan.' >&nbsp;</td></tr>';
        }
    }
    echo '</table>'; // End of view responses table.

    if ($showusers) {
        $questioncount = count($questions);
        $usercount = count($users);
        $classtotal = 0;
        foreach ($users as $user) {
            $user->average = @round($user->score * 100.0 / $questioncount, 2);
            $classtotal += $user->score;
        }
        $classaverage = @round($classtotal * 100.0 / ($usercount * $questioncount), 2);

        echo '<p><center><table border="1">';
        echo '<tr><td class="realtimequiz_report_question_correct"><center>';
        echo '<h2>'.get_string('scorestable', 'realtimequiz').'</h2>';
        echo '</center></td></tr>';

        $x = 1;
        foreach ($users as $user) {
            echo '<tr><td>';
            echo '<pre><span class="inner-pre" style="font-size: 15px">';
            if ($user->score >= $classaverage) {
                echo sprintf('<font color="green">%3u. <b>%24s</b> scored %2u/%2u = <b>%.2u%%</b></font>',
                             $x, $user->fullname, $user->score, $questioncount, $user->average);
            } else {
                echo sprintf('%3u. %24s scored %2u/%2u = %.2u%%',
                             $x, $user->fullname, $user->score, $questioncount, $user->average);

                echo '</span></pre>';
                echo '<br>';
                echo '</td></tr>';
            }
            $x++;
        }
        echo '</table></center>';

        echo '<br><p><p>';

        echo '<h2><center><b>Average class score is '.$classaverage.'%</b></center></h2>';
    }

} else { // Show a single question.
    echo $OUTPUT->box_start('generalbox boxwidthwide boxaligncenter realtimequizplainbox');

    $question = $DB->get_record('realtimequiz_question', ['id' => $questionid]);

    echo '<h2>'.get_string('question', 'realtimequiz').$question->questionnum.'</h2>';
    $questiontext = file_rewrite_pluginfile_urls($question->questiontext, 'pluginfile.php', $context->id,
                                                 'mod_realtimequiz', 'question', $question->id);
    $questiontext = format_text($questiontext, $question->questiontextformat);
    echo '<p>'.$questiontext.'</p><br />';
    echo '<table border="1" class="realtimequiz_report_answer"><tr class="realtimequiz_report_question">'.
        '<td width="30%">&nbsp;</td>';
    $answers = $DB->get_records('realtimequiz_answer', ['questionid' => $questionid], 'id');
    if (!empty($answers)) {
        foreach ($answers as $answer) {
            if ($answer->correct == 1) {
                echo '<td width="10%"><b>'.s($answer->answertext).'</b></td>';
            } else {
                echo '<td width="10%">'.s($answer->answertext).'</td>';
            }
        }
    }
    echo '</tr>';

    if ($showsession == 0) {
        $submitted = $DB->get_records('realtimequiz_submitted', ['questionid' => $questionid], 'userid');
    } else {
        $submitted = $DB->get_records('realtimequiz_submitted', [
            'questionid' => $questionid, 'sessionid' => $showsession,
        ],                            'userid');
    }

    if (empty($submitted)) {
        echo '<tr><td colspan="99">'.get_string('noanswers', 'realtimequiz').'</td></tr>';
    } else {

        foreach ($submitted as $submission) {
            $user = $DB->get_record('user', ['id' => $submission->userid]);
            $fullname = fullname($user, has_capability('moodle/site:viewfullnames', $context));
            echo '<tr><td>'.$fullname.'</td>';
            $iscorrectanswer = false;

            foreach ($answers as $answer) {
                if ($answer->correct == 1) {
                    $iscorrectanswer = true;
                    break;
                }
            }

            foreach ($answers as $answer) {
                echo '<td align="center">';

                if ($answer->id == $submission->answerid) {
                    if (!$iscorrectanswer || $answer->correct == 1) {
                        echo $tickimg;
                    } else {
                        echo $crossimg;
                    }
                } else {
                    echo '&nbsp;';
                }
                echo '</td>';
            }
            echo '</tr>';
        }
    }
    echo '</table>';

    $thisurl = new moodle_url('/mod/realtimequiz/responses.php');
    echo '<br /><form action="'.$thisurl.'" method="get">';
    echo '<input type="hidden" name="id" value="'.$cm->id.'" />';
    echo '<input type="hidden" name="showsession" value="'.$showsession.'" />';
    echo '<input type="hidden" name="questionid" value="'.$questionid.'" />';
    if ($showusers) {
        echo '<input type="hidden" name="showusers" value="1" />';
    }

    echo '<input type="submit" name="prevquestion" value="'.get_string('prevquestion', 'realtimequiz').'" />&nbsp;';
    echo '<input type="submit" name="allquestions" value="'.get_string('allquestions', 'realtimequiz').'" />&nbsp;';
    echo '<input type="submit" name="nextquestion" value="'.get_string('nextquestion', 'realtimequiz').'" />';

    echo '</form>';

    echo $OUTPUT->box_end();
}

echo $OUTPUT->footer();

