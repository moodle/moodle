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

use Google\Service\Clouderrorreporting\ListEventsResponse;

/**
 * The "events" collection of methods.
 * Typical usage is:
 *  <code>
 *   $clouderrorreportingService = new Google\Service\Clouderrorreporting(...);
 *   $events = $clouderrorreportingService->projects_locations_events;
 *  </code>
 */
class ProjectsLocationsEvents extends \Google\Service\Resource
{
  /**
   * Lists the specified events. (events.listProjectsLocationsEvents)
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
   *
   * @opt_param string groupId Required. The group for which events shall be
   * returned. The `group_id` is a unique identifier for a particular error group.
   * The identifier is derived from key parts of the error-log content and is
   * treated as Service Data. For information about how Service Data is handled,
   * see [Google Cloud Privacy Notice](https://cloud.google.com/terms/cloud-
   * privacy-notice).
   * @opt_param int pageSize Optional. The maximum number of results to return per
   * response.
   * @opt_param string pageToken Optional. A `next_page_token` provided by a
   * previous response.
   * @opt_param string serviceFilter.resourceType Optional. The exact value to
   * match against [`ServiceContext.resource_type`](/error-
   * reporting/reference/rest/v1beta1/ServiceContext#FIELDS.resource_type).
   * @opt_param string serviceFilter.service Optional. The exact value to match
   * against [`ServiceContext.service`](/error-
   * reporting/reference/rest/v1beta1/ServiceContext#FIELDS.service).
   * @opt_param string serviceFilter.version Optional. The exact value to match
   * against [`ServiceContext.version`](/error-
   * reporting/reference/rest/v1beta1/ServiceContext#FIELDS.version).
   * @opt_param string timeRange.period Restricts the query to the specified time
   * range.
   * @return ListEventsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsEvents($projectName, $optParams = [])
  {
    $params = ['projectName' => $projectName];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListEventsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsEvents::class, 'Google_Service_Clouderrorreporting_Resource_ProjectsLocationsEvents');
