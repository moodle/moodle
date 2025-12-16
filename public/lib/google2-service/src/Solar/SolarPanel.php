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

class SolarPanel extends \Google\Model
{
  /**
   * No panel orientation is known.
   */
  public const ORIENTATION_SOLAR_PANEL_ORIENTATION_UNSPECIFIED = 'SOLAR_PANEL_ORIENTATION_UNSPECIFIED';
  /**
   * A `LANDSCAPE` panel has its long edge perpendicular to the azimuth
   * direction of the roof segment that it is placed on.
   */
  public const ORIENTATION_LANDSCAPE = 'LANDSCAPE';
  /**
   * A `PORTRAIT` panel has its long edge parallel to the azimuth direction of
   * the roof segment that it is placed on.
   */
  public const ORIENTATION_PORTRAIT = 'PORTRAIT';
  protected $centerType = LatLng::class;
  protected $centerDataType = '';
  /**
   * The orientation of the panel.
   *
   * @var string
   */
  public $orientation;
  /**
   * Index in roof_segment_stats of the `RoofSegmentSizeAndSunshineStats` which
   * corresponds to the roof segment that this panel is placed on.
   *
   * @var int
   */
  public $segmentIndex;
  /**
   * How much sunlight energy this layout captures over the course of a year, in
   * DC kWh.
   *
   * @var float
   */
  public $yearlyEnergyDcKwh;

  /**
   * The centre of the panel.
   *
   * @param LatLng $center
   */
  public function setCenter(LatLng $center)
  {
    $this->center = $center;
  }
  /**
   * @return LatLng
   */
  public function getCenter()
  {
    return $this->center;
  }
  /**
   * The orientation of the panel.
   *
   * Accepted values: SOLAR_PANEL_ORIENTATION_UNSPECIFIED, LANDSCAPE, PORTRAIT
   *
   * @param self::ORIENTATION_* $orientation
   */
  public function setOrientation($orientation)
  {
    $this->orientation = $orientation;
  }
  /**
   * @return self::ORIENTATION_*
   */
  public function getOrientation()
  {
    return $this->orientation;
  }
  /**
   * Index in roof_segment_stats of the `RoofSegmentSizeAndSunshineStats` which
   * corresponds to the roof segment that this panel is placed on.
   *
   * @param int $segmentIndex
   */
  public function setSegmentIndex($segmentIndex)
  {
    $this->segmentIndex = $segmentIndex;
  }
  /**
   * @return int
   */
  public function getSegmentIndex()
  {
    return $this->segmentIndex;
  }
  /**
   * How much sunlight energy this layout captures over the course of a year, in
   * DC kWh.
   *
   * @param float $yearlyEnergyDcKwh
   */
  public function setYearlyEnergyDcKwh($yearlyEnergyDcKwh)
  {
    $this->yearlyEnergyDcKwh = $yearlyEnergyDcKwh;
  }
  /**
   * @return float
   */
  public function getYearlyEnergyDcKwh()
  {
    return $this->yearlyEnergyDcKwh;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SolarPanel::class, 'Google_Service_Solar_SolarPanel');
