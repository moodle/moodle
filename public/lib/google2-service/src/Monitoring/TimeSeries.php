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

class TimeSeries extends \Google\Collection
{
  /**
   * Do not use this default value.
   */
  public const METRIC_KIND_METRIC_KIND_UNSPECIFIED = 'METRIC_KIND_UNSPECIFIED';
  /**
   * An instantaneous measurement of a value.
   */
  public const METRIC_KIND_GAUGE = 'GAUGE';
  /**
   * The change in a value during a time interval.
   */
  public const METRIC_KIND_DELTA = 'DELTA';
  /**
   * A value accumulated over a time interval. Cumulative measurements in a time
   * series should have the same start time and increasing end times, until an
   * event resets the cumulative value to zero and sets a new start time for the
   * following points.
   */
  public const METRIC_KIND_CUMULATIVE = 'CUMULATIVE';
  /**
   * Do not use this default value.
   */
  public const VALUE_TYPE_VALUE_TYPE_UNSPECIFIED = 'VALUE_TYPE_UNSPECIFIED';
  /**
   * The value is a boolean. This value type can be used only if the metric kind
   * is GAUGE.
   */
  public const VALUE_TYPE_BOOL = 'BOOL';
  /**
   * The value is a signed 64-bit integer.
   */
  public const VALUE_TYPE_INT64 = 'INT64';
  /**
   * The value is a double precision floating point number.
   */
  public const VALUE_TYPE_DOUBLE = 'DOUBLE';
  /**
   * The value is a text string. This value type can be used only if the metric
   * kind is GAUGE.
   */
  public const VALUE_TYPE_STRING = 'STRING';
  /**
   * The value is a Distribution.
   */
  public const VALUE_TYPE_DISTRIBUTION = 'DISTRIBUTION';
  /**
   * The value is money.
   */
  public const VALUE_TYPE_MONEY = 'MONEY';
  protected $collection_key = 'points';
  /**
   * Input only. A detailed description of the time series that will be
   * associated with the google.api.MetricDescriptor for the metric. Once set,
   * this field cannot be changed through CreateTimeSeries.
   *
   * @var string
   */
  public $description;
  protected $metadataType = MonitoredResourceMetadata::class;
  protected $metadataDataType = '';
  protected $metricType = Metric::class;
  protected $metricDataType = '';
  /**
   * The metric kind of the time series. When listing time series, this metric
   * kind might be different from the metric kind of the associated metric if
   * this time series is an alignment or reduction of other time series.When
   * creating a time series, this field is optional. If present, it must be the
   * same as the metric kind of the associated metric. If the associated
   * metric's descriptor must be auto-created, then this field specifies the
   * metric kind of the new descriptor and must be either GAUGE (the default) or
   * CUMULATIVE.
   *
   * @var string
   */
  public $metricKind;
  protected $pointsType = Point::class;
  protected $pointsDataType = 'array';
  protected $resourceType = MonitoredResource::class;
  protected $resourceDataType = '';
  /**
   * The units in which the metric value is reported. It is only applicable if
   * the value_type is INT64, DOUBLE, or DISTRIBUTION. The unit defines the
   * representation of the stored metric values. This field can only be changed
   * through CreateTimeSeries when it is empty.
   *
   * @var string
   */
  public $unit;
  /**
   * The value type of the time series. When listing time series, this value
   * type might be different from the value type of the associated metric if
   * this time series is an alignment or reduction of other time series.When
   * creating a time series, this field is optional. If present, it must be the
   * same as the type of the data in the points field.
   *
   * @var string
   */
  public $valueType;

  /**
   * Input only. A detailed description of the time series that will be
   * associated with the google.api.MetricDescriptor for the metric. Once set,
   * this field cannot be changed through CreateTimeSeries.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Output only. The associated monitored resource metadata. When reading a
   * time series, this field will include metadata labels that are explicitly
   * named in the reduction. When creating a time series, this field is ignored.
   *
   * @param MonitoredResourceMetadata $metadata
   */
  public function setMetadata(MonitoredResourceMetadata $metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return MonitoredResourceMetadata
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * The associated metric. A fully-specified metric used to identify the time
   * series.
   *
   * @param Metric $metric
   */
  public function setMetric(Metric $metric)
  {
    $this->metric = $metric;
  }
  /**
   * @return Metric
   */
  public function getMetric()
  {
    return $this->metric;
  }
  /**
   * The metric kind of the time series. When listing time series, this metric
   * kind might be different from the metric kind of the associated metric if
   * this time series is an alignment or reduction of other time series.When
   * creating a time series, this field is optional. If present, it must be the
   * same as the metric kind of the associated metric. If the associated
   * metric's descriptor must be auto-created, then this field specifies the
   * metric kind of the new descriptor and must be either GAUGE (the default) or
   * CUMULATIVE.
   *
   * Accepted values: METRIC_KIND_UNSPECIFIED, GAUGE, DELTA, CUMULATIVE
   *
   * @param self::METRIC_KIND_* $metricKind
   */
  public function setMetricKind($metricKind)
  {
    $this->metricKind = $metricKind;
  }
  /**
   * @return self::METRIC_KIND_*
   */
  public function getMetricKind()
  {
    return $this->metricKind;
  }
  /**
   * The data points of this time series. When listing time series, points are
   * returned in reverse time order.When creating a time series, this field must
   * contain exactly one point and the point's type must be the same as the
   * value type of the associated metric. If the associated metric's descriptor
   * must be auto-created, then the value type of the descriptor is determined
   * by the point's type, which must be BOOL, INT64, DOUBLE, or DISTRIBUTION.
   *
   * @param Point[] $points
   */
  public function setPoints($points)
  {
    $this->points = $points;
  }
  /**
   * @return Point[]
   */
  public function getPoints()
  {
    return $this->points;
  }
  /**
   * The associated monitored resource. Custom metrics can use only certain
   * monitored resource types in their time series data. For more information,
   * see Monitored resources for custom metrics
   * (https://cloud.google.com/monitoring/custom-metrics/creating-
   * metrics#custom-metric-resources).
   *
   * @param MonitoredResource $resource
   */
  public function setResource(MonitoredResource $resource)
  {
    $this->resource = $resource;
  }
  /**
   * @return MonitoredResource
   */
  public function getResource()
  {
    return $this->resource;
  }
  /**
   * The units in which the metric value is reported. It is only applicable if
   * the value_type is INT64, DOUBLE, or DISTRIBUTION. The unit defines the
   * representation of the stored metric values. This field can only be changed
   * through CreateTimeSeries when it is empty.
   *
   * @param string $unit
   */
  public function setUnit($unit)
  {
    $this->unit = $unit;
  }
  /**
   * @return string
   */
  public function getUnit()
  {
    return $this->unit;
  }
  /**
   * The value type of the time series. When listing time series, this value
   * type might be different from the value type of the associated metric if
   * this time series is an alignment or reduction of other time series.When
   * creating a time series, this field is optional. If present, it must be the
   * same as the type of the data in the points field.
   *
   * Accepted values: VALUE_TYPE_UNSPECIFIED, BOOL, INT64, DOUBLE, STRING,
   * DISTRIBUTION, MONEY
   *
   * @param self::VALUE_TYPE_* $valueType
   */
  public function setValueType($valueType)
  {
    $this->valueType = $valueType;
  }
  /**
   * @return self::VALUE_TYPE_*
   */
  public function getValueType()
  {
    return $this->valueType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TimeSeries::class, 'Google_Service_Monitoring_TimeSeries');
