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

class RoofSegmentSummary extends \Google\Model
{
  /**
   * Compass direction the roof segment is pointing in. 0 = North, 90 = East,
   * 180 = South. For a "flat" roof segment (`pitch_degrees` very near 0),
   * azimuth is not well defined, so for consistency, we define it arbitrarily
   * to be 0 (North).
   *
   * @var float
   */
  public $azimuthDegrees;
  /**
   * The total number of panels on this segment.
   *
   * @var int
   */
  public $panelsCount;
  /**
   * Angle of the roof segment relative to the theoretical ground plane. 0 =
   * parallel to the ground, 90 = perpendicular to the ground.
   *
   * @var float
   */
  public $pitchDegrees;
  /**
   * Index in roof_segment_stats of the corresponding
   * `RoofSegmentSizeAndSunshineStats`.
   *
   * @var int
   */
  public $segmentIndex;
  /**
   * How much sunlight energy this part of the layout captures over the course
   * of a year, in DC kWh, assuming the panels described above.
   *
   * @var float
   */
  public $yearlyEnergyDcKwh;

  /**
   * Compass direction the roof segment is pointing in. 0 = North, 90 = East,
   * 180 = South. For a "flat" roof segment (`pitch_degrees` very near 0),
   * azimuth is not well defined, so for consistency, we define it arbitrarily
   * to be 0 (North).
   *
   * @param float $azimuthDegrees
   */
  public function setAzimuthDegrees($azimuthDegrees)
  {
    $this->azimuthDegrees = $azimuthDegrees;
  }
  /**
   * @return float
   */
  public function getAzimuthDegrees()
  {
    return $this->azimuthDegrees;
  }
  /**
   * The total number of panels on this segment.
   *
   * @param int $panelsCount
   */
  public function setPanelsCount($panelsCount)
  {
    $this->panelsCount = $panelsCount;
  }
  /**
   * @return int
   */
  public function getPanelsCount()
  {
    return $this->panelsCount;
  }
  /**
   * Angle of the roof segment relative to the theoretical ground plane. 0 =
   * parallel to the ground, 90 = perpendicular to the ground.
   *
   * @param float $pitchDegrees
   */
  public function setPitchDegrees($pitchDegrees)
  {
    $this->pitchDegrees = $pitchDegrees;
  }
  /**
   * @return float
   */
  public function getPitchDegrees()
  {
    return $this->pitchDegrees;
  }
  /**
   * Index in roof_segment_stats of the corresponding
   * `RoofSegmentSizeAndSunshineStats`.
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
   * How much sunlight energy this part of the layout captures over the course
   * of a year, in DC kWh, assuming the panels described above.
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
class_alias(RoofSegmentSummary::class, 'Google_Service_Solar_RoofSegmentSummary');
