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

namespace Google\Service\CloudNaturalLanguage;

class XPSFloat64Stats extends \Google\Collection
{
  protected $collection_key = 'quantiles';
  protected $commonStatsType = XPSCommonStats::class;
  protected $commonStatsDataType = '';
  protected $histogramBucketsType = XPSFloat64StatsHistogramBucket::class;
  protected $histogramBucketsDataType = 'array';
  /**
   * The mean of the series.
   *
   * @var 
   */
  public $mean;
  /**
   * Ordered from 0 to k k-quantile values of the data series of n values. The
   * value at index i is, approximately, the i*n/k-th smallest value in the
   * series; for i = 0 and i = k these are, respectively, the min and max
   * values.
   *
   * @var []
   */
  public $quantiles;
  /**
   * The standard deviation of the series.
   *
   * @var 
   */
  public $standardDeviation;

  /**
   * @param XPSCommonStats $commonStats
   */
  public function setCommonStats(XPSCommonStats $commonStats)
  {
    $this->commonStats = $commonStats;
  }
  /**
   * @return XPSCommonStats
   */
  public function getCommonStats()
  {
    return $this->commonStats;
  }
  /**
   * Histogram buckets of the data series. Sorted by the min value of the
   * bucket, ascendingly, and the number of the buckets is dynamically
   * generated. The buckets are non-overlapping and completely cover whole
   * FLOAT64 range with min of first bucket being `"-Infinity"`, and max of the
   * last one being `"Infinity"`.
   *
   * @param XPSFloat64StatsHistogramBucket[] $histogramBuckets
   */
  public function setHistogramBuckets($histogramBuckets)
  {
    $this->histogramBuckets = $histogramBuckets;
  }
  /**
   * @return XPSFloat64StatsHistogramBucket[]
   */
  public function getHistogramBuckets()
  {
    return $this->histogramBuckets;
  }
  public function setMean($mean)
  {
    $this->mean = $mean;
  }
  public function getMean()
  {
    return $this->mean;
  }
  public function setQuantiles($quantiles)
  {
    $this->quantiles = $quantiles;
  }
  public function getQuantiles()
  {
    return $this->quantiles;
  }
  public function setStandardDeviation($standardDeviation)
  {
    $this->standardDeviation = $standardDeviation;
  }
  public function getStandardDeviation()
  {
    return $this->standardDeviation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSFloat64Stats::class, 'Google_Service_CloudNaturalLanguage_XPSFloat64Stats');
