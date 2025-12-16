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

class SpeechAdaptationInfo extends \Google\Model
{
  /**
   * Whether there was a timeout when applying speech adaptation. If true,
   * adaptation had no effect in the response transcript.
   *
   * @var bool
   */
  public $adaptationTimeout;
  /**
   * If set, returns a message specifying which part of the speech adaptation
   * request timed out.
   *
   * @var string
   */
  public $timeoutMessage;

  /**
   * Whether there was a timeout when applying speech adaptation. If true,
   * adaptation had no effect in the response transcript.
   *
   * @param bool $adaptationTimeout
   */
  public function setAdaptationTimeout($adaptationTimeout)
  {
    $this->adaptationTimeout = $adaptationTimeout;
  }
  /**
   * @return bool
   */
  public function getAdaptationTimeout()
  {
    return $this->adaptationTimeout;
  }
  /**
   * If set, returns a message specifying which part of the speech adaptation
   * request timed out.
   *
   * @param string $timeoutMessage
   */
  public function setTimeoutMessage($timeoutMessage)
  {
    $this->timeoutMessage = $timeoutMessage;
  }
  /**
   * @return string
   */
  public function getTimeoutMessage()
  {
    return $this->timeoutMessage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SpeechAdaptationInfo::class, 'Google_Service_Speech_SpeechAdaptationInfo');
