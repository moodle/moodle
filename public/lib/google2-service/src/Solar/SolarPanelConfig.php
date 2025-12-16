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

class SolarPanelConfig extends \Google\Collection
{
  protected $collection_key = 'roofSegmentSummaries';
  /**
   * Total number of panels. Note that this is redundant to (the sum of) the
   * corresponding fields in roof_segment_summaries.
   *
   * @var int
   */
  public $panelsCount;
  protected $roofSegmentSummariesType = RoofSegmentSummary::class;
  protected $roofSegmentSummariesDataType = 'array';
  /**
   * How much sunlight energy this layout captures over the course of a year, in
   * DC kWh, assuming the panels described above.
   *
   * @var float
   */
  public $yearlyEnergyDcKwh;

  /**
   * Total number of panels. Note that this is redundant to (the sum of) the
   * corresponding fields in roof_segment_summaries.
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
   * Information about the production of each roof segment that is carrying at
   * least one panel in this layout. `roof_segment_summaries[i]` describes the
   * i-th roof segment, including its size, expected production and orientation.
   *
   * @param RoofSegmentSummary[] $roofSegmentSummaries
   */
  public function setRoofSegmentSummaries($roofSegmentSummaries)
  {
    $this->roofSegmentSummaries = $roofSegmentSummaries;
  }
  /**
   * @return RoofSegmentSummary[]
   */
  public function getRoofSegmentSummaries()
  {
    return $this->roofSegmentSummaries;
  }
  /**
   * How much sunlight energy this layout captures over the course of a year, in
   * DC kWh, assuming the panels described above.
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
class_alias(SolarPanelConfig::class, 'Google_Service_Solar_SolarPanelConfig');
