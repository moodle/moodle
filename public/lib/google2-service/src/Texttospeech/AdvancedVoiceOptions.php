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

namespace Google\Service\Texttospeech;

class AdvancedVoiceOptions extends \Google\Model
{
  /**
   * Only for Journey voices. If false, the synthesis is context aware and has a
   * higher latency.
   *
   * @var bool
   */
  public $lowLatencyJourneySynthesis;
  /**
   * Optional. Input only. If true, relaxes safety filters for Gemini TTS. Only
   * supported for accounts linked to Invoiced (Offline) Cloud billing accounts.
   * Otherwise, will return result google.rpc.Code.INVALID_ARGUMENT.
   *
   * @var bool
   */
  public $relaxSafetyFilters;

  /**
   * Only for Journey voices. If false, the synthesis is context aware and has a
   * higher latency.
   *
   * @param bool $lowLatencyJourneySynthesis
   */
  public function setLowLatencyJourneySynthesis($lowLatencyJourneySynthesis)
  {
    $this->lowLatencyJourneySynthesis = $lowLatencyJourneySynthesis;
  }
  /**
   * @return bool
   */
  public function getLowLatencyJourneySynthesis()
  {
    return $this->lowLatencyJourneySynthesis;
  }
  /**
   * Optional. Input only. If true, relaxes safety filters for Gemini TTS. Only
   * supported for accounts linked to Invoiced (Offline) Cloud billing accounts.
   * Otherwise, will return result google.rpc.Code.INVALID_ARGUMENT.
   *
   * @param bool $relaxSafetyFilters
   */
  public function setRelaxSafetyFilters($relaxSafetyFilters)
  {
    $this->relaxSafetyFilters = $relaxSafetyFilters;
  }
  /**
   * @return bool
   */
  public function getRelaxSafetyFilters()
  {
    return $this->relaxSafetyFilters;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AdvancedVoiceOptions::class, 'Google_Service_Texttospeech_AdvancedVoiceOptions');
