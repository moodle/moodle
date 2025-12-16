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

namespace Google\Service\Container;

class NodeTaint extends \Google\Model
{
  /**
   * Not set
   */
  public const EFFECT_EFFECT_UNSPECIFIED = 'EFFECT_UNSPECIFIED';
  /**
   * NoSchedule
   */
  public const EFFECT_NO_SCHEDULE = 'NO_SCHEDULE';
  /**
   * PreferNoSchedule
   */
  public const EFFECT_PREFER_NO_SCHEDULE = 'PREFER_NO_SCHEDULE';
  /**
   * NoExecute
   */
  public const EFFECT_NO_EXECUTE = 'NO_EXECUTE';
  /**
   * Effect for taint.
   *
   * @var string
   */
  public $effect;
  /**
   * Key for taint.
   *
   * @var string
   */
  public $key;
  /**
   * Value for taint.
   *
   * @var string
   */
  public $value;

  /**
   * Effect for taint.
   *
   * Accepted values: EFFECT_UNSPECIFIED, NO_SCHEDULE, PREFER_NO_SCHEDULE,
   * NO_EXECUTE
   *
   * @param self::EFFECT_* $effect
   */
  public function setEffect($effect)
  {
    $this->effect = $effect;
  }
  /**
   * @return self::EFFECT_*
   */
  public function getEffect()
  {
    return $this->effect;
  }
  /**
   * Key for taint.
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
   * Value for taint.
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
class_alias(NodeTaint::class, 'Google_Service_Container_NodeTaint');
