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

namespace Google\Service\Monitoring;

class CollectdValue extends \Google\Model
{
  /**
   * An unspecified data source type. This corresponds to
   * google.api.MetricDescriptor.MetricKind.METRIC_KIND_UNSPECIFIED.
   */
  public const DATA_SOURCE_TYPE_UNSPECIFIED_DATA_SOURCE_TYPE = 'UNSPECIFIED_DATA_SOURCE_TYPE';
  /**
   * An instantaneous measurement of a varying quantity. This corresponds to
   * google.api.MetricDescriptor.MetricKind.GAUGE.
   */
  public const DATA_SOURCE_TYPE_GAUGE = 'GAUGE';
  /**
   * A cumulative value over time. This corresponds to
   * google.api.MetricDescriptor.MetricKind.CUMULATIVE.
   */
  public const DATA_SOURCE_TYPE_COUNTER = 'COUNTER';
  /**
   * A rate of change of the measurement.
   */
  public const DATA_SOURCE_TYPE_DERIVE = 'DERIVE';
  /**
   * An amount of change since the last measurement interval. This corresponds
   * to google.api.MetricDescriptor.MetricKind.DELTA.
   */
  public const DATA_SOURCE_TYPE_ABSOLUTE = 'ABSOLUTE';
  /**
   * The data source for the collectd value. For example, there are two data
   * sources for network measurements: "rx" and "tx".
   *
   * @var string
   */
  public $dataSourceName;
  /**
   * The type of measurement.
   *
   * @var string
   */
  public $dataSourceType;
  protected $valueType = TypedValue::class;
  protected $valueDataType = '';

  /**
   * The data source for the collectd value. For example, there are two data
   * sources for network measurements: "rx" and "tx".
   *
   * @param string $dataSourceName
   */
  public function setDataSourceName($dataSourceName)
  {
    $this->dataSourceName = $dataSourceName;
  }
  /**
   * @return string
   */
  public function getDataSourceName()
  {
    return $this->dataSourceName;
  }
  /**
   * The type of measurement.
   *
   * Accepted values: UNSPECIFIED_DATA_SOURCE_TYPE, GAUGE, COUNTER, DERIVE,
   * ABSOLUTE
   *
   * @param self::DATA_SOURCE_TYPE_* $dataSourceType
   */
  public function setDataSourceType($dataSourceType)
  {
    $this->dataSourceType = $dataSourceType;
  }
  /**
   * @return self::DATA_SOURCE_TYPE_*
   */
  public function getDataSourceType()
  {
    return $this->dataSourceType;
  }
  /**
   * The measurement value.
   *
   * @param TypedValue $value
   */
  public function setValue(TypedValue $value)
  {
    $this->value = $value;
  }
  /**
   * @return TypedValue
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CollectdValue::class, 'Google_Service_Monitoring_CollectdValue');
