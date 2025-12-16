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

class XPSFloat64StatsHistogramBucket extends \Google\Model
{
  /**
   * The number of data values that are in the bucket, i.e. are between min and
   * max values.
   *
   * @var string
   */
  public $count;
  /**
   * The maximum value of the bucket, exclusive unless max = `"Infinity"`, in
   * which case it's inclusive.
   *
   * @var 
   */
  public $max;
  /**
   * The minimum value of the bucket, inclusive.
   *
   * @var 
   */
  public $min;

  /**
   * The number of data values that are in the bucket, i.e. are between min and
   * max values.
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
  public function setMax($max)
  {
    $this->max = $max;
  }
  public function getMax()
  {
    return $this->max;
  }
  public function setMin($min)
  {
    $this->min = $min;
  }
  public function getMin()
  {
    return $this->min;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSFloat64StatsHistogramBucket::class, 'Google_Service_CloudNaturalLanguage_XPSFloat64StatsHistogramBucket');
