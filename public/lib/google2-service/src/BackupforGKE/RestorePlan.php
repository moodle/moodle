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

namespace Google\Service\BackupforGKE;

class RestorePlan extends \Google\Model
{
  /**
   * Default first value for Enums.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Waiting for cluster state to be RUNNING.
   */
  public const STATE_CLUSTER_PENDING = 'CLUSTER_PENDING';
  /**
   * The RestorePlan has successfully been created and is ready for Restores.
   */
  public const STATE_READY = 'READY';
  /**
   * RestorePlan creation has failed.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The RestorePlan is in the process of being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * Required. Immutable. A reference to the BackupPlan from which Backups may
   * be used as the source for Restores created via this RestorePlan. Format:
   * `projects/locations/backupPlans`.
   *
   * @var string
   */
  public $backupPlan;
  /**
   * Required. Immutable. The target cluster into which Restores created via
   * this RestorePlan will restore data. NOTE: the cluster's region must be the
   * same as the RestorePlan. Valid formats: - `projects/locations/clusters` -
   * `projects/zones/clusters`
   *
   * @var string
   */
  public $cluster;
  /**
   * Output only. The timestamp when this RestorePlan resource was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. User specified descriptive string for this RestorePlan.
   *
   * @var string
   */
  public $description;
  /**
   * Output only. `etag` is used for optimistic concurrency control as a way to
   * help prevent simultaneous updates of a restore from overwriting each other.
   * It is strongly suggested that systems make use of the `etag` in the read-
   * modify-write cycle to perform restore updates in order to avoid race
   * conditions: An `etag` is returned in the response to `GetRestorePlan`, and
   * systems are expected to put that etag in the request to `UpdateRestorePlan`
   * or `DeleteRestorePlan` to ensure that their change will be applied to the
   * same version of the resource.
   *
   * @var string
   */
  public $etag;
  /**
   * Optional. A set of custom labels supplied by user.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. Identifier. The full name of the RestorePlan resource. Format:
   * `projects/locations/restorePlans`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The fully qualified name of the RestoreChannel to be used to
   * create a RestorePlan. This field is set only if the `backup_plan` is in a
   * different project than the RestorePlan. Format:
   * `projects/locations/restoreChannels`
   *
   * @var string
   */
  public $restoreChannel;
  protected $restoreConfigType = RestoreConfig::class;
  protected $restoreConfigDataType = '';
  /**
   * Output only. State of the RestorePlan. This State field reflects the
   * various stages a RestorePlan can be in during the Create operation.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Human-readable description of why RestorePlan is in the
   * current `state`. This field is only meant for human readability and should
   * not be used programmatically as this field is not guaranteed to be
   * consistent.
   *
   * @var string
   */
  public $stateReason;
  /**
   * Output only. Server generated global unique identifier of
   * [UUID](https://en.wikipedia.org/wiki/Universally_unique_identifier) format.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. The timestamp when this RestorePlan resource was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Required. Immutable. A reference to the BackupPlan from which Backups may
   * be used as the source for Restores created via this RestorePlan. Format:
   * `projects/locations/backupPlans`.
   *
   * @param string $backupPlan
   */
  public function setBackupPlan($backupPlan)
  {
    $this->backupPlan = $backupPlan;
  }
  /**
   * @return string
   */
  public function getBackupPlan()
  {
    return $this->backupPlan;
  }
  /**
   * Required. Immutable. The target cluster into which Restores created via
   * this RestorePlan will restore data. NOTE: the cluster's region must be the
   * same as the RestorePlan. Valid formats: - `projects/locations/clusters` -
   * `projects/zones/clusters`
   *
   * @param string $cluster
   */
  public function setCluster($cluster)
  {
    $this->cluster = $cluster;
  }
  /**
   * @return string
   */
  public function getCluster()
  {
    return $this->cluster;
  }
  /**
   * Output only. The timestamp when this RestorePlan resource was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Optional. User specified descriptive string for this RestorePlan.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Output only. `etag` is used for optimistic concurrency control as a way to
   * help prevent simultaneous updates of a restore from overwriting each other.
   * It is strongly suggested that systems make use of the `etag` in the read-
   * modify-write cycle to perform restore updates in order to avoid race
   * conditions: An `etag` is returned in the response to `GetRestorePlan`, and
   * systems are expected to put that etag in the request to `UpdateRestorePlan`
   * or `DeleteRestorePlan` to ensure that their change will be applied to the
   * same version of the resource.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Optional. A set of custom labels supplied by user.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Output only. Identifier. The full name of the RestorePlan resource. Format:
   * `projects/locations/restorePlans`.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Output only. The fully qualified name of the RestoreChannel to be used to
   * create a RestorePlan. This field is set only if the `backup_plan` is in a
   * different project than the RestorePlan. Format:
   * `projects/locations/restoreChannels`
   *
   * @param string $restoreChannel
   */
  public function setRestoreChannel($restoreChannel)
  {
    $this->restoreChannel = $restoreChannel;
  }
  /**
   * @return string
   */
  public function getRestoreChannel()
  {
    return $this->restoreChannel;
  }
  /**
   * Required. Configuration of Restores created via this RestorePlan.
   *
   * @param RestoreConfig $restoreConfig
   */
  public function setRestoreConfig(RestoreConfig $restoreConfig)
  {
    $this->restoreConfig = $restoreConfig;
  }
  /**
   * @return RestoreConfig
   */
  public function getRestoreConfig()
  {
    return $this->restoreConfig;
  }
  /**
   * Output only. State of the RestorePlan. This State field reflects the
   * various stages a RestorePlan can be in during the Create operation.
   *
   * Accepted values: STATE_UNSPECIFIED, CLUSTER_PENDING, READY, FAILED,
   * DELETING
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Output only. Human-readable description of why RestorePlan is in the
   * current `state`. This field is only meant for human readability and should
   * not be used programmatically as this field is not guaranteed to be
   * consistent.
   *
   * @param string $stateReason
   */
  public function setStateReason($stateReason)
  {
    $this->stateReason = $stateReason;
  }
  /**
   * @return string
   */
  public function getStateReason()
  {
    return $this->stateReason;
  }
  /**
   * Output only. Server generated global unique identifier of
   * [UUID](https://en.wikipedia.org/wiki/Universally_unique_identifier) format.
   *
   * @param string $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return string
   */
  public function getUid()
  {
    return $this->uid;
  }
  /**
   * Output only. The timestamp when this RestorePlan resource was last updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RestorePlan::class, 'Google_Service_BackupforGKE_RestorePlan');
