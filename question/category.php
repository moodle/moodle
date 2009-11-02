<?php // $Id$
/**
 * Allows a teacher to create, edit and delete categories
 *
 * @author Martin Dougiamas and many others.
 *         {@link http://moodle.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questionbank
 */

    require_once("../config.php");
    require_once($CFG->dirroot."/question/editlib.php");
    require_once($CFG->dirroot."/question/category_class.php");



    list($thispageurl, $contexts, $cmid, $cm, $module, $pagevars) = question_edit_setup('categories');

    // get values from form for actions on this page
    $param = new stdClass();


    $param->moveup = optional_param('moveup', 0, PARAM_INT);
    $param->movedown = optional_param('movedown', 0, PARAM_INT);
    $param->moveupcontext = optional_param('moveupcontext', 0, PARAM_INT);
    $param->movedowncontext = optional_param('movedowncontext', 0, PARAM_INT);
    $param->tocontext = optional_param('tocontext', 0, PARAM_INT);
    $param->left = optional_param('left', 0, PARAM_INT);
    $param->right = optional_param('right', 0, PARAM_INT);
    $param->delete = optional_param('delete', 0, PARAM_INT);
    $param->confirm = optional_param('confirm', 0, PARAM_INT);
    $param->cancel = optional_param('cancel', '', PARAM_ALPHA);
    $param->move = optional_param('move', 0, PARAM_INT);
    $param->moveto = optional_param('moveto', 0, PARAM_INT);
    $param->edit = optional_param('edit', 0, PARAM_INT);

    $qcobject = new question_category_object($pagevars['cpage'], $thispageurl, $contexts->having_one_edit_tab_cap('categories'), $param->edit, $pagevars['cat'], $param->delete,
                                $contexts->having_cap('moodle/question:add'));

    $streditingcategories = get_string('editcategories', 'quiz');
    if ($param->left || $param->right || $param->moveup || $param->movedown|| $param->moveupcontext || $param->movedowncontext){
        require_sesskey();
        foreach ($qcobject->editlists as $list){
            //processing of these actions is handled in the method where appropriate and page redirects.
            $list->process_actions($param->left, $param->right, $param->moveup, $param->movedown,
                                    $param->moveupcontext, $param->movedowncontext, $param->tocontext);
        }
    }
    if ($param->delete && ($questionstomove = count_records("question", "category", $param->delete))){
        if (!$category = get_record("question_categories", "id", $param->delete)) {  // security
            error("No such category {$param->delete}!", $thispageurl->out());
        }
        $categorycontext = get_context_instance_by_id($category->contextid);
        $qcobject->moveform = new question_move_form($thispageurl,
                    array('contexts'=>array($categorycontext), 'currentcat'=>$param->delete));
        if ($qcobject->moveform->is_cancelled()){
            redirect($thispageurl->out());
        }  elseif ($formdata = $qcobject->moveform->get_data()) {
            /// 'confirm' is the category to move existing questions to
            list($tocategoryid, $tocontextid) = explode(',', $formdata->category);
            $qcobject->move_questions_and_delete_category($formdata->delete, $tocategoryid);
            $thispageurl->remove_params('cat', 'category');
            redirect($thispageurl->out());
        }
    } else {
        $questionstomove = 0;
    }
    if ($qcobject->catform->is_cancelled()) {
        redirect($thispageurl->out());
    } else if ($catformdata = $qcobject->catform->get_data()) {
        if (!$catformdata->id) {//new category
            $qcobject->add_category($catformdata->parent, $catformdata->name, $catformdata->info);
        } else {
            $qcobject->update_category($catformdata->id, $catformdata->parent, $catformdata->name, $catformdata->info);
        }
        redirect($thispageurl->out());
    } else if ((!empty($param->delete) and (!$questionstomove) and confirm_sesskey()))  {
        $qcobject->delete_category($param->delete);//delete the category now no questions to move
        $thispageurl->remove_params('cat', 'category');
        redirect($thispageurl->out());
    }
    $navlinks = array();
    if ($cm!==null) {
        // Page header
        $strupdatemodule = has_capability('moodle/course:manageactivities', $contexts->lowest())
            ? update_module_button($cm->id, $COURSE->id, get_string('modulename', $cm->modname))
            : "";
        $navlinks[] = array('name' => get_string('modulenameplural', $cm->modname),
                            'link' => "$CFG->wwwroot/mod/{$cm->modname}/index.php?id=$COURSE->id",
                            'type' => 'activity');
        $navlinks[] = array('name' => format_string($module->name),
                            'link' => "$CFG->wwwroot/mod/{$cm->modname}/view.php?id={$cm->id}",
                            'type' => 'title');
    } else {
        // Print basic page layout.
        $strupdatemodule = '';
    }

    if (!$param->edit){
        $navlinks[] = array('name' => $streditingcategories, 'link' => '', 'type' => 'title');
    } else {
        $navlinks[] = array('name' => $streditingcategories, 'link' => $thispageurl->out(), 'type' => 'title');
        $navlinks[] = array('name' => get_string('editingcategory', 'question'), 'link' => '', 'type' => 'title');
    }

    $navigation = build_navigation($navlinks);
    print_header_simple($streditingcategories, '', $navigation, "", "", true, $strupdatemodule);

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

    // display UI
    if (!empty($param->edit)) {
        $qcobject->edit_single_category($param->edit);
    } else if ($questionstomove){
        $qcobject->display_move_form($questionstomove, $category);
    } else {
        // display the user interface
        $qcobject->display_user_interface();
    }
    print_footer($COURSE);
?>
