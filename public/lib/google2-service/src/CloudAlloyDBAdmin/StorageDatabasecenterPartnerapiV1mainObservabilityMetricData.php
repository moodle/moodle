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

namespace Google\Service\CloudAlloyDBAdmin;

class StorageDatabasecenterPartnerapiV1mainObservabilityMetricData extends \Google\Model
{
  /**
   * Unspecified aggregation type.
   */
  public const AGGREGATION_TYPE_AGGREGATION_TYPE_UNSPECIFIED = 'AGGREGATION_TYPE_UNSPECIFIED';
  /**
   * PEAK aggregation type.
   */
  public const AGGREGATION_TYPE_PEAK = 'PEAK';
  /**
   * P99 aggregation type.
   */
  public const AGGREGATION_TYPE_P99 = 'P99';
  /**
   * P95 aggregation type.
   */
  public const AGGREGATION_TYPE_P95 = 'P95';
  /**
   * current aggregation type.
   */
  public const AGGREGATION_TYPE_CURRENT = 'CURRENT';
  /**
   * Unspecified metric type.
   */
  public const METRIC_TYPE_METRIC_TYPE_UNSPECIFIED = 'METRIC_TYPE_UNSPECIFIED';
  /**
   * CPU utilization for a resource. The value is a fraction between 0.0 and 1.0
   * (may momentarily exceed 1.0 in some cases).
   */
  public const METRIC_TYPE_CPU_UTILIZATION = 'CPU_UTILIZATION';
  /**
   * Memory utilization for a resource. The value is a fraction between 0.0 and
   * 1.0 (may momentarily exceed 1.0 in some cases).
   */
  public const METRIC_TYPE_MEMORY_UTILIZATION = 'MEMORY_UTILIZATION';
  /**
   * Number of network connections for a resource.
   */
  public const METRIC_TYPE_NETWORK_CONNECTIONS = 'NETWORK_CONNECTIONS';
  /**
   * Storage utilization for a resource. The value is a fraction between 0.0 and
   * 1.0 (may momentarily exceed 1.0 in some cases).
   */
  public const METRIC_TYPE_STORAGE_UTILIZATION = 'STORAGE_UTILIZATION';
  /**
   * Sotrage used by a resource.
   */
  public const METRIC_TYPE_STORAGE_USED_BYTES = 'STORAGE_USED_BYTES';
  /**
   * Node count for a resource. It represents the number of node units in a
   * bigtable/spanner instance.
   */
  public const METRIC_TYPE_NODE_COUNT = 'NODE_COUNT';
  /**
   * Memory used by a resource (in bytes).
   */
  public const METRIC_TYPE_MEMORY_USED_BYTES = 'MEMORY_USED_BYTES';
  /**
   * Processing units used by a resource. It represents the number of processing
   * units in a spanner instance.
   */
  public const METRIC_TYPE_PROCESSING_UNIT_COUNT = 'PROCESSING_UNIT_COUNT';
  /**
   * Required. Type of aggregation performed on the metric.
   *
   * @var string
   */
  public $aggregationType;
  /**
   * Required. Type of metric like CPU, Memory, etc.
   *
   * @var string
   */
  public $metricType;
  /**
   * Required. The time the metric value was observed.
   *
   * @var string
   */
  public $observationTime;
  /**
   * Required. Database resource name associated with the signal. Resource name
   * to follow CAIS resource_name format as noted here go/condor-common-
   * datamodel
   *
   * @var string
   */
  public $resourceName;
  protected $valueType = StorageDatabasecenterProtoCommonTypedValue::class;
  protected $valueDataType = '';

  /**
   * Required. Type of aggregation performed on the metric.
   *
   * Accepted values: AGGREGATION_TYPE_UNSPECIFIED, PEAK, P99, P95, CURRENT
   *
   * @param self::AGGREGATION_TYPE_* $aggregationType
   */
  public function setAggregationType($aggregationType)
  {
    $this->aggregationType = $aggregationType;
  }
  /**
   * @return self::AGGREGATION_TYPE_*
   */
  public function getAggregationType()
  {
    return $this->aggregationType;
  }
  /**
   * Required. Type of metric like CPU, Memory, etc.
   *
   * Accepted values: METRIC_TYPE_UNSPECIFIED, CPU_UTILIZATION,
   * MEMORY_UTILIZATION, NETWORK_CONNECTIONS, STORAGE_UTILIZATION,
   * STORAGE_USED_BYTES, NODE_COUNT, MEMORY_USED_BYTES, PROCESSING_UNIT_COUNT
   *
   * @param self::METRIC_TYPE_* $metricType
   */
  public function setMetricType($metricType)
  {
    $this->metricType = $metricType;
  }
  /**
   * @return self::METRIC_TYPE_*
   */
  public function getMetricType()
  {
    return $this->metricType;
  }
  /**
   * Required. The time the metric value was observed.
   *
   * @param string $observationTime
   */
  public function setObservationTime($observationTime)
  {
    $this->observationTime = $observationTime;
  }
  /**
   * @return string
   */
  public function getObservationTime()
  {
    return $this->observationTime;
  }
  /**
   * Required. Database resource name associated with the signal. Resource name
   * to follow CAIS resource_name format as noted here go/condor-common-
   * datamodel
   *
   * @param string $resourceName
   */
  public function setResourceName($resourceName)
  {
    $this->resourceName = $resourceName;
  }
  /**
   * @return string
   */
  public function getResourceName()
  {
    return $this->resourceName;
  }
  /**
   * Required. Value of the metric type.
   *
   * @param StorageDatabasecenterProtoCommonTypedValue $value
   */
  public function setValue(StorageDatabasecenterProtoCommonTypedValue $value)
  {
    $this->value = $value;
  }
  /**
   * @return StorageDatabasecenterProtoCommonTypedValue
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StorageDatabasecenterPartnerapiV1mainObservabilityMetricData::class, 'Google_Service_CloudAlloyDBAdmin_StorageDatabasecenterPartnerapiV1mainObservabilityMetricData');
