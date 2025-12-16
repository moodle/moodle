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

namespace Google\Service\DisplayVideo;

class ThirdPartyMeasurementConfigs extends \Google\Collection
{
  protected $collection_key = 'viewabilityVendorConfigs';
  protected $brandLiftVendorConfigsType = ThirdPartyVendorConfig::class;
  protected $brandLiftVendorConfigsDataType = 'array';
  protected $brandSafetyVendorConfigsType = ThirdPartyVendorConfig::class;
  protected $brandSafetyVendorConfigsDataType = 'array';
  protected $reachVendorConfigsType = ThirdPartyVendorConfig::class;
  protected $reachVendorConfigsDataType = 'array';
  protected $viewabilityVendorConfigsType = ThirdPartyVendorConfig::class;
  protected $viewabilityVendorConfigsDataType = 'array';

  /**
   * Optional. The third-party vendors measuring brand lift. The following
   * third-party vendors are applicable: * `THIRD_PARTY_VENDOR_DYNATA` *
   * `THIRD_PARTY_VENDOR_KANTAR` * `THIRD_PARTY_VENDOR_KANTAR_MILLWARD_BROWN` *
   * `THIRD_PARTY_VENDOR_GOOGLE_INTERNAL` * `THIRD_PARTY_VENDOR_INTAGE` *
   * `THIRD_PARTY_VENDOR_NIELSEN` * `THIRD_PARTY_VENDOR_MACROMILL`
   *
   * @param ThirdPartyVendorConfig[] $brandLiftVendorConfigs
   */
  public function setBrandLiftVendorConfigs($brandLiftVendorConfigs)
  {
    $this->brandLiftVendorConfigs = $brandLiftVendorConfigs;
  }
  /**
   * @return ThirdPartyVendorConfig[]
   */
  public function getBrandLiftVendorConfigs()
  {
    return $this->brandLiftVendorConfigs;
  }
  /**
   * Optional. The third-party vendors measuring brand safety. The following
   * third-party vendors are applicable: * `THIRD_PARTY_VENDOR_ZERF` *
   * `THIRD_PARTY_VENDOR_DOUBLE_VERIFY` *
   * `THIRD_PARTY_VENDOR_INTEGRAL_AD_SCIENCE` *
   * `THIRD_PARTY_VENDOR_GOOGLE_INTERNAL` * `THIRD_PARTY_VENDOR_ZEFR`
   *
   * @param ThirdPartyVendorConfig[] $brandSafetyVendorConfigs
   */
  public function setBrandSafetyVendorConfigs($brandSafetyVendorConfigs)
  {
    $this->brandSafetyVendorConfigs = $brandSafetyVendorConfigs;
  }
  /**
   * @return ThirdPartyVendorConfig[]
   */
  public function getBrandSafetyVendorConfigs()
  {
    return $this->brandSafetyVendorConfigs;
  }
  /**
   * Optional. The third-party vendors measuring reach. The following third-
   * party vendors are applicable: * `THIRD_PARTY_VENDOR_NIELSEN` *
   * `THIRD_PARTY_VENDOR_COMSCORE` * `THIRD_PARTY_VENDOR_KANTAR` *
   * `THIRD_PARTY_VENDOR_GOOGLE_INTERNAL` *
   * `THIRD_PARTY_VENDOR_KANTAR_MILLWARD_BROWN` *
   * `THIRD_PARTY_VENDOR_VIDEO_RESEARCH` * `THIRD_PARTY_VENDOR_MEDIA_SCOPE` *
   * `THIRD_PARTY_VENDOR_AUDIENCE_PROJECT` * `THIRD_PARTY_VENDOR_VIDEO_AMP` *
   * `THIRD_PARTY_VENDOR_ISPOT_TV`
   *
   * @param ThirdPartyVendorConfig[] $reachVendorConfigs
   */
  public function setReachVendorConfigs($reachVendorConfigs)
  {
    $this->reachVendorConfigs = $reachVendorConfigs;
  }
  /**
   * @return ThirdPartyVendorConfig[]
   */
  public function getReachVendorConfigs()
  {
    return $this->reachVendorConfigs;
  }
  /**
   * Optional. The third-party vendors measuring viewability. The following
   * third-party vendors are applicable: * `THIRD_PARTY_VENDOR_MOAT` *
   * `THIRD_PARTY_VENDOR_DOUBLE_VERIFY` *
   * `THIRD_PARTY_VENDOR_INTEGRAL_AD_SCIENCE` * `THIRD_PARTY_VENDOR_COMSCORE` *
   * `THIRD_PARTY_VENDOR_TELEMETRY` * `THIRD_PARTY_VENDOR_MEETRICS` *
   * `THIRD_PARTY_VENDOR_GOOGLE_INTERNAL`
   *
   * @param ThirdPartyVendorConfig[] $viewabilityVendorConfigs
   */
  public function setViewabilityVendorConfigs($viewabilityVendorConfigs)
  {
    $this->viewabilityVendorConfigs = $viewabilityVendorConfigs;
  }
  /**
   * @return ThirdPartyVendorConfig[]
   */
  public function getViewabilityVendorConfigs()
  {
    return $this->viewabilityVendorConfigs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ThirdPartyMeasurementConfigs::class, 'Google_Service_DisplayVideo_ThirdPartyMeasurementConfigs');
