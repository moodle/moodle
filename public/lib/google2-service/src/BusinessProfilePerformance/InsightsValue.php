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

namespace Google\Service\BusinessProfilePerformance;

class InsightsValue extends \Google\Model
{
  /**
   * Represents the threshold below which the actual value falls.
   *
   * @var string
   */
  public $threshold;
  /**
   * Represents the actual value.
   *
   * @var string
   */
  public $value;

  /**
   * Represents the threshold below which the actual value falls.
   *
   * @param string $threshold
   */
  public function setThreshold($threshold)
  {
    $this->threshold = $threshold;
  }
  /**
   * @return string
   */
  public function getThreshold()
  {
    return $this->threshold;
  }
  /**
   * Represents the actual value.
   *
   * @param string $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InsightsValue::class, 'Google_Service_BusinessProfilePerformance_InsightsValue');
