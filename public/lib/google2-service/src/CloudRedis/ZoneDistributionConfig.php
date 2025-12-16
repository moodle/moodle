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

namespace Google\Service\CloudRedis;

class ZoneDistributionConfig extends \Google\Model
{
  /**
   * Not Set. Default: MULTI_ZONE
   */
  public const MODE_ZONE_DISTRIBUTION_MODE_UNSPECIFIED = 'ZONE_DISTRIBUTION_MODE_UNSPECIFIED';
  /**
   * Distribute all resources across 3 zones picked at random, within the
   * region.
   */
  public const MODE_MULTI_ZONE = 'MULTI_ZONE';
  /**
   * Distribute all resources in a single zone. The zone field must be
   * specified, when this mode is selected.
   */
  public const MODE_SINGLE_ZONE = 'SINGLE_ZONE';
  /**
   * Optional. The mode of zone distribution. Defaults to MULTI_ZONE, when not
   * specified.
   *
   * @var string
   */
  public $mode;
  /**
   * Optional. When SINGLE ZONE distribution is selected, zone field would be
   * used to allocate all resources in that zone. This is not applicable to
   * MULTI_ZONE, and would be ignored for MULTI_ZONE clusters.
   *
   * @var string
   */
  public $zone;

  /**
   * Optional. The mode of zone distribution. Defaults to MULTI_ZONE, when not
   * specified.
   *
   * Accepted values: ZONE_DISTRIBUTION_MODE_UNSPECIFIED, MULTI_ZONE,
   * SINGLE_ZONE
   *
   * @param self::MODE_* $mode
   */
  public function setMode($mode)
  {
    $this->mode = $mode;
  }
  /**
   * @return self::MODE_*
   */
  public function getMode()
  {
    return $this->mode;
  }
  /**
   * Optional. When SINGLE ZONE distribution is selected, zone field would be
   * used to allocate all resources in that zone. This is not applicable to
   * MULTI_ZONE, and would be ignored for MULTI_ZONE clusters.
   *
   * @param string $zone
   */
  public function setZone($zone)
  {
    $this->zone = $zone;
  }
  /**
   * @return string
   */
  public function getZone()
  {
    return $this->zone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ZoneDistributionConfig::class, 'Google_Service_CloudRedis_ZoneDistributionConfig');
