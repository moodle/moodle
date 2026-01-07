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

namespace Google\Service\Dfareporting;

class TechnologyTargeting extends \Google\Collection
{
  protected $collection_key = 'platformTypes';
  protected $browsersType = Browser::class;
  protected $browsersDataType = 'array';
  protected $connectionTypesType = ConnectionType::class;
  protected $connectionTypesDataType = 'array';
  protected $mobileCarriersType = MobileCarrier::class;
  protected $mobileCarriersDataType = 'array';
  protected $operatingSystemVersionsType = OperatingSystemVersion::class;
  protected $operatingSystemVersionsDataType = 'array';
  protected $operatingSystemsType = OperatingSystem::class;
  protected $operatingSystemsDataType = 'array';
  protected $platformTypesType = PlatformType::class;
  protected $platformTypesDataType = 'array';

  /**
   * Browsers that this ad targets. For each browser either set browserVersionId
   * or dartId along with the version numbers. If both are specified, only
   * browserVersionId will be used. The other fields are populated automatically
   * when the ad is inserted or updated.
   *
   * @param Browser[] $browsers
   */
  public function setBrowsers($browsers)
  {
    $this->browsers = $browsers;
  }
  /**
   * @return Browser[]
   */
  public function getBrowsers()
  {
    return $this->browsers;
  }
  /**
   * Connection types that this ad targets. For each connection type only id is
   * required. The other fields are populated automatically when the ad is
   * inserted or updated.
   *
   * @param ConnectionType[] $connectionTypes
   */
  public function setConnectionTypes($connectionTypes)
  {
    $this->connectionTypes = $connectionTypes;
  }
  /**
   * @return ConnectionType[]
   */
  public function getConnectionTypes()
  {
    return $this->connectionTypes;
  }
  /**
   * Mobile carriers that this ad targets. For each mobile carrier only id is
   * required, and the other fields are populated automatically when the ad is
   * inserted or updated. If targeting a mobile carrier, do not set targeting
   * for any zip codes.
   *
   * @param MobileCarrier[] $mobileCarriers
   */
  public function setMobileCarriers($mobileCarriers)
  {
    $this->mobileCarriers = $mobileCarriers;
  }
  /**
   * @return MobileCarrier[]
   */
  public function getMobileCarriers()
  {
    return $this->mobileCarriers;
  }
  /**
   * Operating system versions that this ad targets. To target all versions, use
   * operatingSystems. For each operating system version, only id is required.
   * The other fields are populated automatically when the ad is inserted or
   * updated. If targeting an operating system version, do not set targeting for
   * the corresponding operating system in operatingSystems.
   *
   * @param OperatingSystemVersion[] $operatingSystemVersions
   */
  public function setOperatingSystemVersions($operatingSystemVersions)
  {
    $this->operatingSystemVersions = $operatingSystemVersions;
  }
  /**
   * @return OperatingSystemVersion[]
   */
  public function getOperatingSystemVersions()
  {
    return $this->operatingSystemVersions;
  }
  /**
   * Operating systems that this ad targets. To target specific versions, use
   * operatingSystemVersions. For each operating system only dartId is required.
   * The other fields are populated automatically when the ad is inserted or
   * updated. If targeting an operating system, do not set targeting for
   * operating system versions for the same operating system.
   *
   * @param OperatingSystem[] $operatingSystems
   */
  public function setOperatingSystems($operatingSystems)
  {
    $this->operatingSystems = $operatingSystems;
  }
  /**
   * @return OperatingSystem[]
   */
  public function getOperatingSystems()
  {
    return $this->operatingSystems;
  }
  /**
   * Platform types that this ad targets. For example, desktop, mobile, or
   * tablet. For each platform type, only id is required, and the other fields
   * are populated automatically when the ad is inserted or updated.
   *
   * @param PlatformType[] $platformTypes
   */
  public function setPlatformTypes($platformTypes)
  {
    $this->platformTypes = $platformTypes;
  }
  /**
   * @return PlatformType[]
   */
  public function getPlatformTypes()
  {
    return $this->platformTypes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TechnologyTargeting::class, 'Google_Service_Dfareporting_TechnologyTargeting');
