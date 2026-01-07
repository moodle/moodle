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

namespace Google\Service\OnDemandScanning;

class UpgradeOccurrence extends \Google\Model
{
  protected $distributionType = UpgradeDistribution::class;
  protected $distributionDataType = '';
  /**
   * Required for non-Windows OS. The package this Upgrade is for.
   *
   * @var string
   */
  public $package;
  protected $parsedVersionType = Version::class;
  protected $parsedVersionDataType = '';
  protected $windowsUpdateType = WindowsUpdate::class;
  protected $windowsUpdateDataType = '';

  /**
   * Metadata about the upgrade for available for the specific operating system
   * for the resource_url. This allows efficient filtering, as well as making it
   * easier to use the occurrence.
   *
   * @param UpgradeDistribution $distribution
   */
  public function setDistribution(UpgradeDistribution $distribution)
  {
    $this->distribution = $distribution;
  }
  /**
   * @return UpgradeDistribution
   */
  public function getDistribution()
  {
    return $this->distribution;
  }
  /**
   * Required for non-Windows OS. The package this Upgrade is for.
   *
   * @param string $package
   */
  public function setPackage($package)
  {
    $this->package = $package;
  }
  /**
   * @return string
   */
  public function getPackage()
  {
    return $this->package;
  }
  /**
   * Required for non-Windows OS. The version of the package in a machine +
   * human readable form.
   *
   * @param Version $parsedVersion
   */
  public function setParsedVersion(Version $parsedVersion)
  {
    $this->parsedVersion = $parsedVersion;
  }
  /**
   * @return Version
   */
  public function getParsedVersion()
  {
    return $this->parsedVersion;
  }
  /**
   * Required for Windows OS. Represents the metadata about the Windows update.
   *
   * @param WindowsUpdate $windowsUpdate
   */
  public function setWindowsUpdate(WindowsUpdate $windowsUpdate)
  {
    $this->windowsUpdate = $windowsUpdate;
  }
  /**
   * @return WindowsUpdate
   */
  public function getWindowsUpdate()
  {
    return $this->windowsUpdate;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpgradeOccurrence::class, 'Google_Service_OnDemandScanning_UpgradeOccurrence');
