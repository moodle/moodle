<?php
/**
 * Allows someone with appropriate permissions to move a category and associated
 * files to another context.
 *
 * @author Jamie Pratt
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questionbank
 */

    require_once("../config.php");
    require_once($CFG->dirroot."/question/editlib.php");
    require_once($CFG->dirroot."/question/contextmove_form.php");

    list($thispageurl, $contexts, $cmid, $cm, $module, $pagevars) =
            question_edit_setup('categories', '/question/contextmove.php');

    // get values from form for actions on this page
    $toparent = required_param('toparent', PARAM_SEQUENCE);
    $cattomove = required_param('cattomove', PARAM_INT);
    $totop = optional_param('totop', 0, PARAM_INT); // optional param moves category to top of peers. Default is
        //to add it to the bottom.



    $onerrorurl = new moodle_url('/question/category.php', $thispageurl->params());
    list($toparent, $contextto) = explode(',', $toparent);
    if (!empty($toparent)){//not top level category, make it a child of $toparent
        if (!$toparent = $DB->get_record('question_categories', array('id' => $toparent))){
            print_error('invalidcategoryidforparent', 'question', $onerrorurl);
        }
        $contextto = $toparent->contextid;
    } else {
        $toparent = new object();
        $toparent->id = 0;
        $toparent->contextid = $contextto;
    }
    if (!$cattomove = $DB->get_record('question_categories', array('id' => $cattomove))){
        print_error('invalidcategoryidtomove', 'question', $onerrorurl);
    }
    if ($cattomove->contextid == $contextto){
        print_error('contexterror', '', $onerrorurl);
    }
    $cattomove->categorylist = question_categorylist($cattomove->id);

    $thispageurl->params(array('cattomove'=>$cattomove->id,
                                  'toparent'=>"{$toparent->id},{$toparent->contextid}",
                                  'totop'=>$totop));

    $contextfrom = get_context_instance_by_id($cattomove->contextid);
    $contextto = get_context_instance_by_id($contextto);
    $contexttostring = print_context_name($contextto);

    require_capability('moodle/question:managecategory', $contextfrom);
    require_capability('moodle/question:managecategory', $contextto);


    $fromcoursefilesid = get_filesdir_from_context($contextfrom);//siteid or courseid
    $tocoursefilesid = get_filesdir_from_context($contextto);//siteid or courseid
    if ($fromcoursefilesid != $tocoursefilesid){
        list($usql, $params) = $DB->get_in_or_equal(explode(',', $cattomove->categorylist));
        $questions = $DB->get_records_select('question', "category $usql", $params);
        $urls = array();
        if ($questions){
            foreach ($questions as $id => $question){
                $QTYPES[$questions[$id]->qtype]->get_question_options($questions[$id]);
                $urls = array_merge_recursive($urls, $QTYPES[$questions[$id]->qtype]->find_file_links($questions[$id], $fromcoursefilesid));
            }
        }
        ksort($urls);
    } else {
        $urls = array();
    }
    $brokenurls = array();
    foreach (array_keys($urls) as $url){
        if (!file_exists($CFG->dataroot."/$fromcoursefilesid/".$url)){
            $brokenurls[] = $url;
        }
    }
    if ($fromcoursefilesid == SITEID){
        $fromareaname = get_string('filesareasite', 'question');
    } else {
        $fromareaname = get_string('filesareacourse', 'question');
    }
    if ($tocoursefilesid == SITEID){
        $toareaname = get_string('filesareasite', 'question');
    } else {
        $toareaname = get_string('filesareacourse', 'question');
    }
    $contextmoveform = new question_context_move_form($thispageurl,
                            compact('urls', 'fromareaname', 'toareaname', 'brokenurls',
                                    'fromcoursefilesid', 'tocoursefilesid'));
    if ($contextmoveform->is_cancelled()){
        $thispageurl->remove_params('cattomove', 'toparent', 'totop');
        redirect(new moodle_url("/question/category.php".$thispageurl->params()));
    }elseif ($moveformdata = $contextmoveform->get_data()) {
        if (isset($moveformdata->urls) && is_array($moveformdata->urls)){
            check_dir_exists($CFG->dataroot."/$tocoursefilesid/", true);
            $flipurls = array_keys($urls);
            foreach ($moveformdata->urls as $key => $urlaction){
                $source = $CFG->dataroot."/$fromcoursefilesid/".$flipurls[$key];
                $destination = $flipurls[$key];
                if (($urlaction != QUESTION_FILEDONOTHING) && ($urlaction != QUESTION_FILEMOVELINKSONLY)){
                    // Ensure the target folder exists.
                    check_dir_exists(dirname($CFG->dataroot."/$tocoursefilesid/".$destination), true);

                    // Then make sure the destination file name does not exist. If it does, change the name to be unique.
                    while (file_exists($CFG->dataroot."/$tocoursefilesid/".$destination)){
                        $matches = array();
                        //check for '_'. copyno after filename, before extension.
                        if (preg_match('!\_([0-9]+)(\.[^\.\\/]+)?$!', $destination, $matches)){
                            $copyno = $matches[1]+1;
                        } else {
                            $copyno = 1;
                        }
                        //replace old copy no with incremented one.
                        $destination = preg_replace('!(\_[0-9]+)?(\.[^\.\\/]+)?$!', '_'.$copyno.'\\2', $destination, 1);
                    }
                }
                switch ($urlaction){
                    case QUESTION_FILECOPY :
                        if (!copy($source, $CFG->dataroot."/$tocoursefilesid/".$destination)){
                            print_error('errorfilecannotbecopied', 'question', $onerrorurl, $source);
                        }
                        break;
                    case QUESTION_FILEMOVE :
                        if (!rename($source, $CFG->dataroot."/$tocoursefilesid/".$destination)){
                            print_error('errorfilecannotbemoved', 'question', $onerrorurl, $source);
                        }
                        break;
                    case QUESTION_FILEDONOTHING :
                    case QUESTION_FILEMOVELINKSONLY :
                        break;
                    default :
                        print_error('invalidaction', '', $onerrorurl);
                }
                switch ($urlaction){
                    //now search and replace urls in questions.
                    case QUESTION_FILECOPY :
                    case QUESTION_FILEMOVE :
                    case QUESTION_FILEMOVELINKSONLY :
                        $url = $flipurls[$key];
                        $questionids = array_unique($urls[$url]);
                        foreach ($questionids as $questionid){
                            $question = $questions[$questionid];
                            $QTYPES[$question->qtype]->replace_file_links($question, $fromcoursefilesid, $tocoursefilesid, $url, $destination);
                        }
                        break;
                    case  QUESTION_FILEDONOTHING :
                    default :
                        break;
                }


            }
        }

        //adjust sortorder before we make the cat a peer of it's new peers
        $peers = $DB->get_records_select_menu('question_categories',
                'contextid = ? AND parent = ?', array($toparent->contextid, $toparent->id),
                'sortorder ASC', 'id, 1');
        $peers = array_keys($peers);
        if ($totop){
           array_unshift($peers, $cattomove->id);
        } else {
           $peers[] = $cattomove->id;
        }
        $sortorder = 0;
        foreach ($peers as $peer) {
            if (! $DB->set_field('question_categories', "sortorder", $sortorder, array("id" => $peer))) {
                print_error('listupdatefail', '', $onerrorurl);
            }
            $sortorder++;
        }
        //now move category
        $cat = new object();
        $cat->id = $cattomove->id;
        $cat->parent = $toparent->id;
        //set context of category we are moving and all children also!
        list($usql, $params) = $DB->get_in_or_equal(explode(',', $cattomove->categorylist));
        $params = array_merge(array($contextto->id), $params);

        $DB->execute("UPDATE {question_categories} SET contextid = ? WHERE id $usql", $params);
        //finally set the new parent id
        $DB->update_record("question_categories", $cat);
        $thispageurl->remove_params('cattomove', 'toparent', 'totop');
        $url = new moodle_url('/question/category.php', ($thispageurl->params() + array('cat'=>"{$cattomove->id},{$contextto->id}")));
        redirect($url);
    }

    $streditingcategories = get_string('editcategories', 'quiz');

    $PAGE->set_url($thispageurl->out());
    $PAGE->navbar->add($streditingcategories, $thispageurl->out());
    $PAGE->navbar->add(get_string('movingcategory', 'question'));
    $PAGE->set_title($streditingcategories);
    echo $OUTPUT->header();

    // print tabs
    if ($cm!==null) {
        $currenttab = 'edit';
        $mode = 'categories';
        ${$cm->modname} = $module;
        include($CFG->dirroot."/mod/{$cm->modname}/tabs.php");
    } else {
        $currenttab = 'categories';
        $context = $contexts->lowest();
        include('tabs.php');
    }
    //parameter for get_string
    $cattomove->contextto = $contexttostring;
    if (count($urls)){
        $defaults = array();
        for ($default_key = 0; $default_key < count($urls); $default_key++){
            $defaults['urls'][$default_key] = QUESTION_FILECOPY;
        }
        $contextmoveform->set_data($defaults);
        //some parameters for get_string
        $cattomove->urlcount = count($urls);
        $cattomove->toareaname = $toareaname;
        $cattomove->fromareaname = $fromareaname;

        echo $OUTPUT->box(get_string('movingcategoryandfiles', 'question', $cattomove), 'boxwidthnarrow boxaligncenter generalbox');
    } else {
        echo $OUTPUT->box(get_string('movingcategorynofiles', 'question', $cattomove), 'boxwidthnarrow boxaligncenter generalbox');
    }
    $contextmoveform->display();
    echo $OUTPUT->footer();

