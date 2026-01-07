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

namespace Google\Service\Looker\Resource;

use Google\Service\Looker\InstanceBackup;
use Google\Service\Looker\ListInstanceBackupsResponse;
use Google\Service\Looker\Operation;

/**
 * The "backups" collection of methods.
 * Typical usage is:
 *  <code>
 *   $lookerService = new Google\Service\Looker(...);
 *   $backups = $lookerService->projects_locations_instances_backups;
 *  </code>
 */
class ProjectsLocationsInstancesBackups extends \Google\Service\Resource
{
  /**
   * Backup Looker instance. (backups.create)
   *
   * @param string $parent Required. Format:
   * projects/{project}/locations/{location}/instances/{instance}
   * @param InstanceBackup $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, InstanceBackup $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Delete backup. (backups.delete)
   *
   * @param string $name Required. Format:
   * projects/{project}/locations/{location}/instances/{instance}/backups/{backup}
   * @param array $optParams Optional parameters.
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
   * (backups.get)
   *
   * @param string $name Required. Format: `projects/{project}/locations/{location
   * }/instances/{instance}/backups/{backup}`.
   * @param array $optParams Optional parameters.
   * @return InstanceBackup
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], InstanceBackup::class);
  }
  /**
   * List backups of Looker instance.
   * (backups.listProjectsLocationsInstancesBackups)
   *
   * @param string $parent Required. Format:
   * projects/{project}/locations/{location}/instances/{instance}.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string orderBy Sort results. Default order is "create_time desc".
   * Other supported fields are "state" and "expire_time".
   * https://google.aip.dev/132#ordering
   * @opt_param int pageSize The maximum number of instances to return.
   * @opt_param string pageToken A page token received from a previous
   * ListInstances request.
   * @return ListInstanceBackupsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsInstancesBackups($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListInstanceBackupsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsInstancesBackups::class, 'Google_Service_Looker_Resource_ProjectsLocationsInstancesBackups');
