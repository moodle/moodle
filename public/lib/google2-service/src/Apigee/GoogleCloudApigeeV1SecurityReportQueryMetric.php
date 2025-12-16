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

class GoogleCloudApigeeV1SecurityReportQueryMetric extends \Google\Model
{
  /**
   * Aggregation function: avg, min, max, or sum.
   *
   * @var string
   */
  public $aggregationFunction;
  /**
   * Alias for the metric. Alias will be used to replace metric name in query
   * results.
   *
   * @var string
   */
  public $alias;
  /**
   * Required. Metric name.
   *
   * @var string
   */
  public $name;
  /**
   * One of `+`, `-`, `/`, `%`, `*`.
   *
   * @var string
   */
  public $operator;
  /**
   * Operand value should be provided when operator is set.
   *
   * @var string
   */
  public $value;

  /**
   * Aggregation function: avg, min, max, or sum.
   *
   * @param string $aggregationFunction
   */
  public function setAggregationFunction($aggregationFunction)
  {
    $this->aggregationFunction = $aggregationFunction;
  }
  /**
   * @return string
   */
  public function getAggregationFunction()
  {
    return $this->aggregationFunction;
  }
  /**
   * Alias for the metric. Alias will be used to replace metric name in query
   * results.
   *
   * @param string $alias
   */
  public function setAlias($alias)
  {
    $this->alias = $alias;
  }
  /**
   * @return string
   */
  public function getAlias()
  {
    return $this->alias;
  }
  /**
   * Required. Metric name.
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
   * One of `+`, `-`, `/`, `%`, `*`.
   *
   * @param string $operator
   */
  public function setOperator($operator)
  {
    $this->operator = $operator;
  }
  /**
   * @return string
   */
  public function getOperator()
  {
    return $this->operator;
  }
  /**
   * Operand value should be provided when operator is set.
   *
   * @param string $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1SecurityReportQueryMetric::class, 'Google_Service_Apigee_GoogleCloudApigeeV1SecurityReportQueryMetric');
