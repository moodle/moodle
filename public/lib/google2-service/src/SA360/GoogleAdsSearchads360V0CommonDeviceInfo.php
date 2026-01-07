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

class GoogleAdsSearchads360V0CommonDeviceInfo extends \Google\Model
{
  /**
   * Not specified.
   */
  public const TYPE_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * The value is unknown in this version.
   */
  public const TYPE_UNKNOWN = 'UNKNOWN';
  /**
   * Mobile devices with full browsers.
   */
  public const TYPE_MOBILE = 'MOBILE';
  /**
   * Tablets with full browsers.
   */
  public const TYPE_TABLET = 'TABLET';
  /**
   * Computers.
   */
  public const TYPE_DESKTOP = 'DESKTOP';
  /**
   * Smart TVs and game consoles.
   */
  public const TYPE_CONNECTED_TV = 'CONNECTED_TV';
  /**
   * Other device types.
   */
  public const TYPE_OTHER = 'OTHER';
  /**
   * Type of the device.
   *
   * @var string
   */
  public $type;

  /**
   * Type of the device.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, MOBILE, TABLET, DESKTOP,
   * CONNECTED_TV, OTHER
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
class_alias(GoogleAdsSearchads360V0CommonDeviceInfo::class, 'Google_Service_SA360_GoogleAdsSearchads360V0CommonDeviceInfo');
