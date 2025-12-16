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

use Google\Service\OracleDatabase\ExadbVmCluster;
use Google\Service\OracleDatabase\ListExadbVmClustersResponse;
use Google\Service\OracleDatabase\Operation;
use Google\Service\OracleDatabase\RemoveVirtualMachineExadbVmClusterRequest;

/**
 * The "exadbVmClusters" collection of methods.
 * Typical usage is:
 *  <code>
 *   $oracledatabaseService = new Google\Service\OracleDatabase(...);
 *   $exadbVmClusters = $oracledatabaseService->projects_locations_exadbVmClusters;
 *  </code>
 */
class ProjectsLocationsExadbVmClusters extends \Google\Service\Resource
{
  /**
   * Creates a new Exadb (Exascale) VM Cluster resource. (exadbVmClusters.create)
   *
   * @param string $parent Required. The value for parent of the ExadbVmCluster in
   * the following format: projects/{project}/locations/{location}.
   * @param ExadbVmCluster $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string exadbVmClusterId Required. The ID of the ExadbVmCluster to
   * create. This value is restricted to (^[a-z]([a-z0-9-]{0,61}[a-z0-9])?$) and
   * must be a maximum of 63 characters in length. The value must start with a
   * letter and end with a letter or a number.
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server will know to ignore the request if it has already been completed.
   * The server will guarantee that for at least 60 minutes since the first
   * request. For example, consider a situation where you make an initial request
   * and the request times out. If you make the request again with the same
   * request ID, the server can check if original operation with the same request
   * ID was received, and if so, will ignore the second request. This prevents
   * clients from accidentally creating duplicate commitments. The request ID must
   * be a valid UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, ExadbVmCluster $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a single Exadb (Exascale) VM Cluster. (exadbVmClusters.delete)
   *
   * @param string $name Required. The name of the ExadbVmCluster in the following
   * format:
   * projects/{project}/locations/{location}/exadbVmClusters/{exadb_vm_cluster}.
   * @param array $optParams Optional parameters.
   *
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
   * Gets details of a single Exadb (Exascale) VM Cluster. (exadbVmClusters.get)
   *
   * @param string $name Required. The name of the ExadbVmCluster in the following
   * format:
   * projects/{project}/locations/{location}/exadbVmClusters/{exadb_vm_cluster}.
   * @param array $optParams Optional parameters.
   * @return ExadbVmCluster
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], ExadbVmCluster::class);
  }
  /**
   * Lists all the Exadb (Exascale) VM Clusters for the given project and
   * location. (exadbVmClusters.listProjectsLocationsExadbVmClusters)
   *
   * @param string $parent Required. The parent value for ExadbVmClusters in the
   * following format: projects/{project}/locations/{location}.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. An expression for filtering the results of
   * the request.
   * @opt_param string orderBy Optional. An expression for ordering the results of
   * the request.
   * @opt_param int pageSize Optional. The maximum number of items to return. If
   * unspecified, at most 50 ExadbVmClusters will be returned. The maximum value
   * is 1000; values above 1000 will be coerced to 1000.
   * @opt_param string pageToken Optional. A token identifying a page of results
   * the server should return.
   * @return ListExadbVmClustersResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsExadbVmClusters($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListExadbVmClustersResponse::class);
  }
  /**
   * Updates a single Exadb (Exascale) VM Cluster. To add virtual machines to
   * existing exadb vm cluster, only pass the node count. (exadbVmClusters.patch)
   *
   * @param string $name Identifier. The name of the ExadbVmCluster resource in
   * the following format:
   * projects/{project}/locations/{region}/exadbVmClusters/{exadb_vm_cluster}
   * @param ExadbVmCluster $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. An optional ID to identify the request.
   * This value is used to identify duplicate requests. If you make a request with
   * the same request ID and the original request is still in progress or
   * completed, the server ignores the second request. This prevents clients from
   * accidentally creating duplicate commitments. The request ID must be a valid
   * UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @opt_param string updateMask Optional. A mask specifying which fields in th
   * VM Cluster should be updated. A field specified in the mask is overwritten.
   * If a mask isn't provided then all the fields in the VM Cluster are
   * overwritten.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, ExadbVmCluster $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
  /**
   * Removes virtual machines from an existing exadb vm cluster.
   * (exadbVmClusters.removeVirtualMachine)
   *
   * @param string $name Required. The name of the ExadbVmCluster in the following
   * format:
   * projects/{project}/locations/{location}/exadbVmClusters/{exadb_vm_cluster}.
   * @param RemoveVirtualMachineExadbVmClusterRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function removeVirtualMachine($name, RemoveVirtualMachineExadbVmClusterRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('removeVirtualMachine', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsExadbVmClusters::class, 'Google_Service_OracleDatabase_Resource_ProjectsLocationsExadbVmClusters');
