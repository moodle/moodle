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

namespace Google\Service\Dataproc\Resource;

use Google\Service\Dataproc\NodeGroup;
use Google\Service\Dataproc\Operation;
use Google\Service\Dataproc\RepairNodeGroupRequest;
use Google\Service\Dataproc\ResizeNodeGroupRequest;

/**
 * The "nodeGroups" collection of methods.
 * Typical usage is:
 *  <code>
 *   $dataprocService = new Google\Service\Dataproc(...);
 *   $nodeGroups = $dataprocService->projects_regions_clusters_nodeGroups;
 *  </code>
 */
class ProjectsRegionsClustersNodeGroups extends \Google\Service\Resource
{
  /**
   * Creates a node group in a cluster. The returned Operation.metadata is
   * NodeGroupOperationMetadata (https://cloud.google.com/dataproc/docs/reference/
   * rpc/google.cloud.dataproc.v1#nodegroupoperationmetadata). (nodeGroups.create)
   *
   * @param string $parent Required. The parent resource where this node group
   * will be created. Format:
   * projects/{project}/regions/{region}/clusters/{cluster}
   * @param NodeGroup $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string nodeGroupId Optional. An optional node group ID. Generated
   * if not specified.The ID must contain only letters (a-z, A-Z), numbers (0-9),
   * underscores (_), and hyphens (-). Cannot begin or end with underscore or
   * hyphen. Must consist of from 3 to 33 characters.
   * @opt_param string parentOperationId Optional. operation id of the parent
   * operation sending the create request
   * @opt_param string requestId Optional. A unique ID used to identify the
   * request. If the server receives two CreateNodeGroupRequest (https://cloud.goo
   * gle.com/dataproc/docs/reference/rpc/google.cloud.dataproc.v1#google.cloud.dat
   * aproc.v1.CreateNodeGroupRequest) with the same ID, the second request is
   * ignored and the first google.longrunning.Operation created and stored in the
   * backend is returned.Recommendation: Set this value to a UUID
   * (https://en.wikipedia.org/wiki/Universally_unique_identifier).The ID must
   * contain only letters (a-z, A-Z), numbers (0-9), underscores (_), and hyphens
   * (-). The maximum length is 40 characters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, NodeGroup $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Gets the resource representation for a node group in a cluster.
   * (nodeGroups.get)
   *
   * @param string $name Required. The name of the node group to retrieve. Format:
   * projects/{project}/regions/{region}/clusters/{cluster}/nodeGroups/{nodeGroup}
   * @param array $optParams Optional parameters.
   * @return NodeGroup
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], NodeGroup::class);
  }
  /**
   * Repair nodes in a node group. (nodeGroups.repair)
   *
   * @param string $name Required. The name of the node group to resize. Format:
   * projects/{project}/regions/{region}/clusters/{cluster}/nodeGroups/{nodeGroup}
   * @param RepairNodeGroupRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function repair($name, RepairNodeGroupRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('repair', [$params], Operation::class);
  }
  /**
   * Resizes a node group in a cluster. The returned Operation.metadata is
   * NodeGroupOperationMetadata (https://cloud.google.com/dataproc/docs/reference/
   * rpc/google.cloud.dataproc.v1#nodegroupoperationmetadata). (nodeGroups.resize)
   *
   * @param string $name Required. The name of the node group to resize. Format:
   * projects/{project}/regions/{region}/clusters/{cluster}/nodeGroups/{nodeGroup}
   * @param ResizeNodeGroupRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function resize($name, ResizeNodeGroupRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('resize', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsRegionsClustersNodeGroups::class, 'Google_Service_Dataproc_Resource_ProjectsRegionsClustersNodeGroups');
