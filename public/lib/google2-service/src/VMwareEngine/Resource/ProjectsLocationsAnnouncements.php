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

namespace Google\Service\VMwareEngine\Resource;

use Google\Service\VMwareEngine\Announcement;
use Google\Service\VMwareEngine\ListAnnouncementsResponse;

/**
 * The "announcements" collection of methods.
 * Typical usage is:
 *  <code>
 *   $vmwareengineService = new Google\Service\VMwareEngine(...);
 *   $announcements = $vmwareengineService->projects_locations_announcements;
 *  </code>
 */
class ProjectsLocationsAnnouncements extends \Google\Service\Resource
{
  /**
   * Retrieves a `Announcement` by its resource name. (announcements.get)
   *
   * @param string $name Required. The resource name of the announcement to
   * retrieve. Resource names are schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-west1-a/announcements/announcement-uuid`
   * @param array $optParams Optional parameters.
   * @return Announcement
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Announcement::class);
  }
  /**
   * Lists `Announcements` for a given region and project
   * (announcements.listProjectsLocationsAnnouncements)
   *
   * @param string $parent Required. The resource name of the location to be
   * queried for announcements. Resource names are schemeless URIs that follow the
   * conventions in https://cloud.google.com/apis/design/resource_names. For
   * example: `projects/my-project/locations/us-west1-a`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter A filter expression that matches resources returned
   * in the response. The expression must specify the field name, a comparison
   * operator, and the value that you want to use for filtering. The value must be
   * a string, a number, or a boolean. The comparison operator must be `=`, `!=`,
   * `>`, or `<`. For example, if you are filtering a list of announcement runs,
   * you can exclude the ones named `example-announcement` by specifying `name !=
   * "example-announcement"`. You can also filter nested fields. To filter on
   * multiple expressions, provide each separate expression within parentheses.
   * For example: ``` (name = "example-announcement") (createTime >
   * "2021-04-12T08:15:10.40Z") ``` By default, each expression is an `AND`
   * expression. However, you can include `AND` and `OR` expressions explicitly.
   * For example: ``` (name = "announcement-1") AND (createTime >
   * "2021-04-12T08:15:10.40Z") OR (name = "announcement-2") ```
   * @opt_param string orderBy Sorts list results by a certain order. By default,
   * returned results are ordered by `name` in ascending order. You can also sort
   * results in descending order based on the `name` value using `orderBy="name
   * desc"`. Currently, only ordering by `name` is supported.
   * @opt_param int pageSize The maximum number of announcements to return in one
   * page. The service may return fewer than this value. The maximum value is
   * coerced to 1000. The default value of this field is 500.
   * @opt_param string pageToken A page token, received from a previous
   * `ListAnnouncements` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters provided to `ListAnnouncements` must match
   * the call that provided the page token.
   * @return ListAnnouncementsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsAnnouncements($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListAnnouncementsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsAnnouncements::class, 'Google_Service_VMwareEngine_Resource_ProjectsLocationsAnnouncements');
