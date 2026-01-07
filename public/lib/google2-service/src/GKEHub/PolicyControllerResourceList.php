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

class PolicyControllerResourceList extends \Google\Model
{
  /**
   * CPU requirement expressed in Kubernetes resource units.
   *
   * @var string
   */
  public $cpu;
  /**
   * Memory requirement expressed in Kubernetes resource units.
   *
   * @var string
   */
  public $memory;

  /**
   * CPU requirement expressed in Kubernetes resource units.
   *
   * @param string $cpu
   */
  public function setCpu($cpu)
  {
    $this->cpu = $cpu;
  }
  /**
   * @return string
   */
  public function getCpu()
  {
    return $this->cpu;
  }
  /**
   * Memory requirement expressed in Kubernetes resource units.
   *
   * @param string $memory
   */
  public function setMemory($memory)
  {
    $this->memory = $memory;
  }
  /**
   * @return string
   */
  public function getMemory()
  {
    return $this->memory;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PolicyControllerResourceList::class, 'Google_Service_GKEHub_PolicyControllerResourceList');
