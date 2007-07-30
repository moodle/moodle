<?php // $Id$
/**
 * Class representing question categories
 *
 * @author Martin Dougiamas and many others. {@link http://moodle.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questionbank
 */

// number of categories to display on page
define("QUESTION_PAGE_LENGTH", 20);

require_once("$CFG->libdir/listlib.php");

class question_category_list extends moodle_list {
    var $table = "question_categories";
    var $listitemclassname = 'question_category_list_item';
    

    function get_records() {
        global $COURSE, $CFG;
        $categories = get_records($this->table, 'course', "{$COURSE->id}", $this->sortby);

        $catids = array_keys($categories);
        $select = "WHERE category IN ('".join("', '", $catids)."') AND hidden='0' AND parent='0'";
        $questioncounts = get_records_sql_menu('SELECT category, COUNT(*) FROM '. $CFG->prefix . 'question' .' '. $select.' GROUP BY category');
        foreach ($categories as $categoryid => $category){
            if (isset($questioncounts[$categoryid])){
                $categories[$categoryid]->questioncount = $questioncounts[$categoryid];
            } else {
                $categories[$categoryid]->questioncount = 0;
            }
        }
        $this->records = $categories;
    }
}

class question_category_list_item extends list_item {


    function item_html($extraargs = array()){
        global $CFG;
        $pixpath = $CFG->pixpath;
        $str = $extraargs['str'];
        $category = $this->item;

        $linkcss = $category->publish ? ' class="published" ' : ' class="unpublished" ';


        /// Each section adds html to be displayed as part of this list item
        

        $item = '<a ' . $linkcss . ' title="' . $str->edit. '" href="'.$this->parentlist->pageurl->out_action(array('edit'=>$this->id)).'">
            <img src="' . $pixpath . '/t/edit.gif" class="iconsmall"
            alt="' .$str->edit. '" /> ' . $category->name . '('.$category->questioncount.')'. '</a>';

        $item .= '&nbsp;'. $category->info;


        if (!empty($category->publish)) {
            $item .= '<a title="' . $str->hide . '" href="'.$this->parentlist->pageurl->out_action(array('hide'=>$this->id)).'">
              <img src="' . $pixpath . '/t/hide.gif" class="iconsmall" alt="' .$str->hide. '" /></a> ';
        } else {
            $item .= '<a title="' . $str->publish . '" href="'.$this->parentlist->pageurl->out_action(array('publish'=>$this->id)).'">
              <img src="' . $pixpath . '/t/show.gif" class="iconsmall" alt="' .$str->publish. '" /></a> ';
        }

        if ($category->id != $extraargs['defaultcategory']->id) {
            $item .=  '<a title="' . $str->delete . '" href="'.$this->parentlist->pageurl->out_action(array('delete'=>$this->id)).'">
                    <img src="' . $pixpath . '/t/delete.gif" class="iconsmall" alt="' .$str->delete. '" /></a> ';
        }

        return $item;


    }

}


/**
 * Class representing question categories
 *
 * @package questionbank
 */
class question_category_object {

    var $str;
    var $pixpath;
    /**
     * Nested list to display categories.
     *
     * @var question_category_list
     */
    var $editlist;
    var $newtable;
    var $tab;
    var $tabsize = 3;
    var $categories;
    var $categorystrings;
    var $defaultcategory;
//------------------------------------------------------
    /**
     * @var moodle_url Object representing url for this page
     */
    var $pageurl;

