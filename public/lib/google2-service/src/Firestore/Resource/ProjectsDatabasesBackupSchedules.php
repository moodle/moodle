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

namespace Google\Service\Firestore\Resource;

use Google\Service\Firestore\FirestoreEmpty;
use Google\Service\Firestore\GoogleFirestoreAdminV1BackupSchedule;
use Google\Service\Firestore\GoogleFirestoreAdminV1ListBackupSchedulesResponse;

/**
 * The "backupSchedules" collection of methods.
 * Typical usage is:
 *  <code>
 *   $firestoreService = new Google\Service\Firestore(...);
 *   $backupSchedules = $firestoreService->projects_databases_backupSchedules;
 *  </code>
 */
class ProjectsDatabasesBackupSchedules extends \Google\Service\Resource
{
  /**
   * Creates a backup schedule on a database. At most two backup schedules can be
   * configured on a database, one daily backup schedule and one weekly backup
   * schedule. (backupSchedules.create)
   *
   * @param string $parent Required. The parent database. Format
   * `projects/{project}/databases/{database}`
   * @param GoogleFirestoreAdminV1BackupSchedule $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleFirestoreAdminV1BackupSchedule
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleFirestoreAdminV1BackupSchedule $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleFirestoreAdminV1BackupSchedule::class);
  }
  /**
   * Deletes a backup schedule. (backupSchedules.delete)
   *
   * @param string $name Required. The name of the backup schedule. Format
   * `projects/{project}/databases/{database}/backupSchedules/{backup_schedule}`
   * @param array $optParams Optional parameters.
   * @return FirestoreEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], FirestoreEmpty::class);
  }
  /**
   * Gets information about a backup schedule. (backupSchedules.get)
   *
   * @param string $name Required. The name of the backup schedule. Format
   * `projects/{project}/databases/{database}/backupSchedules/{backup_schedule}`
   * @param array $optParams Optional parameters.
   * @return GoogleFirestoreAdminV1BackupSchedule
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleFirestoreAdminV1BackupSchedule::class);
  }
  /**
   * List backup schedules. (backupSchedules.listProjectsDatabasesBackupSchedules)
   *
   * @param string $parent Required. The parent database. Format is
   * `projects/{project}/databases/{database}`.
   * @param array $optParams Optional parameters.
   * @return GoogleFirestoreAdminV1ListBackupSchedulesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsDatabasesBackupSchedules($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleFirestoreAdminV1ListBackupSchedulesResponse::class);
  }
  /**
   * Updates a backup schedule. (backupSchedules.patch)
   *
   * @param string $name Output only. The unique backup schedule identifier across
   * all locations and databases for the given project. This will be auto-
   * assigned. Format is
   * `projects/{project}/databases/{database}/backupSchedules/{backup_schedule}`
   * @param GoogleFirestoreAdminV1BackupSchedule $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask The list of fields to be updated.
   * @return GoogleFirestoreAdminV1BackupSchedule
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleFirestoreAdminV1BackupSchedule $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleFirestoreAdminV1BackupSchedule::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsDatabasesBackupSchedules::class, 'Google_Service_Firestore_Resource_ProjectsDatabasesBackupSchedules');
