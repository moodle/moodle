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

namespace Google\Service\CloudRun\Resource;

use Google\Service\CloudRun\GoogleCloudRunV2ExportStatusResponse;
use Google\Service\CloudRun\GoogleCloudRunV2ListRevisionsResponse;
use Google\Service\CloudRun\GoogleCloudRunV2Revision;
use Google\Service\CloudRun\GoogleLongrunningOperation;

/**
 * The "revisions" collection of methods.
 * Typical usage is:
 *  <code>
 *   $runService = new Google\Service\CloudRun(...);
 *   $revisions = $runService->projects_locations_services_revisions;
 *  </code>
 */
class ProjectsLocationsServicesRevisions extends \Google\Service\Resource
{
  /**
   * Deletes a Revision. (revisions.delete)
   *
   * @param string $name Required. The name of the Revision to delete. Format: pro
   * jects/{project}/locations/{location}/services/{service}/revisions/{revision}
   * @param array $optParams Optional parameters.
   *
   * @opt_param string etag A system-generated fingerprint for this version of the
   * resource. This may be used to detect modification conflict during updates.
   * @opt_param bool validateOnly Indicates that the request should be validated
   * without actually deleting any resources.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Read the status of an image export operation. (revisions.exportStatus)
   *
   * @param string $name Required. The name of the resource of which image export
   * operation status has to be fetched. Format: `projects/{project_id_or_number}/
   * locations/{location}/services/{service}/revisions/{revision}` for Revision `p
   * rojects/{project_id_or_number}/locations/{location}/jobs/{job}/executions/{ex
   * ecution}` for Execution
   * @param string $operationId Required. The operation id returned from
   * ExportImage.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudRunV2ExportStatusResponse
   * @throws \Google\Service\Exception
   */
  public function exportStatus($name, $operationId, $optParams = [])
  {
    $params = ['name' => $name, 'operationId' => $operationId];
    $params = array_merge($params, $optParams);
    return $this->call('exportStatus', [$params], GoogleCloudRunV2ExportStatusResponse::class);
  }
  /**
   * Gets information about a Revision. (revisions.get)
   *
   * @param string $name Required. The full name of the Revision. Format: projects
   * /{project}/locations/{location}/services/{service}/revisions/{revision}
   * @param array $optParams Optional parameters.
   * @return GoogleCloudRunV2Revision
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudRunV2Revision::class);
  }
  /**
   * Lists Revisions from a given Service, or from a given location. Results are
   * sorted by creation time, descending.
   * (revisions.listProjectsLocationsServicesRevisions)
   *
   * @param string $parent Required. The Service from which the Revisions should
   * be listed. To list all Revisions across Services, use "-" instead of Service
   * name. Format: projects/{project}/locations/{location}/services/{service}
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Maximum number of revisions to return in this call.
   * @opt_param string pageToken A page token received from a previous call to
   * ListRevisions. All other parameters must match.
   * @opt_param bool showDeleted If true, returns deleted (but unexpired)
   * resources along with active ones.
   * @return GoogleCloudRunV2ListRevisionsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsServicesRevisions($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudRunV2ListRevisionsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsServicesRevisions::class, 'Google_Service_CloudRun_Resource_ProjectsLocationsServicesRevisions');
