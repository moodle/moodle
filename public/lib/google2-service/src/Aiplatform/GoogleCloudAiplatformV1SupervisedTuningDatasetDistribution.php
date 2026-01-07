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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1SupervisedTuningDatasetDistribution extends \Google\Collection
{
  protected $collection_key = 'buckets';
  /**
   * Output only. Sum of a given population of values that are billable.
   *
   * @var string
   */
  public $billableSum;
  protected $bucketsType = GoogleCloudAiplatformV1SupervisedTuningDatasetDistributionDatasetBucket::class;
  protected $bucketsDataType = 'array';
  /**
   * Output only. The maximum of the population values.
   *
   * @var 
   */
  public $max;
  /**
   * Output only. The arithmetic mean of the values in the population.
   *
   * @var 
   */
  public $mean;
  /**
   * Output only. The median of the values in the population.
   *
   * @var 
   */
  public $median;
  /**
   * Output only. The minimum of the population values.
   *
   * @var 
   */
  public $min;
  /**
   * Output only. The 5th percentile of the values in the population.
   *
   * @var 
   */
  public $p5;
  /**
   * Output only. The 95th percentile of the values in the population.
   *
   * @var 
   */
  public $p95;
  /**
   * Output only. Sum of a given population of values.
   *
   * @var string
   */
  public $sum;

  /**
   * Output only. Sum of a given population of values that are billable.
   *
   * @param string $billableSum
   */
  public function setBillableSum($billableSum)
  {
    $this->billableSum = $billableSum;
  }
  /**
   * @return string
   */
  public function getBillableSum()
  {
    return $this->billableSum;
  }
  /**
   * Output only. Defines the histogram bucket.
   *
   * @param GoogleCloudAiplatformV1SupervisedTuningDatasetDistributionDatasetBucket[] $buckets
   */
  public function setBuckets($buckets)
  {
    $this->buckets = $buckets;
  }
  /**
   * @return GoogleCloudAiplatformV1SupervisedTuningDatasetDistributionDatasetBucket[]
   */
  public function getBuckets()
  {
    return $this->buckets;
  }
  public function setMax($max)
  {
    $this->max = $max;
  }
  public function getMax()
  {
    return $this->max;
  }
  public function setMean($mean)
  {
    $this->mean = $mean;
  }
  public function getMean()
  {
    return $this->mean;
  }
  public function setMedian($median)
  {
    $this->median = $median;
  }
  public function getMedian()
  {
    return $this->median;
  }
  public function setMin($min)
  {
    $this->min = $min;
  }
  public function getMin()
  {
    return $this->min;
  }
  public function setP5($p5)
  {
    $this->p5 = $p5;
  }
  public function getP5()
  {
    return $this->p5;
  }
  public function setP95($p95)
  {
    $this->p95 = $p95;
  }
  public function getP95()
  {
    return $this->p95;
  }
  /**
   * Output only. Sum of a given population of values.
   *
   * @param string $sum
   */
  public function setSum($sum)
  {
    $this->sum = $sum;
  }
  /**
   * @return string
   */
  public function getSum()
  {
    return $this->sum;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SupervisedTuningDatasetDistribution::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SupervisedTuningDatasetDistribution');
