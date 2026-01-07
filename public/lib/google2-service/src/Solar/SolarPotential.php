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

class SolarPotential extends \Google\Collection
{
  protected $collection_key = 'solarPanels';
  protected $buildingStatsType = SizeAndSunshineStats::class;
  protected $buildingStatsDataType = '';
  /**
   * Equivalent amount of CO2 produced per MWh of grid electricity. This is a
   * measure of the carbon intensity of grid electricity displaced by solar
   * electricity.
   *
   * @var float
   */
  public $carbonOffsetFactorKgPerMwh;
  protected $financialAnalysesType = FinancialAnalysis::class;
  protected $financialAnalysesDataType = 'array';
  /**
   * Size, in square meters, of the maximum array.
   *
   * @var float
   */
  public $maxArrayAreaMeters2;
  /**
   * Size of the maximum array - that is, the maximum number of panels that can
   * fit on the roof.
   *
   * @var int
   */
  public $maxArrayPanelsCount;
  /**
   * Maximum number of sunshine hours received per year, by any point on the
   * roof. Sunshine hours are a measure of the total amount of insolation
   * (energy) received per year. 1 sunshine hour = 1 kWh per kW (where kW refers
   * to kW of capacity under Standard Testing Conditions).
   *
   * @var float
   */
  public $maxSunshineHoursPerYear;
  /**
   * Capacity, in watts, of the panel used in the calculations.
   *
   * @var float
   */
  public $panelCapacityWatts;
  /**
   * Height, in meters in portrait orientation, of the panel used in the
   * calculations.
   *
   * @var float
   */
  public $panelHeightMeters;
  /**
   * The expected lifetime, in years, of the solar panels. This is used in the
   * financial calculations.
   *
   * @var int
   */
  public $panelLifetimeYears;
  /**
   * Width, in meters in portrait orientation, of the panel used in the
   * calculations.
   *
   * @var float
   */
  public $panelWidthMeters;
  protected $roofSegmentStatsType = RoofSegmentSizeAndSunshineStats::class;
  protected $roofSegmentStatsDataType = 'array';
  protected $solarPanelConfigsType = SolarPanelConfig::class;
  protected $solarPanelConfigsDataType = 'array';
  protected $solarPanelsType = SolarPanel::class;
  protected $solarPanelsDataType = 'array';
  protected $wholeRoofStatsType = SizeAndSunshineStats::class;
  protected $wholeRoofStatsDataType = '';

