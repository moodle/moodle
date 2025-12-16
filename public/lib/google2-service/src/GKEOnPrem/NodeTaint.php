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

namespace Google\Service\GKEOnPrem;

class NodeTaint extends \Google\Model
{
  /**
   * Not set.
   */
  public const EFFECT_EFFECT_UNSPECIFIED = 'EFFECT_UNSPECIFIED';
  /**
   * Do not allow new pods to schedule onto the node unless they tolerate the
   * taint, but allow all pods submitted to Kubelet without going through the
   * scheduler to start, and allow all already-running pods to continue running.
   * Enforced by the scheduler.
   */
  public const EFFECT_NO_SCHEDULE = 'NO_SCHEDULE';
  /**
   * Like TaintEffectNoSchedule, but the scheduler tries not to schedule new
   * pods onto the node, rather than prohibiting new pods from scheduling onto
   * the node entirely. Enforced by the scheduler.
   */
  public const EFFECT_PREFER_NO_SCHEDULE = 'PREFER_NO_SCHEDULE';
  /**
   * Evict any already-running pods that do not tolerate the taint. Currently
   * enforced by NodeController.
   */
  public const EFFECT_NO_EXECUTE = 'NO_EXECUTE';
  /**
   * The taint effect.
   *
   * @var string
   */
  public $effect;
  /**
   * Key associated with the effect.
   *
   * @var string
   */
  public $key;
  /**
   * Value associated with the effect.
   *
   * @var string
   */
  public $value;

  /**
   * The taint effect.
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
   * Key associated with the effect.
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
   * Value associated with the effect.
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
class_alias(NodeTaint::class, 'Google_Service_GKEOnPrem_NodeTaint');
