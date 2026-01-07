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

namespace Google\Service\Appengine;

class BasicScaling extends \Google\Model
{
  /**
   * Duration of time after the last request that an instance must wait before
   * the instance is shut down.
   *
   * @var string
   */
  public $idleTimeout;
  /**
   * Maximum number of instances to create for this version.
   *
   * @var int
   */
  public $maxInstances;

  /**
   * Duration of time after the last request that an instance must wait before
   * the instance is shut down.
   *
   * @param string $idleTimeout
   */
  public function setIdleTimeout($idleTimeout)
  {
    $this->idleTimeout = $idleTimeout;
  }
  /**
   * @return string
   */
  public function getIdleTimeout()
  {
    return $this->idleTimeout;
  }
  /**
   * Maximum number of instances to create for this version.
   *
   * @param int $maxInstances
   */
  public function setMaxInstances($maxInstances)
  {
    $this->maxInstances = $maxInstances;
  }
  /**
   * @return int
   */
  public function getMaxInstances()
  {
    return $this->maxInstances;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BasicScaling::class, 'Google_Service_Appengine_BasicScaling');
