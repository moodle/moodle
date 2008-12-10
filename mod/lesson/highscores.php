<?php  // $Id$
/**
 * Provides the interface for viewing and adding high scores
 *
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package lesson
 **/

    require_once('../../config.php');
    require_once('locallib.php');
    require_once('lib.php');

    $id      = required_param('id', PARAM_INT);             // Course Module ID
    $mode    = optional_param('mode', '', PARAM_ALPHA);
    $link = optional_param('link', 0, PARAM_INT);

    list($cm, $course, $lesson) = lesson_get_basics($id);
    
    require_login($course->id, false, $cm);
    
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    
    
    switch ($mode) {
        case 'add':
            // Ensure that we came from view.php
            if (!confirm_sesskey() or !data_submitted("$CFG->wwwroot/mod/lesson/view.php")) {
                error('Incorrect Form Data');
            }
            break;
            
        case 'save':
            if (confirm_sesskey() and $form = data_submitted($CFG->wwwroot.'/mod/lesson/view.php')) {
                $name = trim(optional_param('name', '', PARAM_CLEAN));
                
                // Make sure it is not empty
                if (empty($name)) {
                    lesson_set_message(get_string('missingname', 'lesson'));
                    $mode = 'add';
                    break;
                }     
                // Check for censored words
                $filterwords = explode(',', get_string('censorbadwords'));
                foreach ($filterwords as $filterword) {
                    if (strstr($name, $filterword)) {
                        lesson_set_message(get_string('namereject', 'lesson'));
                        $mode = 'add';
                        break;
                    }
                }
                // Bad word was found
                if ($mode == 'add') {
                    break;
                }
                
                if (!$grades = get_records_select('lesson_grades', "lessonid = $lesson->id", 'completed')) {
                    error('Error: could not find grades');
                }
                if (!$newgrade = get_record_sql("SELECT * 
                                                   FROM {$CFG->prefix}lesson_grades 
                                                  WHERE lessonid = $lesson->id
                                                    AND userid = $USER->id 
                                               ORDER BY completed DESC", true)) {
                    error('Error: could not find newest grade');
                }
                
                // Check for multiple submissions
                if (record_exists('lesson_high_scores', 'gradeid', $newgrade->id)) {
                    error('Only one posting per grade');
                }
                
                // Find out if we need to delete any records
                if ($highscores = get_records_sql("SELECT h.*, g.grade 
                                                     FROM {$CFG->prefix}lesson_grades g, {$CFG->prefix}lesson_high_scores h 
                                                    WHERE h.gradeid = g.id
                                                    AND h.lessonid = $lesson->id
                                                    ORDER BY g.grade DESC")) {
                    // Only count unique scores in our total for max high scores
                    $uniquescores = array();
                    foreach ($highscores as $highscore) {
                        $uniquescores[$highscore->grade] = 1;
                    }
                    if (count($uniquescores) >= $lesson->maxhighscores) {
                        // Top scores list is full, might need to delete a score
                        $flag = true;                
                        // See if the new score is already listed in the top scores list
                        // if it is listed, then dont need to delete any records
                        foreach ($highscores as $highscore) {
                            if ($newgrade->grade == $highscore->grade) {
                                $flag = false;
                            }
                        }    
                        if ($flag) {
                            // Pushing out the lowest score (could be multiple records)
                            $lowscore = 0;
                            foreach ($highscores as $highscore) {
                                if (empty($lowscore) or $lowscore > $highscore->grade) {
                                    $lowscore = $highscore->grade;
                                }
                            }
                            // Now, delete all high scores with the low score
                            foreach ($highscores as $highscore) {
                                if ($highscore->grade == $lowscore) {
                                    delete_records('lesson_high_scores', 'id', $highscore->id);
                                }
                            }
                        }
                    }
                }

                $newhighscore = new stdClass;
                $newhighscore->lessonid = $lesson->id;
                $newhighscore->userid = $USER->id;
                $newhighscore->gradeid = $newgrade->id;
                $newhighscore->nickname = $name;

                if (!insert_record('lesson_high_scores', $newhighscore)) {
                    error("Insert of new high score Failed!");
                }
                
                // Log it
                add_to_log($course->id, 'lesson', 'update highscores', "highscores.php?id=$cm->id", $name, $cm->id);
                
                lesson_set_message(get_string('postsuccess', 'lesson'), 'notifysuccess');
                redirect("$CFG->wwwroot/mod/lesson/highscores.php?id=$cm->id&amp;link=1");
            } else {
                error('Something is wrong with the form data');
            }
            break;
    }

    // Log it
    add_to_log($course->id, 'lesson', 'view highscores', "highscores.php?id=$cm->id", $lesson->name, $cm->id);

    lesson_print_header($cm, $course, $lesson, 'highscores');

    switch ($mode) {
        case 'add':
            print_simple_box_start('center');
            echo '<div class="mdl-align">
                 <form id="nickname" method ="post" action="'.$CFG->wwwroot.'/mod/lesson/highscores.php" autocomplete="off">
                 <input type="hidden" name="id" value="'.$cm->id.'" />
                 <input type="hidden" name="mode" value="save" />
                 <input type="hidden" name="sesskey" value="'.sesskey().'" />';

            echo get_string("entername", "lesson").": <input type=\"text\" name=\"name\" size=\"7\" maxlength=\"5\" />\n<p>\n";
            lesson_print_submit_link(get_string("submitname", "lesson"), 'nickname');
            echo "</p>\n</form>\n</div>\n";
            print_simple_box_end();
            break;
        default:
            if (!$grades = get_records_select("lesson_grades", "lessonid = $lesson->id", "completed")) {
                $grades = array();
            }
        
            print_heading(get_string("topscorestitle", "lesson", $lesson->maxhighscores), 'center', 4);

            if (!$highscores = get_records_select("lesson_high_scores", "lessonid = $lesson->id")) {
                print_heading(get_string("nohighscores", "lesson"), 'center', 3);
            } else {
                foreach ($highscores as $highscore) {
                    $grade = $grades[$highscore->gradeid]->grade;
                    $topscores[$grade][] = $highscore->nickname;
                }
                krsort($topscores);
                       
                $table = new stdClass;
                $table->align = array('center', 'left', 'right');
                $table->wrap = array();
                $table->width = "30%";
                $table->cellspacing = '10px';
                $table->size = array('*', '*', '*');
            
                $table->head = array(get_string("rank", "lesson"), $course->students, get_string("scores", "lesson"));
            
                $printed = 0;
                while (true) {
                    $temp = current($topscores);
                    $score = key($topscores);
                    $rank = $printed + 1;
                    sort($temp); 
                    foreach ($temp as $student) {
                        $table->data[] = array($rank, $student, $score.'%');
                    }
                    $printed++;
                    if (!next($topscores) || !($printed < $lesson->maxhighscores)) { 
                        break;
                    }
                }
                print_table($table);
            }
        
            if (!has_capability('mod/lesson:manage', $context)) {  // teachers don't need the links
                echo '<div style="text-align:center">';
                if ($link) {
                    echo "<br /><div class=\"lessonbutton standardbutton\"><a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">".get_string("returntocourse", "lesson")."</a></div>";
                } else {
                    echo "<br /><span class=\"lessonbutton standardbutton\"><a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">".get_string("cancel", "lesson").'</a></span> '.
                        " <span class=\"lessonbutton standardbutton\"><a href=\"$CFG->wwwroot/mod/lesson/view.php?id=$cm->id&amp;viewed=1\">".get_string("startlesson", "lesson").'</a></span>';
                }
                echo "</div>";
            }
            break;
    }
    
    print_footer($course);

?>
