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

namespace Google\Service\MigrationCenterAPI;

class AggregationResultHistogramBucket extends \Google\Model
{
  /**
   * Count of items in the bucket.
   *
   * @var string
   */
  public $count;
  /**
   * Lower bound - inclusive.
   *
   * @var 
   */
  public $lowerBound;
  /**
   * Upper bound - exclusive.
   *
   * @var 
   */
  public $upperBound;

  /**
   * Count of items in the bucket.
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
  public function setLowerBound($lowerBound)
  {
    $this->lowerBound = $lowerBound;
  }
  public function getLowerBound()
  {
    return $this->lowerBound;
  }
  public function setUpperBound($upperBound)
  {
    $this->upperBound = $upperBound;
  }
  public function getUpperBound()
  {
    return $this->upperBound;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AggregationResultHistogramBucket::class, 'Google_Service_MigrationCenterAPI_AggregationResultHistogramBucket');