    /**
     * Constructor
     *
     * Gets necessary strings and sets relevant path information
     */
    function question_category_object($page, $pageurl) {
        global $CFG, $COURSE;

        $this->tab = str_repeat('&nbsp;', $this->tabsize);

        $this->str->course         = get_string('course');
        $this->str->category       = get_string('category', 'quiz');
        $this->str->categoryinfo   = get_string('categoryinfo', 'quiz');
        $this->str->questions      = get_string('questions', 'quiz');
        $this->str->add            = get_string('add');
        $this->str->delete         = get_string('delete');
        $this->str->moveup         = get_string('moveup');
        $this->str->movedown       = get_string('movedown');
        $this->str->edit           = get_string('editthiscategory');
        $this->str->hide           = get_string('hide');
        $this->str->publish        = get_string('publish', 'quiz');
        $this->str->order          = get_string('order');
        $this->str->parent         = get_string('parent', 'quiz');
        $this->str->add            = get_string('add');
        $this->str->action         = get_string('action');
        $this->str->top            = get_string('top', 'quiz');
        $this->str->addcategory    = get_string('addcategory', 'quiz');
        $this->str->editcategory   = get_string('editcategory', 'quiz');
        $this->str->cancel         = get_string('cancel');
        $this->str->editcategories = get_string('editcategories', 'quiz');
        $this->str->page           = get_string('page');
        $this->pixpath = $CFG->pixpath;

        $this->editlist = new question_category_list('ul', '', true, $pageurl, $page, 'cpage', QUESTION_PAGE_LENGTH);

        $this->pageurl = $pageurl;
        
        $this->initialize();

    }
    
    /**
     * Displays the user interface
     *
     */
    function display_user_interface() {

        /// Interface for editing existing categories
        print_heading_with_help($this->str->editcategories, 'categories', 'quiz');
        $this->output_edit_list();


        echo '<br />';
        /// Interface for adding a new category:
        print_heading_with_help($this->str->addcategory, 'categories_edit', 'quiz');
        $this->output_new_table();
        echo '<br />';

    }


    /**
     * Initializes this classes general category-related variables
     */
    function initialize() {
        global $COURSE, $CFG;

        /// Get the existing categories
        if (!$this->defaultcategory = get_default_question_category($COURSE->id)) {
            error("Error: Could not find or make a category!");
        }

        $this->editlist->list_from_records();

        $this->categories = $this->editlist->records;

        // create the array of id=>full_name strings
        $this->categorystrings = $this->expanded_category_strings($this->categories);


    }


    /**
     * Outputs a table to allow entry of a new category
     */
    function output_new_table() {
        global $USER, $COURSE;
        $publishoptions[0] = get_string("no");
        $publishoptions[1] = get_string("yes");

        $this->newtable->head  = array ($this->str->parent, $this->str->category, $this->str->categoryinfo, $this->str->publish, $this->str->action);
        $this->newtable->width = '200';
        $this->newtable->data[] = array();
        $this->newtable->tablealign = 'center';

        /// Each section below adds a data cell to the table row


        $viableparents[0] = $this->str->top;
        $viableparents = $viableparents + $this->categorystrings;
        $this->newtable->align['parent'] =  "left";
        $this->newtable->wrap['parent'] = "nowrap";
        $row['parent'] = choose_from_menu ($viableparents, "newparent", $this->str->top, "", "", "", true);

        $this->newtable->align['category'] =  "left";
        $this->newtable->wrap['category'] = "nowrap";
        $row['category'] = '<input type="text" name="newcategory" value="" size="15" />';

        $this->newtable->align['info'] =  "left";
        $this->newtable->wrap['info'] = "nowrap";
        $row['info'] = '<input type="text" name="newinfo" value="" size="50" />';

        $this->newtable->align['publish'] =  "left";
        $this->newtable->wrap['publish'] = "nowrap";
        $row['publish'] = choose_from_menu ($publishoptions, "newpublish", "", "", "", "", true);

        $this->newtable->align['action'] =  "left";
        $this->newtable->wrap['action'] = "nowrap";
        $row['action'] = '<input type="submit" value="' . $this->str->add . '" />';


        $this->newtable->data[] = $row;

        // wrap the table in a form and output it
        echo '<form action="category.php" method="post">';
        echo '<fieldset class="invisiblefieldset" style="display: block">';
        echo "<input type=\"hidden\" name=\"sesskey\" value=\"$USER->sesskey\" />";
        echo $this->pageurl->hidden_params_out();
        echo '<input type="hidden" name="addcategory" value="true" />';
        print_table($this->newtable);
        echo '</fieldset>';
        echo '</form>';
    }


