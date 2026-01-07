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

namespace Google\Service\Compute;

class FixedOrPercent extends \Google\Model
{
  /**
   * Output only. [Output Only] Absolute value of VM instances calculated based
   * on the specific mode.
   *
   *             - If the value is fixed, then the calculated      value is
   * equal to the fixed value.     - If the value is a percent, then the
   * calculated      value is percent/100 * targetSize. For example,      the
   * calculated value of a 80% of a managed instance group      with 150
   * instances would be (80/100 * 150) = 120 VM instances. If there      is a
   * remainder, the number is rounded.
   *
   * @var int
   */
  public $calculated;
  /**
   * Specifies a fixed number of VM instances. This must be a positive integer.
   *
   * @var int
   */
  public $fixed;
  /**
   * Specifies a percentage of instances between 0 to 100%, inclusive. For
   * example, specify 80 for 80%.
   *
   * @var int
   */
  public $percent;

  /**
   * Output only. [Output Only] Absolute value of VM instances calculated based
   * on the specific mode.
   *
   *             - If the value is fixed, then the calculated      value is
   * equal to the fixed value.     - If the value is a percent, then the
   * calculated      value is percent/100 * targetSize. For example,      the
   * calculated value of a 80% of a managed instance group      with 150
   * instances would be (80/100 * 150) = 120 VM instances. If there      is a
   * remainder, the number is rounded.
   *
   * @param int $calculated
   */
  public function setCalculated($calculated)
  {
    $this->calculated = $calculated;
  }
  /**
   * @return int
   */
  public function getCalculated()
  {
    return $this->calculated;
  }
  /**
   * Specifies a fixed number of VM instances. This must be a positive integer.
   *
   * @param int $fixed
   */
  public function setFixed($fixed)
  {
    $this->fixed = $fixed;
  }
  /**
   * @return int
   */
  public function getFixed()
  {
    return $this->fixed;
  }
  /**
   * Specifies a percentage of instances between 0 to 100%, inclusive. For
   * example, specify 80 for 80%.
   *
   * @param int $percent
   */
  public function setPercent($percent)
  {
    $this->percent = $percent;
  }
  /**
   * @return int
   */
  public function getPercent()
  {
    return $this->percent;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FixedOrPercent::class, 'Google_Service_Compute_FixedOrPercent');
