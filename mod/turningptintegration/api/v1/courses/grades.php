<?php
require_once('../../../../../config.php');

global $CFG;

require_once($CFG->libdir . '/gradelib.php');

global $DB;

// Check headers first, otherwise return 403
$TT_LMS_CLIENT_ID_HEADER_NAME = "HTTP_X_TT_LMS_CLIENT_ID";
$TT_LMS_CLIENT_SECRET_HEADER_NAME = "HTTP_X_TT_LMS_CLIENT_SECRET";

$client_id = $_SERVER[$TT_LMS_CLIENT_ID_HEADER_NAME];
$client_secret = $_SERVER[$TT_LMS_CLIENT_SECRET_HEADER_NAME];

/*
A require_login, require_course_login nor admin_externalpage_setup is not required
as the application that connects to this API does so through a unique key/secret
configured once the plugin is installed. THis key/secret is used to authenticate requests
to the API.
*/

if (strcmp($CFG->tt_lms_client_id, $client_id) === 0
    && strcmp($CFG->tt_lms_client_secret, $client_secret) === 0) {

  $post_body = file_get_contents('php://input');
  $post_grade_column_request = json_decode($post_body);

  $user = get_complete_user_data('username', $post_grade_column_request->lmsUsername);
  $course_context = context_course::instance($post_grade_column_request->courseId);
  $grade_column_data = $post_grade_column_request->gradeBookColumn;

  if ($user && has_capability('moodle/grade:manage', $course_context, $user->id)) {
    try {
      $output = [];
      $output['transactionId'] = $post_grade_column_request->transactionId;
      $output['failedUsers'] = [];
      $output['successUsers'] = [];

      $grade_item = grade_item::fetch(array('courseid' => $post_grade_column_request->courseId, 'id' => $grade_column_data->id));
      if ($grade_item) {
        $grade_data = $grade_item->get_record_data();

        $grade_data->grademax = unformat_float($grade_column_data->maxScore);
        $grade_data->grademin = unformat_float(0.0);
        $grade_data->itemname = $grade_column_data->name;

        grade_item::set_properties($grade_item, $grade_data);

        $grade_item->update();
      } else {
        $grade_item = new grade_item(array('courseid' => $post_grade_column_request->courseId), false);
        $grade_item->itemTitle = $grade_column_data->name;

        $parent_category = grade_category::fetch_course_category($post_grade_column_request->courseId);

        $grade_data = $grade_item->get_record_data();
        $grade_data->parentcategory = $parent_category->id;
        $grade_data->grademax = unformat_float($grade_column_data->maxScore);
        $grade_data->grademin = unformat_float(0.0);
        $grade_data->itemname = $grade_column_data->name;

        grade_item::set_properties($grade_item, $grade_data);

        $grade_item->outcomeid = null;
        $grade_item->itemtype = 'manual';
        $grade_item->itemmodule = 'turningptintegration';

        $grade_item->insert();
      }

      $output['columnId'] = $grade_item->id;

      foreach ($grade_column_data->grades as $grade) {
        $student = get_complete_user_data('id', $grade->lmsUserId);

        if ($student) {
          $success = false;
          try {
            $success = $grade_item->update_final_grade($grade->lmsUserId, $grade->grade, 'turningptintegration/manual');

            if ($success) {
              //Add student as success
              $output['successUsers'][] = format_student_response($grade->lmsUserUuid, $grade->lmsUserId);
            } else {
              $output['failedUsers'][] = format_student_response($grade->lmsUserUuid, $grade->lmsUserId, 'LMS_USER_ERROR');
            }
          } catch (Exception $e) {
            $output['failedUsers'][] = format_student_response($grade->lmsUserUuid, $grade->lmsUserId, 'LMS_USER_ERROR');
          }
        } else {
          $output['failedUsers'][] = format_student_response($grade->lmsUserUuid, $grade->lmsUserId, 'ITEM_NOT_FOUND');
        }
      }
    } catch (Exception $e) {
      $output['transactionError'] = 'SERVER_EXCEPTION';
      $output['debug'] = $e;
    } finally {
      http_response_code(200);
      header('Content-Type: application/json');

      print json_encode($output);
    }
  } else {
    http_response_code(401);
  }
} else {
  http_response_code(401);
}

function format_student_response($user_uuid, $lms_username, $error = null) {
  $output = [];

  $output['userId'] = $user_uuid;
  $output['lmsUserId'] = $lms_username;
  $output['error'] = $error;

  return $output;
}

function is_instructor_in_context($user_id, $context) {
  return has_capability('mod/turningptintegration:manage', $context, $user_id);
}
