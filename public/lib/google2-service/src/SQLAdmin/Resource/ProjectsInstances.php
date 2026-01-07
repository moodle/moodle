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

use Google\Service\SQLAdmin\Operation;
use Google\Service\SQLAdmin\PerformDiskShrinkContext;
use Google\Service\SQLAdmin\SqlInstancesGetDiskShrinkConfigResponse;
use Google\Service\SQLAdmin\SqlInstancesGetLatestRecoveryTimeResponse;
use Google\Service\SQLAdmin\SqlInstancesRescheduleMaintenanceRequestBody;
use Google\Service\SQLAdmin\SqlInstancesResetReplicaSizeRequest;
use Google\Service\SQLAdmin\SqlInstancesStartExternalSyncRequest;
use Google\Service\SQLAdmin\SqlInstancesVerifyExternalSyncSettingsRequest;
use Google\Service\SQLAdmin\SqlInstancesVerifyExternalSyncSettingsResponse;

/**
 * The "instances" collection of methods.
 * Typical usage is:
 *  <code>
 *   $sqladminService = new Google\Service\SQLAdmin(...);
 *   $instances = $sqladminService->projects_instances;
 *  </code>
 */
class ProjectsInstances extends \Google\Service\Resource
{
  /**
   * Get Disk Shrink Config for a given instance. (instances.getDiskShrinkConfig)
   *
   * @param string $project Project ID of the project that contains the instance.
   * @param string $instance Cloud SQL instance ID. This does not include the
   * project ID.
   * @param array $optParams Optional parameters.
   * @return SqlInstancesGetDiskShrinkConfigResponse
   * @throws \Google\Service\Exception
   */
  public function getDiskShrinkConfig($project, $instance, $optParams = [])
  {
    $params = ['project' => $project, 'instance' => $instance];
    $params = array_merge($params, $optParams);
    return $this->call('getDiskShrinkConfig', [$params], SqlInstancesGetDiskShrinkConfigResponse::class);
  }
  /**
   * Get Latest Recovery Time for a given instance.
   * (instances.getLatestRecoveryTime)
   *
   * @param string $project Project ID of the project that contains the instance.
   * @param string $instance Cloud SQL instance ID. This does not include the
   * project ID.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string sourceInstanceDeletionTime The timestamp used to identify
   * the time when the source instance is deleted. If this instance is deleted,
   * then you must set the timestamp.
   * @return SqlInstancesGetLatestRecoveryTimeResponse
   * @throws \Google\Service\Exception
   */
  public function getLatestRecoveryTime($project, $instance, $optParams = [])
  {
    $params = ['project' => $project, 'instance' => $instance];
    $params = array_merge($params, $optParams);
    return $this->call('getLatestRecoveryTime', [$params], SqlInstancesGetLatestRecoveryTimeResponse::class);
  }
  /**
   * Perform Disk Shrink on primary instance. (instances.performDiskShrink)
   *
   * @param string $project Project ID of the project that contains the instance.
   * @param string $instance Cloud SQL instance ID. This does not include the
   * project ID.
   * @param PerformDiskShrinkContext $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function performDiskShrink($project, $instance, PerformDiskShrinkContext $postBody, $optParams = [])
  {
    $params = ['project' => $project, 'instance' => $instance, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('performDiskShrink', [$params], Operation::class);
  }
  /**
   * Reschedules the maintenance on the given instance.
   * (instances.rescheduleMaintenance)
   *
   * @param string $project ID of the project that contains the instance.
   * @param string $instance Cloud SQL instance ID. This does not include the
   * project ID.
   * @param SqlInstancesRescheduleMaintenanceRequestBody $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function rescheduleMaintenance($project, $instance, SqlInstancesRescheduleMaintenanceRequestBody $postBody, $optParams = [])
  {
    $params = ['project' => $project, 'instance' => $instance, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('rescheduleMaintenance', [$params], Operation::class);
  }
  /**
   * Reset Replica Size to primary instance disk size.
   * (instances.resetReplicaSize)
   *
   * @param string $project ID of the project that contains the read replica.
   * @param string $instance Cloud SQL read replica instance name.
   * @param SqlInstancesResetReplicaSizeRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function resetReplicaSize($project, $instance, SqlInstancesResetReplicaSizeRequest $postBody, $optParams = [])
  {
    $params = ['project' => $project, 'instance' => $instance, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('resetReplicaSize', [$params], Operation::class);
  }
  /**
   * Start External primary instance migration. (instances.startExternalSync)
   *
   * @param string $project ID of the project that contains the instance.
   * @param string $instance Cloud SQL instance ID. This does not include the
   * project ID.
   * @param SqlInstancesStartExternalSyncRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function startExternalSync($project, $instance, SqlInstancesStartExternalSyncRequest $postBody, $optParams = [])
  {
    $params = ['project' => $project, 'instance' => $instance, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('startExternalSync', [$params], Operation::class);
  }
  /**
   * Verify External primary instance external sync settings.
   * (instances.verifyExternalSyncSettings)
   *
   * @param string $project Project ID of the project that contains the instance.
   * @param string $instance Cloud SQL instance ID. This does not include the
   * project ID.
   * @param SqlInstancesVerifyExternalSyncSettingsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return SqlInstancesVerifyExternalSyncSettingsResponse
   * @throws \Google\Service\Exception
   */
  public function verifyExternalSyncSettings($project, $instance, SqlInstancesVerifyExternalSyncSettingsRequest $postBody, $optParams = [])
  {
    $params = ['project' => $project, 'instance' => $instance, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('verifyExternalSyncSettings', [$params], SqlInstancesVerifyExternalSyncSettingsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsInstances::class, 'Google_Service_SQLAdmin_Resource_ProjectsInstances');
