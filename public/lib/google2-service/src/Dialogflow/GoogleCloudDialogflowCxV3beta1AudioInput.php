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

class GoogleCloudDialogflowCxV3beta1AudioInput extends \Google\Model
{
  /**
   * The natural language speech audio to be processed. A single request can
   * contain up to 2 minutes of speech audio data. The transcribed text cannot
   * contain more than 256 bytes. For non-streaming audio detect intent, both
   * `config` and `audio` must be provided. For streaming audio detect intent,
   * `config` must be provided in the first request and `audio` must be provided
   * in all following requests.
   *
   * @var string
   */
  public $audio;
  protected $configType = GoogleCloudDialogflowCxV3beta1InputAudioConfig::class;
  protected $configDataType = '';

  /**
   * The natural language speech audio to be processed. A single request can
   * contain up to 2 minutes of speech audio data. The transcribed text cannot
   * contain more than 256 bytes. For non-streaming audio detect intent, both
   * `config` and `audio` must be provided. For streaming audio detect intent,
   * `config` must be provided in the first request and `audio` must be provided
   * in all following requests.
   *
   * @param string $audio
   */
  public function setAudio($audio)
  {
    $this->audio = $audio;
  }
  /**
   * @return string
   */
  public function getAudio()
  {
    return $this->audio;
  }
  /**
   * Required. Instructs the speech recognizer how to process the speech audio.
   *
   * @param GoogleCloudDialogflowCxV3beta1InputAudioConfig $config
   */
  public function setConfig(GoogleCloudDialogflowCxV3beta1InputAudioConfig $config)
  {
    $this->config = $config;
  }
  /**
   * @return GoogleCloudDialogflowCxV3beta1InputAudioConfig
   */
  public function getConfig()
  {
    return $this->config;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3beta1AudioInput::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3beta1AudioInput');
