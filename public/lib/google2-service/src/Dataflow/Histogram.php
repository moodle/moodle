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

class Histogram extends \Google\Collection
{
  protected $collection_key = 'bucketCounts';
  /**
   * Counts of values in each bucket. For efficiency, prefix and trailing
   * buckets with count = 0 are elided. Buckets can store the full range of
   * values of an unsigned long, with ULLONG_MAX falling into the 59th bucket
   * with range [1e19, 2e19).
   *
   * @var string[]
   */
  public $bucketCounts;
  /**
   * Starting index of first stored bucket. The non-inclusive upper-bound of the
   * ith bucket is given by: pow(10,(i-first_bucket_offset)/3) *
   * (1,2,5)[(i-first_bucket_offset)%3]
   *
   * @var int
   */
  public $firstBucketOffset;

  /**
   * Counts of values in each bucket. For efficiency, prefix and trailing
   * buckets with count = 0 are elided. Buckets can store the full range of
   * values of an unsigned long, with ULLONG_MAX falling into the 59th bucket
   * with range [1e19, 2e19).
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
   * Starting index of first stored bucket. The non-inclusive upper-bound of the
   * ith bucket is given by: pow(10,(i-first_bucket_offset)/3) *
   * (1,2,5)[(i-first_bucket_offset)%3]
   *
   * @param int $firstBucketOffset
   */
  public function setFirstBucketOffset($firstBucketOffset)
  {
    $this->firstBucketOffset = $firstBucketOffset;
  }
  /**
   * @return int
   */
  public function getFirstBucketOffset()
  {
    return $this->firstBucketOffset;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Histogram::class, 'Google_Service_Dataflow_Histogram');
