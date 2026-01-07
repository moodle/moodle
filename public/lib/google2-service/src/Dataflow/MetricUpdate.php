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

namespace Google\Service\Dataflow;

class MetricUpdate extends \Google\Model
{
  /**
   * Worker-computed aggregate value for the "Trie" aggregation kind. The only
   * possible value type is a BoundedTrieNode. Introduced this field to avoid
   * breaking older SDKs when Dataflow service starts to populate the
   * `bounded_trie` field.
   *
   * @var array
   */
  public $boundedTrie;
  /**
   * True if this metric is reported as the total cumulative aggregate value
   * accumulated since the worker started working on this WorkItem. By default
   * this is false, indicating that this metric is reported as a delta that is
   * not associated with any WorkItem.
   *
   * @var bool
   */
  public $cumulative;
  /**
   * A struct value describing properties of a distribution of numeric values.
   *
   * @var array
   */
  public $distribution;
  /**
   * A struct value describing properties of a Gauge. Metrics of gauge type show
   * the value of a metric across time, and is aggregated based on the newest
   * value.
   *
   * @var array
   */
  public $gauge;
  /**
   * Worker-computed aggregate value for internal use by the Dataflow service.
   *
   * @var array
   */
  public $internal;
  /**
   * Metric aggregation kind. The possible metric aggregation kinds are "Sum",
   * "Max", "Min", "Mean", "Set", "And", "Or", and "Distribution". The specified
   * aggregation kind is case-insensitive. If omitted, this is not an aggregated
   * value but instead a single metric sample value.
   *
   * @var string
   */
  public $kind;
  /**
   * Worker-computed aggregate value for the "Mean" aggregation kind. This holds
   * the count of the aggregated values and is used in combination with mean_sum
   * above to obtain the actual mean aggregate value. The only possible value
   * type is Long.
   *
   * @var array
   */
  public $meanCount;
  /**
   * Worker-computed aggregate value for the "Mean" aggregation kind. This holds
   * the sum of the aggregated values and is used in combination with mean_count
   * below to obtain the actual mean aggregate value. The only possible value
   * types are Long and Double.
   *
   * @var array
   */
  public $meanSum;
  protected $nameType = MetricStructuredName::class;
  protected $nameDataType = '';
  /**
   * Worker-computed aggregate value for aggregation kinds "Sum", "Max", "Min",
   * "And", and "Or". The possible value types are Long, Double, and Boolean.
   *
   * @var array
   */
  public $scalar;
  /**
   * Worker-computed aggregate value for the "Set" aggregation kind. The only
   * possible value type is a list of Values whose type can be Long, Double,
   * String, or BoundedTrie according to the metric's type. All Values in the
   * list must be of the same type.
   *
   * @var array
   */
  public $set;
  /**
   * Worker-computed aggregate value for the "Trie" aggregation kind. The only
   * possible value type is a BoundedTrieNode.
   *
   * @var array
   */
  public $trie;
  /**
   * Timestamp associated with the metric value. Optional when workers are
   * reporting work progress; it will be filled in responses from the metrics
   * API.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Worker-computed aggregate value for the "Trie" aggregation kind. The only
   * possible value type is a BoundedTrieNode. Introduced this field to avoid
   * breaking older SDKs when Dataflow service starts to populate the
   * `bounded_trie` field.
   *
   * @param array $boundedTrie
   */
  public function setBoundedTrie($boundedTrie)
  {
    $this->boundedTrie = $boundedTrie;
  }
  /**
   * @return array
   */
  public function getBoundedTrie()
  {
    return $this->boundedTrie;
  }
  /**
   * True if this metric is reported as the total cumulative aggregate value
   * accumulated since the worker started working on this WorkItem. By default
   * this is false, indicating that this metric is reported as a delta that is
   * not associated with any WorkItem.
   *
   * @param bool $cumulative
   */
  public function setCumulative($cumulative)
  {
    $this->cumulative = $cumulative;
  }
  /**
   * @return bool
   */
  public function getCumulative()
  {
    return $this->cumulative;
  }
  /**
   * A struct value describing properties of a distribution of numeric values.
   *
   * @param array $distribution
   */
  public function setDistribution($distribution)
  {
    $this->distribution = $distribution;
  }
  /**
   * @return array
   */
  public function getDistribution()
  {
    return $this->distribution;
  }
  /**
   * A struct value describing properties of a Gauge. Metrics of gauge type show
   * the value of a metric across time, and is aggregated based on the newest
   * value.
   *
   * @param array $gauge
   */
  public function setGauge($gauge)
  {
    $this->gauge = $gauge;
  }
  /**
   * @return array
   */
  public function getGauge()
  {
    return $this->gauge;
  }
  /**
   * Worker-computed aggregate value for internal use by the Dataflow service.
   *
   * @param array $internal
   */
  public function setInternal($internal)
  {
    $this->internal = $internal;
  }
  /**
   * @return array
   */
  public function getInternal()
  {
    return $this->internal;
  }
  /**
   * Metric aggregation kind. The possible metric aggregation kinds are "Sum",
   * "Max", "Min", "Mean", "Set", "And", "Or", and "Distribution". The specified
   * aggregation kind is case-insensitive. If omitted, this is not an aggregated
   * value but instead a single metric sample value.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Worker-computed aggregate value for the "Mean" aggregation kind. This holds
   * the count of the aggregated values and is used in combination with mean_sum
   * above to obtain the actual mean aggregate value. The only possible value
   * type is Long.
   *
   * @param array $meanCount
   */
  public function setMeanCount($meanCount)
  {
    $this->meanCount = $meanCount;
  }
  /**
   * @return array
   */
  public function getMeanCount()
  {
    return $this->meanCount;
  }
  /**
   * Worker-computed aggregate value for the "Mean" aggregation kind. This holds
   * the sum of the aggregated values and is used in combination with mean_count
   * below to obtain the actual mean aggregate value. The only possible value
   * types are Long and Double.
   *
   * @param array $meanSum
   */
  public function setMeanSum($meanSum)
  {
    $this->meanSum = $meanSum;
  }
  /**
   * @return array
   */
  public function getMeanSum()
  {
    return $this->meanSum;
  }
  /**
   * Name of the metric.
   *
   * @param MetricStructuredName $name
   */
  public function setName(MetricStructuredName $name)
  {
    $this->name = $name;
  }
  /**
   * @return MetricStructuredName
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Worker-computed aggregate value for aggregation kinds "Sum", "Max", "Min",
   * "And", and "Or". The possible value types are Long, Double, and Boolean.
   *
   * @param array $scalar
   */
  public function setScalar($scalar)
  {
    $this->scalar = $scalar;
  }
  /**
   * @return array
   */
  public function getScalar()
  {
    return $this->scalar;
  }
  /**
   * Worker-computed aggregate value for the "Set" aggregation kind. The only
   * possible value type is a list of Values whose type can be Long, Double,
   * String, or BoundedTrie according to the metric's type. All Values in the
   * list must be of the same type.
   *
   * @param array $set
   */
  public function setSet($set)
  {
    $this->set = $set;
  }
  /**
   * @return array
   */
  public function getSet()
  {
    return $this->set;
  }
  /**
   * Worker-computed aggregate value for the "Trie" aggregation kind. The only
   * possible value type is a BoundedTrieNode.
   *
   * @param array $trie
   */
  public function setTrie($trie)
  {
    $this->trie = $trie;
  }
  /**
   * @return array
   */
  public function getTrie()
  {
    return $this->trie;
  }
  /**
   * Timestamp associated with the metric value. Optional when workers are
   * reporting work progress; it will be filled in responses from the metrics
   * API.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MetricUpdate::class, 'Google_Service_Dataflow_MetricUpdate');
