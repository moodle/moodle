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

namespace Google\Service\TPU\Resource;

use Google\Service\TPU\ListQueuedResourcesResponse;
use Google\Service\TPU\Operation;
use Google\Service\TPU\QueuedResource;
use Google\Service\TPU\ResetQueuedResourceRequest;

/**
 * The "queuedResources" collection of methods.
 * Typical usage is:
 *  <code>
 *   $tpuService = new Google\Service\TPU(...);
 *   $queuedResources = $tpuService->projects_locations_queuedResources;
 *  </code>
 */
class ProjectsLocationsQueuedResources extends \Google\Service\Resource
{
  /**
   * Creates a QueuedResource TPU instance. (queuedResources.create)
   *
   * @param string $parent Required. The parent resource name.
   * @param QueuedResource $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string queuedResourceId Optional. The unqualified resource name.
   * Should follow the `^[A-Za-z0-9_.~+%-]+$` regex format.
   * @opt_param string requestId Optional. Idempotent request UUID.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, QueuedResource $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a QueuedResource TPU instance. (queuedResources.delete)
   *
   * @param string $name Required. The resource name.
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool force Optional. If set to true, all running nodes belonging
   * to this queued resource will be deleted first and then the queued resource
   * will be deleted. Otherwise (i.e. force=false), the queued resource will only
   * be deleted if its nodes have already been deleted or the queued resource is
   * in the ACCEPTED, FAILED, or SUSPENDED state.
   * @opt_param string requestId Optional. Idempotent request UUID.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], Operation::class);
  }
  /**
   * Gets details of a queued resource. (queuedResources.get)
   *
   * @param string $name Required. The resource name.
   * @param array $optParams Optional parameters.
   * @return QueuedResource
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], QueuedResource::class);
  }
  /**
   * Lists queued resources.
   * (queuedResources.listProjectsLocationsQueuedResources)
   *
   * @param string $parent Required. The parent resource name.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of items to return.
   * @opt_param string pageToken Optional. The next_page_token value returned from
   * a previous List request, if any.
   * @return ListQueuedResourcesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsQueuedResources($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListQueuedResourcesResponse::class);
  }
  /**
   * Resets a QueuedResource TPU instance (queuedResources.reset)
   *
   * @param string $name Required. The name of the queued resource.
   * @param ResetQueuedResourceRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function reset($name, ResetQueuedResourceRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('reset', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsQueuedResources::class, 'Google_Service_TPU_Resource_ProjectsLocationsQueuedResources');
