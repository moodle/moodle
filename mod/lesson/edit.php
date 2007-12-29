<?php  // $Id$
/**
 * Provides the interface for overall authoring of lessons
 *
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package lesson
 **/

    require_once('../../config.php');
    require_once('locallib.php');
    require_once('lib.php');

    $id      = required_param('id', PARAM_INT);             // Course Module ID
    $display = optional_param('display', 0, PARAM_INT);
    $mode    = optional_param('mode', get_user_preferences('lesson_view', 'collapsed'), PARAM_ALPHA);
    $pageid = optional_param('pageid', 0, PARAM_INT);
    
    if ($mode != 'single') {
        set_user_preference('lesson_view', $mode);
    }
    
    list($cm, $course, $lesson) = lesson_get_basics($id);
    
    if ($firstpage = get_record('lesson_pages', 'lessonid', $lesson->id, 'prevpageid', 0)) {
        if (!$pages = get_records('lesson_pages', 'lessonid', $lesson->id)) {
            error('Could not find lesson pages');
        }
    }
    
    if ($pageid) {
        if (!$singlepage = get_record('lesson_pages', 'id', $pageid)) {
            error('Could not find page ID: '.$pageid);
        }
    }

    require_login($course->id, false, $cm);
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    require_capability('mod/lesson:manage', $context);
    
    lesson_print_header($cm, $course, $lesson, $mode);

    if (empty($firstpage)) {
        // There are no pages; give teacher some options
        if (has_capability('mod/lesson:edit', $context)) {
            print_simple_box( "<table cellpadding=\"5\" border=\"0\">\n<tr><th scope=\"col\">".get_string("whatdofirst", "lesson")."</th></tr><tr><td>".
                "<a href=\"import.php?id=$cm->id&amp;pageid=0\">".
                get_string("importquestions", "lesson")."</a></td></tr><tr><td>".
                "<a href=\"importppt.php?id=$cm->id&amp;pageid=0\">".
                get_string("importppt", "lesson")."</a></td></tr><tr><td>".
                "<a href=\"lesson.php?id=$cm->id&amp;action=addbranchtable&amp;pageid=0&amp;firstpage=1\">".
                get_string("addabranchtable", "lesson")."</a></td></tr><tr><td>".
                "<a href=\"lesson.php?id=$cm->id&amp;action=addpage&amp;pageid=0&amp;firstpage=1\">".
                get_string("addaquestionpage", "lesson").
                "</a></td></tr></table>\n", 'center', '20%');
        }
    } else {
        // Set some standard variables
        $pageid = $firstpage->id;
        $prevpageid = 0;
        $npages = count($pages);
        
        switch ($mode) {
            case 'collapsed':
                $table = new stdClass;
                $table->head = array(get_string('pagetitle', 'lesson'), get_string('qtype', 'lesson'), get_string('jumps', 'lesson'), get_string('actions', 'lesson'));
                $table->align = array('left', 'left', 'left', 'center');
                $table->wrap = array('', 'nowrap', '', 'nowrap');
                $table->tablealign = 'center';
                $table->cellspacing = 0;
                $table->cellpadding = '2px';
                $table->data = array();
                
                while ($pageid != 0) {
                    $page = $pages[$pageid];

                    if ($page->qtype == LESSON_MATCHING) {
                        // The jumps for matching question type is stored
                        // in the 3rd and 4rth answer record.
                        $limitfrom = $limitnum = 2;
                    } else {
                        $limitfrom = $limitnum = '';
                    }

                    $jumps = array();
                    if($answers = get_records_select("lesson_answers", "lessonid = $lesson->id and pageid = $pageid", 'id', '*', $limitfrom, $limitnum)) {
                        foreach ($answers as $answer) {
                            $jumps[] = lesson_get_jump_name($answer->jumpto);
                        }
                    }
                    
                    $table->data[] = array("<a href=\"$CFG->wwwroot/mod/lesson/edit.php?id=$cm->id&amp;mode=single&amp;pageid=".$page->id."\">".format_string($pages[$pageid]->title,true).'</a>',
                                           lesson_get_qtype_name($page->qtype),
                                           implode("<br />\n", $jumps),
                                           lesson_print_page_actions($cm->id, $page, $npages, true, true)
                                          );
                    $pageid = $page->nextpageid;
                }
                
                print_table($table);
                break;
                
            case 'single':
                // Only viewing a single page in full - change some variables to display just one
                $prevpageid = $singlepage->prevpageid;
                $pageid     = $singlepage->id;
                
                $pages = array();
                $pages[$singlepage->id] = $singlepage;
                
            case 'full':
                echo '<table class="boxaligncenter" cellpadding="5" border="0" style="width:80%;">
                         <tr>
                             <td align="left">';
                lesson_print_add_links($cm->id, $prevpageid);
                echo '       </td>
                         </tr>';

                while ($pageid != 0) {
                    $page = $pages[$pageid];

                    echo "<tr><td>\n";
                    echo "<table style=\"width:100%;\" border=\"1\" class=\"generalbox\"><tr><th colspan=\"2\" scope=\"col\">".format_string($page->title)."&nbsp;&nbsp;\n";
                    lesson_print_page_actions($cm->id, $page, $npages);
                    echo "</th></tr>\n";             
                    echo "<tr><td colspan=\"2\">\n";
                    $options = new stdClass;
                    $options->noclean = true;
                    echo format_text($page->contents, FORMAT_MOODLE, $options);
                    echo "</td></tr>\n";
                    // get the answers in a set order, the id order
                    if ($answers = get_records("lesson_answers", "pageid", $page->id, "id")) {
                        echo "<tr><td colspan=\"2\" align=\"center\"><strong>\n";
                        echo lesson_get_qtype_name($page->qtype);
                        switch ($page->qtype) {
                            case LESSON_SHORTANSWER :
                                if ($page->qoption) {
                                    echo " - ".get_string("casesensitive", "lesson");
                                }
                                break;
                            case LESSON_MULTICHOICE :
                                if ($page->qoption) {
                                    echo " - ".get_string("multianswer", "lesson");
                                }
                                break;
                            case LESSON_MATCHING :
                                echo get_string("firstanswershould", "lesson");
                                break;
                        }
                        echo "</strong></td></tr>\n";
                        $i = 1;
                        $n = 0;
                        $options = new stdClass;
                        $options->noclean = true;
                        $options->para = false;
                        foreach ($answers as $answer) {
                            switch ($page->qtype) {
                                case LESSON_MULTICHOICE:
                                case LESSON_TRUEFALSE:
                                case LESSON_SHORTANSWER:
                                case LESSON_NUMERICAL:
                                    echo "<tr><td align=\"right\" valign=\"top\" style=\"width:20%;\">\n";
                                    if ($lesson->custom) {
                                        // if the score is > 0, then it is correct
                                        if ($answer->score > 0) {
                                            echo '<span class="labelcorrect">'.get_string("answer", "lesson")." $i</span>: \n";
                                        } else {
                                            echo '<span class="label">'.get_string("answer", "lesson")." $i</span>: \n";
                                        }
                                    } else {
                                        if (lesson_iscorrect($page->id, $answer->jumpto)) {
                                            // underline correct answers
                                            echo '<span class="correct">'.get_string("answer", "lesson")." $i</span>: \n";
                                        } else {
                                            echo '<span class="labelcorrect">'.get_string("answer", "lesson")." $i</span>: \n";
                                        }
                                    }
                                    echo "</td><td style=\"width:80%;\">\n";
                                    echo format_text($answer->answer, FORMAT_MOODLE, $options);
                                    echo "</td></tr>\n";
                                    echo "<tr><td align=\"right\" valign=\"top\"><span class=\"label\">".get_string("response", "lesson")." $i</span>: \n";
                                    echo "</td><td>\n";
                                    echo format_text($answer->response, FORMAT_MOODLE, $options); 
                                    echo "</td></tr>\n";
                                    break;                            
                                case LESSON_MATCHING:
                                    if ($n < 2) {
                                        if ($answer->answer != NULL) {
                                            if ($n == 0) {
                                                echo "<tr><td align=\"right\" valign=\"top\"><span class=\"label\">".get_string("correctresponse", "lesson")."</span>: \n";
                                                echo "</td><td>\n";
                                                echo format_text($answer->answer, FORMAT_MOODLE, $options); 
                                                echo "</td></tr>\n";
                                            } else {
                                                echo "<tr><td align=\"right\" valign=\"top\"><span class=\"label\">".get_string("wrongresponse", "lesson")."</span>: \n";
                                                echo "</td><td>\n";
                                                echo format_text($answer->answer, FORMAT_MOODLE, $options); 
                                                echo "</td></tr>\n";
                                            }
                                        }
                                        $n++;
                                        $i--;
                                    } else {
                                        echo "<tr><td align=\"right\" valign=\"top\" style=\"width:20%;\">\n";
                                        if ($lesson->custom) {
                                            // if the score is > 0, then it is correct
                                            if ($answer->score > 0) {
                                                echo '<span class="labelcorrect">'.get_string("answer", "lesson")." $i</span>: \n";
                                            } else {
                                                echo '<span class="label">'.get_string("answer", "lesson")." $i</span>: \n";
                                            }
                                        } else {
                                            if (lesson_iscorrect($page->id, $answer->jumpto)) {
                                                // underline correct answers
                                                echo '<span class="labelcorrect">'.get_string("answer", "lesson")." $i</span>: \n";
                                            } else {
                                                echo '<span class="label">'.get_string("answer", "lesson")." $i</span>: \n";
                                            }
                                        }
                                        echo "</td><td style=\"width:80%;\">\n";
                                        echo format_text($answer->answer, FORMAT_MOODLE, $options);
                                        echo "</td></tr>\n";
                                        echo "<tr><td align=\"right\" valign=\"top\"><span class=\"label\">".get_string("matchesanswer", "lesson")." $i</span>: \n";
                                        echo "</td><td>\n";
                                        echo format_text($answer->response, FORMAT_MOODLE, $options); 
                                        echo "</td></tr>\n";
                                    }
                                    break;
                                case LESSON_BRANCHTABLE:
                                    echo "<tr><td align=\"right\" valign=\"top\" style=\"width:20%;\">\n";
                                    echo '<span class="label">'.get_string("description", "lesson")." $i</span>: \n";
                                    echo "</td><td style=\"width:80%;\">\n";
                                    echo format_text($answer->answer, FORMAT_MOODLE, $options);
                                    echo "</td></tr>\n";
                                    break;
                            }

                            $jumptitle = lesson_get_jump_name($answer->jumpto);
                            if ($page->qtype == LESSON_MATCHING) {
                                if ($i == 1) {
                                    echo "<tr><td align=\"right\" style=\"width:20%;\"><span class=\"label\">".get_string("correctanswerscore", "lesson");
                                    echo "</span>: </td><td style=\"width:80%;\">\n";
                                    echo "$answer->score</td></tr>\n";
                                    echo "<tr><td align=\"right\" style=\"width:20%;\"><span class=\"label\">".get_string("correctanswerjump", "lesson");
                                    echo "</span>:</td><td style=\"width:80%;\">\n";
                                    echo "$jumptitle</td></tr>\n";
                                } elseif ($i == 2) {
                                    echo "<tr><td align=\"right\" style=\"width:20%;\"><span class=\"label\">".get_string("wronganswerscore", "lesson");
                                    echo "</span>: </td><td style=\"width:80%;\">\n";
                                    echo "$answer->score</td></tr>\n";
                                    echo "<tr><td align=\"right\" style=\"width:20%;\"><span class=\"label\">".get_string("wronganswerjump", "lesson");
                                    echo "</span>: </td><td style=\"width:80%;\">\n";
                                    echo "$jumptitle</td></tr>\n";
                                }
                            } else {
                                if ($lesson->custom and 
                                    $page->qtype != LESSON_BRANCHTABLE and 
                                    $page->qtype != LESSON_ENDOFBRANCH and
                                    $page->qtype != LESSON_CLUSTER and 
                                    $page->qtype != LESSON_ENDOFCLUSTER) {
                                    echo "<tr><td align=\"right\" style=\"width:20%;\"><span class=\"label\">".get_string("score", "lesson")." $i";
                                    echo "</span>: </td><td style=\"width:80%;\">\n";
                                    echo "$answer->score</td></tr>\n";
                                }
                                echo "<tr><td align=\"right\" style=\"width:20%;\"><span class=\"label\">".get_string("jump", "lesson")." $i";
                                echo "</span>: </td><td style=\"width:80%;\">\n";
                                echo "$jumptitle</td></tr>\n";
                            }
                            $i++;
                        }
                    }
                    echo "</table></td></tr>\n<tr><td align=\"left\">";
                    lesson_print_add_links($cm->id, $page->id);
                    echo "</td></tr><tr><td>\n";
                    // check the prev links - fix (silently) if necessary - there was a bug in
                    // versions 1 and 2 when add new pages. Not serious then as the backwards
                    // links were not used in those versions
                    if ($page->prevpageid != $prevpageid) {
                        // fix it
                        set_field("lesson_pages", "prevpageid", $prevpageid, "id", $page->id);
                        debugging("<p>***prevpageid of page $page->id set to $prevpageid***");
                    }
                    
                    if (count($pages) == 1) {
                        echo "</td></tr>";
                        break;
                    }
                    
                    $prevpageid = $page->id;
                    $pageid = $page->nextpageid;
                    echo "</td></tr>";
                }
                echo "</table>";
                break;
        }
    } 

    print_footer($course);
?>
