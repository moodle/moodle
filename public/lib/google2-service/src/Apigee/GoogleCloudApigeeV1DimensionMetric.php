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

class GoogleCloudApigeeV1DimensionMetric extends \Google\Collection
{
  protected $collection_key = 'metrics';
  /**
   * Individual dimension names. E.g. ["dim1_name", "dim2_name"].
   *
   * @var string[]
   */
  public $individualNames;
  protected $metricsType = GoogleCloudApigeeV1Metric::class;
  protected $metricsDataType = 'array';
  /**
   * Comma joined dimension names. E.g. "dim1_name,dim2_name". Deprecated. If
   * name already has comma before join, we may get wrong splits. Please use
   * individual_names.
   *
   * @deprecated
   * @var string
   */
  public $name;

  /**
   * Individual dimension names. E.g. ["dim1_name", "dim2_name"].
   *
   * @param string[] $individualNames
   */
  public function setIndividualNames($individualNames)
  {
    $this->individualNames = $individualNames;
  }
  /**
   * @return string[]
   */
  public function getIndividualNames()
  {
    return $this->individualNames;
  }
  /**
   * List of metrics.
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
   * Comma joined dimension names. E.g. "dim1_name,dim2_name". Deprecated. If
   * name already has comma before join, we may get wrong splits. Please use
   * individual_names.
   *
   * @deprecated
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1DimensionMetric::class, 'Google_Service_Apigee_GoogleCloudApigeeV1DimensionMetric');
