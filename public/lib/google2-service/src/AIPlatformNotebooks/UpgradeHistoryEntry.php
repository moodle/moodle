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

namespace Google\Service\AIPlatformNotebooks;

class UpgradeHistoryEntry extends \Google\Model
{
  /**
   * Operation is not specified.
   */
  public const ACTION_ACTION_UNSPECIFIED = 'ACTION_UNSPECIFIED';
  /**
   * Upgrade.
   */
  public const ACTION_UPGRADE = 'UPGRADE';
  /**
   * Rollback.
   */
  public const ACTION_ROLLBACK = 'ROLLBACK';
  /**
   * State is not specified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The instance upgrade is started.
   */
  public const STATE_STARTED = 'STARTED';
  /**
   * The instance upgrade is succeeded.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * The instance upgrade is failed.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * Optional. Action. Rolloback or Upgrade.
   *
   * @var string
   */
  public $action;
  /**
   * Optional. The container image before this instance upgrade.
   *
   * @var string
   */
  public $containerImage;
  /**
   * Immutable. The time that this instance upgrade history entry is created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. The framework of this notebook instance.
   *
   * @var string
   */
  public $framework;
  /**
   * Optional. The snapshot of the boot disk of this notebook instance before
   * upgrade.
   *
   * @var string
   */
  public $snapshot;
  /**
   * Output only. The state of this instance upgrade history entry.
   *
   * @var string
   */
  public $state;
  /**
   * Optional. Target VM Version, like m63.
   *
   * @var string
   */
  public $targetVersion;
  /**
   * Optional. The version of the notebook instance before this upgrade.
   *
   * @var string
   */
  public $version;
  /**
   * Optional. The VM image before this instance upgrade.
   *
   * @var string
   */
  public $vmImage;

  /**
   * Optional. Action. Rolloback or Upgrade.
   *
   * Accepted values: ACTION_UNSPECIFIED, UPGRADE, ROLLBACK
   *
   * @param self::ACTION_* $action
   */
  public function setAction($action)
  {
    $this->action = $action;
  }
  /**
   * @return self::ACTION_*
   */
  public function getAction()
  {
    return $this->action;
  }
  /**
   * Optional. The container image before this instance upgrade.
   *
   * @param string $containerImage
   */
  public function setContainerImage($containerImage)
  {
    $this->containerImage = $containerImage;
  }
  /**
   * @return string
   */
  public function getContainerImage()
  {
    return $this->containerImage;
  }
  /**
   * Immutable. The time that this instance upgrade history entry is created.
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
   * Optional. The framework of this notebook instance.
   *
   * @param string $framework
   */
  public function setFramework($framework)
  {
    $this->framework = $framework;
  }
  /**
   * @return string
   */
  public function getFramework()
  {
    return $this->framework;
  }
  /**
   * Optional. The snapshot of the boot disk of this notebook instance before
   * upgrade.
   *
   * @param string $snapshot
   */
  public function setSnapshot($snapshot)
  {
    $this->snapshot = $snapshot;
  }
  /**
   * @return string
   */
  public function getSnapshot()
  {
    return $this->snapshot;
  }
  /**
   * Output only. The state of this instance upgrade history entry.
   *
   * Accepted values: STATE_UNSPECIFIED, STARTED, SUCCEEDED, FAILED
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
   * Optional. Target VM Version, like m63.
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
   * Optional. The version of the notebook instance before this upgrade.
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
  /**
   * Optional. The VM image before this instance upgrade.
   *
   * @param string $vmImage
   */
  public function setVmImage($vmImage)
  {
    $this->vmImage = $vmImage;
  }
  /**
   * @return string
   */
  public function getVmImage()
  {
    return $this->vmImage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpgradeHistoryEntry::class, 'Google_Service_AIPlatformNotebooks_UpgradeHistoryEntry');
