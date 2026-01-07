<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\Classroom\Resource;

use Google\Service\Classroom\AddOnAttachmentStudentSubmission;

/**
 * The "studentSubmissions" collection of methods.
 * Typical usage is:
 *  <code>
 *   $classroomService = new Google\Service\Classroom(...);
 *   $studentSubmissions = $classroomService->courses_courseWork_addOnAttachments_studentSubmissions;
 *  </code>
 */
class CoursesCourseWorkAddOnAttachmentsStudentSubmissions extends \Google\Service\Resource
{
  /**
   * Returns a student submission for an add-on attachment. This method returns
   * the following error codes: * `PERMISSION_DENIED` for access errors. *
   * `INVALID_ARGUMENT` if the request is malformed. * `NOT_FOUND` if one of the
   * identified resources does not exist. (studentSubmissions.get)
   *
   * @param string $courseId Required. Identifier of the course.
   * @param string $itemId Identifier of the `Announcement`, `CourseWork`, or
   * `CourseWorkMaterial` under which the attachment is attached. This field is
   * required, but is not marked as such while we are migrating from post_id.
   * @param string $attachmentId Required. Identifier of the attachment.
   * @param string $submissionId Required. Identifier of the student’s submission.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string postId Optional. Deprecated, use `item_id` instead.
   * @return AddOnAttachmentStudentSubmission
   * @throws \Google\Service\Exception
   */
  public function get($courseId, $itemId, $attachmentId, $submissionId, $optParams = [])
  {
    $params = ['courseId' => $courseId, 'itemId' => $itemId, 'attachmentId' => $attachmentId, 'submissionId' => $submissionId];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], AddOnAttachmentStudentSubmission::class);
  }
  /**
   * Updates data associated with an add-on attachment submission. Requires the
   * add-on to have been the original creator of the attachment and the attachment
   * to have a positive `max_points` value set. This method returns the following
   * error codes: * `PERMISSION_DENIED` for access errors. * `INVALID_ARGUMENT` if
   * the request is malformed. * `NOT_FOUND` if one of the identified resources
   * does not exist. (studentSubmissions.patch)
   *
   * @param string $courseId Required. Identifier of the course.
   * @param string $itemId Identifier of the `Announcement`, `CourseWork`, or
   * `CourseWorkMaterial` under which the attachment is attached. This field is
   * required, but is not marked as such while we are migrating from post_id.
   * @param string $attachmentId Required. Identifier of the attachment.
   * @param string $submissionId Required. Identifier of the student's submission.
   * @param AddOnAttachmentStudentSubmission $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string postId Optional. Deprecated, use `item_id` instead.
   * @opt_param string updateMask Required. Mask that identifies which fields on
   * the attachment to update. The update fails if invalid fields are specified.
   * If a field supports empty values, it can be cleared by specifying it in the
   * update mask and not in the `AddOnAttachmentStudentSubmission` object. The
   * following fields may be specified by teachers: * `points_earned`
   * @return AddOnAttachmentStudentSubmission
   * @throws \Google\Service\Exception
   */
  public function patch($courseId, $itemId, $attachmentId, $submissionId, AddOnAttachmentStudentSubmission $postBody, $optParams = [])
  {
    $params = ['courseId' => $courseId, 'itemId' => $itemId, 'attachmentId' => $attachmentId, 'submissionId' => $submissionId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], AddOnAttachmentStudentSubmission::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CoursesCourseWorkAddOnAttachmentsStudentSubmissions::class, 'Google_Service_Classroom_Resource_CoursesCourseWorkAddOnAttachmentsStudentSubmissions');
