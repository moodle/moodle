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
use Google\Service\Firestore\GoogleFirestoreAdminV1Backup;
use Google\Service\Firestore\GoogleFirestoreAdminV1ListBackupsResponse;

/**
 * The "backups" collection of methods.
 * Typical usage is:
 *  <code>
 *   $firestoreService = new Google\Service\Firestore(...);
 *   $backups = $firestoreService->projects_locations_backups;
 *  </code>
 */
class ProjectsLocationsBackups extends \Google\Service\Resource
{
  /**
   * Deletes a backup. (backups.delete)
   *
   * @param string $name Required. Name of the backup to delete. format is
   * `projects/{project}/locations/{location}/backups/{backup}`.
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
   * Gets information about a backup. (backups.get)
   *
   * @param string $name Required. Name of the backup to fetch. Format is
   * `projects/{project}/locations/{location}/backups/{backup}`.
   * @param array $optParams Optional parameters.
   * @return GoogleFirestoreAdminV1Backup
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleFirestoreAdminV1Backup::class);
  }
  /**
   * Lists all the backups. (backups.listProjectsLocationsBackups)
   *
   * @param string $parent Required. The location to list backups from. Format is
   * `projects/{project}/locations/{location}`. Use `{location} = '-'` to list
   * backups from all locations for the given project. This allows listing backups
   * from a single location or from all locations.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter An expression that filters the list of returned
   * backups. A filter expression consists of a field name, a comparison operator,
   * and a value for filtering. The value must be a string, a number, or a
   * boolean. The comparison operator must be one of: `<`, `>`, `<=`, `>=`, `!=`,
   * `=`, or `:`. Colon `:` is the contains operator. Filter rules are not case
   * sensitive. The following fields in the Backup are eligible for filtering: *
   * `database_uid` (supports `=` only)
   * @return GoogleFirestoreAdminV1ListBackupsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsBackups($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleFirestoreAdminV1ListBackupsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsBackups::class, 'Google_Service_Firestore_Resource_ProjectsLocationsBackups');
