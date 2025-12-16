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

use Google\Service\TPU\GetGuestAttributesRequest;
use Google\Service\TPU\GetGuestAttributesResponse;
use Google\Service\TPU\ListNodesResponse;
use Google\Service\TPU\Node;
use Google\Service\TPU\Operation;
use Google\Service\TPU\StartNodeRequest;
use Google\Service\TPU\StopNodeRequest;

/**
 * The "nodes" collection of methods.
 * Typical usage is:
 *  <code>
 *   $tpuService = new Google\Service\TPU(...);
 *   $nodes = $tpuService->projects_locations_nodes;
 *  </code>
 */
class ProjectsLocationsNodes extends \Google\Service\Resource
{
  /**
   * Creates a node. (nodes.create)
   *
   * @param string $parent Required. The parent resource name.
   * @param Node $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string nodeId The unqualified resource name.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, Node $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a node. (nodes.delete)
   *
   * @param string $name Required. The resource name.
   * @param array $optParams Optional parameters.
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
   * Gets the details of a node. (nodes.get)
   *
   * @param string $name Required. The resource name.
   * @param array $optParams Optional parameters.
   * @return Node
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Node::class);
  }
  /**
   * Retrieves the guest attributes for the node. (nodes.getGuestAttributes)
   *
   * @param string $name Required. The resource name.
   * @param GetGuestAttributesRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GetGuestAttributesResponse
   * @throws \Google\Service\Exception
   */
  public function getGuestAttributes($name, GetGuestAttributesRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('getGuestAttributes', [$params], GetGuestAttributesResponse::class);
  }
  /**
   * Lists nodes. (nodes.listProjectsLocationsNodes)
   *
   * @param string $parent Required. The parent resource name.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The maximum number of items to return.
   * @opt_param string pageToken The next_page_token value returned from a
   * previous List request, if any.
   * @return ListNodesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsNodes($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListNodesResponse::class);
  }
  /**
   * Updates the configurations of a node. (nodes.patch)
   *
   * @param string $name Output only. Immutable. The name of the TPU.
   * @param Node $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. Mask of fields from Node to update.
   * Supported fields: [description, tags, labels, metadata,
   * network_config.enable_external_ips].
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, Node $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
  /**
   * Starts a node. (nodes.start)
   *
   * @param string $name Required. The resource name.
   * @param StartNodeRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function start($name, StartNodeRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('start', [$params], Operation::class);
  }
  /**
   * Stops a node. This operation is only available with single TPU nodes.
   * (nodes.stop)
   *
   * @param string $name Required. The resource name.
   * @param StopNodeRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function stop($name, StopNodeRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('stop', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsNodes::class, 'Google_Service_TPU_Resource_ProjectsLocationsNodes');