  /**
   * Size and sunlight quantiles for the entire building, including parts of the
   * roof that were not assigned to some roof segment. Because the orientations
   * of these parts are not well characterised, the roof area estimate is
   * unreliable, but the ground area estimate is reliable. It may be that a more
   * reliable whole building roof area can be obtained by scaling the roof area
   * from whole_roof_stats by the ratio of the ground areas of `building_stats`
   * and `whole_roof_stats`.
   *
   * @param SizeAndSunshineStats $buildingStats
   */
  public function setBuildingStats(SizeAndSunshineStats $buildingStats)
  {
    $this->buildingStats = $buildingStats;
  }
  /**
   * @return SizeAndSunshineStats
   */
  public function getBuildingStats()
  {
    return $this->buildingStats;
  }
  /**
   * Equivalent amount of CO2 produced per MWh of grid electricity. This is a
   * measure of the carbon intensity of grid electricity displaced by solar
   * electricity.
   *
   * @param float $carbonOffsetFactorKgPerMwh
   */
  public function setCarbonOffsetFactorKgPerMwh($carbonOffsetFactorKgPerMwh)
  {
    $this->carbonOffsetFactorKgPerMwh = $carbonOffsetFactorKgPerMwh;
  }
  /**
   * @return float
   */
  public function getCarbonOffsetFactorKgPerMwh()
  {
    return $this->carbonOffsetFactorKgPerMwh;
  }
  /**
   * A FinancialAnalysis gives the savings from going solar assuming a given
   * monthly bill and a given electricity provider. They are in order of
   * increasing order of monthly bill amount. This field will be empty for
   * buildings in areas for which the Solar API does not have enough information
   * to perform financial computations.
   *
   * @param FinancialAnalysis[] $financialAnalyses
   */
  public function setFinancialAnalyses($financialAnalyses)
  {
    $this->financialAnalyses = $financialAnalyses;
  }
  /**
   * @return FinancialAnalysis[]
   */
  public function getFinancialAnalyses()
  {
    return $this->financialAnalyses;
  }
  /**
   * Size, in square meters, of the maximum array.
   *
   * @param float $maxArrayAreaMeters2
   */
  public function setMaxArrayAreaMeters2($maxArrayAreaMeters2)
  {
    $this->maxArrayAreaMeters2 = $maxArrayAreaMeters2;
  }
  /**
   * @return float
   */
  public function getMaxArrayAreaMeters2()
  {
    return $this->maxArrayAreaMeters2;
  }
  /**
   * Size of the maximum array - that is, the maximum number of panels that can
   * fit on the roof.
   *
   * @param int $maxArrayPanelsCount
   */
  public function setMaxArrayPanelsCount($maxArrayPanelsCount)
  {
    $this->maxArrayPanelsCount = $maxArrayPanelsCount;
  }
  /**
   * @return int
   */
  public function getMaxArrayPanelsCount()
  {
    return $this->maxArrayPanelsCount;
  }
  /**
   * Maximum number of sunshine hours received per year, by any point on the
   * roof. Sunshine hours are a measure of the total amount of insolation
   * (energy) received per year. 1 sunshine hour = 1 kWh per kW (where kW refers
   * to kW of capacity under Standard Testing Conditions).
   *
   * @param float $maxSunshineHoursPerYear
   */
  public function setMaxSunshineHoursPerYear($maxSunshineHoursPerYear)
  {
    $this->maxSunshineHoursPerYear = $maxSunshineHoursPerYear;
  }
  /**
   * @return float
   */
  public function getMaxSunshineHoursPerYear()
  {
    return $this->maxSunshineHoursPerYear;
  }
  /**
   * Capacity, in watts, of the panel used in the calculations.
   *
   * @param float $panelCapacityWatts
   */
  public function setPanelCapacityWatts($panelCapacityWatts)
  {
    $this->panelCapacityWatts = $panelCapacityWatts;
  }
  /**
   * @return float
   */
  public function getPanelCapacityWatts()
  {
    return $this->panelCapacityWatts;
  }
  /**
   * Height, in meters in portrait orientation, of the panel used in the
   * calculations.
   *
   * @param float $panelHeightMeters
   */
  public function setPanelHeightMeters($panelHeightMeters)
  {
    $this->panelHeightMeters = $panelHeightMeters;
  }
  /**
   * @return float
   */
  public function getPanelHeightMeters()
  {
    return $this->panelHeightMeters;
  }
  /**
   * The expected lifetime, in years, of the solar panels. This is used in the
   * financial calculations.
   *
   * @param int $panelLifetimeYears
   */
  public function setPanelLifetimeYears($panelLifetimeYears)
  {
    $this->panelLifetimeYears = $panelLifetimeYears;
  }
  /**
   * @return int
   */
  public function getPanelLifetimeYears()
  {
    return $this->panelLifetimeYears;
  }
  /**
   * Width, in meters in portrait orientation, of the panel used in the
   * calculations.
   *
   * @param float $panelWidthMeters
   */
  public function setPanelWidthMeters($panelWidthMeters)
  {
    $this->panelWidthMeters = $panelWidthMeters;
  }
  /**
   * @return float
   */
  public function getPanelWidthMeters()
  {
    return $this->panelWidthMeters;
  }
  /**
   * Size and sunlight quantiles for each roof segment.
   *
   * @param RoofSegmentSizeAndSunshineStats[] $roofSegmentStats
   */
  public function setRoofSegmentStats($roofSegmentStats)
  {
    $this->roofSegmentStats = $roofSegmentStats;
  }
  /**
   * @return RoofSegmentSizeAndSunshineStats[]
   */
  public function getRoofSegmentStats()
  {
    return $this->roofSegmentStats;
  }
  /**
   * Each SolarPanelConfig describes a different arrangement of solar panels on
   * the roof. They are in order of increasing number of panels. The
   * `SolarPanelConfig` with panels_count=N is based on the first N panels in
   * the `solar_panels` list. This field is only populated if at least 4 panels
   * can fit on a roof.
   *
   * @param SolarPanelConfig[] $solarPanelConfigs
   */
  public function setSolarPanelConfigs($solarPanelConfigs)
  {
    $this->solarPanelConfigs = $solarPanelConfigs;
  }
  /**
   * @return SolarPanelConfig[]
   */
  public function getSolarPanelConfigs()
  {
    return $this->solarPanelConfigs;
  }
  /**
   * Each SolarPanel describes a single solar panel. They are listed in the
   * order that the panel layout algorithm placed this. This is usually, though
   * not always, in decreasing order of annual energy production.
   *
   * @param SolarPanel[] $solarPanels
   */
  public function setSolarPanels($solarPanels)
  {
    $this->solarPanels = $solarPanels;
  }
  /**
   * @return SolarPanel[]
   */
  public function getSolarPanels()
  {
    return $this->solarPanels;
  }
  /**
   * Total size and sunlight quantiles for the part of the roof that was
   * assigned to some roof segment. Despite the name, this may not include the
   * entire building. See building_stats.
   *
   * @param SizeAndSunshineStats $wholeRoofStats
   */
  public function setWholeRoofStats(SizeAndSunshineStats $wholeRoofStats)
  {
    $this->wholeRoofStats = $wholeRoofStats;
  }
  /**
   * @return SizeAndSunshineStats
   */
  public function getWholeRoofStats()
  {
    return $this->wholeRoofStats;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SolarPotential::class, 'Google_Service_Solar_SolarPotential');
