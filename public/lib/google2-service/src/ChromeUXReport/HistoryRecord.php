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

namespace Google\Service\ChromeUXReport;

class HistoryRecord extends \Google\Collection
{
  protected $collection_key = 'collectionPeriods';
  protected $collectionPeriodsType = CollectionPeriod::class;
  protected $collectionPeriodsDataType = 'array';
  protected $keyType = HistoryKey::class;
  protected $keyDataType = '';
  protected $metricsType = MetricTimeseries::class;
  protected $metricsDataType = 'map';

  /**
   * The collection periods indicate when each of the data points reflected in
   * the time series data in metrics was collected. Note that all the time
   * series share the same collection periods, and it is enforced in the CrUX
   * pipeline that every time series has the same number of data points.
   *
   * @param CollectionPeriod[] $collectionPeriods
   */
  public function setCollectionPeriods($collectionPeriods)
  {
    $this->collectionPeriods = $collectionPeriods;
  }
  /**
   * @return CollectionPeriod[]
   */
  public function getCollectionPeriods()
  {
    return $this->collectionPeriods;
  }
  /**
   * Key defines all of the unique querying parameters needed to look up a user
   * experience history record.
   *
   * @param HistoryKey $key
   */
  public function setKey(HistoryKey $key)
  {
    $this->key = $key;
  }
  /**
   * @return HistoryKey
   */
  public function getKey()
  {
    return $this->key;
  }
  /**
   * Metrics is the map of user experience time series data available for the
   * record defined in the key field. Metrics are keyed on the metric name.
   * Allowed key values: ["first_contentful_paint", "first_input_delay",
   * "largest_contentful_paint", "cumulative_layout_shift",
   * "experimental_time_to_first_byte",
   * "experimental_interaction_to_next_paint"]
   *
   * @param MetricTimeseries[] $metrics
   */
  public function setMetrics($metrics)
  {
    $this->metrics = $metrics;
  }
  /**
   * @return MetricTimeseries[]
   */
  public function getMetrics()
  {
    return $this->metrics;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HistoryRecord::class, 'Google_Service_ChromeUXReport_HistoryRecord');
