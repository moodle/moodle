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

use Google\Service\OracleDatabase\ListDbNodesResponse;

/**
 * The "dbNodes" collection of methods.
 * Typical usage is:
 *  <code>
 *   $oracledatabaseService = new Google\Service\OracleDatabase(...);
 *   $dbNodes = $oracledatabaseService->projects_locations_cloudVmClusters_dbNodes;
 *  </code>
 */
class ProjectsLocationsCloudVmClustersDbNodes extends \Google\Service\Resource
{
  /**
   * Lists the database nodes of a VM Cluster.
   * (dbNodes.listProjectsLocationsCloudVmClustersDbNodes)
   *
   * @param string $parent Required. The parent value for database node in the
   * following format:
   * projects/{project}/locations/{location}/cloudVmClusters/{cloudVmCluster}. .
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of items to return. If
   * unspecified, at most 50 db nodes will be returned. The maximum value is 1000;
   * values above 1000 will be coerced to 1000.
   * @opt_param string pageToken Optional. A token identifying a page of results
   * the node should return.
   * @return ListDbNodesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsCloudVmClustersDbNodes($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListDbNodesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsCloudVmClustersDbNodes::class, 'Google_Service_OracleDatabase_Resource_ProjectsLocationsCloudVmClustersDbNodes');
