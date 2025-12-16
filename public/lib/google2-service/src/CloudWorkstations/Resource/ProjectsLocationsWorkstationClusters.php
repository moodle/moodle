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

namespace Google\Service\CloudWorkstations\Resource;

use Google\Service\CloudWorkstations\ListWorkstationClustersResponse;
use Google\Service\CloudWorkstations\Operation;
use Google\Service\CloudWorkstations\WorkstationCluster;

/**
 * The "workstationClusters" collection of methods.
 * Typical usage is:
 *  <code>
 *   $workstationsService = new Google\Service\CloudWorkstations(...);
 *   $workstationClusters = $workstationsService->projects_locations_workstationClusters;
 *  </code>
 */
class ProjectsLocationsWorkstationClusters extends \Google\Service\Resource
{
  /**
   * Creates a new workstation cluster. (workstationClusters.create)
   *
   * @param string $parent Required. Parent resource name.
   * @param WorkstationCluster $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool validateOnly Optional. If set, validate the request and
   * preview the review, but do not actually apply it.
   * @opt_param string workstationClusterId Required. ID to use for the
   * workstation cluster.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, WorkstationCluster $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes the specified workstation cluster. (workstationClusters.delete)
   *
   * @param string $name Required. Name of the workstation cluster to delete.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string etag Optional. If set, the request will be rejected if the
   * latest version of the workstation cluster on the server does not have this
   * ETag.
   * @opt_param bool force Optional. If set, any workstation configurations and
   * workstations in the workstation cluster are also deleted. Otherwise, the
   * request only works if the workstation cluster has no configurations or
   * workstations.
   * @opt_param bool validateOnly Optional. If set, validate the request and
   * preview the review, but do not apply it.
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
   * Returns the requested workstation cluster. (workstationClusters.get)
   *
   * @param string $name Required. Name of the requested resource.
   * @param array $optParams Optional parameters.
   * @return WorkstationCluster
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], WorkstationCluster::class);
  }
  /**
   * Returns all workstation clusters in the specified location.
   * (workstationClusters.listProjectsLocationsWorkstationClusters)
   *
   * @param string $parent Required. Parent resource name.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Filter the WorkstationClusters to be
   * listed. Possible filters are described in https://google.aip.dev/160.
   * @opt_param int pageSize Optional. Maximum number of items to return.
   * @opt_param string pageToken Optional. next_page_token value returned from a
   * previous List request, if any.
   * @return ListWorkstationClustersResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsWorkstationClusters($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListWorkstationClustersResponse::class);
  }
  /**
   * Updates an existing workstation cluster. (workstationClusters.patch)
   *
   * @param string $name Identifier. Full name of this workstation cluster.
   * @param WorkstationCluster $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool allowMissing Optional. If set, and the workstation cluster is
   * not found, a new workstation cluster will be created. In this situation,
   * update_mask is ignored.
   * @opt_param string updateMask Required. Mask that specifies which fields in
   * the workstation cluster should be updated.
   * @opt_param bool validateOnly Optional. If set, validate the request and
   * preview the review, but do not actually apply it.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, WorkstationCluster $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsWorkstationClusters::class, 'Google_Service_CloudWorkstations_Resource_ProjectsLocationsWorkstationClusters');
