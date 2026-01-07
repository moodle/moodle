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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1LicenseConfigUsageStats extends \Google\Model
{
  /**
   * Required. The LicenseConfig name.
   *
   * @var string
   */
  public $licenseConfig;
  /**
   * Required. The number of licenses used.
   *
   * @var string
   */
  public $usedLicenseCount;

  /**
   * Required. The LicenseConfig name.
   *
   * @param string $licenseConfig
   */
  public function setLicenseConfig($licenseConfig)
  {
    $this->licenseConfig = $licenseConfig;
  }
  /**
   * @return string
   */
  public function getLicenseConfig()
  {
    return $this->licenseConfig;
  }
  /**
   * Required. The number of licenses used.
   *
   * @param string $usedLicenseCount
   */
  public function setUsedLicenseCount($usedLicenseCount)
  {
    $this->usedLicenseCount = $usedLicenseCount;
  }
  /**
   * @return string
   */
  public function getUsedLicenseCount()
  {
    return $this->usedLicenseCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1LicenseConfigUsageStats::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1LicenseConfigUsageStats');
