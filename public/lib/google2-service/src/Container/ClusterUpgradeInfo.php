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

namespace Google\Service\Container;

class ClusterUpgradeInfo extends \Google\Collection
{
  protected $collection_key = 'upgradeDetails';
  /**
   * The auto upgrade status.
   *
   * @var string[]
   */
  public $autoUpgradeStatus;
  /**
   * The cluster's current minor version's end of extended support timestamp.
   *
   * @var string
   */
  public $endOfExtendedSupportTimestamp;
  /**
   * The cluster's current minor version's end of standard support timestamp.
   *
   * @var string
   */
  public $endOfStandardSupportTimestamp;
  /**
   * minor_target_version indicates the target version for minor upgrade.
   *
   * @var string
   */
  public $minorTargetVersion;
  /**
   * patch_target_version indicates the target version for patch upgrade.
   *
   * @var string
   */
  public $patchTargetVersion;
  /**
   * The auto upgrade paused reason.
   *
   * @var string[]
   */
  public $pausedReason;
  protected $upgradeDetailsType = UpgradeDetails::class;
  protected $upgradeDetailsDataType = 'array';

  /**
   * The auto upgrade status.
   *
   * @param string[] $autoUpgradeStatus
   */
  public function setAutoUpgradeStatus($autoUpgradeStatus)
  {
    $this->autoUpgradeStatus = $autoUpgradeStatus;
  }
  /**
   * @return string[]
   */
  public function getAutoUpgradeStatus()
  {
    return $this->autoUpgradeStatus;
  }
  /**
   * The cluster's current minor version's end of extended support timestamp.
   *
   * @param string $endOfExtendedSupportTimestamp
   */
  public function setEndOfExtendedSupportTimestamp($endOfExtendedSupportTimestamp)
  {
    $this->endOfExtendedSupportTimestamp = $endOfExtendedSupportTimestamp;
  }
  /**
   * @return string
   */
  public function getEndOfExtendedSupportTimestamp()
  {
    return $this->endOfExtendedSupportTimestamp;
  }
  /**
   * The cluster's current minor version's end of standard support timestamp.
   *
   * @param string $endOfStandardSupportTimestamp
   */
  public function setEndOfStandardSupportTimestamp($endOfStandardSupportTimestamp)
  {
    $this->endOfStandardSupportTimestamp = $endOfStandardSupportTimestamp;
  }
  /**
   * @return string
   */
  public function getEndOfStandardSupportTimestamp()
  {
    return $this->endOfStandardSupportTimestamp;
  }
  /**
   * minor_target_version indicates the target version for minor upgrade.
   *
   * @param string $minorTargetVersion
   */
  public function setMinorTargetVersion($minorTargetVersion)
  {
    $this->minorTargetVersion = $minorTargetVersion;
  }
  /**
   * @return string
   */
  public function getMinorTargetVersion()
  {
    return $this->minorTargetVersion;
  }
  /**
   * patch_target_version indicates the target version for patch upgrade.
   *
   * @param string $patchTargetVersion
   */
  public function setPatchTargetVersion($patchTargetVersion)
  {
    $this->patchTargetVersion = $patchTargetVersion;
  }
  /**
   * @return string
   */
  public function getPatchTargetVersion()
  {
    return $this->patchTargetVersion;
  }
  /**
   * The auto upgrade paused reason.
   *
   * @param string[] $pausedReason
   */
  public function setPausedReason($pausedReason)
  {
    $this->pausedReason = $pausedReason;
  }
  /**
   * @return string[]
   */
  public function getPausedReason()
  {
    return $this->pausedReason;
  }
  /**
   * The list of past auto upgrades.
   *
   * @param UpgradeDetails[] $upgradeDetails
   */
  public function setUpgradeDetails($upgradeDetails)
  {
    $this->upgradeDetails = $upgradeDetails;
  }
  /**
   * @return UpgradeDetails[]
   */
  public function getUpgradeDetails()
  {
    return $this->upgradeDetails;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ClusterUpgradeInfo::class, 'Google_Service_Container_ClusterUpgradeInfo');
