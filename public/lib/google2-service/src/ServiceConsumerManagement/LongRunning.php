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

namespace Google\Service\ServiceConsumerManagement;

class LongRunning extends \Google\Model
{
  /**
   * Initial delay after which the first poll request will be made. Default
   * value: 5 seconds.
   *
   * @var string
   */
  public $initialPollDelay;
  /**
   * Maximum time between two subsequent poll requests. Default value: 45
   * seconds.
   *
   * @var string
   */
  public $maxPollDelay;
  /**
   * Multiplier to gradually increase delay between subsequent polls until it
   * reaches max_poll_delay. Default value: 1.5.
   *
   * @var float
   */
  public $pollDelayMultiplier;
  /**
   * Total polling timeout. Default value: 5 minutes.
   *
   * @var string
   */
  public $totalPollTimeout;

  /**
   * Initial delay after which the first poll request will be made. Default
   * value: 5 seconds.
   *
   * @param string $initialPollDelay
   */
  public function setInitialPollDelay($initialPollDelay)
  {
    $this->initialPollDelay = $initialPollDelay;
  }
  /**
   * @return string
   */
  public function getInitialPollDelay()
  {
    return $this->initialPollDelay;
  }
  /**
   * Maximum time between two subsequent poll requests. Default value: 45
   * seconds.
   *
   * @param string $maxPollDelay
   */
  public function setMaxPollDelay($maxPollDelay)
  {
    $this->maxPollDelay = $maxPollDelay;
  }
  /**
   * @return string
   */
  public function getMaxPollDelay()
  {
    return $this->maxPollDelay;
  }
  /**
   * Multiplier to gradually increase delay between subsequent polls until it
   * reaches max_poll_delay. Default value: 1.5.
   *
   * @param float $pollDelayMultiplier
   */
  public function setPollDelayMultiplier($pollDelayMultiplier)
  {
    $this->pollDelayMultiplier = $pollDelayMultiplier;
  }
  /**
   * @return float
   */
  public function getPollDelayMultiplier()
  {
    return $this->pollDelayMultiplier;
  }
  /**
   * Total polling timeout. Default value: 5 minutes.
   *
   * @param string $totalPollTimeout
   */
  public function setTotalPollTimeout($totalPollTimeout)
  {
    $this->totalPollTimeout = $totalPollTimeout;
  }
  /**
   * @return string
   */
  public function getTotalPollTimeout()
  {
    return $this->totalPollTimeout;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LongRunning::class, 'Google_Service_ServiceConsumerManagement_LongRunning');
