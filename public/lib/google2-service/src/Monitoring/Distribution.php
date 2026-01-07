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

namespace Google\Service\Monitoring;

class Distribution extends \Google\Collection
{
  protected $collection_key = 'exemplars';
  /**
   * Required in the Cloud Monitoring API v3. The values for each bucket
   * specified in bucket_options. The sum of the values in bucketCounts must
   * equal the value in the count field of the Distribution object. The order of
   * the bucket counts follows the numbering schemes described for the three
   * bucket types. The underflow bucket has number 0; the finite buckets, if
   * any, have numbers 1 through N-2; and the overflow bucket has number N-1.
   * The size of bucket_counts must not be greater than N. If the size is less
   * than N, then the remaining buckets are assigned values of zero.
   *
   * @var string[]
   */
  public $bucketCounts;
  protected $bucketOptionsType = BucketOptions::class;
  protected $bucketOptionsDataType = '';
  /**
   * The number of values in the population. Must be non-negative. This value
   * must equal the sum of the values in bucket_counts if a histogram is
   * provided.
   *
   * @var string
   */
  public $count;
  protected $exemplarsType = Exemplar::class;
  protected $exemplarsDataType = 'array';
  /**
   * The arithmetic mean of the values in the population. If count is zero then
   * this field must be zero.
   *
   * @var 
   */
  public $mean;
  protected $rangeType = Range::class;
  protected $rangeDataType = '';
  /**
   * The sum of squared deviations from the mean of the values in the
   * population. For values x_i this is: Sum[i=1..n]((x_i - mean)^2) Knuth, "The
   * Art of Computer Programming", Vol. 2, page 232, 3rd edition describes
   * Welford's method for accumulating this sum in one pass.If count is zero
   * then this field must be zero.
   *
   * @var 
   */
  public $sumOfSquaredDeviation;

  /**
   * Required in the Cloud Monitoring API v3. The values for each bucket
   * specified in bucket_options. The sum of the values in bucketCounts must
   * equal the value in the count field of the Distribution object. The order of
   * the bucket counts follows the numbering schemes described for the three
   * bucket types. The underflow bucket has number 0; the finite buckets, if
   * any, have numbers 1 through N-2; and the overflow bucket has number N-1.
   * The size of bucket_counts must not be greater than N. If the size is less
   * than N, then the remaining buckets are assigned values of zero.
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
   * Required in the Cloud Monitoring API v3. Defines the histogram bucket
   * boundaries.
   *
   * @param BucketOptions $bucketOptions
   */
  public function setBucketOptions(BucketOptions $bucketOptions)
  {
    $this->bucketOptions = $bucketOptions;
  }
  /**
   * @return BucketOptions
   */
  public function getBucketOptions()
  {
    return $this->bucketOptions;
  }
  /**
   * The number of values in the population. Must be non-negative. This value
   * must equal the sum of the values in bucket_counts if a histogram is
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
   * Must be in increasing order of value field.
   *
   * @param Exemplar[] $exemplars
   */
  public function setExemplars($exemplars)
  {
    $this->exemplars = $exemplars;
  }
  /**
   * @return Exemplar[]
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
   * not be present if the count is zero. This field is presently ignored by the
   * Cloud Monitoring API v3.
   *
   * @param Range $range
   */
  public function setRange(Range $range)
  {
    $this->range = $range;
  }
  /**
   * @return Range
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
class_alias(Distribution::class, 'Google_Service_Monitoring_Distribution');
