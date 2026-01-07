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

class Metric extends \Google\Model
{
  /**
   * Required default value.
   */
  public const AGGREGATION_AGGREGATION_UNSPECIFIED = 'AGGREGATION_UNSPECIFIED';
  /**
   * Use the maximum of all values.
   */
  public const AGGREGATION_MAX = 'MAX';
  /**
   * Use the sum of all values.
   */
  public const AGGREGATION_SUM = 'SUM';
  /**
   * The aggregation function used to aggregate each key bucket
   *
   * @var string
   */
  public $aggregation;
  protected $categoryType = LocalizedString::class;
  protected $categoryDataType = '';
  protected $derivedType = DerivedMetric::class;
  protected $derivedDataType = '';
  protected $displayLabelType = LocalizedString::class;
  protected $displayLabelDataType = '';
  /**
   * Whether the metric has any non-zero data.
   *
   * @var bool
   */
  public $hasNonzeroData;
  /**
   * The value that is considered hot for the metric. On a per metric basis
   * hotness signals high utilization and something that might potentially be a
   * cause for concern by the end user. hot_value is used to calibrate and scale
   * visual color scales.
   *
   * @var float
   */
  public $hotValue;
  protected $indexedHotKeysType = IndexedHotKey::class;
  protected $indexedHotKeysDataType = 'map';
  protected $indexedKeyRangeInfosType = IndexedKeyRangeInfos::class;
  protected $indexedKeyRangeInfosDataType = 'map';
  protected $infoType = LocalizedString::class;
  protected $infoDataType = '';
  protected $matrixType = MetricMatrix::class;
  protected $matrixDataType = '';
  protected $unitType = LocalizedString::class;
  protected $unitDataType = '';
  /**
   * Whether the metric is visible to the end user.
   *
   * @var bool
   */
  public $visible;

  /**
   * The aggregation function used to aggregate each key bucket
   *
   * Accepted values: AGGREGATION_UNSPECIFIED, MAX, SUM
   *
   * @param self::AGGREGATION_* $aggregation
   */
  public function setAggregation($aggregation)
  {
    $this->aggregation = $aggregation;
  }
  /**
   * @return self::AGGREGATION_*
   */
  public function getAggregation()
  {
    return $this->aggregation;
  }
  /**
   * The category of the metric, e.g. "Activity", "Alerts", "Reads", etc.
   *
   * @param LocalizedString $category
   */
  public function setCategory(LocalizedString $category)
  {
    $this->category = $category;
  }
  /**
   * @return LocalizedString
   */
  public function getCategory()
  {
    return $this->category;
  }
  /**
   * The references to numerator and denominator metrics for a derived metric.
   *
   * @param DerivedMetric $derived
   */
  public function setDerived(DerivedMetric $derived)
  {
    $this->derived = $derived;
  }
  /**
   * @return DerivedMetric
   */
  public function getDerived()
  {
    return $this->derived;
  }
  /**
   * The displayed label of the metric.
   *
   * @param LocalizedString $displayLabel
   */
  public function setDisplayLabel(LocalizedString $displayLabel)
  {
    $this->displayLabel = $displayLabel;
  }
  /**
   * @return LocalizedString
   */
  public function getDisplayLabel()
  {
    return $this->displayLabel;
  }
  /**
   * Whether the metric has any non-zero data.
   *
   * @param bool $hasNonzeroData
   */
  public function setHasNonzeroData($hasNonzeroData)
  {
    $this->hasNonzeroData = $hasNonzeroData;
  }
  /**
   * @return bool
   */
  public function getHasNonzeroData()
  {
    return $this->hasNonzeroData;
  }
  /**
   * The value that is considered hot for the metric. On a per metric basis
   * hotness signals high utilization and something that might potentially be a
   * cause for concern by the end user. hot_value is used to calibrate and scale
   * visual color scales.
   *
   * @param float $hotValue
   */
  public function setHotValue($hotValue)
  {
    $this->hotValue = $hotValue;
  }
  /**
   * @return float
   */
  public function getHotValue()
  {
    return $this->hotValue;
  }
  /**
   * The (sparse) mapping from time index to an IndexedHotKey message,
   * representing those time intervals for which there are hot keys.
   *
   * @param IndexedHotKey[] $indexedHotKeys
   */
  public function setIndexedHotKeys($indexedHotKeys)
  {
    $this->indexedHotKeys = $indexedHotKeys;
  }
  /**
   * @return IndexedHotKey[]
   */
  public function getIndexedHotKeys()
  {
    return $this->indexedHotKeys;
  }
  /**
   * The (sparse) mapping from time interval index to an IndexedKeyRangeInfos
   * message, representing those time intervals for which there are
   * informational messages concerning key ranges.
   *
   * @param IndexedKeyRangeInfos[] $indexedKeyRangeInfos
   */
  public function setIndexedKeyRangeInfos($indexedKeyRangeInfos)
  {
    $this->indexedKeyRangeInfos = $indexedKeyRangeInfos;
  }
  /**
   * @return IndexedKeyRangeInfos[]
   */
  public function getIndexedKeyRangeInfos()
  {
    return $this->indexedKeyRangeInfos;
  }
  /**
   * Information about the metric.
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
   * The data for the metric as a matrix.
   *
   * @param MetricMatrix $matrix
   */
  public function setMatrix(MetricMatrix $matrix)
  {
    $this->matrix = $matrix;
  }
  /**
   * @return MetricMatrix
   */
  public function getMatrix()
  {
    return $this->matrix;
  }
  /**
   * The unit of the metric.
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
   * Whether the metric is visible to the end user.
   *
   * @param bool $visible
   */
  public function setVisible($visible)
  {
    $this->visible = $visible;
  }
  /**
   * @return bool
   */
  public function getVisible()
  {
    return $this->visible;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Metric::class, 'Google_Service_Spanner_Metric');
