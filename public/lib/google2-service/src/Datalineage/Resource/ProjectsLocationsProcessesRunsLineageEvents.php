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

namespace Google\Service\Datalineage\Resource;

use Google\Service\Datalineage\GoogleCloudDatacatalogLineageV1LineageEvent;
use Google\Service\Datalineage\GoogleCloudDatacatalogLineageV1ListLineageEventsResponse;
use Google\Service\Datalineage\GoogleProtobufEmpty;

/**
 * The "lineageEvents" collection of methods.
 * Typical usage is:
 *  <code>
 *   $datalineageService = new Google\Service\Datalineage(...);
 *   $lineageEvents = $datalineageService->projects_locations_processes_runs_lineageEvents;
 *  </code>
 */
class ProjectsLocationsProcessesRunsLineageEvents extends \Google\Service\Resource
{
  /**
   * Creates a new lineage event. (lineageEvents.create)
   *
   * @param string $parent Required. The name of the run that should own the
   * lineage event.
   * @param GoogleCloudDatacatalogLineageV1LineageEvent $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. A unique identifier for this request.
   * Restricted to 36 ASCII characters. A random UUID is recommended. This request
   * is idempotent only if a `request_id` is provided.
   * @return GoogleCloudDatacatalogLineageV1LineageEvent
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudDatacatalogLineageV1LineageEvent $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudDatacatalogLineageV1LineageEvent::class);
  }
  /**
   * Deletes the lineage event with the specified name. (lineageEvents.delete)
   *
   * @param string $name Required. The name of the lineage event to delete.
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool allowMissing If set to true and the lineage event is not
   * found, the request succeeds but the server doesn't perform any actions.
   * @return GoogleProtobufEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], GoogleProtobufEmpty::class);
  }
  /**
   * Gets details of a specified lineage event. (lineageEvents.get)
   *
   * @param string $name Required. The name of the lineage event to get.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDatacatalogLineageV1LineageEvent
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudDatacatalogLineageV1LineageEvent::class);
  }
  /**
   * Lists lineage events in the given project and location. The list order is not
   * defined. (lineageEvents.listProjectsLocationsProcessesRunsLineageEvents)
   *
   * @param string $parent Required. The name of the run that owns the collection
   * of lineage events to get.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The maximum number of lineage events to return. The
   * service may return fewer events than this value. If unspecified, at most 50
   * events are returned. The maximum value is 100; values greater than 100 are
   * cut to 100.
   * @opt_param string pageToken The page token received from a previous
   * `ListLineageEvents` call. Specify it to get the next page. When paginating,
   * all other parameters specified in this call must match the parameters of the
   * call that provided the page token.
   * @return GoogleCloudDatacatalogLineageV1ListLineageEventsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsProcessesRunsLineageEvents($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudDatacatalogLineageV1ListLineageEventsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsProcessesRunsLineageEvents::class, 'Google_Service_Datalineage_Resource_ProjectsLocationsProcessesRunsLineageEvents');
