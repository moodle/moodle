<?php // $Id$

    require_once('../../../config.php');
    require_once($CFG->dirroot.'/question/upgrade.php');
    require_once($CFG->libdir.'/adminlib.php');

    admin_externalpage_setup('reportquestion');
    admin_externalpage_print_header();

    print_heading(get_string('adminreport', 'question'));

    $probstr = '';
    $contextupgradeversion = 2007081000;
    if ($CFG->version < $contextupgradeversion){
        ///cwrqpfs issue
        $probstr = print_heading(get_string('cwrqpfs', 'question'), '', 3, 'main', true);

        if ($updates = question_cwqpfs_to_update()){

            $probstr .=get_string('cwrqpfsinfo', 'question');
            $probstr .= '<ul>';
            $catlist = join(array_keys($updates), ',');
            //get info about cateogries and no of questions used outside category's course
            $categories = get_records_sql('SELECT qc.*, c.fullname as coursename FROM '.$CFG->prefix.'question_categories as qc, '
                        .$CFG->prefix.'course as c WHERE qc.course = c.id AND qc.id IN ('.$catlist.')');
            foreach ($updates as $id => $publish){
                $categories[$id]->caturl = "$CFG->wwwroot/question/category.php?sesskey=".sesskey().
                            "&amp;edit=$id&amp;courseid=".$categories[$id]->course;
                if ($categories[$id]->publish){
                    $categories[$id]->changefrom = get_string('published', 'question');
                    $categories[$id]->changeto = get_string('unpublished', 'question');
                } else {
                    $categories[$id]->changefrom = get_string('unpublished', 'question');
                    $categories[$id]->changeto = get_string('published', 'question');
                }
                $probstr .= '<li>'.get_string('changepublishstatuscat', 'question', $categories[$id]);
                if ($questions = get_records_sql('SELECT q.*, qui.id as quizid, qui.name as quizname, cm.id as cmid, '
                               .'qui.course, c.fullname as coursename FROM '.$CFG->prefix.'question q, '
                               .$CFG->prefix.'quiz_question_instances qqi, '
                               .$CFG->prefix.'quiz qui, '
                               .$CFG->prefix.'course_modules cm, '
                               .$CFG->prefix.'modules m, '
                               .$CFG->prefix.'course c '
                            .'WHERE (q.category = '.$id.' AND qqi.question = q.id '
                            .'AND qqi.quiz = qui.id '
                            .'AND qui.course = c.id '
                            .'AND cm.instance = qui.id '
                            .'AND cm.module = m.id '
                            .'AND m.name = \'quiz\''
                            .'AND ('.$categories[$id]->course.' <> qui.course)) ORDER BY qui.id ASC')){

                    $probstr .= '<ul>';
                    foreach ($questions as $question){
                        $question->quizurl = "$CFG->wwwroot/mod/quiz/edit.php?cmid=".$question->cmid;
                        $question->qurl = "$CFG->wwwroot/question/question.php?cmid={$question->cmid}&amp;id={$question->id}&amp;returnurl=".urlencode($FULLME);
                        $probstr .= '<li>'.get_string('questionaffected', 'question', $question).'</li>';
                    }
                    $probstr .= '</ul>';
                }
                $probstr .= '</li>';
            }
            $probstr .= '</ul>';
        } else {
            $probstr .=('<p>'.get_string('cwrqpfsnoprob', 'question').'</p>');
        }
    }
    if ($probstr) {
        print_box($probstr);
    } else {
        print_box(get_string('noprobs', 'question'), 'boxwidthnarrow boxaligncenter generalbox');
    }
    admin_externalpage_print_footer();
?>
