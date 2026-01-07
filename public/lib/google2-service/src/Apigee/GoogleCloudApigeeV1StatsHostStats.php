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

class GoogleCloudApigeeV1StatsHostStats extends \Google\Collection
{
  protected $collection_key = 'metrics';
  protected $dimensionsType = GoogleCloudApigeeV1DimensionMetric::class;
  protected $dimensionsDataType = 'array';
  protected $metricsType = GoogleCloudApigeeV1Metric::class;
  protected $metricsDataType = 'array';
  /**
   * Hostname used in query.
   *
   * @var string
   */
  public $name;

  /**
   * List of metrics grouped under dimensions.
   *
   * @param GoogleCloudApigeeV1DimensionMetric[] $dimensions
   */
  public function setDimensions($dimensions)
  {
    $this->dimensions = $dimensions;
  }
  /**
   * @return GoogleCloudApigeeV1DimensionMetric[]
   */
  public function getDimensions()
  {
    return $this->dimensions;
  }
  /**
   * In the final response, only one of the following fields will be present
   * based on the dimensions provided. If no dimensions are provided, then only
   * the top-level metrics are provided. If dimensions are included, then there
   * will be a top-level dimensions field under hostnames which will contain
   * metrics values and the dimension name. Example: ``` "hosts": [ {
   * "dimensions": [ { "metrics": [ { "name": "sum(message_count)", "values": [
   * "2.14049521E8" ] } ], "name": "nit_proxy" } ], "name": "example.com" } ]```
   * OR ```"hosts": [ { "metrics": [ { "name": "sum(message_count)", "values": [
   * "2.19026331E8" ] } ], "name": "example.com" } ]``` List of metric values.
   *
   * @param GoogleCloudApigeeV1Metric[] $metrics
   */
  public function setMetrics($metrics)
  {
    $this->metrics = $metrics;
  }
  /**
   * @return GoogleCloudApigeeV1Metric[]
   */
  public function getMetrics()
  {
    return $this->metrics;
  }
  /**
   * Hostname used in query.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1StatsHostStats::class, 'Google_Service_Apigee_GoogleCloudApigeeV1StatsHostStats');