    /**
     * Outputs a list to allow editing/rearranging of existing categories
     *
     * $this->initialize() must have already been called
     *
     */
    function output_edit_list() {
        print_box_start('boxwidthwide boxaligncenter generalbox');
        echo $this->editlist->to_html(0, array('str'=>$this->str,
                                'defaultcategory' => $this->defaultcategory));
        print_box_end();
        echo $this->editlist->display_page_numbers();

    }



    /**
     * gets all the courseids for the given categories
     *
     * @param array categories contains category objects in  a tree representation
     * @return array courseids flat array in form categoryid=>courseid
     */
    function get_course_ids($categories) {
        $courseids = array();
        foreach ($categories as $key=>$cat) {
            $courseids[$key] = $cat->course;
            if (!empty($cat->children)) {
                $courseids = array_merge($courseids, $this->get_course_ids($cat->children));
            }
        }
        return $courseids;
    }



    function edit_single_category($categoryid) {
    /// Interface for adding a new category
        global $CFG, $USER, $COURSE;

        /// Interface for editing existing categories
        if ($category = get_record("question_categories", "id", $categoryid)) {
            print_heading_with_help($this->str->edit, 'categories_edit', 'quiz');
            $this->output_edit_single_table($category);
            echo '<div class="centerpara">';
            print_single_button($CFG->wwwroot . '/question/category.php',
                    $this->pageurl->params, $this->str->cancel);
            echo '</div>';
            print_footer($COURSE);
            exit;
        } else {
            error("Category $categoryid not found", "category.php?id={$COURSE->id}");
        }
    }

    /**
     * Outputs a table to allow editing of an existing category
     *
     * @param object category
     * @param int page current page
     */
    function output_edit_single_table($category) {
        global $USER;
        $publishoptions[0] = get_string("no");
        $publishoptions[1] = get_string("yes");
        $strupdate = get_string('update');

        $edittable = new stdClass;

        $edittable->head  = array ($this->str->parent, $this->str->category, $this->str->categoryinfo, $this->str->publish, $this->str->action);
        $edittable->width = 200;
        $edittable->data[] = array();
        $edittable->tablealign = 'center';

        /// Each section below adds a data cell to the table row

        $viableparents = $this->categorystrings;
        $this->set_viable_parents($viableparents, $category);
        $viableparents = array(0=>$this->str->top) + $viableparents;
        $edittable->align['parent'] =  "left";
        $edittable->wrap['parent'] = "nowrap";
        $row['parent'] = choose_from_menu ($viableparents, "updateparent", "{$category->parent}", "", "", "", true);

        $edittable->align['category'] =  "left";
        $edittable->wrap['category'] = "nowrap";
        $row['category'] = '<input type="text" name="updatename" value="' . format_string($category->name) . '" size="15" />';

        $edittable->align['info'] =  "left";
        $edittable->wrap['info'] = "nowrap";
        $row['info'] = '<input type="text" name="updateinfo" value="' . $category->info . '" size="50" />';

        $edittable->align['publish'] =  "left";
        $edittable->wrap['publish'] = "nowrap";
        $selected = (boolean)$category->publish ? 1 : 0;
        $row['publish'] = choose_from_menu ($publishoptions, "updatepublish", $selected, "", "", "", true);

        $edittable->align['action'] =  "left";
        $edittable->wrap['action'] = "nowrap";
        $row['action'] = '<input type="submit" value="' . $strupdate . '" />';

        $edittable->data[] = $row;

        // wrap the table in a form and output it
        echo '<p><form action="category.php" method="post">';
        echo '<fieldset class="invisiblefieldset" style="display: block;">';
        echo '<input type="hidden" name="sesskey" value="' . $USER->sesskey . '" />';
        echo $this->pageurl->hidden_params_out();
        echo '<input type="hidden" name="updateid" value="' . $category->id . '" />';
        print_table($edittable);
        echo '</fieldset>';
        echo '</form></p>';
    }

