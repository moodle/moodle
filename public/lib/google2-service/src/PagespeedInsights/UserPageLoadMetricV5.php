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

namespace Google\Service\PagespeedInsights;

class UserPageLoadMetricV5 extends \Google\Collection
{
  protected $collection_key = 'distributions';
  /**
   * The category of the specific time metric.
   *
   * @var string
   */
  public $category;
  protected $distributionsType = Bucket::class;
  protected $distributionsDataType = 'array';
  /**
   * Identifies the form factor of the metric being collected.
   *
   * @var string
   */
  public $formFactor;
  /**
   * The median number of the metric, in millisecond.
   *
   * @var int
   */
  public $median;
  /**
   * Identifies the type of the metric.
   *
   * @var string
   */
  public $metricId;
  /**
   * We use this field to store certain percentile value for this metric. For
   * v4, this field contains pc50. For v5, this field contains pc90.
   *
   * @var int
   */
  public $percentile;

  /**
   * The category of the specific time metric.
   *
   * @param string $category
   */
  public function setCategory($category)
  {
    $this->category = $category;
  }
  /**
   * @return string
   */
  public function getCategory()
  {
    return $this->category;
  }
  /**
   * Metric distributions. Proportions should sum up to 1.
   *
   * @param Bucket[] $distributions
   */
  public function setDistributions($distributions)
  {
    $this->distributions = $distributions;
  }
  /**
   * @return Bucket[]
   */
  public function getDistributions()
  {
    return $this->distributions;
  }
  /**
   * Identifies the form factor of the metric being collected.
   *
   * @param string $formFactor
   */
  public function setFormFactor($formFactor)
  {
    $this->formFactor = $formFactor;
  }
  /**
   * @return string
   */
  public function getFormFactor()
  {
    return $this->formFactor;
  }
  /**
   * The median number of the metric, in millisecond.
   *
   * @param int $median
   */
  public function setMedian($median)
  {
    $this->median = $median;
  }
  /**
   * @return int
   */
  public function getMedian()
  {
    return $this->median;
  }
  /**
   * Identifies the type of the metric.
   *
   * @param string $metricId
   */
  public function setMetricId($metricId)
  {
    $this->metricId = $metricId;
  }
  /**
   * @return string
   */
  public function getMetricId()
  {
    return $this->metricId;
  }
  /**
   * We use this field to store certain percentile value for this metric. For
   * v4, this field contains pc50. For v5, this field contains pc90.
   *
   * @param int $percentile
   */
  public function setPercentile($percentile)
  {
    $this->percentile = $percentile;
  }
  /**
   * @return int
   */
  public function getPercentile()
  {
    return $this->percentile;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UserPageLoadMetricV5::class, 'Google_Service_PagespeedInsights_UserPageLoadMetricV5');
