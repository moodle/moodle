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

namespace Google\Service\DiscoveryEngine;

class GoogleApiDistribution extends \Google\Collection
{
  protected $collection_key = 'exemplars';
  /**
   * The number of values in each bucket of the histogram, as described in
   * `bucket_options`. If the distribution does not have a histogram, then omit
   * this field. If there is a histogram, then the sum of the values in
   * `bucket_counts` must equal the value in the `count` field of the
   * distribution. If present, `bucket_counts` should contain N values, where N
   * is the number of buckets specified in `bucket_options`. If you supply fewer
   * than N values, the remaining values are assumed to be 0. The order of the
   * values in `bucket_counts` follows the bucket numbering schemes described
   * for the three bucket types. The first value must be the count for the
   * underflow bucket (number 0). The next N-2 values are the counts for the
   * finite buckets (number 1 through N-2). The N'th value in `bucket_counts` is
   * the count for the overflow bucket (number N-1).
   *
   * @var string[]
   */
  public $bucketCounts;
  protected $bucketOptionsType = GoogleApiDistributionBucketOptions::class;
  protected $bucketOptionsDataType = '';
  /**
   * The number of values in the population. Must be non-negative. This value
   * must equal the sum of the values in `bucket_counts` if a histogram is
   * provided.
   *
   * @var string
   */
  public $count;
  protected $exemplarsType = GoogleApiDistributionExemplar::class;
  protected $exemplarsDataType = 'array';
  /**
   * The arithmetic mean of the values in the population. If `count` is zero
   * then this field must be zero.
   *
   * @var 
   */
  public $mean;
  protected $rangeType = GoogleApiDistributionRange::class;
  protected $rangeDataType = '';
  /**
   * The sum of squared deviations from the mean of the values in the
   * population. For values x_i this is: Sum[i=1..n]((x_i - mean)^2) Knuth, "The
   * Art of Computer Programming", Vol. 2, page 232, 3rd edition describes
   * Welford's method for accumulating this sum in one pass. If `count` is zero
   * then this field must be zero.
   *
   * @var 
   */
  public $sumOfSquaredDeviation;

  /**
   * The number of values in each bucket of the histogram, as described in
   * `bucket_options`. If the distribution does not have a histogram, then omit
   * this field. If there is a histogram, then the sum of the values in
   * `bucket_counts` must equal the value in the `count` field of the
   * distribution. If present, `bucket_counts` should contain N values, where N
   * is the number of buckets specified in `bucket_options`. If you supply fewer
   * than N values, the remaining values are assumed to be 0. The order of the
   * values in `bucket_counts` follows the bucket numbering schemes described
   * for the three bucket types. The first value must be the count for the
   * underflow bucket (number 0). The next N-2 values are the counts for the
   * finite buckets (number 1 through N-2). The N'th value in `bucket_counts` is
   * the count for the overflow bucket (number N-1).
   *
   * @param string[] $bucketCounts
   */
  public function setBucketCounts($bucketCounts)
  {
    $this->bucketCounts = $bucketCounts;
  }
  /**
   * @return string[]
   */
  public function getBucketCounts()
  {
    return $this->bucketCounts;
  }
  /**
   * Defines the histogram bucket boundaries. If the distribution does not
   * contain a histogram, then omit this field.
   *
   * @param GoogleApiDistributionBucketOptions $bucketOptions
   */
  public function setBucketOptions(GoogleApiDistributionBucketOptions $bucketOptions)
  {
    $this->bucketOptions = $bucketOptions;
  }
  /**
   * @return GoogleApiDistributionBucketOptions
   */
  public function getBucketOptions()
  {
    return $this->bucketOptions;
  }
  /**
   * The number of values in the population. Must be non-negative. This value
   * must equal the sum of the values in `bucket_counts` if a histogram is
   * provided.
   *
   * @param string $count
   */
  public function setCount($count)
  {
    $this->count = $count;
  }
  /**
   * @return string
   */
  public function getCount()
  {
    return $this->count;
  }
  /**
   * Must be in increasing order of `value` field.
   *
   * @param GoogleApiDistributionExemplar[] $exemplars
   */
  public function setExemplars($exemplars)
  {
    $this->exemplars = $exemplars;
  }
  /**
   * @return GoogleApiDistributionExemplar[]
   */
  public function getExemplars()
  {
    return $this->exemplars;
  }
  public function setMean($mean)
  {
    $this->mean = $mean;
  }
  public function getMean()
  {
    return $this->mean;
  }
  /**
   * If specified, contains the range of the population values. The field must
   * not be present if the `count` is zero.
   *
   * @param GoogleApiDistributionRange $range
   */
  public function setRange(GoogleApiDistributionRange $range)
  {
    $this->range = $range;
  }
  /**
   * @return GoogleApiDistributionRange
   */
  public function getRange()
  {
    return $this->range;
  }
  public function setSumOfSquaredDeviation($sumOfSquaredDeviation)
  {
    $this->sumOfSquaredDeviation = $sumOfSquaredDeviation;
  }
  public function getSumOfSquaredDeviation()
  {
    return $this->sumOfSquaredDeviation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleApiDistribution::class, 'Google_Service_DiscoveryEngine_GoogleApiDistribution');
