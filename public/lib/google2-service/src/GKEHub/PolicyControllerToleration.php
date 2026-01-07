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

namespace Google\Service\GKEHub;

class PolicyControllerToleration extends \Google\Model
{
  /**
   * Matches a taint effect.
   *
   * @var string
   */
  public $effect;
  /**
   * Matches a taint key (not necessarily unique).
   *
   * @var string
   */
  public $key;
  /**
   * Matches a taint operator.
   *
   * @var string
   */
  public $operator;
  /**
   * Matches a taint value.
   *
   * @var string
   */
  public $value;

  /**
   * Matches a taint effect.
   *
   * @param string $effect
   */
  public function setEffect($effect)
  {
    $this->effect = $effect;
  }
  /**
   * @return string
   */
  public function getEffect()
  {
    return $this->effect;
  }
  /**
   * Matches a taint key (not necessarily unique).
   *
   * @param string $key
   */
  public function setKey($key)
  {
    $this->key = $key;
  }
  /**
   * @return string
   */
  public function getKey()
  {
    return $this->key;
  }
  /**
   * Matches a taint operator.
   *
   * @param string $operator
   */
  public function setOperator($operator)
  {
    $this->operator = $operator;
  }
  /**
   * @return string
   */
  public function getOperator()
  {
    return $this->operator;
  }
  /**
   * Matches a taint value.
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
class_alias(PolicyControllerToleration::class, 'Google_Service_GKEHub_PolicyControllerToleration');
