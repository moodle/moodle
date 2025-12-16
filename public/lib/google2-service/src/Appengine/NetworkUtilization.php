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

class NetworkUtilization extends \Google\Model
{
  /**
   * Target bytes received per second.
   *
   * @var int
   */
  public $targetReceivedBytesPerSecond;
  /**
   * Target packets received per second.
   *
   * @var int
   */
  public $targetReceivedPacketsPerSecond;
  /**
   * Target bytes sent per second.
   *
   * @var int
   */
  public $targetSentBytesPerSecond;
  /**
   * Target packets sent per second.
   *
   * @var int
   */
  public $targetSentPacketsPerSecond;

  /**
   * Target bytes received per second.
   *
   * @param int $targetReceivedBytesPerSecond
   */
  public function setTargetReceivedBytesPerSecond($targetReceivedBytesPerSecond)
  {
    $this->targetReceivedBytesPerSecond = $targetReceivedBytesPerSecond;
  }
  /**
   * @return int
   */
  public function getTargetReceivedBytesPerSecond()
  {
    return $this->targetReceivedBytesPerSecond;
  }
  /**
   * Target packets received per second.
   *
   * @param int $targetReceivedPacketsPerSecond
   */
  public function setTargetReceivedPacketsPerSecond($targetReceivedPacketsPerSecond)
  {
    $this->targetReceivedPacketsPerSecond = $targetReceivedPacketsPerSecond;
  }
  /**
   * @return int
   */
  public function getTargetReceivedPacketsPerSecond()
  {
    return $this->targetReceivedPacketsPerSecond;
  }
  /**
   * Target bytes sent per second.
   *
   * @param int $targetSentBytesPerSecond
   */
  public function setTargetSentBytesPerSecond($targetSentBytesPerSecond)
  {
    $this->targetSentBytesPerSecond = $targetSentBytesPerSecond;
  }
  /**
   * @return int
   */
  public function getTargetSentBytesPerSecond()
  {
    return $this->targetSentBytesPerSecond;
  }
  /**
   * Target packets sent per second.
   *
   * @param int $targetSentPacketsPerSecond
   */
  public function setTargetSentPacketsPerSecond($targetSentPacketsPerSecond)
  {
    $this->targetSentPacketsPerSecond = $targetSentPacketsPerSecond;
  }
  /**
   * @return int
   */
  public function getTargetSentPacketsPerSecond()
  {
    return $this->targetSentPacketsPerSecond;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NetworkUtilization::class, 'Google_Service_Appengine_NetworkUtilization');
