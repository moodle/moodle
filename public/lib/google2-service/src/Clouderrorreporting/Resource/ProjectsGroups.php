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

namespace Google\Service\Clouderrorreporting\Resource;

use Google\Service\Clouderrorreporting\ErrorGroup;

/**
 * The "groups" collection of methods.
 * Typical usage is:
 *  <code>
 *   $clouderrorreportingService = new Google\Service\Clouderrorreporting(...);
 *   $groups = $clouderrorreportingService->projects_groups;
 *  </code>
 */
class ProjectsGroups extends \Google\Service\Resource
{
  /**
   * Get the specified group. (groups.get)
   *
   * @param string $groupName Required. The group resource name. Written as either
   * `projects/{projectID}/groups/{group_id}` or
   * `projects/{projectID}/locations/{location}/groups/{group_id}`. Call
   * groupStats.list to return a list of groups belonging to this project.
   * Examples: `projects/my-project-123/groups/my-group`, `projects/my-
   * project-123/locations/global/groups/my-group` In the group resource name, the
   * `group_id` is a unique identifier for a particular error group. The
   * identifier is derived from key parts of the error-log content and is treated
   * as Service Data. For information about how Service Data is handled, see
   * [Google Cloud Privacy Notice](https://cloud.google.com/terms/cloud-privacy-
   * notice). For a list of supported locations, see [Supported
   * Regions](https://cloud.google.com/logging/docs/region-support). `global` is
   * the default when unspecified.
   * @param array $optParams Optional parameters.
   * @return ErrorGroup
   * @throws \Google\Service\Exception
   */
  public function get($groupName, $optParams = [])
  {
    $params = ['groupName' => $groupName];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], ErrorGroup::class);
  }
  /**
   * Replace the data for the specified group. Fails if the group does not exist.
   * (groups.update)
   *
   * @param string $name The group resource name. Written as
   * `projects/{projectID}/groups/{group_id}` or
   * `projects/{projectID}/locations/{location}/groups/{group_id}` Examples:
   * `projects/my-project-123/groups/my-group`, `projects/my-
   * project-123/locations/us-central1/groups/my-group` In the group resource
   * name, the `group_id` is a unique identifier for a particular error group. The
   * identifier is derived from key parts of the error-log content and is treated
   * as Service Data. For information about how Service Data is handled, see
   * [Google Cloud Privacy Notice](https://cloud.google.com/terms/cloud-privacy-
   * notice). For a list of supported locations, see [Supported
   * Regions](https://cloud.google.com/logging/docs/region-support). `global` is
   * the default when unspecified.
   * @param ErrorGroup $postBody
   * @param array $optParams Optional parameters.
   * @return ErrorGroup
   * @throws \Google\Service\Exception
   */
  public function update($name, ErrorGroup $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('update', [$params], ErrorGroup::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsGroups::class, 'Google_Service_Clouderrorreporting_Resource_ProjectsGroups');
