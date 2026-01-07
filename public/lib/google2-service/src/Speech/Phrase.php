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

namespace Google\Service\Speech;

class Phrase extends \Google\Model
{
  /**
   * Hint Boost. Overrides the boost set at the phrase set level. Positive value
   * will increase the probability that a specific phrase will be recognized
   * over other similar sounding phrases. The higher the boost, the higher the
   * chance of false positive recognition as well. Negative boost will simply be
   * ignored. Though `boost` can accept a wide range of positive values, most
   * use cases are best served with values between 0 and 20. We recommend using
   * a binary search approach to finding the optimal value for your use case as
   * well as adding phrases both with and without boost to your requests.
   *
   * @var float
   */
  public $boost;
  /**
   * The phrase itself.
   *
   * @var string
   */
  public $value;

  /**
   * Hint Boost. Overrides the boost set at the phrase set level. Positive value
   * will increase the probability that a specific phrase will be recognized
   * over other similar sounding phrases. The higher the boost, the higher the
   * chance of false positive recognition as well. Negative boost will simply be
   * ignored. Though `boost` can accept a wide range of positive values, most
   * use cases are best served with values between 0 and 20. We recommend using
   * a binary search approach to finding the optimal value for your use case as
   * well as adding phrases both with and without boost to your requests.
   *
   * @param float $boost
   */
  public function setBoost($boost)
  {
    $this->boost = $boost;
  }
  /**
   * @return float
   */
  public function getBoost()
  {
    return $this->boost;
  }
  /**
   * The phrase itself.
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
class_alias(Phrase::class, 'Google_Service_Speech_Phrase');
