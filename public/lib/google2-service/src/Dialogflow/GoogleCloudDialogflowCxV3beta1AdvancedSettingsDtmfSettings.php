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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowCxV3beta1AdvancedSettingsDtmfSettings extends \Google\Model
{
  /**
   * If true, incoming audio is processed for DTMF (dual tone multi frequency)
   * events. For example, if the caller presses a button on their telephone
   * keypad and DTMF processing is enabled, Dialogflow will detect the event
   * (e.g. a "3" was pressed) in the incoming audio and pass the event to the
   * bot to drive business logic (e.g. when 3 is pressed, return the account
   * balance).
   *
   * @var bool
   */
  public $enabled;
  /**
   * Endpoint timeout setting for matching dtmf input to regex.
   *
   * @var string
   */
  public $endpointingTimeoutDuration;
  /**
   * The digit that terminates a DTMF digit sequence.
   *
   * @var string
   */
  public $finishDigit;
  /**
   * Interdigit timeout setting for matching dtmf input to regex.
   *
   * @var string
   */
  public $interdigitTimeoutDuration;
  /**
   * Max length of DTMF digits.
   *
   * @var int
   */
  public $maxDigits;

  /**
   * If true, incoming audio is processed for DTMF (dual tone multi frequency)
   * events. For example, if the caller presses a button on their telephone
   * keypad and DTMF processing is enabled, Dialogflow will detect the event
   * (e.g. a "3" was pressed) in the incoming audio and pass the event to the
   * bot to drive business logic (e.g. when 3 is pressed, return the account
   * balance).
   *
   * @param bool $enabled
   */
  public function setEnabled($enabled)
  {
    $this->enabled = $enabled;
  }
  /**
   * @return bool
   */
  public function getEnabled()
  {
    return $this->enabled;
  }
  /**
   * Endpoint timeout setting for matching dtmf input to regex.
   *
   * @param string $endpointingTimeoutDuration
   */
  public function setEndpointingTimeoutDuration($endpointingTimeoutDuration)
  {
    $this->endpointingTimeoutDuration = $endpointingTimeoutDuration;
  }
  /**
   * @return string
   */
  public function getEndpointingTimeoutDuration()
  {
    return $this->endpointingTimeoutDuration;
  }
  /**
   * The digit that terminates a DTMF digit sequence.
   *
   * @param string $finishDigit
   */
  public function setFinishDigit($finishDigit)
  {
    $this->finishDigit = $finishDigit;
  }
  /**
   * @return string
   */
  public function getFinishDigit()
  {
    return $this->finishDigit;
  }
  /**
   * Interdigit timeout setting for matching dtmf input to regex.
   *
   * @param string $interdigitTimeoutDuration
   */
  public function setInterdigitTimeoutDuration($interdigitTimeoutDuration)
  {
    $this->interdigitTimeoutDuration = $interdigitTimeoutDuration;
  }
  /**
   * @return string
   */
  public function getInterdigitTimeoutDuration()
  {
    return $this->interdigitTimeoutDuration;
  }
  /**
   * Max length of DTMF digits.
   *
   * @param int $maxDigits
   */
  public function setMaxDigits($maxDigits)
  {
    $this->maxDigits = $maxDigits;
  }
  /**
   * @return int
   */
  public function getMaxDigits()
  {
    return $this->maxDigits;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3beta1AdvancedSettingsDtmfSettings::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3beta1AdvancedSettingsDtmfSettings');
