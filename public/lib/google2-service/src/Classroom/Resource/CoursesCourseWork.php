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

use Google\Service\Classroom\AddOnContext;
use Google\Service\Classroom\ClassroomEmpty;
use Google\Service\Classroom\CourseWork;
use Google\Service\Classroom\ListCourseWorkResponse;
use Google\Service\Classroom\ModifyCourseWorkAssigneesRequest;
use Google\Service\Classroom\Rubric;

/**
 * The "courseWork" collection of methods.
 * Typical usage is:
 *  <code>
 *   $classroomService = new Google\Service\Classroom(...);
 *   $courseWork = $classroomService->courses_courseWork;
 *  </code>
 */
class CoursesCourseWork extends \Google\Service\Resource
{
  /**
   * Creates course work. The resulting course work (and corresponding student
   * submissions) are associated with the Developer Console project of the [OAuth
   * client ID](https://support.google.com/cloud/answer/6158849) used to make the
   * request. Classroom API requests to modify course work and student submissions
   * must be made with an OAuth client ID from the associated Developer Console
   * project. This method returns the following error codes: * `PERMISSION_DENIED`
   * if the requesting user is not permitted to access the requested course,
   * create course work in the requested course, share a Drive attachment, or for
   * access errors. * `INVALID_ARGUMENT` if the request is malformed. *
   * `NOT_FOUND` if the requested course does not exist. * `FAILED_PRECONDITION`
   * for the following request error: * AttachmentNotVisible (courseWork.create)
   *
   * @param string $courseId Identifier of the course. This identifier can be
   * either the Classroom-assigned identifier or an alias.
   * @param CourseWork $postBody
   * @param array $optParams Optional parameters.
   * @return CourseWork
   * @throws \Google\Service\Exception
   */
  public function create($courseId, CourseWork $postBody, $optParams = [])
  {
    $params = ['courseId' => $courseId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], CourseWork::class);
  }
  /**
   * Deletes a course work. This request must be made by the Developer Console
   * project of the [OAuth client
   * ID](https://support.google.com/cloud/answer/6158849) used to create the
   * corresponding course work item. This method returns the following error
   * codes: * `PERMISSION_DENIED` if the requesting developer project did not
   * create the corresponding course work, if the requesting user is not permitted
   * to delete the requested course or for access errors. * `FAILED_PRECONDITION`
   * if the requested course work has already been deleted. * `NOT_FOUND` if no
   * course exists with the requested ID. (courseWork.delete)
   *
   * @param string $courseId Identifier of the course. This identifier can be
   * either the Classroom-assigned identifier or an alias.
   * @param string $id Identifier of the course work to delete. This identifier is
   * a Classroom-assigned identifier.
   * @param array $optParams Optional parameters.
   * @return ClassroomEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($courseId, $id, $optParams = [])
  {
    $params = ['courseId' => $courseId, 'id' => $id];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], ClassroomEmpty::class);
  }
  /**
   * Returns course work. This method returns the following error codes: *
   * `PERMISSION_DENIED` if the requesting user is not permitted to access the
   * requested course or course work, or for access errors. * `INVALID_ARGUMENT`
   * if the request is malformed. * `NOT_FOUND` if the requested course or course
   * work does not exist. (courseWork.get)
   *
   * @param string $courseId Identifier of the course. This identifier can be
   * either the Classroom-assigned identifier or an alias.
   * @param string $id Identifier of the course work.
   * @param array $optParams Optional parameters.
   * @return CourseWork
   * @throws \Google\Service\Exception
   */
  public function get($courseId, $id, $optParams = [])
  {
    $params = ['courseId' => $courseId, 'id' => $id];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], CourseWork::class);
  }
  /**
   * Gets metadata for Classroom add-ons in the context of a specific post. To
   * maintain the integrity of its own data and permissions model, an add-on
   * should call this to validate query parameters and the requesting user's role
   * whenever the add-on is opened in an
   * [iframe](https://developers.google.com/workspace/classroom/add-ons/get-
   * started/iframes/iframes-overview). This method returns the following error
   * codes: * `PERMISSION_DENIED` for access errors. * `INVALID_ARGUMENT` if the
   * request is malformed. * `NOT_FOUND` if one of the identified resources does
   * not exist. (courseWork.getAddOnContext)
   *
   * @param string $courseId Required. Identifier of the course.
   * @param string $itemId Identifier of the `Announcement`, `CourseWork`, or
   * `CourseWorkMaterial` under which the attachment is attached. This field is
   * required, but is not marked as such while we are migrating from post_id.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string addOnToken Optional. Token that authorizes the request. The
   * token is passed as a query parameter when the user is redirected from
   * Classroom to the add-on's URL. The authorization token is required when
   * neither of the following is true: * The add-on has attachments on the post. *
   * The developer project issuing the request is the same project that created
   * the post.
   * @opt_param string attachmentId Optional. The identifier of the attachment.
   * This field is required for all requests except when the user is in the
   * [Attachment Discovery
   * iframe](https://developers.google.com/workspace/classroom/add-ons/get-
   * started/iframes/attachment-discovery-iframe).
   * @opt_param string postId Optional. Deprecated, use `item_id` instead.
   * @return AddOnContext
   * @throws \Google\Service\Exception
   */
  public function getAddOnContext($courseId, $itemId, $optParams = [])
  {
    $params = ['courseId' => $courseId, 'itemId' => $itemId];
    $params = array_merge($params, $optParams);
    return $this->call('getAddOnContext', [$params], AddOnContext::class);
  }
  /**
   * Returns a list of course work that the requester is permitted to view. Course
   * students may only view `PUBLISHED` course work. Course teachers and domain
   * administrators may view all course work. This method returns the following
   * error codes: * `PERMISSION_DENIED` if the requesting user is not permitted to
   * access the requested course or for access errors. * `INVALID_ARGUMENT` if the
   * request is malformed. * `NOT_FOUND` if the requested course does not exist.
   * (courseWork.listCoursesCourseWork)
   *
   * @param string $courseId Identifier of the course. This identifier can be
   * either the Classroom-assigned identifier or an alias.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string courseWorkStates Restriction on the work status to return.
   * Only courseWork that matches is returned. If unspecified, items with a work
   * status of `PUBLISHED` is returned.
   * @opt_param string orderBy Optional sort ordering for results. A comma-
   * separated list of fields with an optional sort direction keyword. Supported
   * fields are `updateTime` and `dueDate`. Supported direction keywords are `asc`
   * and `desc`. If not specified, `updateTime desc` is the default behavior.
   * Examples: `dueDate asc,updateTime desc`, `updateTime,dueDate desc`
   * @opt_param int pageSize Maximum number of items to return. Zero or
   * unspecified indicates that the server may assign a maximum. The server may
   * return fewer than the specified number of results.
   * @opt_param string pageToken nextPageToken value returned from a previous list
   * call, indicating that the subsequent page of results should be returned. The
   * list request must be otherwise identical to the one that resulted in this
   * token.
   * @return ListCourseWorkResponse
   * @throws \Google\Service\Exception
   */
  public function listCoursesCourseWork($courseId, $optParams = [])
  {
    $params = ['courseId' => $courseId];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListCourseWorkResponse::class);
  }
  /**
   * Modifies assignee mode and options of a coursework. Only a teacher of the
   * course that contains the coursework may call this method. This method returns
   * the following error codes: * `PERMISSION_DENIED` if the requesting user is
   * not permitted to access the requested course or course work or for access
   * errors. * `INVALID_ARGUMENT` if the request is malformed. * `NOT_FOUND` if
   * the requested course or course work does not exist. * `FAILED_PRECONDITION`
   * for the following request error: * EmptyAssignees
   * (courseWork.modifyAssignees)
   *
   * @param string $courseId Identifier of the course. This identifier can be
   * either the Classroom-assigned identifier or an alias.
   * @param string $id Identifier of the coursework.
   * @param ModifyCourseWorkAssigneesRequest $postBody
   * @param array $optParams Optional parameters.
   * @return CourseWork
   * @throws \Google\Service\Exception
   */
  public function modifyAssignees($courseId, $id, ModifyCourseWorkAssigneesRequest $postBody, $optParams = [])
  {
    $params = ['courseId' => $courseId, 'id' => $id, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('modifyAssignees', [$params], CourseWork::class);
  }
  /**
   * Updates one or more fields of a course work. See
   * google.classroom.v1.CourseWork for details of which fields may be updated and
   * who may change them. This request must be made by the Developer Console
   * project of the [OAuth client
   * ID](https://support.google.com/cloud/answer/6158849) used to create the
   * corresponding course work item. This method returns the following error
   * codes: * `PERMISSION_DENIED` if the requesting developer project did not
   * create the corresponding course work, if the user is not permitted to make
   * the requested modification to the student submission, or for access errors. *
   * `INVALID_ARGUMENT` if the request is malformed. * `FAILED_PRECONDITION` if
   * the requested course work has already been deleted. * `NOT_FOUND` if the
   * requested course or course work does not exist. (courseWork.patch)
   *
   * @param string $courseId Identifier of the course. This identifier can be
   * either the Classroom-assigned identifier or an alias.
   * @param string $id Identifier of the course work.
   * @param CourseWork $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Mask that identifies which fields on the course
   * work to update. This field is required to do an update. The update fails if
   * invalid fields are specified. If a field supports empty values, it can be
   * cleared by specifying it in the update mask and not in the `CourseWork`
   * object. If a field that does not support empty values is included in the
   * update mask and not set in the `CourseWork` object, an `INVALID_ARGUMENT`
   * error is returned. The following fields may be specified by teachers: *
   * `title` * `description` * `state` * `due_date` * `due_time` * `max_points` *
   * `scheduled_time` * `submission_modification_mode` * `topic_id` *
   * `grading_period_id`
   * @return CourseWork
   * @throws \Google\Service\Exception
   */
  public function patch($courseId, $id, CourseWork $postBody, $optParams = [])
  {
    $params = ['courseId' => $courseId, 'id' => $id, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], CourseWork::class);
  }
  /**
   * Updates a rubric. See google.classroom.v1.Rubric for details of which fields
   * can be updated. Rubric update capabilities are
   * [limited](/classroom/rubrics/limitations) once grading has started. The
   * requesting user and course owner must have rubrics creation capabilities. For
   * details, see [licensing requirements](https://developers.google.com/workspace
   * /classroom/rubrics/limitations#license-requirements). This request must be
   * made by the Google Cloud console of the [OAuth client
   * ID](https://support.google.com/cloud/answer/6158849) used to create the
   * parent course work item. This method returns the following error codes: *
   * `PERMISSION_DENIED` if the requesting developer project didn't create the
   * corresponding course work, if the user isn't permitted to make the requested
   * modification to the rubric, or for access errors. This error code is also
   * returned if grading has already started on the rubric. * `INVALID_ARGUMENT`
   * if the request is malformed and for the following request error: *
   * `RubricCriteriaInvalidFormat` * `NOT_FOUND` if the requested course, course
   * work, or rubric doesn't exist or if the user doesn't have access to the
   * corresponding course work. * `INTERNAL` if grading has already started on the
   * rubric. (courseWork.updateRubric)
   *
   * @param string $courseId Required. Identifier of the course.
   * @param string $courseWorkId Required. Identifier of the course work.
   * @param Rubric $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string id Optional. Identifier of the rubric.
   * @opt_param string updateMask Optional. Mask that identifies which fields on
   * the rubric to update. This field is required to do an update. The update
   * fails if invalid fields are specified. There are multiple options to define
   * the criteria of a rubric: the `source_spreadsheet_id` and the `criteria`
   * list. Only one of these can be used at a time to define a rubric. The rubric
   * `criteria` list is fully replaced by the rubric criteria specified in the
   * update request. For example, if a criterion or level is missing from the
   * request, it is deleted. New criteria and levels are added and an ID is
   * assigned. Existing criteria and levels retain the previously assigned ID if
   * the ID is specified in the request. The following fields can be specified by
   * teachers: * `criteria` * `source_spreadsheet_id`
   * @return Rubric
   * @throws \Google\Service\Exception
   */
  public function updateRubric($courseId, $courseWorkId, Rubric $postBody, $optParams = [])
  {
    $params = ['courseId' => $courseId, 'courseWorkId' => $courseWorkId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('updateRubric', [$params], Rubric::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CoursesCourseWork::class, 'Google_Service_Classroom_Resource_CoursesCourseWork');
