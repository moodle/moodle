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

class BackupPlanBinding extends \Google\Model
{
  /**
   * Output only. Immutable. The fully qualified name of the BackupPlan bound
   * with the parent BackupChannel.
   * `projects/locations/backupPlans/{backup_plan}`
   *
   * @var string
   */
  public $backupPlan;
  protected $backupPlanDetailsType = BackupPlanDetails::class;
  protected $backupPlanDetailsDataType = '';
  /**
   * Output only. Immutable. The fully qualified name of the cluster that is
   * being backed up Valid formats: - `projects/locations/clusters` -
   * `projects/zones/clusters`
   *
   * @var string
   */
  public $cluster;
  /**
   * Output only. The timestamp when this binding was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. `etag` is used for optimistic concurrency control as a way to
   * help prevent simultaneous updates of a BackupPlanBinding from overwriting
   * each other. It is strongly suggested that systems make use of the 'etag' in
   * the read-modify-write cycle to perform BackupPlanBinding updates in order
   * to avoid race conditions: An `etag` is returned in the response to
   * `GetBackupPlanBinding`, and systems are expected to put that etag in the
   * request to `UpdateBackupPlanBinding` or `DeleteBackupPlanBinding` to ensure
   * that their change will be applied to the same version of the resource.
   *
   * @var string
   */
  public $etag;
  /**
   * Identifier. The fully qualified name of the BackupPlanBinding.
   * `projects/locations/backupChannels/backupPlanBindings`
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Server generated global unique identifier of
   * [UUID4](https://en.wikipedia.org/wiki/Universally_unique_identifier)
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. The timestamp when this binding was created.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Immutable. The fully qualified name of the BackupPlan bound
   * with the parent BackupChannel.
   * `projects/locations/backupPlans/{backup_plan}`
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
   * Output only. Contains details about the backup plan/backup.
   *
   * @param BackupPlanDetails $backupPlanDetails
   */
  public function setBackupPlanDetails(BackupPlanDetails $backupPlanDetails)
  {
    $this->backupPlanDetails = $backupPlanDetails;
  }
  /**
   * @return BackupPlanDetails
   */
  public function getBackupPlanDetails()
  {
    return $this->backupPlanDetails;
  }
  /**
   * Output only. Immutable. The fully qualified name of the cluster that is
   * being backed up Valid formats: - `projects/locations/clusters` -
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
   * Output only. The timestamp when this binding was created.
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
   * Output only. `etag` is used for optimistic concurrency control as a way to
   * help prevent simultaneous updates of a BackupPlanBinding from overwriting
   * each other. It is strongly suggested that systems make use of the 'etag' in
   * the read-modify-write cycle to perform BackupPlanBinding updates in order
   * to avoid race conditions: An `etag` is returned in the response to
   * `GetBackupPlanBinding`, and systems are expected to put that etag in the
   * request to `UpdateBackupPlanBinding` or `DeleteBackupPlanBinding` to ensure
   * that their change will be applied to the same version of the resource.
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
   * Identifier. The fully qualified name of the BackupPlanBinding.
   * `projects/locations/backupChannels/backupPlanBindings`
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
   * Output only. Server generated global unique identifier of
   * [UUID4](https://en.wikipedia.org/wiki/Universally_unique_identifier)
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
   * Output only. The timestamp when this binding was created.
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
class_alias(BackupPlanBinding::class, 'Google_Service_BackupforGKE_BackupPlanBinding');
