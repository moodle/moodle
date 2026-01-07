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

namespace Google\Service\SecurityCommandCenter;

class PolicyDriftDetails extends \Google\Model
{
  /**
   * The detected value that violates the deployed posture, for example, `false`
   * or `allowed_values={"projects/22831892"}`.
   *
   * @var string
   */
  public $detectedValue;
  /**
   * The value of this field that was configured in a posture, for example,
   * `true` or `allowed_values={"projects/29831892"}`.
   *
   * @var string
   */
  public $expectedValue;
  /**
   * The name of the updated field, for example
   * constraint.implementation.policy_rules[0].enforce
   *
   * @var string
   */
  public $field;

  /**
   * The detected value that violates the deployed posture, for example, `false`
   * or `allowed_values={"projects/22831892"}`.
   *
   * @param string $detectedValue
   */
  public function setDetectedValue($detectedValue)
  {
    $this->detectedValue = $detectedValue;
  }
  /**
   * @return string
   */
  public function getDetectedValue()
  {
    return $this->detectedValue;
  }
  /**
   * The value of this field that was configured in a posture, for example,
   * `true` or `allowed_values={"projects/29831892"}`.
   *
   * @param string $expectedValue
   */
  public function setExpectedValue($expectedValue)
  {
    $this->expectedValue = $expectedValue;
  }
  /**
   * @return string
   */
  public function getExpectedValue()
  {
    return $this->expectedValue;
  }
  /**
   * The name of the updated field, for example
   * constraint.implementation.policy_rules[0].enforce
   *
   * @param string $field
   */
  public function setField($field)
  {
    $this->field = $field;
  }
  /**
   * @return string
   */
  public function getField()
  {
    return $this->field;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PolicyDriftDetails::class, 'Google_Service_SecurityCommandCenter_PolicyDriftDetails');
