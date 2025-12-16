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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1MetricAggregation extends \Google\Model
{
  /**
   * Unspecified Aggregation function.
   */
  public const AGGREGATION_AGGREGATION_FUNCTION_UNSPECIFIED = 'AGGREGATION_FUNCTION_UNSPECIFIED';
  /**
   * Average.
   */
  public const AGGREGATION_AVG = 'AVG';
  /**
   * Summation.
   */
  public const AGGREGATION_SUM = 'SUM';
  /**
   * Min.
   */
  public const AGGREGATION_MIN = 'MIN';
  /**
   * Max.
   */
  public const AGGREGATION_MAX = 'MAX';
  /**
   * Count distinct
   */
  public const AGGREGATION_COUNT_DISTINCT = 'COUNT_DISTINCT';
  /**
   * Unspecified order. Default is Descending.
   */
  public const ORDER_ORDER_UNSPECIFIED = 'ORDER_UNSPECIFIED';
  /**
   * Ascending sort order.
   */
  public const ORDER_ASCENDING = 'ASCENDING';
  /**
   * Descending sort order.
   */
  public const ORDER_DESCENDING = 'DESCENDING';
  /**
   * Aggregation function associated with the metric.
   *
   * @var string
   */
  public $aggregation;
  /**
   * Name of the metric
   *
   * @var string
   */
  public $name;
  /**
   * Ordering for this aggregation in the result. For time series this is
   * ignored since the ordering of points depends only on the timestamp, not the
   * values.
   *
   * @var string
   */
  public $order;

  /**
   * Aggregation function associated with the metric.
   *
   * Accepted values: AGGREGATION_FUNCTION_UNSPECIFIED, AVG, SUM, MIN, MAX,
   * COUNT_DISTINCT
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
   * Name of the metric
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Ordering for this aggregation in the result. For time series this is
   * ignored since the ordering of points depends only on the timestamp, not the
   * values.
   *
   * Accepted values: ORDER_UNSPECIFIED, ASCENDING, DESCENDING
   *
   * @param self::ORDER_* $order
   */
  public function setOrder($order)
  {
    $this->order = $order;
  }
  /**
   * @return self::ORDER_*
   */
  public function getOrder()
  {
    return $this->order;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1MetricAggregation::class, 'Google_Service_Apigee_GoogleCloudApigeeV1MetricAggregation');
