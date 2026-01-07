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

use Google\Service\Backupdr\Backup;
use Google\Service\Backupdr\FetchBackupsForResourceTypeResponse;
use Google\Service\Backupdr\ListBackupsResponse;
use Google\Service\Backupdr\Operation;
use Google\Service\Backupdr\RestoreBackupRequest;

/**
 * The "backups" collection of methods.
 * Typical usage is:
 *  <code>
 *   $backupdrService = new Google\Service\Backupdr(...);
 *   $backups = $backupdrService->projects_locations_backupVaults_dataSources_backups;
 *  </code>
 */
class ProjectsLocationsBackupVaultsDataSourcesBackups extends \Google\Service\Resource
{
  /**
   * Deletes a Backup. (backups.delete)
   *
   * @param string $name Required. Name of the resource.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server will know to ignore the request if it has already been completed.
   * The server will guarantee that for at least 60 minutes after the first
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
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], Operation::class);
  }
  /**
   * Fetch Backups for a given resource type. (backups.fetchForResourceType)
   *
   * @param string $parent Required. Datasources are the parent resource for the
   * backups. Format: projects/{project}/locations/{location}/backupVaults/{backup
   * VaultId}/dataSources/{datasourceId}
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. A filter expression that filters the
   * results fetched in the response. The expression must specify the field name,
   * a comparison operator, and the value that you want to use for filtering.
   * Supported fields: * name * state * backup_type * create_time * expire_time *
   * enforced_retention_end_time * gcp_backup_plan_info.backup_plan *
   * cloud_sql_instance_backup_properties.instance_tier *
   * cloud_sql_instance_backup_properties.database_installed_version
   * @opt_param string orderBy Optional. A comma-separated list of fields to order
   * by, sorted in ascending order. Use "desc" after a field name for descending.
   * Supported fields: * name
   * @opt_param int pageSize Optional. The maximum number of Backups to return.
   * The service may return fewer than this value. If unspecified, at most 50
   * Backups will be returned. The maximum value is 100; values above 100 will be
   * coerced to 100.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * call of `FetchBackupsForResourceType`. Provide this to retrieve the
   * subsequent page. When paginating, all other parameters provided to
   * `FetchBackupsForResourceType` must match the call that provided the page
   * token.
   * @opt_param string resourceType Required. The type of the GCP resource. Ex:
   * sqladmin.googleapis.com/Instance
   * @opt_param string view Optional. This parameter is used to specify the view
   * of the backup. If not specified, the default view is BASIC.
   * @return FetchBackupsForResourceTypeResponse
   * @throws \Google\Service\Exception
   */
  public function fetchForResourceType($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('fetchForResourceType', [$params], FetchBackupsForResourceTypeResponse::class);
  }
  /**
   * Gets details of a Backup. (backups.get)
   *
   * @param string $name Required. Name of the data source resource name, in the
   * format 'projects/{project_id}/locations/{location}/backupVaults/{backupVault}
   * /dataSources/{datasource}/backups/{backup}'
   * @param array $optParams Optional parameters.
   *
   * @opt_param string view Optional. Reserved for future use to provide a BASIC &
   * FULL view of Backup resource.
   * @return Backup
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Backup::class);
  }
  /**
   * Lists Backups in a given project and location.
   * (backups.listProjectsLocationsBackupVaultsDataSourcesBackups)
   *
   * @param string $parent Required. The project and location for which to
   * retrieve backup information, in the format
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
   * @opt_param string view Optional. Reserved for future use to provide a BASIC &
   * FULL view of Backup resource.
   * @return ListBackupsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsBackupVaultsDataSourcesBackups($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListBackupsResponse::class);
  }
  /**
   * Updates the settings of a Backup. (backups.patch)
   *
   * @param string $name Output only. Identifier. Name of the backup to create. It
   * must have the format`"projects//locations//backupVaults//dataSources/{datasou
   * rce}/backups/{backup}"`. `{backup}` cannot be changed after creation. It must
   * be between 3-63 characters long and must be unique within the datasource.
   * @param Backup $postBody
   * @param array $optParams Optional parameters.
   *
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
   * fields to be overwritten in the Backup resource by the update. The fields
   * specified in the update_mask are relative to the resource, not the full
   * request. A field will be overwritten if it is in the mask. If the user does
   * not provide a mask then the request will fail.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, Backup $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
  /**
   * Restore from a Backup (backups.restore)
   *
   * @param string $name Required. The resource name of the Backup instance, in
   * the format 'projects/locations/backupVaults/dataSources/backups/'.
   * @param RestoreBackupRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function restore($name, RestoreBackupRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('restore', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsBackupVaultsDataSourcesBackups::class, 'Google_Service_Backupdr_Resource_ProjectsLocationsBackupVaultsDataSourcesBackups');
