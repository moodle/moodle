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

namespace Google\Service\Backupdr\Resource;

use Google\Service\Backupdr\AbandonBackupRequest;
use Google\Service\Backupdr\DataSource;
use Google\Service\Backupdr\FetchAccessTokenRequest;
use Google\Service\Backupdr\FetchAccessTokenResponse;
use Google\Service\Backupdr\FinalizeBackupRequest;
use Google\Service\Backupdr\InitiateBackupRequest;
use Google\Service\Backupdr\InitiateBackupResponse;
use Google\Service\Backupdr\ListDataSourcesResponse;
use Google\Service\Backupdr\Operation;
use Google\Service\Backupdr\RemoveDataSourceRequest;
use Google\Service\Backupdr\SetInternalStatusRequest;

/**
 * The "dataSources" collection of methods.
 * Typical usage is:
 *  <code>
 *   $backupdrService = new Google\Service\Backupdr(...);
 *   $dataSources = $backupdrService->projects_locations_backupVaults_dataSources;
 *  </code>
 */
class ProjectsLocationsBackupVaultsDataSources extends \Google\Service\Resource
{
  /**
   * Internal only. Abandons a backup. (dataSources.abandonBackup)
   *
   * @param string $dataSource Required. The resource name of the instance, in the
   * format 'projects/locations/backupVaults/dataSources/'.
   * @param AbandonBackupRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function abandonBackup($dataSource, AbandonBackupRequest $postBody, $optParams = [])
  {
    $params = ['dataSource' => $dataSource, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('abandonBackup', [$params], Operation::class);
  }
  /**
   * Internal only. Fetch access token for a given data source.
   * (dataSources.fetchAccessToken)
   *
   * @param string $name Required. The resource name for the location for which
   * static IPs should be returned. Must be in the format
   * 'projects/locations/backupVaults/dataSources'.
   * @param FetchAccessTokenRequest $postBody
   * @param array $optParams Optional parameters.
   * @return FetchAccessTokenResponse
   * @throws \Google\Service\Exception
   */
  public function fetchAccessToken($name, FetchAccessTokenRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('fetchAccessToken', [$params], FetchAccessTokenResponse::class);
  }
  /**
   * Internal only. Finalize a backup that was started by a call to
   * InitiateBackup. (dataSources.finalizeBackup)
   *
   * @param string $dataSource Required. The resource name of the instance, in the
   * format 'projects/locations/backupVaults/dataSources/'.
   * @param FinalizeBackupRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function finalizeBackup($dataSource, FinalizeBackupRequest $postBody, $optParams = [])
  {
    $params = ['dataSource' => $dataSource, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('finalizeBackup', [$params], Operation::class);
  }
  /**
   * Gets details of a DataSource. (dataSources.get)
   *
   * @param string $name Required. Name of the data source resource name, in the
   * format 'projects/{project_id}/locations/{location}/backupVaults/{resource_nam
   * e}/dataSource/{resource_name}'
   * @param array $optParams Optional parameters.
   * @return DataSource
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], DataSource::class);
  }
  /**
   * Internal only. Initiates a backup. (dataSources.initiateBackup)
   *
   * @param string $dataSource Required. The resource name of the instance, in the
   * format 'projects/locations/backupVaults/dataSources/'.
   * @param InitiateBackupRequest $postBody
   * @param array $optParams Optional parameters.
   * @return InitiateBackupResponse
   * @throws \Google\Service\Exception
   */
  public function initiateBackup($dataSource, InitiateBackupRequest $postBody, $optParams = [])
  {
    $params = ['dataSource' => $dataSource, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('initiateBackup', [$params], InitiateBackupResponse::class);
  }
  /**
   * Lists DataSources in a given project and location.
   * (dataSources.listProjectsLocationsBackupVaultsDataSources)
   *
   * @param string $parent Required. The project and location for which to
   * retrieve data sources information, in the format
   * 'projects/{project_id}/locations/{location}'. In Cloud Backup and DR,
   * locations map to Google Cloud regions, for example **us-central1**. To
   * retrieve data sources for all locations, use "-" for the '{location}' value.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Filtering results.
   * @opt_param string orderBy Optional. Hint for how to order the results.
   * @opt_param int pageSize Optional. Requested page size. Server may return
   * fewer items than requested. If unspecified, server will pick an appropriate
   * default.
   * @opt_param string pageToken Optional. A token identifying a page of results
   * the server should return.
   * @return ListDataSourcesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsBackupVaultsDataSources($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListDataSourcesResponse::class);
  }
  /**
   * Updates the settings of a DataSource. (dataSources.patch)
   *
   * @param string $name Output only. Identifier. Name of the datasource to
   * create. It must have the format`"projects/{project}/locations/{location}/back
   * upVaults/{backupvault}/dataSources/{datasource}"`. `{datasource}` cannot be
   * changed after creation. It must be between 3-63 characters long and must be
   * unique within the backup vault.
   * @param DataSource $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool allowMissing Optional. Enable upsert.
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
   * @opt_param string updateMask Required. Field mask is used to specify the
   * fields to be overwritten in the DataSource resource by the update. The fields
   * specified in the update_mask are relative to the resource, not the full
   * request. A field will be overwritten if it is in the mask. If the user does
   * not provide a mask then the request will fail.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, DataSource $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
  /**
   * Deletes a DataSource. This is a custom method instead of a standard delete
   * method because external clients will not delete DataSources except for
   * BackupDR backup appliances. (dataSources.remove)
   *
   * @param string $name Required. Name of the resource.
   * @param RemoveDataSourceRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function remove($name, RemoveDataSourceRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('remove', [$params], Operation::class);
  }
  /**
   * Sets the internal status of a DataSource. (dataSources.setInternalStatus)
   *
   * @param string $dataSource Required. The resource name of the instance, in the
   * format 'projects/locations/backupVaults/dataSources/'.
   * @param SetInternalStatusRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function setInternalStatus($dataSource, SetInternalStatusRequest $postBody, $optParams = [])
  {
    $params = ['dataSource' => $dataSource, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('setInternalStatus', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsBackupVaultsDataSources::class, 'Google_Service_Backupdr_Resource_ProjectsLocationsBackupVaultsDataSources');
