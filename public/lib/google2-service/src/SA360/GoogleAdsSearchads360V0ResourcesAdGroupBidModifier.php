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

namespace Google\Service\SA360;

class GoogleAdsSearchads360V0ResourcesAdGroupBidModifier extends \Google\Model
{
  /**
   * The modifier for the bid when the criterion matches. The modifier must be
   * in the range: 0.1 - 10.0. Use 0 to opt out of a Device type.
   *
   * @var 
   */
  public $bidModifier;
  protected $deviceType = GoogleAdsSearchads360V0CommonDeviceInfo::class;
  protected $deviceDataType = '';
  /**
   * Immutable. The resource name of the ad group bid modifier. Ad group bid
   * modifier resource names have the form:
   * `customers/{customer_id}/adGroupBidModifiers/{ad_group_id}~{criterion_id}`
   *
   * @var string
   */
  public $resourceName;

  public function setBidModifier($bidModifier)
  {
    $this->bidModifier = $bidModifier;
  }
  public function getBidModifier()
  {
    return $this->bidModifier;
  }
  /**
   * Immutable. A device criterion.
   *
   * @param GoogleAdsSearchads360V0CommonDeviceInfo $device
   */
  public function setDevice(GoogleAdsSearchads360V0CommonDeviceInfo $device)
  {
    $this->device = $device;
  }
  /**
   * @return GoogleAdsSearchads360V0CommonDeviceInfo
   */
  public function getDevice()
  {
    return $this->device;
  }
  /**
   * Immutable. The resource name of the ad group bid modifier. Ad group bid
   * modifier resource names have the form:
   * `customers/{customer_id}/adGroupBidModifiers/{ad_group_id}~{criterion_id}`
   *
   * @param string $resourceName
   */
  public function setResourceName($resourceName)
  {
    $this->resourceName = $resourceName;
  }
  /**
   * @return string
   */
  public function getResourceName()
  {
    return $this->resourceName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0ResourcesAdGroupBidModifier::class, 'Google_Service_SA360_GoogleAdsSearchads360V0ResourcesAdGroupBidModifier');
