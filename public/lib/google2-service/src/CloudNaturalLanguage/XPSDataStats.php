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

namespace Google\Service\CloudNaturalLanguage;

class XPSDataStats extends \Google\Model
{
  protected $arrayStatsType = XPSArrayStats::class;
  protected $arrayStatsDataType = '';
  protected $categoryStatsType = XPSCategoryStats::class;
  protected $categoryStatsDataType = '';
  /**
   * The number of distinct values.
   *
   * @var string
   */
  public $distinctValueCount;
  protected $float64StatsType = XPSFloat64Stats::class;
  protected $float64StatsDataType = '';
  /**
   * The number of values that are null.
   *
   * @var string
   */
  public $nullValueCount;
  protected $stringStatsType = XPSStringStats::class;
  protected $stringStatsDataType = '';
  protected $structStatsType = XPSStructStats::class;
  protected $structStatsDataType = '';
  protected $timestampStatsType = XPSTimestampStats::class;
  protected $timestampStatsDataType = '';
  /**
   * The number of values that are valid.
   *
   * @var string
   */
  public $validValueCount;

  /**
   * The statistics for ARRAY DataType.
   *
   * @param XPSArrayStats $arrayStats
   */
  public function setArrayStats(XPSArrayStats $arrayStats)
  {
    $this->arrayStats = $arrayStats;
  }
  /**
   * @return XPSArrayStats
   */
  public function getArrayStats()
  {
    return $this->arrayStats;
  }
  /**
   * The statistics for CATEGORY DataType.
   *
   * @param XPSCategoryStats $categoryStats
   */
  public function setCategoryStats(XPSCategoryStats $categoryStats)
  {
    $this->categoryStats = $categoryStats;
  }
  /**
   * @return XPSCategoryStats
   */
  public function getCategoryStats()
  {
    return $this->categoryStats;
  }
  /**
   * The number of distinct values.
   *
   * @param string $distinctValueCount
   */
  public function setDistinctValueCount($distinctValueCount)
  {
    $this->distinctValueCount = $distinctValueCount;
  }
  /**
   * @return string
   */
  public function getDistinctValueCount()
  {
    return $this->distinctValueCount;
  }
  /**
   * The statistics for FLOAT64 DataType.
   *
   * @param XPSFloat64Stats $float64Stats
   */
  public function setFloat64Stats(XPSFloat64Stats $float64Stats)
  {
    $this->float64Stats = $float64Stats;
  }
  /**
   * @return XPSFloat64Stats
   */
  public function getFloat64Stats()
  {
    return $this->float64Stats;
  }
  /**
   * The number of values that are null.
   *
   * @param string $nullValueCount
   */
  public function setNullValueCount($nullValueCount)
  {
    $this->nullValueCount = $nullValueCount;
  }
  /**
   * @return string
   */
  public function getNullValueCount()
  {
    return $this->nullValueCount;
  }
  /**
   * The statistics for STRING DataType.
   *
   * @param XPSStringStats $stringStats
   */
  public function setStringStats(XPSStringStats $stringStats)
  {
    $this->stringStats = $stringStats;
  }
  /**
   * @return XPSStringStats
   */
  public function getStringStats()
  {
    return $this->stringStats;
  }
  /**
   * The statistics for STRUCT DataType.
   *
   * @param XPSStructStats $structStats
   */
  public function setStructStats(XPSStructStats $structStats)
  {
    $this->structStats = $structStats;
  }
  /**
   * @return XPSStructStats
   */
  public function getStructStats()
  {
    return $this->structStats;
  }
  /**
   * The statistics for TIMESTAMP DataType.
   *
   * @param XPSTimestampStats $timestampStats
   */
  public function setTimestampStats(XPSTimestampStats $timestampStats)
  {
    $this->timestampStats = $timestampStats;
  }
  /**
   * @return XPSTimestampStats
   */
  public function getTimestampStats()
  {
    return $this->timestampStats;
  }
  /**
   * The number of values that are valid.
   *
   * @param string $validValueCount
   */
  public function setValidValueCount($validValueCount)
  {
    $this->validValueCount = $validValueCount;
  }
  /**
   * @return string
   */
  public function getValidValueCount()
  {
    return $this->validValueCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSDataStats::class, 'Google_Service_CloudNaturalLanguage_XPSDataStats');
