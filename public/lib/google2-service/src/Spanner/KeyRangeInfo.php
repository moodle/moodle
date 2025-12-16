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

namespace Google\Service\Spanner;

class KeyRangeInfo extends \Google\Collection
{
  protected $collection_key = 'contextValues';
  protected $contextValuesType = ContextValue::class;
  protected $contextValuesDataType = 'array';
  /**
   * The index of the end key in indexed_keys.
   *
   * @var int
   */
  public $endKeyIndex;
  protected $infoType = LocalizedString::class;
  protected $infoDataType = '';
  /**
   * The number of keys this range covers.
   *
   * @var string
   */
  public $keysCount;
  protected $metricType = LocalizedString::class;
  protected $metricDataType = '';
  /**
   * The index of the start key in indexed_keys.
   *
   * @var int
   */
  public $startKeyIndex;
  /**
   * The time offset. This is the time since the start of the time interval.
   *
   * @var string
   */
  public $timeOffset;
  protected $unitType = LocalizedString::class;
  protected $unitDataType = '';
  /**
   * The value of the metric.
   *
   * @var float
   */
  public $value;

  /**
   * The list of context values for this key range.
   *
   * @param ContextValue[] $contextValues
   */
  public function setContextValues($contextValues)
  {
    $this->contextValues = $contextValues;
  }
  /**
   * @return ContextValue[]
   */
  public function getContextValues()
  {
    return $this->contextValues;
  }
  /**
   * The index of the end key in indexed_keys.
   *
   * @param int $endKeyIndex
   */
  public function setEndKeyIndex($endKeyIndex)
  {
    $this->endKeyIndex = $endKeyIndex;
  }
  /**
   * @return int
   */
  public function getEndKeyIndex()
  {
    return $this->endKeyIndex;
  }
  /**
   * Information about this key range, for all metrics.
   *
   * @param LocalizedString $info
   */
  public function setInfo(LocalizedString $info)
  {
    $this->info = $info;
  }
  /**
   * @return LocalizedString
   */
  public function getInfo()
  {
    return $this->info;
  }
  /**
   * The number of keys this range covers.
   *
   * @param string $keysCount
   */
  public function setKeysCount($keysCount)
  {
    $this->keysCount = $keysCount;
  }
  /**
   * @return string
   */
  public function getKeysCount()
  {
    return $this->keysCount;
  }
  /**
   * The name of the metric. e.g. "latency".
   *
   * @param LocalizedString $metric
   */
  public function setMetric(LocalizedString $metric)
  {
    $this->metric = $metric;
  }
  /**
   * @return LocalizedString
   */
  public function getMetric()
  {
    return $this->metric;
  }
  /**
   * The index of the start key in indexed_keys.
   *
   * @param int $startKeyIndex
   */
  public function setStartKeyIndex($startKeyIndex)
  {
    $this->startKeyIndex = $startKeyIndex;
  }
  /**
   * @return int
   */
  public function getStartKeyIndex()
  {
    return $this->startKeyIndex;
  }
  /**
   * The time offset. This is the time since the start of the time interval.
   *
   * @param string $timeOffset
   */
  public function setTimeOffset($timeOffset)
  {
    $this->timeOffset = $timeOffset;
  }
  /**
   * @return string
   */
  public function getTimeOffset()
  {
    return $this->timeOffset;
  }
  /**
   * The unit of the metric. This is an unstructured field and will be mapped as
   * is to the user.
   *
   * @param LocalizedString $unit
   */
  public function setUnit(LocalizedString $unit)
  {
    $this->unit = $unit;
  }
  /**
   * @return LocalizedString
   */
  public function getUnit()
  {
    return $this->unit;
  }
  /**
   * The value of the metric.
   *
   * @param float $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return float
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(KeyRangeInfo::class, 'Google_Service_Spanner_KeyRangeInfo');
