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

namespace Google\Service\Compute;

class InstanceGroupManagerActionsSummary extends \Google\Model
{
  /**
   * Output only. [Output Only] The total number of instances in the managed
   * instance group that are scheduled to be abandoned. Abandoning an instance
   * removes it from the managed instance group without deleting it.
   *
   * @var int
   */
  public $abandoning;
  /**
   * Output only. [Output Only] The number of instances in the managed instance
   * group that are scheduled to be created or are currently being created. If
   * the group fails to create any of these instances, it tries again until it
   * creates the instance successfully.
   *
   * If you have disabled creation retries, this field will not be populated;
   * instead, the creatingWithoutRetries field will be populated.
   *
   * @var int
   */
  public $creating;
  /**
   * Output only. [Output Only] The number of instances that the managed
   * instance group will attempt to create. The group attempts to create each
   * instance only once. If the group fails to create any of these instances, it
   * decreases the group's targetSize value accordingly.
   *
   * @var int
   */
  public $creatingWithoutRetries;
  /**
   * Output only. [Output Only] The number of instances in the managed instance
   * group that are scheduled to be deleted or are currently being deleted.
   *
   * @var int
   */
  public $deleting;
  /**
   * Output only. [Output Only] The number of instances in the managed instance
   * group that are running and have no scheduled actions.
   *
   * @var int
   */
  public $none;
  /**
   * Output only. [Output Only] The number of instances in the managed instance
   * group that are scheduled to be recreated or are currently being being
   * recreated. Recreating an instance deletes the existing root persistent disk
   * and creates a new disk from the image that is defined in the instance
   * template.
   *
   * @var int
   */
  public $recreating;
  /**
   * Output only. [Output Only] The number of instances in the managed instance
   * group that are being reconfigured with properties that do not require a
   * restart or a recreate action. For example, setting or removing target pools
   * for the instance.
   *
   * @var int
   */
  public $refreshing;
  /**
   * Output only. [Output Only] The number of instances in the managed instance
   * group that are scheduled to be restarted or are currently being restarted.
   *
   * @var int
   */
  public $restarting;
  /**
   * Output only. [Output Only] The number of instances in the managed instance
   * group that are scheduled to be resumed or are currently being resumed.
   *
   * @var int
   */
  public $resuming;
  /**
   * Output only. [Output Only] The number of instances in the managed instance
   * group that are scheduled to be started or are currently being started.
   *
   * @var int
   */
  public $starting;
  /**
   * Output only. [Output Only] The number of instances in the managed instance
   * group that are scheduled to be stopped or are currently being stopped.
   *
   * @var int
   */
  public $stopping;
  /**
   * Output only. [Output Only] The number of instances in the managed instance
   * group that are scheduled to be suspended or are currently being suspended.
   *
   * @var int
   */
  public $suspending;
  /**
   * Output only. [Output Only] The number of instances in the managed instance
   * group that are being verified. See the managedInstances[].currentAction
   * property in the listManagedInstances method documentation.
   *
   * @var int
   */
  public $verifying;

