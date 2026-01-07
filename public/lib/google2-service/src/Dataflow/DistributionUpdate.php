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

class DistributionUpdate extends \Google\Model
{
  protected $countType = SplitInt64::class;
  protected $countDataType = '';
  protected $histogramType = Histogram::class;
  protected $histogramDataType = '';
  protected $maxType = SplitInt64::class;
  protected $maxDataType = '';
  protected $minType = SplitInt64::class;
  protected $minDataType = '';
  protected $sumType = SplitInt64::class;
  protected $sumDataType = '';
  /**
   * Use a double since the sum of squares is likely to overflow int64.
   *
   * @var 
   */
  public $sumOfSquares;

  /**
   * The count of the number of elements present in the distribution.
   *
   * @param SplitInt64 $count
   */
  public function setCount(SplitInt64 $count)
  {
    $this->count = $count;
  }
  /**
   * @return SplitInt64
   */
  public function getCount()
  {
    return $this->count;
  }
  /**
   * (Optional) Histogram of value counts for the distribution.
   *
   * @param Histogram $histogram
   */
  public function setHistogram(Histogram $histogram)
  {
    $this->histogram = $histogram;
  }
  /**
   * @return Histogram
   */
  public function getHistogram()
  {
    return $this->histogram;
  }
  /**
   * The maximum value present in the distribution.
   *
   * @param SplitInt64 $max
   */
  public function setMax(SplitInt64 $max)
  {
    $this->max = $max;
  }
  /**
   * @return SplitInt64
   */
  public function getMax()
  {
    return $this->max;
  }
  /**
   * The minimum value present in the distribution.
   *
   * @param SplitInt64 $min
   */
  public function setMin(SplitInt64 $min)
  {
    $this->min = $min;
  }
  /**
   * @return SplitInt64
   */
  public function getMin()
  {
    return $this->min;
  }
  /**
   * Use an int64 since we'd prefer the added precision. If overflow is a common
   * problem we can detect it and use an additional int64 or a double.
   *
   * @param SplitInt64 $sum
   */
  public function setSum(SplitInt64 $sum)
  {
    $this->sum = $sum;
  }
  /**
   * @return SplitInt64
   */
  public function getSum()
  {
    return $this->sum;
  }
  public function setSumOfSquares($sumOfSquares)
  {
    $this->sumOfSquares = $sumOfSquares;
  }
  public function getSumOfSquares()
  {
    return $this->sumOfSquares;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DistributionUpdate::class, 'Google_Service_Dataflow_DistributionUpdate');
