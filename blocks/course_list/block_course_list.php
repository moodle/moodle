<?PHP //$Id$

class CourseBlock_course_list extends MoodleBlock {
    function CourseBlock_course_list ($course) {
        $this->title = get_string('courses');
        $this->content_type = BLOCK_TYPE_LIST;
        $this->course = $course;
        $this->version = 2004081200;
    }
    
    function has_config() {
        return true;
    }

    function print_config() {
        global $CFG, $THEME;
        print_simple_box_start('center', '', $THEME->cellheading);
        include($CFG->dirroot.'/blocks/'.$this->name().'/config.html');
        print_simple_box_end();
        return true;
    }

    function handle_config($config) {
        foreach ($config as $name => $value) {
            set_config($name, $value);
        }
        return true;
    }

    function applicable_formats() {
        return COURSE_FORMAT_WEEKS | COURSE_FORMAT_TOPICS | COURSE_FORMAT_SOCIAL | COURSE_FORMAT_SITE;
    }

    function get_content() {
        global $THEME, $CFG, $USER;

        if($this->content !== NULL) {
            return $this->content;
        }

        $this->content = New object;
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';

        if (empty($THEME->custompix)) {
            $icon  = "<img src=\"$CFG->wwwroot/pix/i/course.gif\"".
                     " height=\"16\" width=\"16\" alt=\"".get_string("course")."\">";
        } else {
            $icon  = "<img src=\"$CFG->wwwroot/theme/$CFG->theme/pix/i/course.gif\"".
                     " height=\"16\" width=\"16\" alt=\"".get_string("course")."\">";
        }
        
        $adminseesall = true;
        if (isset($CFG->block_course_list_adminview)) {
           if ( $CFG->block_course_list_adminview == 'own'){
               $adminseesall = false;
           }
        }

        if (isset($USER->id) and !(isadmin() and $adminseesall)) {    // Just print My Courses
            if ($courses = get_my_courses($USER->id)) {
                foreach ($courses as $course) {
                    if (!$course->category) {
                        continue;
                    }
                    $linkcss = $course->visible ? "" : " class=\"dimmed\" ";
                    $this->content->items[]="<a $linkcss title=\"$course->shortname\" ".
                               "href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->fullname</a>";
                    $this->content->icons[]=$icon;
                }
                $this->title = get_string('mycourses');
                $this->content->footer = "<a href=\"$CFG->wwwroot/course/index.php\">".get_string("fulllistofcourses")."</a>...";
                if ($this->content->items) { // make sure we don't return an empty list
                    return $this->content;
                }
            }
        }

        $categories = get_categories("0");  // Parent = 0   ie top-level categories only
        if ($categories) {   //Check we have categories
            if (count($categories) > 1) {     // Just print top level category links
                foreach ($categories as $category) {
                    $linkcss = $category->visible ? "" : " class=\"dimmed\" ";
                    $this->content->items[]="<a $linkcss href=\"$CFG->wwwroot/course/category.php?id=$category->id\">$category->name</a>";
                    $this->content->icons[]=$icon;
                }
                $this->content->footer = "<a href=\"$CFG->wwwroot/course/\">".get_string("searchcourses")."</a>...<br />".
                                         "<a href=\"$CFG->wwwroot/course/index.php\">".get_string("fulllistofcourses")."</a>...";
                $this->title = get_string('categories');
            } else {                          // Just print course names of single category
                $category = array_shift($categories);
                $courses = get_courses($category->id);

                if ($courses) {
                    foreach ($courses as $course) {
                        $linkcss = $course->visible ? "" : " class=\"dimmed\" ";
                        $this->content->items[]="<a $linkcss title=\"$course->shortname\" ".
                                   "href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->fullname</a>";
                        $this->content->icons[]=$icon;
                    }
                    $this->content->footer = "<a href=\"$CFG->wwwroot/course/index.php\">".get_string("fulllistofcourses")."</a>...";
                } else {
                    $this->content->items = array();
                    $this->content->icons = array();
                    $this->content->footer = get_string('nocoursesyet');
                }
                $this->title = get_string('courses');
            }
        }
        return $this->content;
    }
}

?>
