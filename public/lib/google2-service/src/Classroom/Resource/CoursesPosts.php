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

/**
 * The "posts" collection of methods.
 * Typical usage is:
 *  <code>
 *   $classroomService = new Google\Service\Classroom(...);
 *   $posts = $classroomService->courses_posts;
 *  </code>
 */
class CoursesPosts extends \Google\Service\Resource
{
  /**
   * Gets metadata for Classroom add-ons in the context of a specific post. To
   * maintain the integrity of its own data and permissions model, an add-on
   * should call this to validate query parameters and the requesting user's role
   * whenever the add-on is opened in an
   * [iframe](https://developers.google.com/workspace/classroom/add-ons/get-
   * started/iframes/iframes-overview). This method returns the following error
   * codes: * `PERMISSION_DENIED` for access errors. * `INVALID_ARGUMENT` if the
   * request is malformed. * `NOT_FOUND` if one of the identified resources does
   * not exist. (posts.getAddOnContext)
   *
   * @param string $courseId Required. Identifier of the course.
   * @param string $postId Optional. Deprecated, use `item_id` instead.
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
   * @opt_param string itemId Identifier of the `Announcement`, `CourseWork`, or
   * `CourseWorkMaterial` under which the attachment is attached. This field is
   * required, but is not marked as such while we are migrating from post_id.
   * @return AddOnContext
   * @throws \Google\Service\Exception
   */
  public function getAddOnContext($courseId, $postId, $optParams = [])
  {
    $params = ['courseId' => $courseId, 'postId' => $postId];
    $params = array_merge($params, $optParams);
    return $this->call('getAddOnContext', [$params], AddOnContext::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CoursesPosts::class, 'Google_Service_Classroom_Resource_CoursesPosts');
