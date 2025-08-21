<?php
require_once('../../../../../config.php');

global $CFG;
global $DB;

// Check headers first, otherwise return 403
$TT_LMS_CLIENT_ID_HEADER_NAME = "HTTP_X_TT_LMS_CLIENT_ID";
$TT_LMS_CLIENT_SECRET_HEADER_NAME = "HTTP_X_TT_LMS_CLIENT_SECRET";

$client_id = $_SERVER[$TT_LMS_CLIENT_ID_HEADER_NAME];
$client_secret = $_SERVER[$TT_LMS_CLIENT_SECRET_HEADER_NAME];
$user_name = required_param('userName', PARAM_NOTAGS);
$course_id = required_param('courseId', PARAM_INT);
/*
A require_login, require_course_login nor admin_externalpage_setup is not required
as the application that connects to this API does so through a unique key/secret
configured once the plugin is installed. THis key/secret is used to authenticate requests
to the API.
*/

if (strcmp($CFG->tt_lms_client_id, $client_id) === 0
    && strcmp($CFG->tt_lms_client_secret, $client_secret) === 0) {

  $user = get_complete_user_data('username', $user_name);
  $course_context = context_course::instance($course_id);

  if ($user && has_capability('moodle/course:viewparticipants', $course_context, $user->id)) {
    try {
      $output = [];
      $course_record = $DB->get_record('course', array('id' => $course_id));

      $output['name'] = $course_record->fullname;
      $output['sectionCode'] = '';
      $output['participants'] = [];
      $output['instructors'] = [];

      $participants = get_enrolled_users($course_context);

      foreach ($participants as $participant) {
        if (is_instructor_in_context($participant->id, $course_context)) {
          $output['instructors'][] = format_user($participant);
        } else {
          $output['participants'][] = format_user($participant);
        }
      }

      http_response_code(200);
      header('Content-Type: application/json');
      print json_encode($output);
    } catch (Exception $e) {
      http_response_code(400);
    }
  } else {
    http_response_code(401);
  }
} else {
  http_response_code(401);
}

function format_user($user) {
  $participant = [];

  $participant['id'] = $user->id;
  $participant['firstName'] = $user->firstname;
  $participant['lastName'] = $user->lastname;
  $participant['username'] = $user->username;
  $participant['email'] = $user->email;

  return $participant;
}

function is_instructor_in_context($user_id, $context) {
  return has_capability('mod/turningptintegration:manage', $context, $user_id);
}
