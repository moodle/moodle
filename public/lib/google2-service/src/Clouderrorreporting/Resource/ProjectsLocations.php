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

use Google\Service\Clouderrorreporting\DeleteEventsResponse;

/**
 * The "locations" collection of methods.
 * Typical usage is:
 *  <code>
 *   $clouderrorreportingService = new Google\Service\Clouderrorreporting(...);
 *   $locations = $clouderrorreportingService->projects_locations;
 *  </code>
 */
class ProjectsLocations extends \Google\Service\Resource
{
  /**
   * Deletes all error events of a given project. (locations.deleteEvents)
   *
   * @param string $projectName Required. The resource name of the Google Cloud
   * Platform project. Written as `projects/{projectID}` or
   * `projects/{projectID}/locations/{location}`, where `{projectID}` is the
   * [Google Cloud Platform project
   * ID](https://support.google.com/cloud/answer/6158840) and `{location}` is a
   * Cloud region. Examples: `projects/my-project-123`, `projects/my-
   * project-123/locations/global`. For a list of supported locations, see
   * [Supported Regions](https://cloud.google.com/logging/docs/region-support).
   * `global` is the default when unspecified.
   * @param array $optParams Optional parameters.
   * @return DeleteEventsResponse
   * @throws \Google\Service\Exception
   */
  public function deleteEvents($projectName, $optParams = [])
  {
    $params = ['projectName' => $projectName];
    $params = array_merge($params, $optParams);
    return $this->call('deleteEvents', [$params], DeleteEventsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocations::class, 'Google_Service_Clouderrorreporting_Resource_ProjectsLocations');
