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

namespace Google\Service\AirQuality;

class HourInfo extends \Google\Collection
{
  protected $collection_key = 'pollutants';
  /**
   * A rounded down timestamp indicating the time the data refers to in RFC3339
   * UTC "Zulu" format, with nanosecond resolution and up to nine fractional
   * digits. For example: "2014-10-02T15:00:00Z".
   *
   * @var string
   */
  public $dateTime;
  protected $healthRecommendationsType = HealthRecommendations::class;
  protected $healthRecommendationsDataType = '';
  protected $indexesType = AirQualityIndex::class;
  protected $indexesDataType = 'array';
  protected $pollutantsType = Pollutant::class;
  protected $pollutantsDataType = 'array';

  /**
   * A rounded down timestamp indicating the time the data refers to in RFC3339
   * UTC "Zulu" format, with nanosecond resolution and up to nine fractional
   * digits. For example: "2014-10-02T15:00:00Z".
   *
   * @param string $dateTime
   */
  public function setDateTime($dateTime)
  {
    $this->dateTime = $dateTime;
  }
  /**
   * @return string
   */
  public function getDateTime()
  {
    return $this->dateTime;
  }
  /**
   * Health advice and recommended actions related to the reported air quality
   * conditions. Recommendations are tailored differently for populations at
   * risk, groups with greater sensitivities to pollutants, and the general
   * population.
   *
   * @param HealthRecommendations $healthRecommendations
   */
  public function setHealthRecommendations(HealthRecommendations $healthRecommendations)
  {
    $this->healthRecommendations = $healthRecommendations;
  }
  /**
   * @return HealthRecommendations
   */
  public function getHealthRecommendations()
  {
    return $this->healthRecommendations;
  }
  /**
   * Based on the request parameters, this list will include (up to) two air
   * quality indexes: - Universal AQI. Will be returned if the universalAqi
   * boolean is set to true. - Local AQI. Will be returned if the LOCAL_AQI
   * extra computation is specified.
   *
   * @param AirQualityIndex[] $indexes
   */
  public function setIndexes($indexes)
  {
    $this->indexes = $indexes;
  }
  /**
   * @return AirQualityIndex[]
   */
  public function getIndexes()
  {
    return $this->indexes;
  }
  /**
   * A list of pollutants affecting the location specified in the request. Note:
   * This field will be returned only for requests that specified one or more of
   * the following extra computations: POLLUTANT_ADDITIONAL_INFO,
   * DOMINANT_POLLUTANT_CONCENTRATION, POLLUTANT_CONCENTRATION.
   *
   * @param Pollutant[] $pollutants
   */
  public function setPollutants($pollutants)
  {
    $this->pollutants = $pollutants;
  }
  /**
   * @return Pollutant[]
   */
  public function getPollutants()
  {
    return $this->pollutants;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HourInfo::class, 'Google_Service_AirQuality_HourInfo');
