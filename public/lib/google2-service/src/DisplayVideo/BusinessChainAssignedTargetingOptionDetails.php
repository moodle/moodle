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

class BusinessChainAssignedTargetingOptionDetails extends \Google\Model
{
  /**
   * Type value is not specified or is unknown in this version.
   */
  public const PROXIMITY_RADIUS_UNIT_DISTANCE_UNIT_UNSPECIFIED = 'DISTANCE_UNIT_UNSPECIFIED';
  /**
   * Miles.
   */
  public const PROXIMITY_RADIUS_UNIT_DISTANCE_UNIT_MILES = 'DISTANCE_UNIT_MILES';
  /**
   * Kilometers.
   */
  public const PROXIMITY_RADIUS_UNIT_DISTANCE_UNIT_KILOMETERS = 'DISTANCE_UNIT_KILOMETERS';
  /**
   * Output only. The display name of a business chain, e.g. "KFC", "Chase
   * Bank".
   *
   * @var string
   */
  public $displayName;
  /**
   * Required. The radius of the area around the business chain that will be
   * targeted. The units of the radius are specified by proximity_radius_unit.
   * Must be 1 to 800 if unit is `DISTANCE_UNIT_KILOMETERS` and 1 to 500 if unit
   * is `DISTANCE_UNIT_MILES`. The minimum increment for both cases is 0.1.
   * Inputs will be rounded to the nearest acceptable value if it is too
   * granular, e.g. 15.57 will become 15.6.
   *
   * @var 
   */
  public $proximityRadiusAmount;
  /**
   * Required. The unit of distance by which the targeting radius is measured.
   *
   * @var string
   */
  public $proximityRadiusUnit;
  /**
   * Required. The targeting_option_id of a TargetingOption of type
   * `TARGETING_TYPE_BUSINESS_CHAIN`. Accepted business chain targeting option
   * IDs can be retrieved using SearchTargetingOptions.
   *
   * @var string
   */
  public $targetingOptionId;

  /**
   * Output only. The display name of a business chain, e.g. "KFC", "Chase
   * Bank".
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
  public function setProximityRadiusAmount($proximityRadiusAmount)
  {
    $this->proximityRadiusAmount = $proximityRadiusAmount;
  }
  public function getProximityRadiusAmount()
  {
    return $this->proximityRadiusAmount;
  }
  /**
   * Required. The unit of distance by which the targeting radius is measured.
   *
   * Accepted values: DISTANCE_UNIT_UNSPECIFIED, DISTANCE_UNIT_MILES,
   * DISTANCE_UNIT_KILOMETERS
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
  /**
   * Required. The targeting_option_id of a TargetingOption of type
   * `TARGETING_TYPE_BUSINESS_CHAIN`. Accepted business chain targeting option
   * IDs can be retrieved using SearchTargetingOptions.
   *
   * @param string $targetingOptionId
   */
  public function setTargetingOptionId($targetingOptionId)
  {
    $this->targetingOptionId = $targetingOptionId;
  }
  /**
   * @return string
   */
  public function getTargetingOptionId()
  {
    return $this->targetingOptionId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BusinessChainAssignedTargetingOptionDetails::class, 'Google_Service_DisplayVideo_BusinessChainAssignedTargetingOptionDetails');
