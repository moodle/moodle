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

namespace Google\Service\SQLAdmin\Resource;

use Google\Service\SQLAdmin\Backup;
use Google\Service\SQLAdmin\ListBackupsResponse;
use Google\Service\SQLAdmin\Operation;

/**
 * The "Backups" collection of methods.
 * Typical usage is:
 *  <code>
 *   $sqladminService = new Google\Service\SQLAdmin(...);
 *   $Backups = $sqladminService->Backups;
 *  </code>
 */
class Backups extends \Google\Service\Resource
{
  /**
   * Creates a backup for a Cloud SQL instance. This API can be used only to
   * create on-demand backups. (Backups.CreateBackup)
   *
   * @param string $parent Required. The parent resource where this backup is
   * created. Format: projects/{project}
   * @param Backup $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function CreateBackup($parent, Backup $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('CreateBackup', [$params], Operation::class);
  }
  /**
   * Deletes the backup. (Backups.DeleteBackup)
   *
   * @param string $name Required. The name of the backup to delete. Format:
   * projects/{project}/backups/{backup}
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function DeleteBackup($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('DeleteBackup', [$params], Operation::class);
  }
  /**
   * Retrieves a resource containing information about a backup.
   * (Backups.GetBackup)
   *
   * @param string $name Required. The name of the backup to retrieve. Format:
   * projects/{project}/backups/{backup}
   * @param array $optParams Optional parameters.
   * @return Backup
   * @throws \Google\Service\Exception
   */
  public function GetBackup($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('GetBackup', [$params], Backup::class);
  }
  /**
   * Lists all backups associated with the project. (Backups.ListBackups)
   *
   * @param string $parent Required. The parent that owns this collection of
   * backups. Format: projects/{project}
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Multiple filter queries are separated by spaces. For
   * example, 'instance:abc AND type:FINAL, 'location:us',
   * 'backupInterval.startTime>=1950-01-01T01:01:25.771Z'. You can filter by type,
   * instance, backupInterval.startTime (creation time), or location.
   * @opt_param int pageSize The maximum number of backups to return per response.
   * The service might return fewer backups than this value. If a value for this
   * parameter isn't specified, then, at most, 500 backups are returned. The
   * maximum value is 2,000. Any values that you set, which are greater than
   * 2,000, are changed to 2,000.
   * @opt_param string pageToken A page token, received from a previous
   * `ListBackups` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters provided to `ListBackups` must match the
   * call that provided the page token.
   * @return ListBackupsResponse
   * @throws \Google\Service\Exception
   */
  public function ListBackups($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('ListBackups', [$params], ListBackupsResponse::class);
  }
  /**
   * Updates the retention period and description of the backup. You can use this
   * API to update final backups only. (Backups.UpdateBackup)
   *
   * @param string $name Output only. The resource name of the backup. Format:
   * projects/{project}/backups/{backup}.
   * @param Backup $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask The list of fields that you can update. You can
   * update only the description and retention period of the final backup.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function UpdateBackup($name, Backup $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('UpdateBackup', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Backups::class, 'Google_Service_SQLAdmin_Resource_Backups');
