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

use Google\Service\Backupdr\BackupVault;
use Google\Service\Backupdr\FetchUsableBackupVaultsResponse;
use Google\Service\Backupdr\ListBackupVaultsResponse;
use Google\Service\Backupdr\Operation;
use Google\Service\Backupdr\TestIamPermissionsRequest;
use Google\Service\Backupdr\TestIamPermissionsResponse;

/**
 * The "backupVaults" collection of methods.
 * Typical usage is:
 *  <code>
 *   $backupdrService = new Google\Service\Backupdr(...);
 *   $backupVaults = $backupdrService->projects_locations_backupVaults;
 *  </code>
 */
class ProjectsLocationsBackupVaults extends \Google\Service\Resource
{
  /**
   * Creates a new BackupVault in a given project and location.
   * (backupVaults.create)
   *
   * @param string $parent Required. Value for parent.
   * @param BackupVault $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string backupVaultId Required. ID of the requesting object If
   * auto-generating ID server-side, remove this field and backup_vault_id from
   * the method_signature of Create RPC
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
   * @opt_param bool validateOnly Optional. Only validate the request, but do not
   * perform mutations. The default is 'false'.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, BackupVault $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a BackupVault. (backupVaults.delete)
   *
   * @param string $name Required. Name of the resource.
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool allowMissing Optional. If true and the BackupVault is not
   * found, the request will succeed but no action will be taken.
   * @opt_param string etag The current etag of the backup vault. If an etag is
   * provided and does not match the current etag of the connection, deletion will
   * be blocked.
   * @opt_param bool force Optional. If set to true, any data source from this
   * backup vault will also be deleted.
   * @opt_param bool ignoreBackupPlanReferences Optional. If set to true,
   * backupvault deletion will proceed even if there are backup plans referencing
   * the backupvault. The default is 'false'.
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
   * @opt_param bool validateOnly Optional. Only validate the request, but do not
   * perform mutations. The default is 'false'.
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
   * FetchUsableBackupVaults lists usable BackupVaults in a given project and
   * location. Usable BackupVault are the ones that user has
   * backupdr.backupVaults.get permission. (backupVaults.fetchUsable)
   *
   * @param string $parent Required. The project and location for which to
   * retrieve backupvault stores information, in the format
   * 'projects/{project_id}/locations/{location}'. In Cloud Backup and DR,
   * locations map to Google Cloud regions, for example **us-central1**. To
   * retrieve backupvault stores for all locations, use "-" for the '{location}'
   * value.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Filtering results.
   * @opt_param string orderBy Optional. Hint for how to order the results.
   * @opt_param int pageSize Optional. Requested page size. Server may return
   * fewer items than requested. If unspecified, server will pick an appropriate
   * default.
   * @opt_param string pageToken Optional. A token identifying a page of results
   * the server should return.
   * @return FetchUsableBackupVaultsResponse
   * @throws \Google\Service\Exception
   */
  public function fetchUsable($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('fetchUsable', [$params], FetchUsableBackupVaultsResponse::class);
  }
  /**
   * Gets details of a BackupVault. (backupVaults.get)
   *
   * @param string $name Required. Name of the backupvault store resource name, in
   * the format
   * 'projects/{project_id}/locations/{location}/backupVaults/{resource_name}'
   * @param array $optParams Optional parameters.
   *
   * @opt_param string view Optional. Reserved for future use to provide a BASIC &
   * FULL view of Backup Vault
   * @return BackupVault
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], BackupVault::class);
  }
  /**
   * Lists BackupVaults in a given project and location.
   * (backupVaults.listProjectsLocationsBackupVaults)
   *
   * @param string $parent Required. The project and location for which to
   * retrieve backupvault stores information, in the format
   * 'projects/{project_id}/locations/{location}'. In Cloud Backup and DR,
   * locations map to Google Cloud regions, for example **us-central1**. To
   * retrieve backupvault stores for all locations, use "-" for the '{location}'
   * value.
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
   * FULL view of Backup Vault.
   * @return ListBackupVaultsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsBackupVaults($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListBackupVaultsResponse::class);
  }
  /**
   * Updates the settings of a BackupVault. (backupVaults.patch)
   *
   * @param string $name Output only. Identifier. Name of the backup vault to
   * create. It must have the
   * format`"projects/{project}/locations/{location}/backupVaults/{backupvault}"`.
   * `{backupvault}` cannot be changed after creation. It must be between 3-63
   * characters long and must be unique within the project and location.
   * @param BackupVault $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool force Optional. If set to true, will not check plan duration
   * against backup vault enforcement duration.
   * @opt_param bool forceUpdateAccessRestriction Optional. If set to true, we
   * will force update access restriction even if some non compliant data sources
   * are present. The default is 'false'.
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
   * fields to be overwritten in the BackupVault resource by the update. The
   * fields specified in the update_mask are relative to the resource, not the
   * full request. A field will be overwritten if it is in the mask. If the user
   * does not provide a mask then the request will fail.
   * @opt_param bool validateOnly Optional. Only validate the request, but do not
   * perform mutations. The default is 'false'.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, BackupVault $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
  /**
   * Returns the caller's permissions on a BackupVault resource. A caller is not
   * required to have Google IAM permission to make this request.
   * (backupVaults.testIamPermissions)
   *
   * @param string $resource REQUIRED: The resource for which the policy detail is
   * being requested. See [Resource
   * names](https://cloud.google.com/apis/design/resource_names) for the
   * appropriate value for this field.
   * @param TestIamPermissionsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return TestIamPermissionsResponse
   * @throws \Google\Service\Exception
   */
  public function testIamPermissions($resource, TestIamPermissionsRequest $postBody, $optParams = [])
  {
    $params = ['resource' => $resource, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('testIamPermissions', [$params], TestIamPermissionsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsBackupVaults::class, 'Google_Service_Backupdr_Resource_ProjectsLocationsBackupVaults');