    /**
     * Creates an array of "full-path" category strings
     * Structure:
     *    key => string
     *    where key is the category id, and string contains the name of all ancestors as well as the particular category name
     *   E.g. '123'=>'Language / English / Grammar / Modal Verbs"
     *
     * @param array $categories an array containing categories arranged in a tree structure
     */
    function expanded_category_strings($categories, $parent=null) {
        $prefix = is_null($parent) ? '' : "$parent / ";
        $categorystrings = array();
        foreach ($categories as $key => $category) {
            $expandedname = "$prefix$category->name";
            $categorystrings[$key] = $expandedname;
            if (isset($category->children)) {
                $categorystrings = $categorystrings + $this->expanded_category_strings($category->children, $expandedname);
            }
        }
        return $categorystrings;
    }


    /**
     * Sets the viable parents
     *
     *  Viable parents are any except for the category itself, or any of it's descendants
     *  The parentstrings parameter is passed by reference and changed by this function.
     *
     * @param    array parentstrings a list of parentstrings
     * @param   object category
     */
    function set_viable_parents(&$parentstrings, $category) {

        unset($parentstrings[$category->id]);
        if (isset($category->children)) {
            foreach ($category->children as $child) {
                $this->set_viable_parents($parentstrings, $child);
            }
        }
    }

    /**
     * Gets question categories
     *
     * @param    int parent - if given, restrict records to those with this parent id.
     * @param    string sort - [[sortfield [,sortfield]] {ASC|DESC}]
     * @return   array categories
     */
    function get_question_categories($parent=null, $sort="sortorder ASC") {
        global $COURSE;
        if (is_null($parent)) {
            $categories = get_records('question_categories', 'course', "{$COURSE->id}", $sort);
        } else {
            $select = "parent = '$parent' AND course = '{$COURSE->id}'";
            $categories = get_records_select('question_categories', $select, $sort);
        }
        return $categories;
    }

    /**
     * Deletes an existing question category
     *
     * @param    int deletecat  id of category to delete
     * @param    int destcategoryid id of category which will inherit the orphans of deletecat
     */
    function delete_category($deletecat, $destcategoryid = null) {
        global $USER, $COURSE;

        if (!$category = get_record("question_categories", "id", $deletecat)) {  // security
            error("No such category $deletecat!", "category.php?id={$COURSE->id}");
        }

        if (!is_null($destcategoryid)) { // Need to move some questions before deleting the category
            if (!$category2 = get_record("question_categories", "id", $destcategoryid)) {  // security
                error("No such category $destcategoryid!", "category.php?id={$COURSE->id}");
            }
            if (! set_field('question', 'category', $destcategoryid, 'category', $deletecat)) {
                error("Error while moving questions from category '" . format_string($category->name) . "' to '$category2->name'", "category.php?id={$COURSE->id}");
            }

        } else {
            // todo: delete any hidden questions that are not actually in use any more
            if ($count = count_records("question", "category", $category->id)) {
                $vars = new stdClass;
                $vars->name = $category->name;
                $vars->count = $count;
                print_simple_box(get_string("categorymove", "quiz", $vars), "center");
                $this->initialize();
                $categorystrings = $this->categorystrings;
                unset ($categorystrings[$category->id]);
                echo "<p><div align=\"center\"><form action=\"category.php\" method=\"get\">";
                echo '<fieldset class="invisiblefieldset">';
                echo "<input type=\"hidden\" name=\"sesskey\" value=\"$USER->sesskey\" />";
                echo "<input type=\"hidden\" name=\"id\" value=\"{$COURSE->id}\" />";
                echo "<input type=\"hidden\" name=\"delete\" value=\"$category->id\" />";
                choose_from_menu($categorystrings, "confirm", "", "");
                echo "<input type=\"submit\" value=\"". get_string("categorymoveto", "quiz") . "\" />";
                echo "<input type=\"submit\" name=\"cancel\" value=\"{$this->str->cancel}\" />";
                echo '</fieldset>';
                echo "</form></div></p>";
                print_footer($COURSE);
                exit;
            }
        }

        /// Send the children categories to live with their grandparent
        if ($childcats = get_records("question_categories", "parent", $category->id)) {
            foreach ($childcats as $childcat) {
                if (! set_field("question_categories", "parent", $category->parent, "id", $childcat->id)) {
                    error("Could not update a child category!", "category.php?id={$COURSE->id}");
                }
            }
        }

        /// Finally delete the category itself
        if (delete_records("question_categories", "id", $category->id)) {
            notify(get_string("categorydeleted", "quiz", format_string($category->name)), 'notifysuccess');
            redirect($this->pageurl->out());//always redirect after successful action
        }
    }



