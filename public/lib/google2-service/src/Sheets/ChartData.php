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

namespace Google\Service\Sheets;

class ChartData extends \Google\Model
{
  /**
   * Default value, do not use.
   */
  public const AGGREGATE_TYPE_CHART_AGGREGATE_TYPE_UNSPECIFIED = 'CHART_AGGREGATE_TYPE_UNSPECIFIED';
  /**
   * Average aggregate function.
   */
  public const AGGREGATE_TYPE_AVERAGE = 'AVERAGE';
  /**
   * Count aggregate function.
   */
  public const AGGREGATE_TYPE_COUNT = 'COUNT';
  /**
   * Maximum aggregate function.
   */
  public const AGGREGATE_TYPE_MAX = 'MAX';
  /**
   * Median aggregate function.
   */
  public const AGGREGATE_TYPE_MEDIAN = 'MEDIAN';
  /**
   * Minimum aggregate function.
   */
  public const AGGREGATE_TYPE_MIN = 'MIN';
  /**
   * Sum aggregate function.
   */
  public const AGGREGATE_TYPE_SUM = 'SUM';
  /**
   * The aggregation type for the series of a data source chart. Only supported
   * for data source charts.
   *
   * @var string
   */
  public $aggregateType;
  protected $columnReferenceType = DataSourceColumnReference::class;
  protected $columnReferenceDataType = '';
  protected $groupRuleType = ChartGroupRule::class;
  protected $groupRuleDataType = '';
  protected $sourceRangeType = ChartSourceRange::class;
  protected $sourceRangeDataType = '';

  /**
   * The aggregation type for the series of a data source chart. Only supported
   * for data source charts.
   *
   * Accepted values: CHART_AGGREGATE_TYPE_UNSPECIFIED, AVERAGE, COUNT, MAX,
   * MEDIAN, MIN, SUM
   *
   * @param self::AGGREGATE_TYPE_* $aggregateType
   */
  public function setAggregateType($aggregateType)
  {
    $this->aggregateType = $aggregateType;
  }
  /**
   * @return self::AGGREGATE_TYPE_*
   */
  public function getAggregateType()
  {
    return $this->aggregateType;
  }
  /**
   * The reference to the data source column that the data reads from.
   *
   * @param DataSourceColumnReference $columnReference
   */
  public function setColumnReference(DataSourceColumnReference $columnReference)
  {
    $this->columnReference = $columnReference;
  }
  /**
   * @return DataSourceColumnReference
   */
  public function getColumnReference()
  {
    return $this->columnReference;
  }
  /**
   * The rule to group the data by if the ChartData backs the domain of a data
   * source chart. Only supported for data source charts.
   *
   * @param ChartGroupRule $groupRule
   */
  public function setGroupRule(ChartGroupRule $groupRule)
  {
    $this->groupRule = $groupRule;
  }
  /**
   * @return ChartGroupRule
   */
  public function getGroupRule()
  {
    return $this->groupRule;
  }
  /**
   * The source ranges of the data.
   *
   * @param ChartSourceRange $sourceRange
   */
  public function setSourceRange(ChartSourceRange $sourceRange)
  {
    $this->sourceRange = $sourceRange;
  }
  /**
   * @return ChartSourceRange
   */
  public function getSourceRange()
  {
    return $this->sourceRange;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ChartData::class, 'Google_Service_Sheets_ChartData');
