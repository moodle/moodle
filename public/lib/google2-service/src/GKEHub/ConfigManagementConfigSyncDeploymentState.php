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

namespace Google\Service\GKEHub;

class ConfigManagementConfigSyncDeploymentState extends \Google\Model
{
  /**
   * Deployment's state cannot be determined.
   */
  public const ADMISSION_WEBHOOK_DEPLOYMENT_STATE_UNSPECIFIED = 'DEPLOYMENT_STATE_UNSPECIFIED';
  /**
   * Deployment is not installed.
   */
  public const ADMISSION_WEBHOOK_NOT_INSTALLED = 'NOT_INSTALLED';
  /**
   * Deployment is installed.
   */
  public const ADMISSION_WEBHOOK_INSTALLED = 'INSTALLED';
  /**
   * Deployment was attempted to be installed, but has errors.
   */
  public const ADMISSION_WEBHOOK_ERROR = 'ERROR';
  /**
   * Deployment is installing or terminating
   */
  public const ADMISSION_WEBHOOK_PENDING = 'PENDING';
  /**
   * Deployment's state cannot be determined.
   */
  public const GIT_SYNC_DEPLOYMENT_STATE_UNSPECIFIED = 'DEPLOYMENT_STATE_UNSPECIFIED';
  /**
   * Deployment is not installed.
   */
  public const GIT_SYNC_NOT_INSTALLED = 'NOT_INSTALLED';
  /**
   * Deployment is installed.
   */
  public const GIT_SYNC_INSTALLED = 'INSTALLED';
  /**
   * Deployment was attempted to be installed, but has errors.
   */
  public const GIT_SYNC_ERROR = 'ERROR';
  /**
   * Deployment is installing or terminating
   */
  public const GIT_SYNC_PENDING = 'PENDING';
  /**
   * Deployment's state cannot be determined.
   */
  public const IMPORTER_DEPLOYMENT_STATE_UNSPECIFIED = 'DEPLOYMENT_STATE_UNSPECIFIED';
  /**
   * Deployment is not installed.
   */
  public const IMPORTER_NOT_INSTALLED = 'NOT_INSTALLED';
  /**
   * Deployment is installed.
   */
  public const IMPORTER_INSTALLED = 'INSTALLED';
  /**
   * Deployment was attempted to be installed, but has errors.
   */
  public const IMPORTER_ERROR = 'ERROR';
  /**
   * Deployment is installing or terminating
   */
  public const IMPORTER_PENDING = 'PENDING';
  /**
   * Deployment's state cannot be determined.
   */
  public const MONITOR_DEPLOYMENT_STATE_UNSPECIFIED = 'DEPLOYMENT_STATE_UNSPECIFIED';
  /**
   * Deployment is not installed.
   */
  public const MONITOR_NOT_INSTALLED = 'NOT_INSTALLED';
  /**
   * Deployment is installed.
   */
  public const MONITOR_INSTALLED = 'INSTALLED';
  /**
   * Deployment was attempted to be installed, but has errors.
   */
  public const MONITOR_ERROR = 'ERROR';
  /**
   * Deployment is installing or terminating
   */
  public const MONITOR_PENDING = 'PENDING';
  /**
   * Deployment's state cannot be determined.
   */
  public const OTEL_COLLECTOR_DEPLOYMENT_STATE_UNSPECIFIED = 'DEPLOYMENT_STATE_UNSPECIFIED';
  /**
   * Deployment is not installed.
   */
  public const OTEL_COLLECTOR_NOT_INSTALLED = 'NOT_INSTALLED';
  /**
   * Deployment is installed.
   */
  public const OTEL_COLLECTOR_INSTALLED = 'INSTALLED';
  /**
   * Deployment was attempted to be installed, but has errors.
   */
  public const OTEL_COLLECTOR_ERROR = 'ERROR';
  /**
   * Deployment is installing or terminating
   */
  public const OTEL_COLLECTOR_PENDING = 'PENDING';
  /**
   * Deployment's state cannot be determined.
   */
  public const RECONCILER_MANAGER_DEPLOYMENT_STATE_UNSPECIFIED = 'DEPLOYMENT_STATE_UNSPECIFIED';
  /**
   * Deployment is not installed.
   */
  public const RECONCILER_MANAGER_NOT_INSTALLED = 'NOT_INSTALLED';
  /**
   * Deployment is installed.
   */
  public const RECONCILER_MANAGER_INSTALLED = 'INSTALLED';
  /**
   * Deployment was attempted to be installed, but has errors.
   */
  public const RECONCILER_MANAGER_ERROR = 'ERROR';
  /**
   * Deployment is installing or terminating
   */
  public const RECONCILER_MANAGER_PENDING = 'PENDING';
  /**
   * Deployment's state cannot be determined.
   */
  public const RESOURCE_GROUP_CONTROLLER_MANAGER_DEPLOYMENT_STATE_UNSPECIFIED = 'DEPLOYMENT_STATE_UNSPECIFIED';
  /**
   * Deployment is not installed.
   */
  public const RESOURCE_GROUP_CONTROLLER_MANAGER_NOT_INSTALLED = 'NOT_INSTALLED';
  /**
   * Deployment is installed.
   */
  public const RESOURCE_GROUP_CONTROLLER_MANAGER_INSTALLED = 'INSTALLED';
  /**
   * Deployment was attempted to be installed, but has errors.
   */
  public const RESOURCE_GROUP_CONTROLLER_MANAGER_ERROR = 'ERROR';
  /**
   * Deployment is installing or terminating
   */
  public const RESOURCE_GROUP_CONTROLLER_MANAGER_PENDING = 'PENDING';
  /**
   * Deployment's state cannot be determined.
   */
  public const ROOT_RECONCILER_DEPLOYMENT_STATE_UNSPECIFIED = 'DEPLOYMENT_STATE_UNSPECIFIED';
  /**
   * Deployment is not installed.
   */
  public const ROOT_RECONCILER_NOT_INSTALLED = 'NOT_INSTALLED';
  /**
   * Deployment is installed.
   */
  public const ROOT_RECONCILER_INSTALLED = 'INSTALLED';
  /**
   * Deployment was attempted to be installed, but has errors.
   */
  public const ROOT_RECONCILER_ERROR = 'ERROR';
  /**
   * Deployment is installing or terminating
   */
  public const ROOT_RECONCILER_PENDING = 'PENDING';
  /**
   * Deployment's state cannot be determined.
   */
  public const SYNCER_DEPLOYMENT_STATE_UNSPECIFIED = 'DEPLOYMENT_STATE_UNSPECIFIED';
  /**
   * Deployment is not installed.
   */
  public const SYNCER_NOT_INSTALLED = 'NOT_INSTALLED';
  /**
   * Deployment is installed.
   */
  public const SYNCER_INSTALLED = 'INSTALLED';
  /**
   * Deployment was attempted to be installed, but has errors.
   */
  public const SYNCER_ERROR = 'ERROR';
  /**
   * Deployment is installing or terminating
   */
  public const SYNCER_PENDING = 'PENDING';
  /**
   * Deployment state of admission-webhook.
   *
   * @var string
   */
  public $admissionWebhook;
  /**
   * Deployment state of the git-sync pod.
   *
   * @var string
   */
  public $gitSync;
  /**
   * Deployment state of the importer pod.
   *
   * @var string
   */
  public $importer;
  /**
   * Deployment state of the monitor pod.
   *
   * @var string
   */
  public $monitor;
  /**
   * Deployment state of otel-collector
   *
   * @var string
   */
  public $otelCollector;
  /**
   * Deployment state of reconciler-manager pod.
   *
   * @var string
   */
  public $reconcilerManager;
  /**
   * Deployment state of resource-group-controller-manager
   *
   * @var string
   */
  public $resourceGroupControllerManager;
  /**
   * Deployment state of root-reconciler.
   *
   * @var string
   */
  public $rootReconciler;
  /**
   * Deployment state of the syncer pod.
   *
   * @var string
   */
  public $syncer;

