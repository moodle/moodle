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

namespace Google\Service\Dataproc;

class Quantiles extends \Google\Model
{
  /**
   * @var string
   */
  public $count;
  /**
   * @var string
   */
  public $maximum;
  /**
   * @var string
   */
  public $minimum;
  /**
   * @var string
   */
  public $percentile25;
  /**
   * @var string
   */
  public $percentile50;
  /**
   * @var string
   */
  public $percentile75;
  /**
   * @var string
   */
  public $sum;

  /**
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
   * @param string $maximum
   */
  public function setMaximum($maximum)
  {
    $this->maximum = $maximum;
  }
  /**
   * @return string
   */
  public function getMaximum()
  {
    return $this->maximum;
  }
  /**
   * @param string $minimum
   */
  public function setMinimum($minimum)
  {
    $this->minimum = $minimum;
  }
  /**
   * @return string
   */
  public function getMinimum()
  {
    return $this->minimum;
  }
  /**
   * @param string $percentile25
   */
  public function setPercentile25($percentile25)
  {
    $this->percentile25 = $percentile25;
  }
  /**
   * @return string
   */
  public function getPercentile25()
  {
    return $this->percentile25;
  }
  /**
   * @param string $percentile50
   */
  public function setPercentile50($percentile50)
  {
    $this->percentile50 = $percentile50;
  }
  /**
   * @return string
   */
  public function getPercentile50()
  {
    return $this->percentile50;
  }
  /**
   * @param string $percentile75
   */
  public function setPercentile75($percentile75)
  {
    $this->percentile75 = $percentile75;
  }
  /**
   * @return string
   */
  public function getPercentile75()
  {
    return $this->percentile75;
  }
  /**
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
class_alias(Quantiles::class, 'Google_Service_Dataproc_Quantiles');
