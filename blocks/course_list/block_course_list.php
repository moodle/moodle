<?PHP //$Id$

class CourseBlock_course_list extends MoodleBlock {
    function CourseBlock_course_list ($course) {
        $this->title = get_string('mycourses');
        $this->content_type = BLOCK_TYPE_LIST;
        $this->course = $course;
        $this->version = 2004041800;
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

        if (isset($USER->id) and !isadmin()) {    // Just print My Courses
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
                $this->content->footer = "<p><a href=\"$CFG->wwwroot/course/index.php\">".get_string("fulllistofcourses")."</a>...";
                return $this->content;
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
                $this->content->footer = "<p><a href=\"$CFG->wwwroot/course/\">".get_string("searchcourses")."</a>...";
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
                    $this->content->footer = "<p><a href=\"$CFG->wwwroot/course/index.php\">".get_string("fulllistofcourses")."</a>...";
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
