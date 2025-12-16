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

class ConfigManagementConfigSyncVersion extends \Google\Model
{
  /**
   * Version of the deployed admission-webhook pod.
   *
   * @var string
   */
  public $admissionWebhook;
  /**
   * Version of the deployed git-sync pod.
   *
   * @var string
   */
  public $gitSync;
  /**
   * Version of the deployed importer pod.
   *
   * @var string
   */
  public $importer;
  /**
   * Version of the deployed monitor pod.
   *
   * @var string
   */
  public $monitor;
  /**
   * Version of the deployed otel-collector pod
   *
   * @var string
   */
  public $otelCollector;
  /**
   * Version of the deployed reconciler-manager pod.
   *
   * @var string
   */
  public $reconcilerManager;
  /**
   * Version of the deployed resource-group-controller-manager pod
   *
   * @var string
   */
  public $resourceGroupControllerManager;
  /**
   * Version of the deployed reconciler container in root-reconciler pod.
   *
   * @var string
   */
  public $rootReconciler;
  /**
   * Version of the deployed syncer pod.
   *
   * @var string
   */
  public $syncer;

  /**
   * Version of the deployed admission-webhook pod.
   *
   * @param string $admissionWebhook
   */
  public function setAdmissionWebhook($admissionWebhook)
  {
    $this->admissionWebhook = $admissionWebhook;
  }
  /**
   * @return string
   */
  public function getAdmissionWebhook()
  {
    return $this->admissionWebhook;
  }
  /**
   * Version of the deployed git-sync pod.
   *
   * @param string $gitSync
   */
  public function setGitSync($gitSync)
  {
    $this->gitSync = $gitSync;
  }
  /**
   * @return string
   */
  public function getGitSync()
  {
    return $this->gitSync;
  }
  /**
   * Version of the deployed importer pod.
   *
   * @param string $importer
   */
  public function setImporter($importer)
  {
    $this->importer = $importer;
  }
  /**
   * @return string
   */
  public function getImporter()
  {
    return $this->importer;
  }
  /**
   * Version of the deployed monitor pod.
   *
   * @param string $monitor
   */
  public function setMonitor($monitor)
  {
    $this->monitor = $monitor;
  }
  /**
   * @return string
   */
  public function getMonitor()
  {
    return $this->monitor;
  }
  /**
   * Version of the deployed otel-collector pod
   *
   * @param string $otelCollector
   */
  public function setOtelCollector($otelCollector)
  {
    $this->otelCollector = $otelCollector;
  }
  /**
   * @return string
   */
  public function getOtelCollector()
  {
    return $this->otelCollector;
  }
  /**
   * Version of the deployed reconciler-manager pod.
   *
   * @param string $reconcilerManager
   */
  public function setReconcilerManager($reconcilerManager)
  {
    $this->reconcilerManager = $reconcilerManager;
  }
  /**
   * @return string
   */
  public function getReconcilerManager()
  {
    return $this->reconcilerManager;
  }
  /**
   * Version of the deployed resource-group-controller-manager pod
   *
   * @param string $resourceGroupControllerManager
   */
  public function setResourceGroupControllerManager($resourceGroupControllerManager)
  {
    $this->resourceGroupControllerManager = $resourceGroupControllerManager;
  }
  /**
   * @return string
   */
  public function getResourceGroupControllerManager()
  {
    return $this->resourceGroupControllerManager;
  }
  /**
   * Version of the deployed reconciler container in root-reconciler pod.
   *
   * @param string $rootReconciler
   */
  public function setRootReconciler($rootReconciler)
  {
    $this->rootReconciler = $rootReconciler;
  }
  /**
   * @return string
   */
  public function getRootReconciler()
  {
    return $this->rootReconciler;
  }
  /**
   * Version of the deployed syncer pod.
   *
   * @param string $syncer
   */
  public function setSyncer($syncer)
  {
    $this->syncer = $syncer;
  }
  /**
   * @return string
   */
  public function getSyncer()
  {
    return $this->syncer;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConfigManagementConfigSyncVersion::class, 'Google_Service_GKEHub_ConfigManagementConfigSyncVersion');
