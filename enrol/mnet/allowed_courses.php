<?php

/*
 * Created on 12/12/2006
 *
 * MNET enrol allowed courses and categories form
 */

require_once dirname(dirname(dirname(__FILE__))) . '/config.php';
require_once $CFG->libdir . '/adminlib.php';
include_once $CFG->dirroot . '/mnet/lib.php';


admin_externalpage_setup('ssoaccesscontrol');
admin_externalpage_print_header();

$addcategory    = optional_param('addcategory', 0, PARAM_BOOL);
$removecategory = optional_param('removecategory', 0, PARAM_BOOL);
$addcourse      = optional_param('addcourse', 0, PARAM_BOOL);
$removecourse   = optional_param('removecourse', 0, PARAM_BOOL);

$sitecontext = get_context_instance(CONTEXT_SYSTEM);
$sesskey = sesskey();
$formerror = array();

require_capability('moodle/user:delete', $sitecontext);

// process returned form data
if ($form = data_submitted() and confirm_sesskey()) {

    // add and remove categories as needed
    if (!empty($CFG->enrol_mnet_allowed_categories)) {
        $allowedcategories = explode(',', $CFG->enrol_mnet_allowed_categories);
    }
    if ($addcategory and !empty($form->addcategories)) {
        foreach ($form->addcategories as $category) {
            if (!$category = clean_param($category, PARAM_INT)) {
                continue;
            }
            $allowedcategories[] = $category;
        }
    }
    if ($removecategory and !empty($form->removecategories)) {
        foreach ($form->removecategories as $category) {
            if ($category = clean_param($category, PARAM_INT)) {
                $removedcategories[] = $category;
            }
        }
        $allowedcategories = array_diff($allowedcategories, $removedcategories);
    }

    // add and remove courses as needed
    if (!empty($CFG->enrol_mnet_allowed_courses)) {
        $allowedcourses = explode(',', $CFG->enrol_mnet_allowed_courses);
    }
    if ($addcourse and !empty($form->addcourses)) {
        foreach ($form->addcourses as $course) {
            if ($course = clean_param($course, PARAM_INT)) {
                $allowedcourses[] = $course;
            }
        }
    }
    if ($removecourse and !empty($form->removecourses)) {
        foreach ($form->removecourses as $course) {
            if (!$course = clean_param($course, PARAM_INT)) {
                continue;
            }
            $removedcourses[] = $course;
        }
        $allowedcourses = array_diff($allowedcourses, $removedcourses);
    }

    // save config
    $cfg = empty($allowedcategories) ? '' : implode(',', $allowedcategories);
    set_config('enrol_mnet_allowed_categories', $cfg);
    $cfg = empty($allowedcourses) ? '' : implode(',', $allowedcourses);
    set_config('enrol_mnet_allowed_courses', $cfg);

    // redirect('allowed_courses.php', get_string('changessaved'));
}



// setup arrays for allowed categories and courses
$categories = array();
if ($categories = get_records('course_categories', '', '', 'name', 'id, name')) {
    $allowedcategories = array();
    if (empty($CFG->enrol_mnet_allowed_categories)) {
        $potentialcategories = $categories;
    } else {
        $potentialcategories = array();
        $explode_categories = explode(',', $CFG->enrol_mnet_allowed_categories);
        foreach($categories as $category) {
            if (in_array($category->id, $explode_categories)) {
                $allowedcategories[] = $category;
            } else {
                $potentialcategories[] = $category;
            }
        }
    }
}
$courses = array();
if ($courses = get_records('course', '', '', 'shortname', 'id, shortname')) {
    unset($courses[SITEID]); // never list or offer the siteid
    $allowedcourses = array();
    if (empty($CFG->enrol_mnet_allowed_courses)) {
        $potentialcourses = $courses;
    } else {
        $potentialcourses = array();
        $explode_courses = explode(',', $CFG->enrol_mnet_allowed_courses);
        foreach($courses as $course) {
            if (in_array($course->id, $explode_courses)) {
                $allowedcourses[] = $course;
            } else {
                $potentialcourses[] = $course;
            }
        }
    }
}



// output the form
print_simple_box_start('center','90%','','20');