  /**
   * Output only. [Output Only] The total number of instances in the managed
   * instance group that are scheduled to be abandoned. Abandoning an instance
   * removes it from the managed instance group without deleting it.
   *
   * @param int $abandoning
   */
  public function setAbandoning($abandoning)
  {
    $this->abandoning = $abandoning;
  }
  /**
   * @return int
   */
  public function getAbandoning()
  {
    return $this->abandoning;
  }
  /**
   * Output only. [Output Only] The number of instances in the managed instance
   * group that are scheduled to be created or are currently being created. If
   * the group fails to create any of these instances, it tries again until it
   * creates the instance successfully.
   *
   * If you have disabled creation retries, this field will not be populated;
   * instead, the creatingWithoutRetries field will be populated.
   *
   * @param int $creating
   */
  public function setCreating($creating)
  {
    $this->creating = $creating;
  }
  /**
   * @return int
   */
  public function getCreating()
  {
    return $this->creating;
  }
  /**
   * Output only. [Output Only] The number of instances that the managed
   * instance group will attempt to create. The group attempts to create each
   * instance only once. If the group fails to create any of these instances, it
   * decreases the group's targetSize value accordingly.
   *
   * @param int $creatingWithoutRetries
   */
  public function setCreatingWithoutRetries($creatingWithoutRetries)
  {
    $this->creatingWithoutRetries = $creatingWithoutRetries;
  }
  /**
   * @return int
   */
  public function getCreatingWithoutRetries()
  {
    return $this->creatingWithoutRetries;
  }
  /**
   * Output only. [Output Only] The number of instances in the managed instance
   * group that are scheduled to be deleted or are currently being deleted.
   *
   * @param int $deleting
   */
  public function setDeleting($deleting)
  {
    $this->deleting = $deleting;
  }
  /**
   * @return int
   */
  public function getDeleting()
  {
    return $this->deleting;
  }
  /**
   * Output only. [Output Only] The number of instances in the managed instance
   * group that are running and have no scheduled actions.
   *
   * @param int $none
   */
  public function setNone($none)
  {
    $this->none = $none;
  }
  /**
   * @return int
   */
  public function getNone()
  {
    return $this->none;
  }
  /**
   * Output only. [Output Only] The number of instances in the managed instance
   * group that are scheduled to be recreated or are currently being being
   * recreated. Recreating an instance deletes the existing root persistent disk
   * and creates a new disk from the image that is defined in the instance
   * template.
   *
   * @param int $recreating
   */
  public function setRecreating($recreating)
  {
    $this->recreating = $recreating;
  }
  /**
   * @return int
   */
  public function getRecreating()
  {
    return $this->recreating;
  }
  /**
   * Output only. [Output Only] The number of instances in the managed instance
   * group that are being reconfigured with properties that do not require a
   * restart or a recreate action. For example, setting or removing target pools
   * for the instance.
   *
   * @param int $refreshing
   */
  public function setRefreshing($refreshing)
  {
    $this->refreshing = $refreshing;
  }
  /**
   * @return int
   */
  public function getRefreshing()
  {
    return $this->refreshing;
  }
  /**
   * Output only. [Output Only] The number of instances in the managed instance
   * group that are scheduled to be restarted or are currently being restarted.
   *
   * @param int $restarting
   */
  public function setRestarting($restarting)
  {
    $this->restarting = $restarting;
  }
  /**
   * @return int
   */
  public function getRestarting()
  {
    return $this->restarting;
  }
  /**
   * Output only. [Output Only] The number of instances in the managed instance
   * group that are scheduled to be resumed or are currently being resumed.
   *
   * @param int $resuming
   */
  public function setResuming($resuming)
  {
    $this->resuming = $resuming;
  }
  /**
   * @return int
   */
  public function getResuming()
  {
    return $this->resuming;
  }
  /**
   * Output only. [Output Only] The number of instances in the managed instance
   * group that are scheduled to be started or are currently being started.
   *
   * @param int $starting
   */
  public function setStarting($starting)
  {
    $this->starting = $starting;
  }
  /**
   * @return int
   */
  public function getStarting()
  {
    return $this->starting;
  }
  /**
   * Output only. [Output Only] The number of instances in the managed instance
   * group that are scheduled to be stopped or are currently being stopped.
   *
   * @param int $stopping
   */
  public function setStopping($stopping)
  {
    $this->stopping = $stopping;
  }
  /**
   * @return int
   */
  public function getStopping()
  {
    return $this->stopping;
  }
  /**
   * Output only. [Output Only] The number of instances in the managed instance
   * group that are scheduled to be suspended or are currently being suspended.
   *
   * @param int $suspending
   */
  public function setSuspending($suspending)
  {
    $this->suspending = $suspending;
  }
  /**
   * @return int
   */
  public function getSuspending()
  {
    return $this->suspending;
  }
  /**
   * Output only. [Output Only] The number of instances in the managed instance
   * group that are being verified. See the managedInstances[].currentAction
   * property in the listManagedInstances method documentation.
   *
   * @param int $verifying
   */
  public function setVerifying($verifying)
  {
    $this->verifying = $verifying;
  }
  /**
   * @return int
   */
  public function getVerifying()
  {
    return $this->verifying;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InstanceGroupManagerActionsSummary::class, 'Google_Service_Compute_InstanceGroupManagerActionsSummary');
