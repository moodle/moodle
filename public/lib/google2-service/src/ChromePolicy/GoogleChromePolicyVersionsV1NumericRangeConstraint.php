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

namespace Google\Service\ChromePolicy;

class GoogleChromePolicyVersionsV1NumericRangeConstraint extends \Google\Model
{
  /**
   * Maximum value.
   *
   * @var string
   */
  public $maximum;
  /**
   * Minimum value.
   *
   * @var string
   */
  public $minimum;

  /**
   * Maximum value.
   *
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
   * Minimum value.
   *
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromePolicyVersionsV1NumericRangeConstraint::class, 'Google_Service_ChromePolicy_GoogleChromePolicyVersionsV1NumericRangeConstraint');