?>
 <div class="allowedcoursesdiv"> 
  <form id="allowedcoursesform" method="post">
    <input type="hidden" name="sesskey" value="<?php echo $sesskey; ?>" />
<?php

// display course category selector
if (count($categories) < 1) {
    echo get_string('nocategoriesdefined', 'enrol_mnet', "$CFG->wwwroot/course/index.php?categoryedit=on");
} else {

?>
   <table align="center" border="0" cellpadding="5" cellspacing="0">
    <tr>
      <td valign="top">
          <?php print_string('allowedcategories', 'enrol_mnet', count($allowedcategories)); ?>
      </td>
      <td></td>
      <td valign="top">
          <?php print_string('allcategories', 'enrol_mnet', count($potentialcategories)); ?>
      </td>
    </tr>

    <tr>
      <td valign="top">
          <select name="removecategories[]" size="20" id="removecategories" multiple="multiple"
                  onfocus="getElementById('allowedcoursesform').addcategory.disabled=true;
                           getElementById('allowedcoursesform').removecategory.disabled=false;
                           getElementById('allowedcoursesform').addcategories.selectedIndex=-1;" >
          <?php
              foreach ($allowedcategories as $category) {
                  echo "<option value=\"$category->id\"> " . format_string($category->name) . " </option>\n";
              }
          ?>
          </select>
      </td>

      <td valign="top">
        <br />
        <input name="addcategory" type="submit" id="add" value="&larr;" />
        <br />
        <input name="removecategory" type="submit" id="remove" value="&rarr;" />
        <br />
      </td>

      <td valign="top">
          <select name="addcategories[]" size="20" id="addcategories" multiple="multiple"
                  onFocus="getElementById('allowedcoursesform').addcategory.disabled=false;
                           getElementById('allowedcoursesform').removecategory.disabled=true;
                           getElementById('allowedcoursesform').removecategories.selectedIndex=-1;">
          <?php
            foreach ($potentialcategories as $category) {
                echo "<option value=\"$category->id\"> " . format_string($category->name) . " </option>\n";
            }
        ?>
        </select>
       </td>
    </tr>
   </table>
<?php

}

// display course selector
if (count($courses) < 1) {
    echo get_string('nocoursesdefined', 'enrol_mnet', "TODO: $course_admin_url"); // TODO
} else {

?>
   <table align="center" border="0" cellpadding="5" cellspacing="0">
    <tr>
      <td valign="top">
          <?php print_string('allowedcourses', 'enrol_mnet', count($allowedcourses)); ?>
      </td>
      <td></td>
      <td valign="top">
          <?php print_string('allcourses', 'enrol_mnet', count($potentialcourses)); ?>
      </td>
    </tr>

    <tr>
      <td valign="top">
          <select name="removecourses[]" size="20" id="removecourses" multiple="multiple"
                  onFocus="getElementById('allowedcoursesform').addcourse.disabled=true;
                           getElementById('allowedcoursesform').removecourse.disabled=false;
                           getElementById('allowedcoursesform').addcourses.selectedIndex=-1;">
          <?php
              foreach ($allowedcourses as $course) {
                  echo "<option value=\"$course->id\"> " . format_string($course->shortname) . " </option>\n";
              }
          ?>
          </select>
      </td>

      <td valign="top">
        <br />
        <input name="addcourse" type="submit" id="add" value="&larr;" />
        <br />
        <input name="removecourse" type="submit" id="remove" value="&rarr;" />
        <br />
      </td>

      <td valign="top">
          <select name="addcourses[]" size="20" id="addcourses" multiple="multiple"
                  onFocus="getElementById('allowedcoursesform').addcourse.disabled=false;
                           getElementById('allowedcoursesform').removecourse.disabled=true;
                           getElementById('allowedcoursesform').removecourses.selectedIndex=-1;">
          <?php
            foreach ($potentialcourses as $course) {
                echo "<option value=\"$course->id\"> " . format_string($course->shortname) . " </option>\n";
            }
        ?>
        </select>
       </td>
    </tr>
   </table>
<?php

}

?>
   </form>
  </div>
<?php

print_simple_box_end();
admin_externalpage_print_footer();

?>
