<?php
defined('MOODLE_INTERNAL') || die();

class theme_elegance_core_course_renderer extends core_course_renderer {
    
    /**
     * Renders HTML to display particular course category - list of it's subcategories and courses
     *
     * Invoked from /course/index.php
     *
     * @param int|stdClass|coursecat $category
     */
    public function course_category($category) {
        global $CFG;
        require_once($CFG->libdir. '/coursecatlib.php');
        $coursecat = coursecat::get(is_object($category) ? $category->id : $category);
        $site = get_site();
        $output = '';
    
        if (can_edit_in_category($coursecat->id)) {
            // Add 'Manage' button if user has permissions to edit this category.
            $managebutton = $this->single_button(new moodle_url('/course/management.php',
                array('categoryid' => $coursecat->id)), get_string('managecourses'), 'get');
            $this->page->set_button($managebutton);
        }
        if (!$coursecat->id) {
            if (coursecat::count_all() == 1) {
                // There exists only one category in the system, do not display link to it
                $coursecat = coursecat::get_default();
                $strfulllistofcourses = get_string('fulllistofcourses');
                $this->page->set_title("$site->shortname: $strfulllistofcourses");
            } else {
                $strcategories = get_string('categories');
                $this->page->set_title("$site->shortname: $strcategories");
            }
        }
        
        //Print current category title
        $fields = new stdClass();
        $fields->title = format_text($coursecat->name);
        $output .= $this->render_from_template('theme_elegance/categorytitle', $fields);
    
        // Print current category description
        $chelper = new coursecat_helper();
        if ($description = $chelper->get_category_formatted_description($coursecat)) {
            $output .= $this->box($description, array('class' => 'generalbox info'));
        }
    
        // Prepare parameters for courses and categories lists in the tree
        $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_AUTO)
        ->set_attributes(array('class' => 'category-browse category-browse-'.$coursecat->id));
    
        $coursedisplayoptions = array();
        $catdisplayoptions = array();
        $browse = optional_param('browse', null, PARAM_ALPHA);
        $perpage = optional_param('perpage', $CFG->coursesperpage, PARAM_INT);
        $page = optional_param('page', 0, PARAM_INT);
        $baseurl = new moodle_url('/course/index.php');
        if ($coursecat->id) {
            $baseurl->param('categoryid', $coursecat->id);
        }
        if ($perpage != $CFG->coursesperpage) {
            $baseurl->param('perpage', $perpage);
        }
        $coursedisplayoptions['limit'] = $perpage;
        $catdisplayoptions['limit'] = $perpage;
        if ($browse === 'courses' || !$coursecat->has_children()) {
            $coursedisplayoptions['offset'] = $page * $perpage;
            $coursedisplayoptions['paginationurl'] = new moodle_url($baseurl, array('browse' => 'courses'));
            $catdisplayoptions['nodisplay'] = true;
            $catdisplayoptions['viewmoreurl'] = new moodle_url($baseurl, array('browse' => 'categories'));
            $catdisplayoptions['viewmoretext'] = new lang_string('viewallsubcategories');
        } else if ($browse === 'categories' || !$coursecat->has_courses()) {
            $coursedisplayoptions['nodisplay'] = true;
            $catdisplayoptions['offset'] = $page * $perpage;
            $catdisplayoptions['paginationurl'] = new moodle_url($baseurl, array('browse' => 'categories'));
            $coursedisplayoptions['viewmoreurl'] = new moodle_url($baseurl, array('browse' => 'courses'));
            $coursedisplayoptions['viewmoretext'] = new lang_string('viewallcourses');
        } else {
            // we have a category that has both subcategories and courses, display pagination separately
            $coursedisplayoptions['viewmoreurl'] = new moodle_url($baseurl, array('browse' => 'courses', 'page' => 1));
            $catdisplayoptions['viewmoreurl'] = new moodle_url($baseurl, array('browse' => 'categories', 'page' => 1));
        }
        $chelper->set_courses_display_options($coursedisplayoptions)->set_categories_display_options($catdisplayoptions);
        
    
        // Display course category tree.
        $output .= $this->coursecat_tree($chelper, $coursecat);
        
        // Add course search form.
        $output .= $this->course_search_form();
    
