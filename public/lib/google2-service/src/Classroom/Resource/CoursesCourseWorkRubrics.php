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

use Google\Service\Classroom\ClassroomEmpty;
use Google\Service\Classroom\ListRubricsResponse;
use Google\Service\Classroom\Rubric;

/**
 * The "rubrics" collection of methods.
 * Typical usage is:
 *  <code>
 *   $classroomService = new Google\Service\Classroom(...);
 *   $rubrics = $classroomService->courses_courseWork_rubrics;
 *  </code>
 */
class CoursesCourseWorkRubrics extends \Google\Service\Resource
{
  /**
   * Creates a rubric. The requesting user and course owner must have rubrics
   * creation capabilities. For details, see [licensing requirements](https://deve
   * lopers.google.com/workspace/classroom/rubrics/limitations#license-
   * requirements). For further details, see [Rubrics structure and known
   * limitations](/classroom/rubrics/limitations). This request must be made by
   * the Google Cloud console of the [OAuth client
   * ID](https://support.google.com/cloud/answer/6158849) used to create the
   * parent course work item. This method returns the following error codes: *
   * `PERMISSION_DENIED` if the requesting user isn't permitted to create rubrics
   * for course work in the requested course. * `INTERNAL` if the request has
   * insufficient OAuth scopes. * `INVALID_ARGUMENT` if the request is malformed
   * and for the following request error: * `RubricCriteriaInvalidFormat` *
   * `NOT_FOUND` if the requested course or course work don't exist or the user
   * doesn't have access to the course or course work. * `FAILED_PRECONDITION` for
   * the following request error: * `AttachmentNotVisible` (rubrics.create)
   *
   * @param string $courseId Required. Identifier of the course.
   * @param string $courseWorkId Required. Identifier of the course work.
   * @param Rubric $postBody
   * @param array $optParams Optional parameters.
   * @return Rubric
   * @throws \Google\Service\Exception
   */
  public function create($courseId, $courseWorkId, Rubric $postBody, $optParams = [])
  {
    $params = ['courseId' => $courseId, 'courseWorkId' => $courseWorkId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Rubric::class);
  }
  /**
   * Deletes a rubric. The requesting user and course owner must have rubrics
   * creation capabilities. For details, see [licensing requirements](https://deve
   * lopers.google.com/workspace/classroom/rubrics/limitations#license-
   * requirements). This request must be made by the Google Cloud console of the
   * [OAuth client ID](https://support.google.com/cloud/answer/6158849) used to
   * create the corresponding rubric. This method returns the following error
   * codes: * `PERMISSION_DENIED` if the requesting developer project didn't
   * create the corresponding rubric, or if the requesting user isn't permitted to
   * delete the requested rubric. * `NOT_FOUND` if no rubric exists with the
   * requested ID or the user does not have access to the course, course work, or
   * rubric. * `INVALID_ARGUMENT` if grading has already started on the rubric.
   * (rubrics.delete)
   *
   * @param string $courseId Required. Identifier of the course.
   * @param string $courseWorkId Required. Identifier of the course work.
   * @param string $id Required. Identifier of the rubric.
   * @param array $optParams Optional parameters.
   * @return ClassroomEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($courseId, $courseWorkId, $id, $optParams = [])
  {
    $params = ['courseId' => $courseId, 'courseWorkId' => $courseWorkId, 'id' => $id];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], ClassroomEmpty::class);
  }
  /**
   * Returns a rubric. This method returns the following error codes: *
   * `PERMISSION_DENIED` for access errors. * `INVALID_ARGUMENT` if the request is
   * malformed. * `NOT_FOUND` if the requested course, course work, or rubric
   * doesn't exist or if the user doesn't have access to the corresponding course
   * work. (rubrics.get)
   *
   * @param string $courseId Required. Identifier of the course.
   * @param string $courseWorkId Required. Identifier of the course work.
   * @param string $id Required. Identifier of the rubric.
   * @param array $optParams Optional parameters.
   * @return Rubric
   * @throws \Google\Service\Exception
   */
  public function get($courseId, $courseWorkId, $id, $optParams = [])
  {
    $params = ['courseId' => $courseId, 'courseWorkId' => $courseWorkId, 'id' => $id];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Rubric::class);
  }
  /**
   * Returns a list of rubrics that the requester is permitted to view. This
   * method returns the following error codes: * `PERMISSION_DENIED` for access
   * errors. * `INVALID_ARGUMENT` if the request is malformed. * `NOT_FOUND` if
   * the requested course or course work doesn't exist or if the user doesn't have
   * access to the corresponding course work.
   * (rubrics.listCoursesCourseWorkRubrics)
   *
   * @param string $courseId Required. Identifier of the course.
   * @param string $courseWorkId Required. Identifier of the course work.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The maximum number of rubrics to return. If
   * unspecified, at most 1 rubric is returned. The maximum value is 1; values
   * above 1 are coerced to 1.
   * @opt_param string pageToken nextPageToken value returned from a previous list
   * call, indicating that the subsequent page of results should be returned. The
   * list request must be otherwise identical to the one that resulted in this
   * token.
   * @return ListRubricsResponse
   * @throws \Google\Service\Exception
   */
  public function listCoursesCourseWorkRubrics($courseId, $courseWorkId, $optParams = [])
  {
    $params = ['courseId' => $courseId, 'courseWorkId' => $courseWorkId];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListRubricsResponse::class);
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
   * rubric. (rubrics.patch)
   *
   * @param string $courseId Required. Identifier of the course.
   * @param string $courseWorkId Required. Identifier of the course work.
   * @param string $id Optional. Identifier of the rubric.
   * @param Rubric $postBody
   * @param array $optParams Optional parameters.
   *
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
  public function patch($courseId, $courseWorkId, $id, Rubric $postBody, $optParams = [])
  {
    $params = ['courseId' => $courseId, 'courseWorkId' => $courseWorkId, 'id' => $id, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Rubric::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CoursesCourseWorkRubrics::class, 'Google_Service_Classroom_Resource_CoursesCourseWorkRubrics');
