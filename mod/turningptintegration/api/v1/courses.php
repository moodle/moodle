<?php
require_once('../../../../config.php');

global $CFG;
global $DB;

// Check headers first, otherwise return 403
$TT_LMS_CLIENT_ID_HEADER_NAME = "HTTP_X_TT_LMS_CLIENT_ID";
$TT_LMS_CLIENT_SECRET_HEADER_NAME = "HTTP_X_TT_LMS_CLIENT_SECRET";

$client_id = $_SERVER[$TT_LMS_CLIENT_ID_HEADER_NAME];
$client_secret = $_SERVER[$TT_LMS_CLIENT_SECRET_HEADER_NAME];
$user_name = required_param('userName', PARAM_NOTAGS);
$lms_id = required_param('lmsId', PARAM_NOTAGS);
/*
A require_login, require_course_login nor admin_externalpage_setup is not required
as the application that connects to this API does so through a unique key/secret
configured once the plugin is installed. THis key/secret is used to authenticate requests
to the API.
*/

if (strcmp($CFG->tt_lms_client_id, $client_id) === 0
    && strcmp($CFG->tt_lms_client_secret, $client_secret) === 0) {

  //Need to authenticate as the chosen user
  $user = get_complete_user_data('username', $user_name);

  if ($user) {
    try {
      $query_lms_name = "SELECT fullname FROM {course} WHERE id = 1";
      $lms_name_result = $DB->get_record('course', array('id' => 1));
      $output = [];
      $courses = enrol_get_users_courses($user->id, false);

      foreach ($courses as $course_id => $course) {
        $course_context = context_course::instance($course_id);
        if (is_instructor_in_context($user->id, $course_context)) {
          $output_course = [];
          $output_course['name'] = $course->fullname;
          $output_course['lmsId'] = $lms_id;
          $output_course['lmsCourseId'] = $course->id;
          $output_course['lmsName'] = $lms_name_result->fullname;
          $output_course['sectionCode'] = '';
          $output[] = $output_course;
        }
      }

      http_response_code(200);
      header('Content-Type: application/json');

      print json_encode($output);
    } catch (Exception $e) {
      http_response_code(500);
      print $e->getMessage();
    }
  } else {
    http_response_code(401);
  }
} else {
  http_response_code(401);
}

function is_instructor_in_context($user_id, $context) {
  return has_capability('mod/turningptintegration:manage', $context, $user_id);
}
