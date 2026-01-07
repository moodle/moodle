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

use Google\Service\VMwareEngine\ListNodesResponse;
use Google\Service\VMwareEngine\Node;

/**
 * The "nodes" collection of methods.
 * Typical usage is:
 *  <code>
 *   $vmwareengineService = new Google\Service\VMwareEngine(...);
 *   $nodes = $vmwareengineService->projects_locations_privateClouds_clusters_nodes;
 *  </code>
 */
class ProjectsLocationsPrivateCloudsClustersNodes extends \Google\Service\Resource
{
  /**
   * Gets details of a single node. (nodes.get)
   *
   * @param string $name Required. The resource name of the node to retrieve. For
   * example: `projects/{project}/locations/{location}/privateClouds/{private_clou
   * d}/clusters/{cluster}/nodes/{node}`
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
   * Lists nodes in a given cluster.
   * (nodes.listProjectsLocationsPrivateCloudsClustersNodes)
   *
   * @param string $parent Required. The resource name of the cluster to be
   * queried for nodes. Resource names are schemeless URIs that follow the
   * conventions in https://cloud.google.com/apis/design/resource_names. For
   * example: `projects/my-project/locations/us-central1-a/privateClouds/my-
   * cloud/clusters/my-cluster`
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The maximum number of nodes to return in one page.
   * The service may return fewer than this value. The maximum value is coerced to
   * 1000. The default value of this field is 500.
   * @opt_param string pageToken A page token, received from a previous
   * `ListNodes` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters provided to `ListNodes` must match the call
   * that provided the page token.
   * @return ListNodesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsPrivateCloudsClustersNodes($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListNodesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsPrivateCloudsClustersNodes::class, 'Google_Service_VMwareEngine_Resource_ProjectsLocationsPrivateCloudsClustersNodes');
