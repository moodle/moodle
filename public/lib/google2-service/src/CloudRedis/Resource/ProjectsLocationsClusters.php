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

namespace Google\Service\CloudRedis\Resource;

use Google\Service\CloudRedis\BackupClusterRequest;
use Google\Service\CloudRedis\CertificateAuthority;
use Google\Service\CloudRedis\Cluster;
use Google\Service\CloudRedis\ListClustersResponse;
use Google\Service\CloudRedis\Operation;
use Google\Service\CloudRedis\RescheduleClusterMaintenanceRequest;

/**
 * The "clusters" collection of methods.
 * Typical usage is:
 *  <code>
 *   $redisService = new Google\Service\CloudRedis(...);
 *   $clusters = $redisService->projects_locations_clusters;
 *  </code>
 */
class ProjectsLocationsClusters extends \Google\Service\Resource
{
  /**
   * Backup Redis Cluster. If this is the first time a backup is being created, a
   * backup collection will be created at the backend, and this backup belongs to
   * this collection. Both collection and backup will have a resource name. Backup
   * will be executed for each shard. A replica (primary if nonHA) will be
   * selected to perform the execution. Backup call will be rejected if there is
   * an ongoing backup or update operation. Be aware that during preview, if the
   * cluster's internal software version is too old, critical update will be
   * performed before actual backup. Once the internal software version is updated
   * to the minimum version required by the backup feature, subsequent backups
   * will not require critical update. After preview, there will be no critical
   * update needed for backup. (clusters.backup)
   *
   * @param string $name Required. Redis cluster resource name using the form:
   * `projects/{project_id}/locations/{location_id}/clusters/{cluster_id}` where
   * `location_id` refers to a Google Cloud region.
   * @param BackupClusterRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function backup($name, BackupClusterRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('backup', [$params], Operation::class);
  }
  /**
   * Creates a Redis cluster based on the specified properties. The creation is
   * executed asynchronously and callers may check the returned operation to track
   * its progress. Once the operation is completed the Redis cluster will be fully
   * functional. The completed longrunning.Operation will contain the new cluster
   * object in the response field. The returned operation is automatically deleted
   * after a few hours, so there is no need to call DeleteOperation.
   * (clusters.create)
   *
   * @param string $parent Required. The resource name of the cluster location
   * using the form: `projects/{project_id}/locations/{location_id}` where
   * `location_id` refers to a Google Cloud region.
   * @param Cluster $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string clusterId Required. The logical name of the Redis cluster
   * in the customer project with the following restrictions: * Must contain only
   * lowercase letters, numbers, and hyphens. * Must start with a letter. * Must
   * be between 1-63 characters. * Must end with a number or a letter. * Must be
   * unique within the customer project / location
   * @opt_param string requestId Optional. Idempotent request UUID.
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
   * Deletes a specific Redis cluster. Cluster stops serving and data is deleted.
   * (clusters.delete)
   *
   * @param string $name Required. Redis cluster resource name using the form:
   * `projects/{project_id}/locations/{location_id}/clusters/{cluster_id}` where
   * `location_id` refers to a Google Cloud region.
   * @param array $optParams Optional parameters.
   *
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
   * Gets the details of a specific Redis cluster. (clusters.get)
   *
   * @param string $name Required. Redis cluster resource name using the form:
   * `projects/{project_id}/locations/{location_id}/clusters/{cluster_id}` where
   * `location_id` refers to a Google Cloud region.
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
   * Gets the details of certificate authority information for Redis cluster.
   * (clusters.getCertificateAuthority)
   *
   * @param string $name Required. Redis cluster certificate authority resource
   * name using the form: `projects/{project_id}/locations/{location_id}/clusters/
   * {cluster_id}/certificateAuthority` where `location_id` refers to a Google
   * Cloud region.
   * @param array $optParams Optional parameters.
   * @return CertificateAuthority
   * @throws \Google\Service\Exception
   */
  public function getCertificateAuthority($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('getCertificateAuthority', [$params], CertificateAuthority::class);
  }
  /**
   * Lists all Redis clusters owned by a project in either the specified location
   * (region) or all locations. The location should have the following format: *
   * `projects/{project_id}/locations/{location_id}` If `location_id` is specified
   * as `-` (wildcard), then all regions available to the project are queried, and
   * the results are aggregated. (clusters.listProjectsLocationsClusters)
   *
   * @param string $parent Required. The resource name of the cluster location
   * using the form: `projects/{project_id}/locations/{location_id}` where
   * `location_id` refers to a Google Cloud region.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The maximum number of items to return. If not
   * specified, a default value of 1000 will be used by the service. Regardless of
   * the page_size value, the response may include a partial list and a caller
   * should only rely on response's `next_page_token` to determine if there are
   * more clusters left to be queried.
   * @opt_param string pageToken The `next_page_token` value returned from a
   * previous ListClusters request, if any.
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
   * Updates the metadata and configuration of a specific Redis cluster. Completed
   * longrunning.Operation will contain the new cluster object in the response
   * field. The returned operation is automatically deleted after a few hours, so
   * there is no need to call DeleteOperation. (clusters.patch)
   *
   * @param string $name Required. Identifier. Unique name of the resource in this
   * scope including project and location using the form:
   * `projects/{project_id}/locations/{location_id}/clusters/{cluster_id}`
   * @param Cluster $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. Idempotent request UUID.
   * @opt_param string updateMask Required. Mask of fields to update. At least one
   * path must be supplied in this field. The elements of the repeated paths field
   * may only include these fields from Cluster: * `size_gb` * `replica_count` *
   * `cluster_endpoints`
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
   * Reschedules upcoming maintenance event.
   * (clusters.rescheduleClusterMaintenance)
   *
   * @param string $name Required. Redis Cluster instance resource name using the
   * form: `projects/{project_id}/locations/{location_id}/clusters/{cluster_id}`
   * where `location_id` refers to a Google Cloud region.
   * @param RescheduleClusterMaintenanceRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function rescheduleClusterMaintenance($name, RescheduleClusterMaintenanceRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('rescheduleClusterMaintenance', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsClusters::class, 'Google_Service_CloudRedis_Resource_ProjectsLocationsClusters');