        // Add action buttons
        $output .= $this->container_start('buttons');
        $context = get_category_or_system_context($coursecat->id);
        if (has_capability('moodle/course:create', $context)) {
            // Print link to create a new course, for the 1st available category.
            if ($coursecat->id) {
                $url = new moodle_url('/course/edit.php', array('category' => $coursecat->id, 'returnto' => 'category'));
            } else {
                $url = new moodle_url('/course/edit.php', array('category' => $CFG->defaultrequestcategory, 'returnto' => 'topcat'));
            }
            $output .= $this->single_button($url, get_string('addnewcourse'), 'get');
        }
        ob_start();
        if (coursecat::count_all() == 1) {
            print_course_request_buttons(context_system::instance());
        } else {
            print_course_request_buttons($context);
        }
        $output .= ob_get_contents();
        ob_end_clean();
        $output .= $this->container_end();
    
        return $output;
    }
    
    protected function coursecat_tree(coursecat_helper $chelper, $coursecat) {
        global $DB, $CFG;
        
        $items = [];
        $this->get_son_cats($coursecat->id, $items);
        
        if($coursecat->id > 0) {
            $courses = $DB->get_records('course', array("category" => $coursecat->id),'sortorder');
            
            foreach ($courses as $course) {
                $items[] = $course;
            }
        }
        
        $template = new stdClass();
        
        $template->links = [];
        
        foreach ($items as $item) {
            $tmp = new stdClass();
            if(!empty($item->fullname)) {
                $tmp->md = 2;
                $tmp->xs = 4;
                $tmp->title = format_string($item->fullname);
                $tmp->url = $CFG->wwwroot.'/course/view.php?id='.$item->id;
                $currcoursecon = context_course::instance($item->id);
                if($image = $DB->get_record_sql("SELECT * FROM {files} WHERE contextid = ? AND component LIKE 'course' AND filearea LIKE 'overviewfiles' AND itemid = ? AND filename NOT LIKE '.'", array($currcoursecon->id, 0))) {
                     
                    $fs = get_file_storage();
                    $file = $fs->get_file($currcoursecon->id, 'course', 'overviewfiles', 0, '/', $image->filename);
                     
                    $tmp->img = moodle_url::make_pluginfile_url(
                        $file->get_contextid(),
                        $file->get_component(),
                        $file->get_filearea(),
                        '',
                        $file->get_filepath(),
                        $file->get_filename()
                        );
                }
            }
            else {
                $tmp->md = 2;
                $tmp->xs = 4;
                $tmp->title = format_string($item->name);
                $tmp->url = $CFG->wwwroot.'/course/index.php?categoryid='.$item->id;
                $context = context_coursecat::instance($item->id);
                $tmp->img = file_rewrite_pluginfile_urls($item->description,
                    'pluginfile.php', $context->id, 'coursecat', 'description', null);
                $tmp->img = explode("src=\"", $tmp->img);
                if(!empty($tmp->img[1])) {
                $tmp->img = explode("\"", $tmp->img[1]);
                $tmp->img = $tmp->img[0];
                } else {
                    $tmp->img = "";
                }
            }
            
            $template->links[] = $tmp;
        }
        $stub = new stdClass();
        $linkscount = count($template->links);
        $whertoslise = floor($linkscount/6)*6;
        $offset = ($linkscount % 6);
        switch ($offset){
            case 1:
                $stub->md=5;
                $stub->xs=0;
                array_splice($template->links,$whertoslise,0,array($stub));
                break;
            case 2:
                $stub->md=4;
                $stub->xs=0;
                array_splice($template->links,$whertoslise,0,array($stub));
                break;
            case 3:
                $stub->md=3;
                $stub->xs=0;
                array_splice($template->links,$whertoslise,0,array($stub));
                break;
            case 4:
                 $stub->md=2;
                 $stub->xs=0;
                 array_splice($template->links,$whertoslise,0,array($stub));
                 break;
            case 5:
                 $stub->md=1;
                 $stub->xs=0;
                 array_splice($template->links,$whertoslise,0,array($stub));
                 break;
            case 6:
                 break;
        }
        
        


        return $this->render_from_template('theme_elegance/categorylist', $template);
    }
    
    private function get_son_cats($current, &$categories) {
        global $DB;
        
        $cats = $DB->get_records('course_categories', array("parent" => $current),'sortorder');
        foreach ($cats as $cat) {
            $categories[] = $cat;
        }
    }
    
}
