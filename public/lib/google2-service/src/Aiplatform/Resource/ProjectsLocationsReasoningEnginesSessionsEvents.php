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

namespace Google\Service\Aiplatform\Resource;

use Google\Service\Aiplatform\GoogleCloudAiplatformV1ListEventsResponse;

/**
 * The "events" collection of methods.
 * Typical usage is:
 *  <code>
 *   $aiplatformService = new Google\Service\Aiplatform(...);
 *   $events = $aiplatformService->projects_locations_reasoningEngines_sessions_events;
 *  </code>
 */
class ProjectsLocationsReasoningEnginesSessionsEvents extends \Google\Service\Resource
{
  /**
   * Lists Events in a given session.
   * (events.listProjectsLocationsReasoningEnginesSessionsEvents)
   *
   * @param string $parent Required. The resource name of the session to list
   * events from. Format: `projects/{project}/locations/{location}/reasoningEngine
   * s/{reasoning_engine}/sessions/{session}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. The standard list filter. Supported
   * fields: * `timestamp` range (i.e. `timestamp>="2025-01-31T11:30:00-04:00"`
   * where the timestamp is in RFC 3339 format) More detail in
   * [AIP-160](https://google.aip.dev/160).
   * @opt_param string orderBy Optional. A comma-separated list of fields to order
   * by, sorted in ascending order. Use "desc" after a field name for descending.
   * Supported fields: * `timestamp` Example: `timestamp desc`.
   * @opt_param int pageSize Optional. The maximum number of events to return. The
   * service may return fewer than this value. If unspecified, at most 100 events
   * will be returned. These events are ordered by timestamp in ascending order.
   * @opt_param string pageToken Optional. The next_page_token value returned from
   * a previous list SessionService.ListEvents call.
   * @return GoogleCloudAiplatformV1ListEventsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsReasoningEnginesSessionsEvents($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudAiplatformV1ListEventsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsReasoningEnginesSessionsEvents::class, 'Google_Service_Aiplatform_Resource_ProjectsLocationsReasoningEnginesSessionsEvents');
