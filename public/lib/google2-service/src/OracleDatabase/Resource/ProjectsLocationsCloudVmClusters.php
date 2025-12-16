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

namespace Google\Service\OracleDatabase\Resource;

use Google\Service\OracleDatabase\CloudVmCluster;
use Google\Service\OracleDatabase\ListCloudVmClustersResponse;
use Google\Service\OracleDatabase\Operation;

/**
 * The "cloudVmClusters" collection of methods.
 * Typical usage is:
 *  <code>
 *   $oracledatabaseService = new Google\Service\OracleDatabase(...);
 *   $cloudVmClusters = $oracledatabaseService->projects_locations_cloudVmClusters;
 *  </code>
 */
class ProjectsLocationsCloudVmClusters extends \Google\Service\Resource
{
  /**
   * Creates a new VM Cluster in a given project and location.
   * (cloudVmClusters.create)
   *
   * @param string $parent Required. The name of the parent in the following
   * format: projects/{project}/locations/{location}.
   * @param CloudVmCluster $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string cloudVmClusterId Required. The ID of the VM Cluster to
   * create. This value is restricted to (^[a-z]([a-z0-9-]{0,61}[a-z0-9])?$) and
   * must be a maximum of 63 characters in length. The value must start with a
   * letter and end with a letter or a number.
   * @opt_param string requestId Optional. An optional ID to identify the request.
   * This value is used to identify duplicate requests. If you make a request with
   * the same request ID and the original request is still in progress or
   * completed, the server ignores the second request. This prevents clients from
   * accidentally creating duplicate commitments. The request ID must be a valid
   * UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, CloudVmCluster $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a single VM Cluster. (cloudVmClusters.delete)
   *
   * @param string $name Required. The name of the Cloud VM Cluster in the
   * following format:
   * projects/{project}/locations/{location}/cloudVmClusters/{cloud_vm_cluster}.
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool force Optional. If set to true, all child resources for the
   * VM Cluster will be deleted. A VM Cluster can only be deleted once all its
   * child resources have been deleted.
   * @opt_param string requestId Optional. An optional ID to identify the request.
   * This value is used to identify duplicate requests. If you make a request with
   * the same request ID and the original request is still in progress or
   * completed, the server ignores the second request. This prevents clients from
   * accidentally creating duplicate commitments. The request ID must be a valid
   * UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
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
   * Gets details of a single VM Cluster. (cloudVmClusters.get)
   *
   * @param string $name Required. The name of the Cloud VM Cluster in the
   * following format:
   * projects/{project}/locations/{location}/cloudVmClusters/{cloud_vm_cluster}.
   * @param array $optParams Optional parameters.
   * @return CloudVmCluster
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], CloudVmCluster::class);
  }
  /**
   * Lists the VM Clusters in a given project and location.
   * (cloudVmClusters.listProjectsLocationsCloudVmClusters)
   *
   * @param string $parent Required. The name of the parent in the following
   * format: projects/{project}/locations/{location}.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. An expression for filtering the results of
   * the request.
   * @opt_param int pageSize Optional. The number of VM clusters to return. If
   * unspecified, at most 50 VM clusters will be returned. The maximum value is
   * 1,000.
   * @opt_param string pageToken Optional. A token identifying the page of results
   * the server returns.
   * @return ListCloudVmClustersResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsCloudVmClusters($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListCloudVmClustersResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsCloudVmClusters::class, 'Google_Service_OracleDatabase_Resource_ProjectsLocationsCloudVmClusters');
