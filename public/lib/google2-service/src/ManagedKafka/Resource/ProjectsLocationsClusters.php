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

namespace Google\Service\ManagedKafka\Resource;

use Google\Service\ManagedKafka\Cluster;
use Google\Service\ManagedKafka\ListClustersResponse;
use Google\Service\ManagedKafka\Operation;

/**
 * The "clusters" collection of methods.
 * Typical usage is:
 *  <code>
 *   $managedkafkaService = new Google\Service\ManagedKafka(...);
 *   $clusters = $managedkafkaService->projects_locations_clusters;
 *  </code>
 */
class ProjectsLocationsClusters extends \Google\Service\Resource
{
  /**
   * Creates a new cluster in a given project and location. (clusters.create)
   *
   * @param string $parent Required. The parent region in which to create the
   * cluster. Structured like `projects/{project}/locations/{location}`.
   * @param Cluster $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string clusterId Required. The ID to use for the cluster, which
   * will become the final component of the cluster's name. The ID must be 1-63
   * characters long, and match the regular expression
   * `[a-z]([-a-z0-9]*[a-z0-9])?` to comply with RFC 1035. This value is
   * structured like: `my-cluster-id`.
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID to avoid duplication of requests. If a
   * request times out or fails, retrying with the same ID allows the server to
   * recognize the previous attempt. For at least 60 minutes, the server ignores
   * duplicate requests bearing the same ID. For example, consider a situation
   * where you make an initial request and the request times out. If you make the
   * request again with the same request ID within 60 minutes of the last request,
   * the server checks if an original operation with the same request ID was
   * received. If so, the server ignores the second request. The request ID must
   * be a valid UUID. A zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
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
   * Deletes a single cluster. (clusters.delete)
   *
   * @param string $name Required. The name of the cluster to delete.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID to avoid duplication of requests. If a
   * request times out or fails, retrying with the same ID allows the server to
   * recognize the previous attempt. For at least 60 minutes, the server ignores
   * duplicate requests bearing the same ID. For example, consider a situation
   * where you make an initial request and the request times out. If you make the
   * request again with the same request ID within 60 minutes of the last request,
   * the server checks if an original operation with the same request ID was
   * received. If so, the server ignores the second request. The request ID must
   * be a valid UUID. A zero UUID is not supported
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
   * Returns the properties of a single cluster. (clusters.get)
   *
   * @param string $name Required. The name of the cluster whose configuration to
   * return.
   * @param array $optParams Optional parameters.
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
   * Lists the clusters in a given project and location.
   * (clusters.listProjectsLocationsClusters)
   *
   * @param string $parent Required. The parent location whose clusters are to be
   * listed. Structured like `projects/{project}/locations/{location}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Filter expression for the result.
   * @opt_param string orderBy Optional. Order by fields for the result.
   * @opt_param int pageSize Optional. The maximum number of clusters to return.
   * The service may return fewer than this value. If unspecified, server will
   * pick an appropriate default.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListClusters` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters provided to `ListClusters` must match the
   * call that provided the page token.
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
   * Updates the properties of a single cluster. (clusters.patch)
   *
   * @param string $name Identifier. The name of the cluster. Structured like:
   * projects/{project_number}/locations/{location}/clusters/{cluster_id}
   * @param Cluster $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID to avoid duplication of requests. If a
   * request times out or fails, retrying with the same ID allows the server to
   * recognize the previous attempt. For at least 60 minutes, the server ignores
   * duplicate requests bearing the same ID. For example, consider a situation
   * where you make an initial request and the request times out. If you make the
   * request again with the same request ID within 60 minutes of the last request,
   * the server checks if an original operation with the same request ID was
   * received. If so, the server ignores the second request. The request ID must
   * be a valid UUID. A zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @opt_param string updateMask Required. Field mask is used to specify the
   * fields to be overwritten in the cluster resource by the update. The fields
   * specified in the update_mask are relative to the resource, not the full
   * request. A field will be overwritten if it is in the mask. The mask is
   * required and a value of * will update all fields.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, Cluster $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsClusters::class, 'Google_Service_ManagedKafka_Resource_ProjectsLocationsClusters');
