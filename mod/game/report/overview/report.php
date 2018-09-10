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
 * This script lists student attempts.
 *
 * @package    mod_game
 * @copyright  2007 Vasilis Daloukas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/tablelib.php');

/**
 * The class prints a report
 *
 * @package    mod_game
 * @copyright  2014 Vasilis Daloukas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class game_report extends game_default_report {

    /**
     * Display the report.
     *
     * @param stdClass $game
     * @param stdClass $cm
     * @param stdClass $course
     */
    public function display($game, $cm, $course) {
        global $CFG, $SESSION, $DB;

        // Define some strings.
        $strreallydel  = addslashes(get_string('deleteattemptcheck', 'game'));
        $strtimeformat = get_string('strftimedatetime');
        $strreviewquestion = get_string('reviewresponse', 'quiz');

        // Only print headers if not asked to download data.
        if (!$download = optional_param('download', null)) {
            $this->print_header_and_tabs($cm, $course, $game, $reportmode = "overview");
        }

        // Deal with actions.
        $action = optional_param('action', '', PARAM_ACTION);

        switch ($action) {
            case 'delete': // Some attempts need to be deleted.
                $attemptids = optional_param('attemptid', array(), PARAM_INT);

                foreach ($attemptids as $attemptid) {
                    if ($attemptid && $todelete = get_record('game_attempts', 'id', $attemptid)) {
                        delete_records('game_attempts', 'id', $attemptid);
                        delete_records('game_queries', 'attemptid', $attemptid);

                        // Search game_attempts for other instances by this user.
                        // If none, then delete record for this game, this user from game_grades.
                        // else recalculate best grade.

                        $userid = $todelete->userid;
                        if (!record_exists('game_attempts', 'userid', $userid, 'gameid', $game->id)) {
                            delete_records('game_grades', 'userid', $userid, 'gameid', $game->id);
                        } else {
                            game_save_best_score( $game, $userid);
                        }
                    }
                }
            break;
        }

        // Print information on the number of existing attempts.
        if (!$download) {
            // Do not print notices when downloading.
            if ($attemptnum = count_records('game_attempts', 'gameid', $game->id)) {
                $a = new stdClass;
                $a->attemptnum = $attemptnum;
                $a->studentnum = count_records_select('game_attempts',
                    "gameid = '$game->id' AND preview = '0'", 'COUNT(DISTINCT userid)');
                $a->studentstring = $course->students;

                notify( get_string('numattempts', 'game', $a));
            }
        }

        $context = get_context_instance(CONTEXT_MODULE, $cm->id);
        // Find out current groups mode.
        if ($groupmode = groupmode($course, $cm)) { // Groups are being used.
            if (!$download) {
                $currentgroup = setup_and_print_groups($course, $groupmode, "report.php?id=$cm->id&amp;mode=overview");
            } else {
                $currentgroup = get_and_set_current_group($course, $groupmode);
            }
        } else {
            $currentgroup = get_and_set_current_group($course, $groupmode);
        }

        // Set table options.
        $noattempts = optional_param('noattempts', 0, PARAM_INT);
        $detailedmarks = optional_param('detailedmarks', 0, PARAM_INT);
        $pagesize = optional_param('pagesize', 10, PARAM_INT);
        $hasfeedback = game_has_feedback($game->id) && $game->grade > 1.e-7;
        if ($pagesize < 1) {
            $pagesize = 10;
        }

        // Now check if asked download of data.
        if ($download) {
            $filename = clean_filename("$course->shortname ".format_string($game->name, true));
            $sort = '';
        }

        // Define table columns.
        $tablecolumns = array('checkbox', 'picture', 'fullname', 'timestart', 'timefinish', 'duration');
        $tableheaders = array(null, '', get_string('fullname'), get_string('startedon', 'game'),
            get_string('timecompleted', 'game'), get_string('attemptduration', 'game'));

        if ($game->grade) {
            $tablecolumns[] = 'grade';
            $tableheaders[] = get_string('grade', 'game').'/'.$game->grade;
        }

        if ($detailedmarks) {
            // We want to display marks for all questions.
            // Start by getting all questions.
            $questionlist = game_questions_in_game( $game->questions);
            $questionids = explode(',', $questionlist);
            $sql = "SELECT q.*, i.score AS maxgrade, i.id AS instance".
                    "  FROM {question} q,".
                    "       {game_queries} i".
                    " WHERE i.gameid = '$game->id' AND q.id = i.questionid".
                    "   AND q.id IN ($questionlist)";
            if (!$questions = get_records_sql($sql)) {
                print_error('No questions found');
            }
            $number = 1;
            foreach ($questionids as $key => $id) {
                if ($questions[$id]->length) {
                    // Only print questions of non-zero length.
                    $tablecolumns[] = '$'.$id;
                    $tableheaders[] = '#'.$number;
                    $questions[$id]->number = $number;
                    $number += $questions[$id]->length;
                } else {
                    // Get rid of zero length questions.
                    unset($questions[$id]);
                    unset($questionids[$key]);
                }
            }
        }

        if ($hasfeedback) {
            $tablecolumns[] = 'feedbacktext';
            $tableheaders[] = get_string('feedback', 'game');
        }

        if (!$download) {
            // Set up the table.

            $table = new flexible_table('mod-game-report-overview-report');

            $table->define_columns($tablecolumns);
            $table->define_headers($tableheaders);
            $table->define_baseurl($CFG->wwwroot.'/mod/game/report.php?mode=overview&amp;id=' .
                $cm->id . '&amp;noattempts=' . $noattempts . '&amp;detailedmarks=' . $detailedmarks .
                '&amp;pagesize=' . $pagesize);

            $table->sortable(true);
            $table->collapsible(true);

            $table->column_suppress('picture');
            $table->column_suppress('fullname');

            $table->column_class('picture', 'picture');

            $table->set_attribute('cellspacing', '0');
            $table->set_attribute('id', 'attempts');
            $table->set_attribute('class', 'generaltable generalbox');

            // Start working -- this is necessary as soon as the niceties are over.
            $table->setup();
        } else if ($download == 'ODS') {
            require_once("$CFG->libdir/odslib.class.php");

            $filename .= ".ods";
            // Creating a workbook.
            $workbook = new MoodleODSWorkbook("-");
            // Sending HTTP headers.
            $workbook->send($filename);
            // Creating the first worksheet.
            $sheettitle = get_string('reportoverview', 'game');
            $myxls =& $workbook->add_worksheet($sheettitle);
            // Format types.
            $format =& $workbook->add_format();
            $format->set_bold(0);
            $formatbc =& $workbook->add_format();
            $formatbc->set_bold(1);
            $formatbc->set_align('center');
            $formatb =& $workbook->add_format();
            $formatb->set_bold(1);
            $formaty =& $workbook->add_format();
            $formaty->set_bg_color('yellow');
            $formatc =& $workbook->add_format();
            $formatc->set_align('center');
            $formatr =& $workbook->add_format();
            $formatr->set_bold(1);
            $formatr->set_color('red');
            $formatr->set_align('center');
            $formatg =& $workbook->add_format();
            $formatg->set_bold(1);
            $formatg->set_color('green');
            $formatg->set_align('center');

            // Here starts workshhet headers.
            $headers = array(get_string('fullname'),
                    get_string('startedon', 'game'),
                    get_string('timecompleted', 'game'),
                    get_string('attemptduration', 'game')
                );

            if ($game->grade) {
                $headers[] = get_string('grade', 'game').'/'.$game->grade;
            }
            if ($detailedmarks) {
                foreach ($questionids as $id) {
                    $headers[] = '#'.$questions[$id]->number;
                }
            }
            if ($hasfeedback) {
                $headers[] = get_string('feedback', 'game');
            }
            $colnum = 0;
            foreach ($headers as $item) {
                $myxls->write(0, $colnum, $item, $formatbc);
                $colnum++;
            }
            $rownum = 1;
        } else if ($download == 'Excel') {
            require_once("$CFG->libdir/excellib.class.php");

            $filename .= ".xls";
            // Creating a workbook.
            $workbook = new MoodleExcelWorkbook("-");
            // Sending HTTP headers.
            $workbook->send($filename);
            // Creating the first worksheet.
            $sheettitle = get_string('reportoverview', 'game');
            $myxls =& $workbook->add_worksheet($sheettitle);
            // Format types.
            $format =& $workbook->add_format();
            $format->set_bold(0);
            $formatbc =& $workbook->add_format();
            $formatbc->set_bold(1);
            $formatbc->set_align('center');
            $formatb =& $workbook->add_format();
            $formatb->set_bold(1);
            $formaty =& $workbook->add_format();
            $formaty->set_bg_color('yellow');
            $formatc =& $workbook->add_format();
            $formatc->set_align('center');
            $formatr =& $workbook->add_format();
            $formatr->set_bold(1);
            $formatr->set_color('red');
            $formatr->set_align('center');
            $formatg =& $workbook->add_format();
            $formatg->set_bold(1);
            $formatg->set_color('green');
            $formatg->set_align('center');

            // Here starts workshhet headers.
            $headers = array(get_string('fullname'), get_string('startedon', 'game'),
                get_string('timecompleted', 'game'), get_string('attemptduration', 'game'));

            if ($game->grade) {
                $headers[] = get_string('grade', 'game').'/'.$game->grade;
            }
            if ($detailedmarks) {
                foreach ($questionids as $id) {
                    $headers[] = '#'.$questions[$id]->number;
                }
            }
            if ($hasfeedback) {
                $headers[] = get_string('feedback', 'game');
            }
            $colnum = 0;
            foreach ($headers as $item) {
                $myxls->write(0, $colnum, $item, $formatbc);
                $colnum++;
            }
            $rownum = 1;
        } else if ($download == 'CSV') {
            $filename .= ".txt";

            header("Content-Type: application/download\n");
            header("Content-Disposition: attachment; filename=\"$filename\"");
            header("Expires: 0");
            header("Cache-Control: must-revalidate,post-check=0,pre-check=0");
            header("Pragma: public");

            $headers = get_string('fullname')."\t".get_string('startedon', 'game').
                "\t".get_string('timecompleted', 'game')."\t".get_string('attemptduration', 'game');

            if ($game->grade) {
                $headers .= "\t".get_string('grade', 'game')."/".$game->grade;
            }
            if ($detailedmarks) {
                foreach ($questionids as $id) {
                    $headers .= "\t#".$questions[$id]->number;
                }
            }
            if ($hasfeedback) {
                $headers .= "\t" . get_string('feedback', 'game');
            }
            echo $headers." \n";
        }

        $contextlists = get_related_contexts_string( get_context_instance( CONTEXT_COURSE, $course->id));

        // Construct the SQL.
        $select = 'SELECT qa.id,'.sql_concat('u.id', '\'#\'', $db->IfNull('qa.attempt', '0')).' AS uniqueid, '.
            'qa.id as attemptuniqueid, qa.id AS attempt, u.id AS userid, u.firstname, u.lastname, u.picture, '.
            'qa.score, qa.timefinish, qa.timestart, qa.timefinish - qa.timestart AS duration ';
        if ($course->id != SITEID) {
            // This is too complicated, so just do it for each of the four cases.
            if (!empty($currentgroup) && empty($noattempts)) {
                // We want a particular group and we only want to see students WITH attempts.
                // So join on groups_members and do an inner join on attempts.
                $from  = 'FROM {user} u JOIN {role_assignments} ra ON ra.userid = u.id '.
                    groups_members_join_sql().
                    'JOIN {game_attempts} qa ON u.id = qa.userid AND qa.gameid = '.$game->id;
                $where = ' WHERE ra.contextid ' . $contextlists .
                        ' AND '. groups_members_where_sql($currentgroup) .' AND qa.preview = 0';
            } else if (!empty($currentgroup) && !empty($noattempts)) {
                // We want a particular group and we want to do something funky with attempts.
                // So join on groups_members and left join on attempts...
                $from  = 'FROM {user} u JOIN {role_assignments} ra ON ra.userid = u.id '.
                    groups_members_join_sql().
                    'LEFT JOIN {game_attempts} qa ON u.id = qa.userid AND qa.gameid = '.$game->id;
                $where = ' WHERE ra.contextid ' .$contextlists . ' AND '.groups_members_where_sql($currentgroup);
                if ($noattempts == 1) {
                    // Noattempts = 1 means only no attempts, so make the left join ask.
                    // For only records where the right is null (no attempts).
                    $where .= ' AND qa.userid IS NULL'; // Show ONLY no attempts.
                } else {
                    // We are including attempts, so exclude previews.
                    $where .= ' AND qa.preview = 0';
                }
            } else if (empty($currentgroup)) {
                // We don't care about group, and we to do something funky with attempts.
                // So do a left join on attempts.
                $from  = 'FROM {user} u JOIN {role_assignments} ra ON ra.userid = u.id '.
                    ' LEFT JOIN {game_attempts} qa ON u.id = qa.userid AND qa.gameid = '.$game->id;
                $where = " WHERE ra.contextid $contextlists";
                if (empty($noattempts)) {
                    // Show ONLY students with attempts.
                    $where .= ' AND qa.userid IS NOT NULL AND qa.preview = 0';
                } else if ($noattempts == 1) {
                    // The noattempts = 1 means only no attempts,.
                    // So make the left join ask for only records where the right is null (no attempts).
                    // Show ONLY students without attempts.
                    $where .= ' AND qa.userid IS NULL';
                } else if ($noattempts == 3) {
                    // We want all attempts.
                    $from  = 'FROM {user} u JOIN {game_attempts} qa ON u.id = qa.userid ';
                    $where = ' WHERE qa.gameid = '.$game->id.' AND qa.preview = 0';
                } // The noattempts = 2 means we want all students, with or without attempts.
            }
            $countsql = 'SELECT COUNT(DISTINCT('.sql_concat('u.id', '\'#\'', $db->IfNull('qa.attempt', '0')).')) '.$from.$where;
        } else {
            if (empty($noattempts)) {
                $from   = 'FROM {user} u JOIN {game_attempts} qa ON u.id = qa.userid ';
                $where = ' WHERE qa.gameid = '.$game->id.' AND qa.preview = 0';
                $countsql = 'SELECT COUNT(DISTINCT('.sql_concat('u.id', '\'#\'', $db->IfNull('qa.attempt', '0')).')) '.$from.$where;
            }
        }
        if (!$download) {
            // Add extra limits due to initials bar.
            if ($table->get_sql_where()) {
                $where .= ' AND '.$table->get_sql_where();
            }

            // Count the records NOW, before funky question grade sorting messes up $from.
            if (!empty($countsql)) {
                $totalinitials = count_records_sql($countsql);
                if ($table->get_sql_where()) {
                    $countsql .= ' AND '.$table->get_sql_where();
                }
                $total  = count_records_sql($countsql);
            }

            // Add extra limits due to sorting by question grade.
            if ($sort = $table->get_sql_sort()) {
                $sortparts    = explode(',', $sort);
                $newsort      = array();
                $questionsort = false;
                foreach ($sortparts as $sortpart) {
                    $sortpart = trim($sortpart);
                    if (substr($sortpart, 0, 1) == '$') {
                        if (!$questionsort) {
                            $qid          = intval(substr($sortpart, 1));
                            $select .= ', grade ';
                            $from        .= ' LEFT JOIN {question_sessions} qns ON qns.attemptid = qa.id '.
                                                'LEFT JOIN {question_states} qs ON qs.id = qns.newgraded ';
                            $where       .= ' AND ('.sql_isnull('qns.questionid').' OR qns.questionid = '.$qid.')';
                            $newsort[]    = 'grade '.(strpos($sortpart, 'ASC') ? 'ASC' : 'DESC');
                            $questionsort = true;
                        }
                    } else {
                        $newsort[] = $sortpart;
                    }
                }

                // Reconstruct the sort string.
                $sort = ' ORDER BY '.implode(', ', $newsort);
            }

            // Fix some wired sorting.
            if (empty($sort)) {
                $sort = ' ORDER BY qa.id';
            }

            $table->pagesize($pagesize, $total);
        }

        // If there is feedback, include it in the query.
        if ($hasfeedback) {
            $select .= ', qf.feedbacktext ';
            $from .= " JOIN {game_feedback} qf ON " .
                    "qf.gameid = $game->id AND qf.mingrade <= qa.score * $game->grade  AND qa.score * $game->grade < qf.maxgrade";
        }

        // Fetch the attempts.
        if (!empty($from)) {
            // If we're in the site course and displaying no attempts, it makes no sense to do the query.
            if (!$download) {
                $attempts = get_records_sql($select.$from.$where.$sort,
                    $table->get_page_start(), $table->get_page_size());
            } else {
                $attempts = get_records_sql($select.$from.$where.$sort);
            }
        } else {
            $attempts = array();
        }

        // Build table rows.
        if (!$download) {
            $table->initialbars($totalinitials > 20);
        }
        if (!empty($attempts) || !empty($noattempts)) {
            if ($attempts) {
                foreach ($attempts as $attempt) {
                    $picture = print_user_picture($attempt->userid, $course->id, $attempt->picture, false, true);
                    /* Uncomment the commented lines below if you are choosing to show unenrolled users and
                     * have uncommented the corresponding lines earlier in this script
                     * if (in_array($attempt->userid, $unenrolledusers)) {
                     *    $userlink = '<a class="dimmed" href="'.$CFG->wwwroot.
                     *       '/user/view.php?id='.$attempt->userid.'&amp;course='.$course->id.'">'.fullname($attempt).'</a>';
                     *}
                     *else {
                     *   $userlink = '<a href="'.$CFG->wwwroot.'/user/view.php?id='.
                     *      $attempt->userid.'&amp;course='.$course->id.'">'.fullname($attempt).'</a>';
                     *}
                     */
                    if (!$download) {
                        $row = array(
                                '<input type="checkbox" name="attemptid[]" value="'.$attempt->attempt.'" />',
                                $picture,
                                $userlink,
                                empty($attempt->attempt) ? '-' : '<a href="review.php?q='.
                                    $game->id.'&amp;attempt='.$attempt->attempt.'">'.
                                    userdate($attempt->timestart, $strtimeformat).'</a>',
                                empty($attempt->timefinish) ? '-' : '<a href="review.php?q='.
                                    $game->id.'&amp;attempt='.$attempt->attempt.'">'.
                                    userdate($attempt->timefinish, $strtimeformat).'</a>',
                                empty($attempt->attempt) ? '-' : (
                                    empty($attempt->timefinish) ? get_string('unfinished', 'game') : format_time(
                                    $attempt->duration))
                        );
                    } else {
                        $row = array(fullname($attempt),
                                empty($attempt->attempt) ? '-' : userdate($attempt->timestart, $strtimeformat),
                                empty($attempt->timefinish) ? '-' : userdate($attempt->timefinish, $strtimeformat),
                                empty($attempt->attempt) ? '-' : (
                                    empty($attempt->timefinish) ? get_string(
                                    'unfinished', 'game') : format_time($attempt->duration))
                        );
                    }

                    if ($game->grade) {
                        if (!$download) {
                            $row[] = $attempt->score === null ? '-' : '<a href="review.php?q='.
                                $game->id.'&amp;attempt='.$attempt->attempt.'">'.
                                round($attempt->score * $game->grade, $game->decimalpoints).'</a>';
                        } else {
                            $row[] = $attempt->score === null ? '-' : round($attempt->score * $game->grade, $game->decimalpoints);
                        }
                    }
                    if ($detailedmarks) {
                        if (empty($attempt->attempt)) {
                            foreach ($questionids as $questionid) {
                                $row[] = '-';
                            }
                        } else {
                            foreach ($questionids as $questionid) {
                                if ($gradedstateid = get_field('question_sessions', 'newgraded',
                                    'attemptid', $attempt->attemptuniqueid, 'questionid', $questionid)) {
                                    $grade = round(get_field('question_states', 'grade', 'id',
                                        $gradedstateid), $game->decimalpoints);
                                } else {
                                    $grade = '--';
                                }
                                if (!$download) {
                                    $row[] = link_to_popup_window (
                                        '/mod/game/reviewquestion.php?state='.
                                        $gradedstateid.'&amp;number='.
                                        $questions[$questionid]->number, 'reviewquestion', $grade,
                                        450, 650, $strreviewquestion, 'none', true);
                                } else {
                                    $row[] = $grade;
                                }
                            }
                        }
                    }
                    if ($hasfeedback) {
                        if ($attempt->timefinish) {
                            $row[] = $attempt->feedbacktext;
                        } else {
                            $row[] = '-';
                        }
                    }
                    if (!$download) {
                        $table->add_data($row);
                    } else if ($download == 'Excel' or $download == 'ODS') {
                        $colnum = 0;
                        foreach ($row as $item) {
                            $myxls->write($rownum, $colnum, $item, $format);
                            $colnum++;
                        }
                        $rownum++;
                    } else if ($download == 'CSV') {
                        $text = implode("\t", $row);
                        echo $text." \n";
                    }
                }
            }
            if (!$download) {
                // Start form.
                echo '<div id="tablecontainer">';
                echo '<form id="attemptsform" method="post" action="report.php" '.
                    'onsubmit="var menu = document.getElementById(\'menuaction\'); '.
                    'return (menu.options[menu.selectedIndex].value == \'delete\' ? confirm(\''.$strreallydel.'\') : true);">';
                echo '<div>';
                echo '<input type="hidden" name="id" value="'.$cm->id.'" />';
                echo '<input type="hidden" name="mode" value="overview" />';

                // Print table.
                $table->print_html();

                // Print "Select all" etc..
                if (!empty($attempts)) {
                    echo '<table id="commands">';
                    echo '<tr><td>';
                    echo '<a href="javascript:select_all_in(\'DIV\',null,\'tablecontainer\');">'.
                        get_string('selectall', 'game').'</a> / ';
                    echo '<a href="javascript:deselect_all_in(\'DIV\',null,\'tablecontainer\');">'.
                        get_string('selectnone', 'game').'</a> ';
                    echo '&nbsp;&nbsp;';
                    $options = array('delete' => get_string('delete'));
                    echo choose_from_menu($options, 'action', '', get_string('withselected', 'game'),
                        'if(this.selectedIndex > 0) submitFormById(\'attemptsform\');', '', true);
                    echo '<noscript id="noscriptmenuaction" style="display: inline;"><div>';
                    echo '<input type="submit" value="'.get_string('go').'" /></div></noscript>';
                    echo '<script type="text/javascript">'."\n<!--\n".
                        'document.getElementById("noscriptmenuaction").style.display = "none";'
                        ."\n-->\n".'</script>';
                    echo '</td></tr></table>';
                }
                // Close form.
                echo '</div>';
                echo '</form></div>';

                if (!empty($attempts)) {
                    echo '<table class="boxaligncenter"><tr>';
                    $options = array();
                    $options["id"] = "$cm->id";
                    $options["q"] = "$game->id";
                    $options["mode"] = "overview";
                    $options['sesskey'] = sesskey();
                    $options["noheader"] = "yes";
                    $options['noattempts'] = $noattempts;
                    $options['detailedmarks'] = $detailedmarks;
                    echo '<td>';
                    $options["download"] = "ODS";
                    print_single_button("report.php", $options, get_string("downloadods", 'game'));
                    echo "</td>\n";
                    echo '<td>';
                    $options["download"] = "Excel";
                    print_single_button("report.php", $options, get_string("downloadexcel"));
                    echo "</td>\n";
                    echo '<td>';
                    $options["download"] = "CSV";
                    print_single_button('report.php', $options, get_string("downloadtext"));
                    echo "</td>\n";
                    echo "<td>";
                    helpbutton('overviewdownload', get_string('overviewdownload', 'quiz'), 'game');
                    echo "</td>\n";
                    echo '</tr></table>';
                }
            } else if ($download == 'Excel' or $download == 'ODS') {
                $workbook->close();
                exit;
            } else if ($download == 'CSV') {
                exit;
            }

        } else {
            if (!$download) {
                $table->print_html();
            }
        }
        // Print display options.
        echo '<div class="controls">';
        echo '<form id="options" action="report.php" method="get">';
        echo '<div>';
        echo '<p>'.get_string('displayoptions', 'game').': </p>';
        echo '<input type="hidden" name="id" value="'.$cm->id.'" />';
        echo '<input type="hidden" name="q" value="'.$game->id.'" />';
        echo '<input type="hidden" name="mode" value="overview" />';
        echo '<input type="hidden" name="noattempts" value="0" />';
        echo '<input type="hidden" name="detailedmarks" value="0" />';
        echo '<table id="overview-options" class="boxaligncenter">';
        echo '<tr align="left">';
        echo '<td><label for="pagesize">'.get_string('pagesize', 'game').'</label></td>';
        echo '<td><input type="text" id="pagesize" name="pagesize" size="3" value="'.$pagesize.'" /></td>';
        echo '</tr>';
        echo '<tr align="left">';
        echo '<td colspan="2">';
        $options = array(0 => get_string('attemptsonly', 'game', $course->students));
        if ($course->id != SITEID) {
            $options[1] = get_string('noattemptsonly', 'game', $course->students);
            $options[2] = get_string('allstudents', 'game', $course->students);
            $options[3] = get_string('allattempts', 'game');
        }
        choose_from_menu($options, 'noattempts', $noattempts, '');
        echo '</td></tr>';
        echo '<tr align="left">';
        echo '<td colspan="2"><input type="checkbox" id="checkdetailedmarks" name="detailedmarks" '.
            ($detailedmarks ? 'checked="checked" ' : '').
            'value="1" /> <label for="checkdetailedmarks">'.
            get_string('showdetailedmarks', 'game').'</label> ';
        echo '</td></tr>';
        echo '<tr><td colspan="2" align="center">';
        echo '<input type="submit" value="'.get_string('go').'" />';
        echo '</td></tr></table>';
        echo '</div>';
        echo '</form>';
        echo '</div>';
        echo "\n";

        return true;
    }
}
