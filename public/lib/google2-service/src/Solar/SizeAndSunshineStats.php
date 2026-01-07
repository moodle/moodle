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

namespace Google\Service\Solar;

class SizeAndSunshineStats extends \Google\Collection
{
  protected $collection_key = 'sunshineQuantiles';
  /**
   * The area of the roof or roof segment, in m^2. This is the roof area
   * (accounting for tilt), not the ground footprint area.
   *
   * @var float
   */
  public $areaMeters2;
  /**
   * The ground footprint area covered by the roof or roof segment, in m^2.
   *
   * @var float
   */
  public $groundAreaMeters2;
  /**
   * Quantiles of the pointwise sunniness across the area. If there are N values
   * here, this represents the (N-1)-iles. For example, if there are 5 values,
   * then they would be the quartiles (min, 25%, 50%, 75%, max). Values are in
   * annual kWh/kW like max_sunshine_hours_per_year.
   *
   * @var float[]
   */
  public $sunshineQuantiles;

  /**
   * The area of the roof or roof segment, in m^2. This is the roof area
   * (accounting for tilt), not the ground footprint area.
   *
   * @param float $areaMeters2
   */
  public function setAreaMeters2($areaMeters2)
  {
    $this->areaMeters2 = $areaMeters2;
  }
  /**
   * @return float
   */
  public function getAreaMeters2()
  {
    return $this->areaMeters2;
  }
  /**
   * The ground footprint area covered by the roof or roof segment, in m^2.
   *
   * @param float $groundAreaMeters2
   */
  public function setGroundAreaMeters2($groundAreaMeters2)
  {
    $this->groundAreaMeters2 = $groundAreaMeters2;
  }
  /**
   * @return float
   */
  public function getGroundAreaMeters2()
  {
    return $this->groundAreaMeters2;
  }
  /**
   * Quantiles of the pointwise sunniness across the area. If there are N values
   * here, this represents the (N-1)-iles. For example, if there are 5 values,
   * then they would be the quartiles (min, 25%, 50%, 75%, max). Values are in
   * annual kWh/kW like max_sunshine_hours_per_year.
   *
   * @param float[] $sunshineQuantiles
   */
  public function setSunshineQuantiles($sunshineQuantiles)
  {
    $this->sunshineQuantiles = $sunshineQuantiles;
  }
  /**
   * @return float[]
   */
  public function getSunshineQuantiles()
  {
    return $this->sunshineQuantiles;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SizeAndSunshineStats::class, 'Google_Service_Solar_SizeAndSunshineStats');
