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

class CarrierAndIspTargetingOptionDetails extends \Google\Model
{
  /**
   * Default value when type is not specified or is unknown in this version.
   */
  public const TYPE_CARRIER_AND_ISP_TYPE_UNSPECIFIED = 'CARRIER_AND_ISP_TYPE_UNSPECIFIED';
  /**
   * Indicates this targeting resource refers to an ISP.
   */
  public const TYPE_CARRIER_AND_ISP_TYPE_ISP = 'CARRIER_AND_ISP_TYPE_ISP';
  /**
   * Indicates this targeting resource refers to a mobile carrier.
   */
  public const TYPE_CARRIER_AND_ISP_TYPE_CARRIER = 'CARRIER_AND_ISP_TYPE_CARRIER';
  /**
   * Output only. The display name of the carrier or ISP.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. The type indicating if it's carrier or ISP.
   *
   * @var string
   */
  public $type;

  /**
   * Output only. The display name of the carrier or ISP.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Output only. The type indicating if it's carrier or ISP.
   *
   * Accepted values: CARRIER_AND_ISP_TYPE_UNSPECIFIED,
   * CARRIER_AND_ISP_TYPE_ISP, CARRIER_AND_ISP_TYPE_CARRIER
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CarrierAndIspTargetingOptionDetails::class, 'Google_Service_DisplayVideo_CarrierAndIspTargetingOptionDetails');
