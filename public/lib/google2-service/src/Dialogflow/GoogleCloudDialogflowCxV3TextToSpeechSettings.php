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

class GoogleCloudDialogflowCxV3TextToSpeechSettings extends \Google\Model
{
  protected $synthesizeSpeechConfigsType = GoogleCloudDialogflowCxV3SynthesizeSpeechConfig::class;
  protected $synthesizeSpeechConfigsDataType = 'map';

  /**
   * Configuration of how speech should be synthesized, mapping from language
   * (https://cloud.google.com/dialogflow/cx/docs/reference/language) to
   * SynthesizeSpeechConfig. These settings affect: - The [phone gateway](https:
   * //cloud.google.com/dialogflow/cx/docs/concept/integration/phone-gateway)
   * synthesize configuration set via Agent.text_to_speech_settings. - How
   * speech is synthesized when invoking session APIs.
   * Agent.text_to_speech_settings only applies if
   * OutputAudioConfig.synthesize_speech_config is not specified.
   *
   * @param GoogleCloudDialogflowCxV3SynthesizeSpeechConfig[] $synthesizeSpeechConfigs
   */
  public function setSynthesizeSpeechConfigs($synthesizeSpeechConfigs)
  {
    $this->synthesizeSpeechConfigs = $synthesizeSpeechConfigs;
  }
  /**
   * @return GoogleCloudDialogflowCxV3SynthesizeSpeechConfig[]
   */
  public function getSynthesizeSpeechConfigs()
  {
    return $this->synthesizeSpeechConfigs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3TextToSpeechSettings::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3TextToSpeechSettings');
