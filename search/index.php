<?php  
  /*$id = required_param('id', PARAM_INT);   // course
  if (! $course = get_record("course", "id", $id)) {
    error("Course ID is incorrect");
  }
  require_course_login($course);
  add_to_log($course->id, "wiki", "view all", "index.php?id=$course->id", "");*/

  header("Location: query.php");    
?>