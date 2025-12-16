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

namespace Google\Service\VMwareEngine;

class Upgrade extends \Google\Collection
{
  /**
   * The default value. This value should never be used.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The upgrade is scheduled but not started yet.
   */
  public const STATE_SCHEDULED = 'SCHEDULED';
  /**
   * The upgrade is currently in progress and has not completed yet.
   */
  public const STATE_ONGOING = 'ONGOING';
  /**
   * The upgrade completed successfully.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * The upgrade is currently paused.
   */
  public const STATE_PAUSED = 'PAUSED';
  /**
   * The upgrade failed.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The upgrade is in process of being canceled.
   */
  public const STATE_CANCELLING = 'CANCELLING';
  /**
   * The upgrade is canceled.
   */
  public const STATE_CANCELLED = 'CANCELLED';
  /**
   * The upgrade is in process of being rescheduled.
   */
  public const STATE_RESCHEDULING = 'RESCHEDULING';
  /**
   * The default value. This value should never be used.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Upgrade of vmware components when a major version is available. 7.0u2 ->
   * 7.0u3.
   */
  public const TYPE_VSPHERE_UPGRADE = 'VSPHERE_UPGRADE';
  /**
   * Patching of vmware components when a minor version is available. 7.0u2c ->
   * 7.0u2d.
   */
  public const TYPE_VSPHERE_PATCH = 'VSPHERE_PATCH';
  /**
   * Workarounds are hotfixes for vulnerabilities or issues applied to mitigate
   * the known vulnerability or issue until a patch or update is released. The
   * description of the upgrade will have more details.
   */
  public const TYPE_WORKAROUND = 'WORKAROUND';
  /**
   * Firmware upgrade for VMware product used in the private cloud.
   */
  public const TYPE_FIRMWARE_UPGRADE = 'FIRMWARE_UPGRADE';
  /**
   * Switch upgrade.
   */
  public const TYPE_SWITCH_UPGRADE = 'SWITCH_UPGRADE';
  /**
   * The upgrade type that doesn't fall into any other category.
   */
  public const TYPE_OTHER = 'OTHER';
  /**
   * Infrastructure upgrade in BM node maintenance.
   */
  public const TYPE_INFRASTRUCTURE_UPGRADE = 'INFRASTRUCTURE_UPGRADE';
  protected $collection_key = 'componentUpgrades';
  protected $componentUpgradesType = VmwareUpgradeComponent::class;
  protected $componentUpgradesDataType = 'array';
  /**
   * Output only. Output Only. Creation time of this resource.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. Output Only. The description of the upgrade. This is used to
   * provide additional information about the private cloud upgrade, such as the
   * upgrade's purpose, the changes included in the upgrade, or any other
   * relevant information about the upgrade.
   *
   * @var string
   */
  public $description;
  /**
   * Output only. Output Only. End time of the upgrade.
   *
   * @var string
   */
  public $endTime;
  /**
   * Output only. Output Only. The estimated total duration of the upgrade. This
   * information can be used to plan or schedule upgrades to minimize
   * disruptions. Please note that the estimated duration is only an estimate.
   * The actual upgrade duration may vary.
   *
   * @var string
   */
  public $estimatedDuration;
  /**
   * The etag for the upgrade resource. If this is provided on update, it must
   * match the server's etag.
   *
   * @var string
   */
  public $etag;
  /**
   * Output only. Identifier. The resource name of the private cloud `Upgrade`.
   * Resource names are schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-west1-a/privateClouds/my-
   * cloud/upgrades/my-upgrade`
   *
   * @var string
   */
  public $name;
  protected $scheduleType = Schedule::class;
  protected $scheduleDataType = '';
  /**
   * Output only. Output Only. The start version
   *
   * @var string
   */
  public $startVersion;
  /**
   * Output only. The current state of the upgrade.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Output Only. The target version
   *
   * @var string
   */
  public $targetVersion;
  /**
   * Output only. Output Only. The type of upgrade.
   *
   * @var string
   */
  public $type;
  /**
   * Output only. System-generated unique identifier for the resource.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. Output Only. Last update time of this resource.
   *
   * @var string
   */
  public $updateTime;
  /**
   * Output only.
   *
   * @var string
   */
  public $version;

  /**
   * Output only. Output Only. The list of component upgrades.
   *
   * @param VmwareUpgradeComponent[] $componentUpgrades
   */
  public function setComponentUpgrades($componentUpgrades)
  {
    $this->componentUpgrades = $componentUpgrades;
  }
  /**
   * @return VmwareUpgradeComponent[]
   */
  public function getComponentUpgrades()
  {
    return $this->componentUpgrades;
  }
  /**
   * Output only. Output Only. Creation time of this resource.
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
   * Output only. Output Only. The description of the upgrade. This is used to
   * provide additional information about the private cloud upgrade, such as the
   * upgrade's purpose, the changes included in the upgrade, or any other
   * relevant information about the upgrade.
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
   * Output only. Output Only. End time of the upgrade.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * Output only. Output Only. The estimated total duration of the upgrade. This
   * information can be used to plan or schedule upgrades to minimize
   * disruptions. Please note that the estimated duration is only an estimate.
   * The actual upgrade duration may vary.
   *
   * @param string $estimatedDuration
   */
  public function setEstimatedDuration($estimatedDuration)
  {
    $this->estimatedDuration = $estimatedDuration;
  }
  /**
   * @return string
   */
  public function getEstimatedDuration()
  {
    return $this->estimatedDuration;
  }
  /**
   * The etag for the upgrade resource. If this is provided on update, it must
   * match the server's etag.
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
   * Output only. Identifier. The resource name of the private cloud `Upgrade`.
   * Resource names are schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-west1-a/privateClouds/my-
   * cloud/upgrades/my-upgrade`
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
   * Schedule details for the upgrade.
   *
   * @param Schedule $schedule
   */
  public function setSchedule(Schedule $schedule)
  {
    $this->schedule = $schedule;
  }
  /**
   * @return Schedule
   */
  public function getSchedule()
  {
    return $this->schedule;
  }
  /**
   * Output only. Output Only. The start version
   *
   * @param string $startVersion
   */
  public function setStartVersion($startVersion)
  {
    $this->startVersion = $startVersion;
  }
  /**
   * @return string
   */
  public function getStartVersion()
  {
    return $this->startVersion;
  }
  /**
   * Output only. The current state of the upgrade.
   *
   * Accepted values: STATE_UNSPECIFIED, SCHEDULED, ONGOING, SUCCEEDED, PAUSED,
   * FAILED, CANCELLING, CANCELLED, RESCHEDULING
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
   * Output only. Output Only. The target version
   *
   * @param string $targetVersion
   */
  public function setTargetVersion($targetVersion)
  {
    $this->targetVersion = $targetVersion;
  }
  /**
   * @return string
   */
  public function getTargetVersion()
  {
    return $this->targetVersion;
  }
  /**
   * Output only. Output Only. The type of upgrade.
   *
   * Accepted values: TYPE_UNSPECIFIED, VSPHERE_UPGRADE, VSPHERE_PATCH,
   * WORKAROUND, FIRMWARE_UPGRADE, SWITCH_UPGRADE, OTHER, INFRASTRUCTURE_UPGRADE
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Output only. System-generated unique identifier for the resource.
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
   * Output only. Output Only. Last update time of this resource.
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
  /**
   * Output only.
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Upgrade::class, 'Google_Service_VMwareEngine_Upgrade');
