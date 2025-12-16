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

use Google\Service\Backupdr\BackupPlanRevision;
use Google\Service\Backupdr\ListBackupPlanRevisionsResponse;

/**
 * The "revisions" collection of methods.
 * Typical usage is:
 *  <code>
 *   $backupdrService = new Google\Service\Backupdr(...);
 *   $revisions = $backupdrService->projects_locations_backupPlans_revisions;
 *  </code>
 */
class ProjectsLocationsBackupPlansRevisions extends \Google\Service\Resource
{
  /**
   * Gets details of a single BackupPlanRevision. (revisions.get)
   *
   * @param string $name Required. The resource name of the `BackupPlanRevision`
   * to retrieve. Format: `projects/{project}/locations/{location}/backupPlans/{ba
   * ckup_plan}/revisions/{revision}`
   * @param array $optParams Optional parameters.
   * @return BackupPlanRevision
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], BackupPlanRevision::class);
  }
  /**
   * Lists BackupPlanRevisions in a given project and location.
   * (revisions.listProjectsLocationsBackupPlansRevisions)
   *
   * @param string $parent Required. The project and location for which to
   * retrieve `BackupPlanRevisions` information. Format:
   * `projects/{project}/locations/{location}/backupPlans/{backup_plan}`. In Cloud
   * BackupDR, locations map to GCP regions, for e.g. **us-central1**.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of `BackupPlans` to
   * return in a single response. If not specified, a default value will be chosen
   * by the service. Note that the response may include a partial list and a
   * caller should only rely on the response's next_page_token to determine if
   * there are more instances left to be queried.
   * @opt_param string pageToken Optional. The value of next_page_token received
   * from a previous `ListBackupPlans` call. Provide this to retrieve the
   * subsequent page in a multi-page list of results. When paginating, all other
   * parameters provided to `ListBackupPlans` must match the call that provided
   * the page token.
   * @return ListBackupPlanRevisionsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsBackupPlansRevisions($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListBackupPlanRevisionsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsBackupPlansRevisions::class, 'Google_Service_Backupdr_Resource_ProjectsLocationsBackupPlansRevisions');
