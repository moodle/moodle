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

class BareMetalDrainingMachine extends \Google\Model
{
  /**
   * Draining machine IP address.
   *
   * @var string
   */
  public $nodeIp;
  /**
   * The count of pods yet to drain.
   *
   * @var int
   */
  public $podCount;

  /**
   * Draining machine IP address.
   *
   * @param string $nodeIp
   */
  public function setNodeIp($nodeIp)
  {
    $this->nodeIp = $nodeIp;
  }
  /**
   * @return string
   */
  public function getNodeIp()
  {
    return $this->nodeIp;
  }
  /**
   * The count of pods yet to drain.
   *
   * @param int $podCount
   */
  public function setPodCount($podCount)
  {
    $this->podCount = $podCount;
  }
  /**
   * @return int
   */
  public function getPodCount()
  {
    return $this->podCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BareMetalDrainingMachine::class, 'Google_Service_GKEOnPrem_BareMetalDrainingMachine');
