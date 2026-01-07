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

class ScorecardChartSpec extends \Google\Model
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
   * Default value, do not use.
   */
  public const NUMBER_FORMAT_SOURCE_CHART_NUMBER_FORMAT_SOURCE_UNDEFINED = 'CHART_NUMBER_FORMAT_SOURCE_UNDEFINED';
  /**
   * Inherit number formatting from data.
   */
  public const NUMBER_FORMAT_SOURCE_FROM_DATA = 'FROM_DATA';
  /**
   * Apply custom formatting as specified by ChartCustomNumberFormatOptions.
   */
  public const NUMBER_FORMAT_SOURCE_CUSTOM = 'CUSTOM';
  /**
   * The aggregation type for key and baseline chart data in scorecard chart.
   * This field is not supported for data source charts. Use the
   * ChartData.aggregateType field of the key_value_data or baseline_value_data
   * instead for data source charts. This field is optional.
   *
   * @var string
   */
  public $aggregateType;
  protected $baselineValueDataType = ChartData::class;
  protected $baselineValueDataDataType = '';
  protected $baselineValueFormatType = BaselineValueFormat::class;
  protected $baselineValueFormatDataType = '';
  protected $customFormatOptionsType = ChartCustomNumberFormatOptions::class;
  protected $customFormatOptionsDataType = '';
  protected $keyValueDataType = ChartData::class;
  protected $keyValueDataDataType = '';
  protected $keyValueFormatType = KeyValueFormat::class;
  protected $keyValueFormatDataType = '';
  /**
   * The number format source used in the scorecard chart. This field is
   * optional.
   *
   * @var string
   */
  public $numberFormatSource;
  /**
   * Value to scale scorecard key and baseline value. For example, a factor of
   * 10 can be used to divide all values in the chart by 10. This field is
   * optional.
   *
   * @var 
   */
  public $scaleFactor;

  /**
   * The aggregation type for key and baseline chart data in scorecard chart.
   * This field is not supported for data source charts. Use the
   * ChartData.aggregateType field of the key_value_data or baseline_value_data
   * instead for data source charts. This field is optional.
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
   * The data for scorecard baseline value. This field is optional.
   *
   * @param ChartData $baselineValueData
   */
  public function setBaselineValueData(ChartData $baselineValueData)
  {
    $this->baselineValueData = $baselineValueData;
  }
  /**
   * @return ChartData
   */
  public function getBaselineValueData()
  {
    return $this->baselineValueData;
  }
  /**
   * Formatting options for baseline value. This field is needed only if
   * baseline_value_data is specified.
   *
   * @param BaselineValueFormat $baselineValueFormat
   */
  public function setBaselineValueFormat(BaselineValueFormat $baselineValueFormat)
  {
    $this->baselineValueFormat = $baselineValueFormat;
  }
  /**
   * @return BaselineValueFormat
   */
  public function getBaselineValueFormat()
  {
    return $this->baselineValueFormat;
  }
  /**
   * Custom formatting options for numeric key/baseline values in scorecard
   * chart. This field is used only when number_format_source is set to CUSTOM.
   * This field is optional.
   *
   * @param ChartCustomNumberFormatOptions $customFormatOptions
   */
  public function setCustomFormatOptions(ChartCustomNumberFormatOptions $customFormatOptions)
  {
    $this->customFormatOptions = $customFormatOptions;
  }
  /**
   * @return ChartCustomNumberFormatOptions
   */
  public function getCustomFormatOptions()
  {
    return $this->customFormatOptions;
  }
  /**
   * The data for scorecard key value.
   *
   * @param ChartData $keyValueData
   */
  public function setKeyValueData(ChartData $keyValueData)
  {
    $this->keyValueData = $keyValueData;
  }
  /**
   * @return ChartData
   */
  public function getKeyValueData()
  {
    return $this->keyValueData;
  }
  /**
   * Formatting options for key value.
   *
   * @param KeyValueFormat $keyValueFormat
   */
  public function setKeyValueFormat(KeyValueFormat $keyValueFormat)
  {
    $this->keyValueFormat = $keyValueFormat;
  }
  /**
   * @return KeyValueFormat
   */
  public function getKeyValueFormat()
  {
    return $this->keyValueFormat;
  }
  /**
   * The number format source used in the scorecard chart. This field is
   * optional.
   *
   * Accepted values: CHART_NUMBER_FORMAT_SOURCE_UNDEFINED, FROM_DATA, CUSTOM
   *
   * @param self::NUMBER_FORMAT_SOURCE_* $numberFormatSource
   */
  public function setNumberFormatSource($numberFormatSource)
  {
    $this->numberFormatSource = $numberFormatSource;
  }
  /**
   * @return self::NUMBER_FORMAT_SOURCE_*
   */
  public function getNumberFormatSource()
  {
    return $this->numberFormatSource;
  }
  public function setScaleFactor($scaleFactor)
  {
    $this->scaleFactor = $scaleFactor;
  }
  public function getScaleFactor()
  {
    return $this->scaleFactor;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ScorecardChartSpec::class, 'Google_Service_Sheets_ScorecardChartSpec');