  /**
   * Deployment state of admission-webhook.
   *
   * Accepted values: DEPLOYMENT_STATE_UNSPECIFIED, NOT_INSTALLED, INSTALLED,
   * ERROR, PENDING
   *
   * @param self::ADMISSION_WEBHOOK_* $admissionWebhook
   */
  public function setAdmissionWebhook($admissionWebhook)
  {
    $this->admissionWebhook = $admissionWebhook;
  }
  /**
   * @return self::ADMISSION_WEBHOOK_*
   */
  public function getAdmissionWebhook()
  {
    return $this->admissionWebhook;
  }
  /**
   * Deployment state of the git-sync pod.
   *
   * Accepted values: DEPLOYMENT_STATE_UNSPECIFIED, NOT_INSTALLED, INSTALLED,
   * ERROR, PENDING
   *
   * @param self::GIT_SYNC_* $gitSync
   */
  public function setGitSync($gitSync)
  {
    $this->gitSync = $gitSync;
  }
  /**
   * @return self::GIT_SYNC_*
   */
  public function getGitSync()
  {
    return $this->gitSync;
  }
  /**
   * Deployment state of the importer pod.
   *
   * Accepted values: DEPLOYMENT_STATE_UNSPECIFIED, NOT_INSTALLED, INSTALLED,
   * ERROR, PENDING
   *
   * @param self::IMPORTER_* $importer
   */
  public function setImporter($importer)
  {
    $this->importer = $importer;
  }
  /**
   * @return self::IMPORTER_*
   */
  public function getImporter()
  {
    return $this->importer;
  }
  /**
   * Deployment state of the monitor pod.
   *
   * Accepted values: DEPLOYMENT_STATE_UNSPECIFIED, NOT_INSTALLED, INSTALLED,
   * ERROR, PENDING
   *
   * @param self::MONITOR_* $monitor
   */
  public function setMonitor($monitor)
  {
    $this->monitor = $monitor;
  }
  /**
   * @return self::MONITOR_*
   */
  public function getMonitor()
  {
    return $this->monitor;
  }
  /**
   * Deployment state of otel-collector
   *
   * Accepted values: DEPLOYMENT_STATE_UNSPECIFIED, NOT_INSTALLED, INSTALLED,
   * ERROR, PENDING
   *
   * @param self::OTEL_COLLECTOR_* $otelCollector
   */
  public function setOtelCollector($otelCollector)
  {
    $this->otelCollector = $otelCollector;
  }
  /**
   * @return self::OTEL_COLLECTOR_*
   */
  public function getOtelCollector()
  {
    return $this->otelCollector;
  }
  /**
   * Deployment state of reconciler-manager pod.
   *
   * Accepted values: DEPLOYMENT_STATE_UNSPECIFIED, NOT_INSTALLED, INSTALLED,
   * ERROR, PENDING
   *
   * @param self::RECONCILER_MANAGER_* $reconcilerManager
   */
  public function setReconcilerManager($reconcilerManager)
  {
    $this->reconcilerManager = $reconcilerManager;
  }
  /**
   * @return self::RECONCILER_MANAGER_*
   */
  public function getReconcilerManager()
  {
    return $this->reconcilerManager;
  }
  /**
   * Deployment state of resource-group-controller-manager
   *
   * Accepted values: DEPLOYMENT_STATE_UNSPECIFIED, NOT_INSTALLED, INSTALLED,
   * ERROR, PENDING
   *
   * @param self::RESOURCE_GROUP_CONTROLLER_MANAGER_* $resourceGroupControllerManager
   */
  public function setResourceGroupControllerManager($resourceGroupControllerManager)
  {
    $this->resourceGroupControllerManager = $resourceGroupControllerManager;
  }
  /**
   * @return self::RESOURCE_GROUP_CONTROLLER_MANAGER_*
   */
  public function getResourceGroupControllerManager()
  {
    return $this->resourceGroupControllerManager;
  }
  /**
   * Deployment state of root-reconciler.
   *
   * Accepted values: DEPLOYMENT_STATE_UNSPECIFIED, NOT_INSTALLED, INSTALLED,
   * ERROR, PENDING
   *
   * @param self::ROOT_RECONCILER_* $rootReconciler
   */
  public function setRootReconciler($rootReconciler)
  {
    $this->rootReconciler = $rootReconciler;
  }
  /**
   * @return self::ROOT_RECONCILER_*
   */
  public function getRootReconciler()
  {
    return $this->rootReconciler;
  }
  /**
   * Deployment state of the syncer pod.
   *
   * Accepted values: DEPLOYMENT_STATE_UNSPECIFIED, NOT_INSTALLED, INSTALLED,
   * ERROR, PENDING
   *
   * @param self::SYNCER_* $syncer
   */
  public function setSyncer($syncer)
  {
    $this->syncer = $syncer;
  }
  /**
   * @return self::SYNCER_*
   */
  public function getSyncer()
  {
    return $this->syncer;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConfigManagementConfigSyncDeploymentState::class, 'Google_Service_GKEHub_ConfigManagementConfigSyncDeploymentState');
