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

namespace Google\Service\CloudAlloyDBAdmin\Resource;

use Google\Service\CloudAlloyDBAdmin\Cluster;
use Google\Service\CloudAlloyDBAdmin\ExportClusterRequest;
use Google\Service\CloudAlloyDBAdmin\ImportClusterRequest;
use Google\Service\CloudAlloyDBAdmin\ListClustersResponse;
use Google\Service\CloudAlloyDBAdmin\Operation;
use Google\Service\CloudAlloyDBAdmin\PromoteClusterRequest;
use Google\Service\CloudAlloyDBAdmin\RestoreClusterRequest;
use Google\Service\CloudAlloyDBAdmin\RestoreFromCloudSQLRequest;
use Google\Service\CloudAlloyDBAdmin\SwitchoverClusterRequest;
use Google\Service\CloudAlloyDBAdmin\UpgradeClusterRequest;

/**
 * The "clusters" collection of methods.
 * Typical usage is:
 *  <code>
 *   $alloydbService = new Google\Service\CloudAlloyDBAdmin(...);
 *   $clusters = $alloydbService->projects_locations_clusters;
 *  </code>
 */
class ProjectsLocationsClusters extends \Google\Service\Resource
{
  /**
   * Creates a new Cluster in a given project and location. (clusters.create)
   *
   * @param string $parent Required. The location of the new cluster. For the
   * required format, see the comment on the Cluster.name field.
   * @param Cluster $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string clusterId Required. ID of the requesting object.
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server ignores the request if it has already been completed. The server
   * guarantees that for at least 60 minutes since the first request. For example,
   * consider a situation where you make an initial request and the request times
   * out. If you make the request again with the same request ID, the server can
   * check if the original operation with the same request ID was received, and if
   * so, ignores the second request. This prevents clients from accidentally
   * creating duplicate commitments. The request ID must be a valid UUID with the
   * exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @opt_param bool validateOnly Optional. If set, performs request validation,
   * for example, permission checks and any other type of validation, but does not
   * actually execute the create request.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, Cluster $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Creates a cluster of type SECONDARY in the given location using the primary
   * cluster as the source. (clusters.createsecondary)
   *
   * @param string $parent Required. The location of the new cluster. For the
   * required format, see the comment on the Cluster.name field.
   * @param Cluster $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string clusterId Required. ID of the requesting object (the
   * secondary cluster).
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server ignores the request if it has already been completed. The server
   * guarantees that for at least 60 minutes since the first request. For example,
   * consider a situation where you make an initial request and the request times
   * out. If you make the request again with the same request ID, the server can
   * check if the original operation with the same request ID was received, and if
   * so, ignores the second request. This prevents clients from accidentally
   * creating duplicate commitments. The request ID must be a valid UUID with the
   * exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @opt_param bool validateOnly Optional. If set, performs request validation,
   * for example, permission checks and any other type of validation, but does not
   * actually execute the create request.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function createsecondary($parent, Cluster $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('createsecondary', [$params], Operation::class);
  }
  /**
   * Deletes a single Cluster. (clusters.delete)
   *
   * @param string $name Required. The name of the resource. For the required
   * format, see the comment on the Cluster.name field.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string etag Optional. The current etag of the Cluster. If an etag
   * is provided and does not match the current etag of the Cluster, deletion will
   * be blocked and an ABORTED error will be returned.
   * @opt_param bool force Optional. Whether to cascade delete child instances for
   * given cluster.
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server ignores the request if it has already been completed. The server
   * guarantees that for at least 60 minutes since the first request. For example,
   * consider a situation where you make an initial request and the request times
   * out. If you make the request again with the same request ID, the server can
   * check if the original operation with the same request ID was received, and if
   * so, ignores the second request. This prevents clients from accidentally
   * creating duplicate commitments. The request ID must be a valid UUID with the
   * exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @opt_param bool validateOnly Optional. If set, performs request validation,
   * for example, permission checks and any other type of validation, but does not
   * actually execute the create request.
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
   * Exports data from the cluster. Imperative only. (clusters.export)
   *
   * @param string $name Required. The resource name of the cluster.
   * @param ExportClusterRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function export($name, ExportClusterRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('export', [$params], Operation::class);
  }
  /**
   * Gets details of a single Cluster. (clusters.get)
   *
   * @param string $name Required. The name of the resource. For the required
   * format, see the comment on the Cluster.name field.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string view Optional. The view of the cluster to return. Returns
   * all default fields if not set.
   * @return Cluster
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Cluster::class);
  }
  /**
   * Imports data to the cluster. Imperative only. (clusters.import)
   *
   * @param string $name Required. The resource name of the cluster.
   * @param ImportClusterRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function import($name, ImportClusterRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('import', [$params], Operation::class);
  }
  /**
   * Lists Clusters in a given project and location.
   * (clusters.listProjectsLocationsClusters)
   *
   * @param string $parent Required. The name of the parent resource. For the
   * required format, see the comment on the Cluster.name field. Additionally, you
   * can perform an aggregated list operation by specifying a value with the
   * following format: * projects/{project}/locations/-
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Filtering results
   * @opt_param string orderBy Optional. Hint for how to order the results
   * @opt_param int pageSize Optional. Requested page size. Server may return
   * fewer items than requested. If unspecified, server will pick an appropriate
   * default.
   * @opt_param string pageToken A token identifying a page of results the server
   * should return.
   * @return ListClustersResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsClusters($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListClustersResponse::class);
  }
  /**
   * Updates the parameters of a single Cluster. (clusters.patch)
   *
   * @param string $name Output only. The name of the cluster resource with the
   * format: * projects/{project}/locations/{region}/clusters/{cluster_id} where
   * the cluster ID segment should satisfy the regex expression `[a-z0-9-]+`. For
   * more details see https://google.aip.dev/122. The prefix of the cluster
   * resource name is the name of the parent resource: *
   * projects/{project}/locations/{region}
   * @param Cluster $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool allowMissing Optional. If set to true, update succeeds even
   * if cluster is not found. In that case, a new cluster is created and
   * `update_mask` is ignored.
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server ignores the request if it has already been completed. The server
   * guarantees that for at least 60 minutes since the first request. For example,
   * consider a situation where you make an initial request and the request times
   * out. If you make the request again with the same request ID, the server can
   * check if the original operation with the same request ID was received, and if
   * so, ignores the second request. This prevents clients from accidentally
   * creating duplicate commitments. The request ID must be a valid UUID with the
   * exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @opt_param string updateMask Optional. Field mask is used to specify the
   * fields to be overwritten in the Cluster resource by the update. The fields
   * specified in the update_mask are relative to the resource, not the full
   * request. A field will be overwritten if it is in the mask. If the user does
   * not provide a mask then all fields will be overwritten.
   * @opt_param bool validateOnly Optional. If set, performs request validation,
   * for example, permission checks and any other type of validation, but does not
   * actually execute the create request.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, Cluster $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
  /**
   * Promotes a SECONDARY cluster. This turns down replication from the PRIMARY
   * cluster and promotes a secondary cluster into its own standalone cluster.
   * Imperative only. (clusters.promote)
   *
   * @param string $name Required. The name of the resource. For the required
   * format, see the comment on the Cluster.name field
   * @param PromoteClusterRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function promote($name, PromoteClusterRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('promote', [$params], Operation::class);
  }
  /**
   * Creates a new Cluster in a given project and location, with a volume restored
   * from the provided source, either a backup ID or a point-in-time and a source
   * cluster. (clusters.restore)
   *
   * @param string $parent Required. The name of the parent resource. For the
   * required format, see the comment on the Cluster.name field.
   * @param RestoreClusterRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function restore($parent, RestoreClusterRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('restore', [$params], Operation::class);
  }
  /**
   * Restores an AlloyDB cluster from a CloudSQL resource.
   * (clusters.restoreFromCloudSQL)
   *
   * @param string $parent Required. The location of the new cluster. For the
   * required format, see the comment on Cluster.name field.
   * @param RestoreFromCloudSQLRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function restoreFromCloudSQL($parent, RestoreFromCloudSQLRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('restoreFromCloudSQL', [$params], Operation::class);
  }
  /**
   * Switches the roles of PRIMARY and SECONDARY clusters without any data loss.
   * This promotes the SECONDARY cluster to PRIMARY and sets up the original
   * PRIMARY cluster to replicate from this newly promoted cluster.
   * (clusters.switchover)
   *
   * @param string $name Required. The name of the resource. For the required
   * format, see the comment on the Cluster.name field
   * @param SwitchoverClusterRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function switchover($name, SwitchoverClusterRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('switchover', [$params], Operation::class);
  }
  /**
   * Upgrades a single Cluster. Imperative only. (clusters.upgrade)
   *
   * @param string $name Required. The resource name of the cluster.
   * @param UpgradeClusterRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function upgrade($name, UpgradeClusterRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('upgrade', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsClusters::class, 'Google_Service_CloudAlloyDBAdmin_Resource_ProjectsLocationsClusters');