    /**
     * Changes the published status of a category
     *
     * @param    boolean publish
     * @param    int categoryid
     */
    function publish_category($publish, $categoryid) {
    /// Hide or publish a category

        $publish = ($publish == false) ? 0 : 1;
        $tempcat = get_record("question_categories", "id", $categoryid);
        if ($tempcat) {
            if (! set_field("question_categories", "publish", $publish, "id", $tempcat->id)) {
                notify("Could not update that category!");
            } else {
                redirect($this->pageurl->out());//always redirect after successful action
            }
                
        }
    }

    /**
     * Creates a new category with given params
     *
     * @param int $newparent       id of the parent category
     * @param string $newcategory  the name for the new category
     * @param string $newinfo      the info field for the new category
     * @param int $newpublish      whether to publish the category
     * @param int $newcourse       the id of the associated course
     */
    function add_category($newparent, $newcategory, $newinfo, $newpublish, $newcourse) {
        if (empty($newcategory)) {
            notify(get_string('categorynamecantbeblank', 'quiz'), 'notifyproblem');
            return false;
        }

        if ($newparent) {
            // first check that the parent category is in the correct course
            if(!(get_field('question_categories', 'course', 'id', $newparent) == $newcourse)) {
                return false;
            }
        }

        $cat = NULL;
        $cat->parent = $newparent;
        $cat->name = $newcategory;
        $cat->info = $newinfo;
        $cat->publish = $newpublish;
        $cat->course = $newcourse;
        $cat->sortorder = 999;
        $cat->stamp = make_unique_id_code();
        if (!insert_record("question_categories", $cat)) {
            error("Could not insert the new question category '$newcategory'", "category.php?id={$newcourse}");
        } else {
            notify(get_string("categoryadded", "quiz", $newcategory), 'notifysuccess');
            redirect($this->pageurl->out());//always redirect after successful action
        }
    }

    /**
     * Updates an existing category with given params
     *
     * @param    int updateid
     * @param    int updateparent
     * @param    string updatename
     * @param    string updateinfo
     * @param    int updatepublish
     * @param    int courseid the id of the associated course
     */
    function update_category($updateid, $updateparent, $updatename, $updateinfo, $updatepublish, $courseid) {
        if (empty($updatename)) {
            notify(get_string('categorynamecantbeblank', 'quiz'), 'notifyproblem');
            return false;
        }

        $cat = NULL;
        $cat->id = $updateid;
        $cat->parent = $updateparent;
        $cat->name = $updatename;
        $cat->info = $updateinfo;
        $cat->publish = $updatepublish;
        if (!update_record("question_categories", $cat)) {
            error("Could not update the category '$updatename'", "category.php?id={$courseid}");
        } else {
            notify(get_string("categoryupdated", 'quiz'), 'notifysuccess');
            redirect($this->pageurl->out());
        }
    }
}

?>
