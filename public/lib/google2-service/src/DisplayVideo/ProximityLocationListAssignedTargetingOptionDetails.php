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

class ProximityLocationListAssignedTargetingOptionDetails extends \Google\Model
{
  /**
   * Default value when distance units is not specified in this version. This
   * enum is a place holder for default value and does not represent a real
   * distance unit.
   */
  public const PROXIMITY_RADIUS_UNIT_PROXIMITY_RADIUS_UNIT_UNSPECIFIED = 'PROXIMITY_RADIUS_UNIT_UNSPECIFIED';
  /**
   * Radius distance unit in miles.
   */
  public const PROXIMITY_RADIUS_UNIT_PROXIMITY_RADIUS_UNIT_MILES = 'PROXIMITY_RADIUS_UNIT_MILES';
  /**
   * Radius distance unit in kilometeres
   */
  public const PROXIMITY_RADIUS_UNIT_PROXIMITY_RADIUS_UNIT_KILOMETERS = 'PROXIMITY_RADIUS_UNIT_KILOMETERS';
  /**
   * Required. ID of the proximity location list. Should refer to the
   * location_list_id field of a LocationList resource whose type is
   * `TARGETING_LOCATION_TYPE_PROXIMITY`.
   *
   * @var string
   */
  public $proximityLocationListId;
  /**
   * Required. Radius expressed in the distance units set in
   * proximity_radius_unit. This represents the size of the area around a chosen
   * location that will be targeted. Radius should be between 1 and 500 miles or
   * 800 kilometers.
   *
   * @var 
   */
  public $proximityRadius;
  /**
   * Required. Radius distance units.
   *
   * @var string
   */
  public $proximityRadiusUnit;

  /**
   * Required. ID of the proximity location list. Should refer to the
   * location_list_id field of a LocationList resource whose type is
   * `TARGETING_LOCATION_TYPE_PROXIMITY`.
   *
   * @param string $proximityLocationListId
   */
  public function setProximityLocationListId($proximityLocationListId)
  {
    $this->proximityLocationListId = $proximityLocationListId;
  }
  /**
   * @return string
   */
  public function getProximityLocationListId()
  {
    return $this->proximityLocationListId;
  }
  public function setProximityRadius($proximityRadius)
  {
    $this->proximityRadius = $proximityRadius;
  }
  public function getProximityRadius()
  {
    return $this->proximityRadius;
  }
  /**
   * Required. Radius distance units.
   *
   * Accepted values: PROXIMITY_RADIUS_UNIT_UNSPECIFIED,
   * PROXIMITY_RADIUS_UNIT_MILES, PROXIMITY_RADIUS_UNIT_KILOMETERS
   *
   * @param self::PROXIMITY_RADIUS_UNIT_* $proximityRadiusUnit
   */
  public function setProximityRadiusUnit($proximityRadiusUnit)
  {
    $this->proximityRadiusUnit = $proximityRadiusUnit;
  }
  /**
   * @return self::PROXIMITY_RADIUS_UNIT_*
   */
  public function getProximityRadiusUnit()
  {
    return $this->proximityRadiusUnit;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProximityLocationListAssignedTargetingOptionDetails::class, 'Google_Service_DisplayVideo_ProximityLocationListAssignedTargetingOptionDetails');
