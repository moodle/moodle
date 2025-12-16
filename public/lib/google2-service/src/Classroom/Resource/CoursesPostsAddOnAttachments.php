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

use Google\Service\Classroom\AddOnAttachment;
use Google\Service\Classroom\ClassroomEmpty;
use Google\Service\Classroom\ListAddOnAttachmentsResponse;

/**
 * The "addOnAttachments" collection of methods.
 * Typical usage is:
 *  <code>
 *   $classroomService = new Google\Service\Classroom(...);
 *   $addOnAttachments = $classroomService->courses_posts_addOnAttachments;
 *  </code>
 */
class CoursesPostsAddOnAttachments extends \Google\Service\Resource
{
  /**
   * Creates an add-on attachment under a post. Requires the add-on to have
   * permission to create new attachments on the post. This method returns the
   * following error codes: * `PERMISSION_DENIED` for access errors. *
   * `INVALID_ARGUMENT` if the request is malformed. * `NOT_FOUND` if one of the
   * identified resources does not exist. (addOnAttachments.create)
   *
   * @param string $courseId Required. Identifier of the course.
   * @param string $postId Optional. Deprecated, use `item_id` instead.
   * @param AddOnAttachment $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string addOnToken Optional. Token that authorizes the request. The
   * token is passed as a query parameter when the user is redirected from
   * Classroom to the add-on's URL. This authorization token is required for in-
   * Classroom attachment creation but optional for partner-first attachment
   * creation. Returns an error if not provided for partner-first attachment
   * creation and the developer projects that created the attachment and its
   * parent stream item do not match.
   * @opt_param string itemId Identifier of the `Announcement`, `CourseWork`, or
   * `CourseWorkMaterial` under which to create the attachment. This field is
   * required, but is not marked as such while we are migrating from post_id.
   * @return AddOnAttachment
   * @throws \Google\Service\Exception
   */
  public function create($courseId, $postId, AddOnAttachment $postBody, $optParams = [])
  {
    $params = ['courseId' => $courseId, 'postId' => $postId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], AddOnAttachment::class);
  }
  /**
   * Deletes an add-on attachment. Requires the add-on to have been the original
   * creator of the attachment. This method returns the following error codes: *
   * `PERMISSION_DENIED` for access errors. * `INVALID_ARGUMENT` if the request is
   * malformed. * `NOT_FOUND` if one of the identified resources does not exist.
   * (addOnAttachments.delete)
   *
   * @param string $courseId Required. Identifier of the course.
   * @param string $postId Optional. Deprecated, use `item_id` instead.
   * @param string $attachmentId Required. Identifier of the attachment.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string itemId Identifier of the `Announcement`, `CourseWork`, or
   * `CourseWorkMaterial` under which the attachment is attached. This field is
   * required, but is not marked as such while we are migrating from post_id.
   * @return ClassroomEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($courseId, $postId, $attachmentId, $optParams = [])
  {
    $params = ['courseId' => $courseId, 'postId' => $postId, 'attachmentId' => $attachmentId];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], ClassroomEmpty::class);
  }
  /**
   * Returns an add-on attachment. Requires the add-on requesting the attachment
   * to be the original creator of the attachment. This method returns the
   * following error codes: * `PERMISSION_DENIED` for access errors. *
   * `INVALID_ARGUMENT` if the request is malformed. * `NOT_FOUND` if one of the
   * identified resources does not exist. (addOnAttachments.get)
   *
   * @param string $courseId Required. Identifier of the course.
   * @param string $postId Optional. Deprecated, use `item_id` instead.
   * @param string $attachmentId Required. Identifier of the attachment.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string itemId Identifier of the `Announcement`, `CourseWork`, or
   * `CourseWorkMaterial` under which the attachment is attached. This field is
   * required, but is not marked as such while we are migrating from post_id.
   * @return AddOnAttachment
   * @throws \Google\Service\Exception
   */
  public function get($courseId, $postId, $attachmentId, $optParams = [])
  {
    $params = ['courseId' => $courseId, 'postId' => $postId, 'attachmentId' => $attachmentId];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], AddOnAttachment::class);
  }
  /**
   * Returns all attachments created by an add-on under the post. Requires the
   * add-on to have active attachments on the post or have permission to create
   * new attachments on the post. This method returns the following error codes: *
   * `PERMISSION_DENIED` for access errors. * `INVALID_ARGUMENT` if the request is
   * malformed. * `NOT_FOUND` if one of the identified resources does not exist.
   * (addOnAttachments.listCoursesPostsAddOnAttachments)
   *
   * @param string $courseId Required. Identifier of the course.
   * @param string $postId Optional. Identifier of the post under the course whose
   * attachments to enumerate. Deprecated, use `item_id` instead.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string itemId Identifier of the `Announcement`, `CourseWork`, or
   * `CourseWorkMaterial` whose attachments should be enumerated. This field is
   * required, but is not marked as such while we are migrating from post_id.
   * @opt_param int pageSize The maximum number of attachments to return. The
   * service may return fewer than this value. If unspecified, at most 20
   * attachments will be returned. The maximum value is 20; values above 20 will
   * be coerced to 20.
   * @opt_param string pageToken A page token, received from a previous
   * `ListAddOnAttachments` call. Provide this to retrieve the subsequent page.
   * When paginating, all other parameters provided to `ListAddOnAttachments` must
   * match the call that provided the page token.
   * @return ListAddOnAttachmentsResponse
   * @throws \Google\Service\Exception
   */
  public function listCoursesPostsAddOnAttachments($courseId, $postId, $optParams = [])
  {
    $params = ['courseId' => $courseId, 'postId' => $postId];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListAddOnAttachmentsResponse::class);
  }
  /**
   * Updates an add-on attachment. Requires the add-on to have been the original
   * creator of the attachment. This method returns the following error codes: *
   * `PERMISSION_DENIED` for access errors. * `INVALID_ARGUMENT` if the request is
   * malformed. * `NOT_FOUND` if one of the identified resources does not exist.
   * (addOnAttachments.patch)
   *
   * @param string $courseId Required. Identifier of the course.
   * @param string $postId Required. Identifier of the post under which the
   * attachment is attached.
   * @param string $attachmentId Required. Identifier of the attachment.
   * @param AddOnAttachment $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string itemId Identifier of the post under which the attachment is
   * attached.
   * @opt_param string updateMask Required. Mask that identifies which fields on
   * the attachment to update. The update fails if invalid fields are specified.
   * If a field supports empty values, it can be cleared by specifying it in the
   * update mask and not in the `AddOnAttachment` object. If a field that does not
   * support empty values is included in the update mask and not set in the
   * `AddOnAttachment` object, an `INVALID_ARGUMENT` error is returned. The
   * following fields may be specified by teachers: * `title` * `teacher_view_uri`
   * * `student_view_uri` * `student_work_review_uri` * `due_date` * `due_time` *
   * `max_points`
   * @return AddOnAttachment
   * @throws \Google\Service\Exception
   */
  public function patch($courseId, $postId, $attachmentId, AddOnAttachment $postBody, $optParams = [])
  {
    $params = ['courseId' => $courseId, 'postId' => $postId, 'attachmentId' => $attachmentId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], AddOnAttachment::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CoursesPostsAddOnAttachments::class, 'Google_Service_Classroom_Resource_CoursesPostsAddOnAttachments');
