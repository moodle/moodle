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

class BlueGreenSettings extends \Google\Model
{
  protected $autoscaledRolloutPolicyType = AutoscaledRolloutPolicy::class;
  protected $autoscaledRolloutPolicyDataType = '';
  /**
   * Time needed after draining entire blue pool. After this period, blue pool
   * will be cleaned up.
   *
   * @var string
   */
  public $nodePoolSoakDuration;
  protected $standardRolloutPolicyType = StandardRolloutPolicy::class;
  protected $standardRolloutPolicyDataType = '';

  /**
   * Autoscaled policy for cluster autoscaler enabled blue-green upgrade.
   *
   * @param AutoscaledRolloutPolicy $autoscaledRolloutPolicy
   */
  public function setAutoscaledRolloutPolicy(AutoscaledRolloutPolicy $autoscaledRolloutPolicy)
  {
    $this->autoscaledRolloutPolicy = $autoscaledRolloutPolicy;
  }
  /**
   * @return AutoscaledRolloutPolicy
   */
  public function getAutoscaledRolloutPolicy()
  {
    return $this->autoscaledRolloutPolicy;
  }
  /**
   * Time needed after draining entire blue pool. After this period, blue pool
   * will be cleaned up.
   *
   * @param string $nodePoolSoakDuration
   */
  public function setNodePoolSoakDuration($nodePoolSoakDuration)
  {
    $this->nodePoolSoakDuration = $nodePoolSoakDuration;
  }
  /**
   * @return string
   */
  public function getNodePoolSoakDuration()
  {
    return $this->nodePoolSoakDuration;
  }
  /**
   * Standard policy for the blue-green upgrade.
   *
   * @param StandardRolloutPolicy $standardRolloutPolicy
   */
  public function setStandardRolloutPolicy(StandardRolloutPolicy $standardRolloutPolicy)
  {
    $this->standardRolloutPolicy = $standardRolloutPolicy;
  }
  /**
   * @return StandardRolloutPolicy
   */
  public function getStandardRolloutPolicy()
  {
    return $this->standardRolloutPolicy;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BlueGreenSettings::class, 'Google_Service_Container_BlueGreenSettings');
