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

namespace Google\Service\Spanner\Resource;

use Google\Service\Spanner\Backup;
use Google\Service\Spanner\CopyBackupRequest;
use Google\Service\Spanner\GetIamPolicyRequest;
use Google\Service\Spanner\ListBackupsResponse;
use Google\Service\Spanner\Operation;
use Google\Service\Spanner\Policy;
use Google\Service\Spanner\SetIamPolicyRequest;
use Google\Service\Spanner\SpannerEmpty;
use Google\Service\Spanner\TestIamPermissionsRequest;
use Google\Service\Spanner\TestIamPermissionsResponse;

/**
 * The "backups" collection of methods.
 * Typical usage is:
 *  <code>
 *   $spannerService = new Google\Service\Spanner(...);
 *   $backups = $spannerService->projects_instances_backups;
 *  </code>
 */
class ProjectsInstancesBackups extends \Google\Service\Resource
{
  /**
   * Starts copying a Cloud Spanner Backup. The returned backup long-running
   * operation will have a name of the format
   * `projects//instances//backups//operations/` and can be used to track copying
   * of the backup. The operation is associated with the destination backup. The
   * metadata field type is CopyBackupMetadata. The response field type is Backup,
   * if successful. Cancelling the returned operation will stop the copying and
   * delete the destination backup. Concurrent CopyBackup requests can run on the
   * same source backup. (backups.copy)
   *
   * @param string $parent Required. The name of the destination instance that
   * will contain the backup copy. Values are of the form: `projects//instances/`.
   * @param CopyBackupRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function copy($parent, CopyBackupRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('copy', [$params], Operation::class);
  }
  /**
   * Starts creating a new Cloud Spanner Backup. The returned backup long-running
   * operation will have a name of the format
   * `projects//instances//backups//operations/` and can be used to track creation
   * of the backup. The metadata field type is CreateBackupMetadata. The response
   * field type is Backup, if successful. Cancelling the returned operation will
   * stop the creation and delete the backup. There can be only one pending backup
   * creation per database. Backup creation of different databases can run
   * concurrently. (backups.create)
   *
   * @param string $parent Required. The name of the instance in which the backup
   * is created. This must be the same instance that contains the database the
   * backup is created from. The backup will be stored in the locations specified
   * in the instance configuration of this instance. Values are of the form
   * `projects//instances/`.
   * @param Backup $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string backupId Required. The id of the backup to be created. The
   * `backup_id` appended to `parent` forms the full backup name of the form
   * `projects//instances//backups/`.
   * @opt_param string encryptionConfig.encryptionType Required. The encryption
   * type of the backup.
   * @opt_param string encryptionConfig.kmsKeyName Optional. This field is
   * maintained for backwards compatibility. For new callers, we recommend using
   * `kms_key_names` to specify the KMS key. Only use `kms_key_name` if the
   * location of the KMS key matches the database instance's configuration
   * (location) exactly. For example, if the KMS location is in `us-central1` or
   * `nam3`, then the database instance must also be in `us-central1` or `nam3`.
   * The Cloud KMS key that is used to encrypt and decrypt the restored database.
   * Set this field only when encryption_type is `CUSTOMER_MANAGED_ENCRYPTION`.
   * Values are of the form `projects//locations//keyRings//cryptoKeys/`.
   * @opt_param string encryptionConfig.kmsKeyNames Optional. Specifies the KMS
   * configuration for the one or more keys used to protect the backup. Values are
   * of the form `projects//locations//keyRings//cryptoKeys/`. The keys referenced
   * by `kms_key_names` must fully cover all regions of the backup's instance
   * configuration. Some examples: * For regional (single-region) instance
   * configurations, specify a regional location KMS key. * For multi-region
   * instance configurations of type `GOOGLE_MANAGED`, either specify a multi-
   * region location KMS key or multiple regional location KMS keys that cover all
   * regions in the instance configuration. * For an instance configuration of
   * type `USER_MANAGED`, specify only regional location KMS keys to cover each
   * region in the instance configuration. Multi-region location KMS keys aren't
   * supported for `USER_MANAGED` type instance configurations.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, Backup $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a pending or completed Backup. (backups.delete)
   *
   * @param string $name Required. Name of the backup to delete. Values are of the
   * form `projects//instances//backups/`.
   * @param array $optParams Optional parameters.
   * @return SpannerEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], SpannerEmpty::class);
  }
  /**
   * Gets metadata on a pending or completed Backup. (backups.get)
   *
   * @param string $name Required. Name of the backup. Values are of the form
   * `projects//instances//backups/`.
   * @param array $optParams Optional parameters.
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
   * Gets the access control policy for a database or backup resource. Returns an
   * empty policy if a database or backup exists but does not have a policy set.
   * Authorization requires `spanner.databases.getIamPolicy` permission on
   * resource. For backups, authorization requires `spanner.backups.getIamPolicy`
   * permission on resource. For backup schedules, authorization requires
   * `spanner.backupSchedules.getIamPolicy` permission on resource.
   * (backups.getIamPolicy)
   *
   * @param string $resource REQUIRED: The Cloud Spanner resource for which the
   * policy is being retrieved. The format is `projects//instances/` for instance
   * resources and `projects//instances//databases/` for database resources.
   * @param GetIamPolicyRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Policy
   * @throws \Google\Service\Exception
   */
  public function getIamPolicy($resource, GetIamPolicyRequest $postBody, $optParams = [])
  {
    $params = ['resource' => $resource, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('getIamPolicy', [$params], Policy::class);
  }
  /**
   * Lists completed and pending backups. Backups returned are ordered by
   * `create_time` in descending order, starting from the most recent
   * `create_time`. (backups.listProjectsInstancesBackups)
   *
   * @param string $parent Required. The instance to list backups from. Values are
   * of the form `projects//instances/`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter An expression that filters the list of returned
   * backups. A filter expression consists of a field name, a comparison operator,
   * and a value for filtering. The value must be a string, a number, or a
   * boolean. The comparison operator must be one of: `<`, `>`, `<=`, `>=`, `!=`,
   * `=`, or `:`. Colon `:` is the contains operator. Filter rules are not case
   * sensitive. The following fields in the Backup are eligible for filtering: *
   * `name` * `database` * `state` * `create_time` (and values are of the format
   * YYYY-MM-DDTHH:MM:SSZ) * `expire_time` (and values are of the format YYYY-MM-
   * DDTHH:MM:SSZ) * `version_time` (and values are of the format YYYY-MM-
   * DDTHH:MM:SSZ) * `size_bytes` * `backup_schedules` You can combine multiple
   * expressions by enclosing each expression in parentheses. By default,
   * expressions are combined with AND logic, but you can specify AND, OR, and NOT
   * logic explicitly. Here are a few examples: * `name:Howl` - The backup's name
   * contains the string "howl". * `database:prod` - The database's name contains
   * the string "prod". * `state:CREATING` - The backup is pending creation. *
   * `state:READY` - The backup is fully created and ready for use. * `(name:howl)
   * AND (create_time < \"2018-03-28T14:50:00Z\")` - The backup name contains the
   * string "howl" and `create_time` of the backup is before 2018-03-28T14:50:00Z.
   * * `expire_time < \"2018-03-28T14:50:00Z\"` - The backup `expire_time` is
   * before 2018-03-28T14:50:00Z. * `size_bytes > 10000000000` - The backup's size
   * is greater than 10GB * `backup_schedules:daily` - The backup is created from
   * a schedule with "daily" in its name.
   * @opt_param int pageSize Number of backups to be returned in the response. If
   * 0 or less, defaults to the server's maximum allowed page size.
   * @opt_param string pageToken If non-empty, `page_token` should contain a
   * next_page_token from a previous ListBackupsResponse to the same `parent` and
   * with the same `filter`.
   * @return ListBackupsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsInstancesBackups($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListBackupsResponse::class);
  }
  /**
   * Updates a pending or completed Backup. (backups.patch)
   *
   * @param string $name Output only for the CreateBackup operation. Required for
   * the UpdateBackup operation. A globally unique identifier for the backup which
   * cannot be changed. Values are of the form
   * `projects//instances//backups/a-z*[a-z0-9]` The final segment of the name
   * must be between 2 and 60 characters in length. The backup is stored in the
   * location(s) specified in the instance configuration of the instance
   * containing the backup, identified by the prefix of the backup name of the
   * form `projects//instances/`.
   * @param Backup $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. A mask specifying which fields (for
   * example, `expire_time`) in the backup resource should be updated. This mask
   * is relative to the backup resource, not to the request message. The field
   * mask must always be specified; this prevents any future fields from being
   * erased accidentally by clients that do not know about them.
   * @return Backup
   * @throws \Google\Service\Exception
   */
  public function patch($name, Backup $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Backup::class);
  }
  /**
   * Sets the access control policy on a database or backup resource. Replaces any
   * existing policy. Authorization requires `spanner.databases.setIamPolicy`
   * permission on resource. For backups, authorization requires
   * `spanner.backups.setIamPolicy` permission on resource. For backup schedules,
   * authorization requires `spanner.backupSchedules.setIamPolicy` permission on
   * resource. (backups.setIamPolicy)
   *
   * @param string $resource REQUIRED: The Cloud Spanner resource for which the
   * policy is being set. The format is `projects//instances/` for instance
   * resources and `projects//instances//databases/` for databases resources.
   * @param SetIamPolicyRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Policy
   * @throws \Google\Service\Exception
   */
  public function setIamPolicy($resource, SetIamPolicyRequest $postBody, $optParams = [])
  {
    $params = ['resource' => $resource, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('setIamPolicy', [$params], Policy::class);
  }
  /**
   * Returns permissions that the caller has on the specified database or backup
   * resource. Attempting this RPC on a non-existent Cloud Spanner database will
   * result in a NOT_FOUND error if the user has `spanner.databases.list`
   * permission on the containing Cloud Spanner instance. Otherwise returns an
   * empty set of permissions. Calling this method on a backup that does not exist
   * will result in a NOT_FOUND error if the user has `spanner.backups.list`
   * permission on the containing instance. Calling this method on a backup
   * schedule that does not exist will result in a NOT_FOUND error if the user has
   * `spanner.backupSchedules.list` permission on the containing database.
   * (backups.testIamPermissions)
   *
   * @param string $resource REQUIRED: The Cloud Spanner resource for which
   * permissions are being tested. The format is `projects//instances/` for
   * instance resources and `projects//instances//databases/` for database
   * resources.
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
class_alias(ProjectsInstancesBackups::class, 'Google_Service_Spanner_Resource_ProjectsInstancesBackups');
