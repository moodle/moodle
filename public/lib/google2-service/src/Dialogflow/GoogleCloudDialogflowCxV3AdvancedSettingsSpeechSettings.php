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

class GoogleCloudDialogflowCxV3AdvancedSettingsSpeechSettings extends \Google\Model
{
  /**
   * Sensitivity of the speech model that detects the end of speech. Scale from
   * 0 to 100.
   *
   * @var int
   */
  public $endpointerSensitivity;
  /**
   * Mapping from language to Speech-to-Text model. The mapped Speech-to-Text
   * model will be selected for requests from its corresponding language. For
   * more information, see [Speech
   * models](https://cloud.google.com/dialogflow/cx/docs/concept/speech-models).
   *
   * @var string[]
   */
  public $models;
  /**
   * Timeout before detecting no speech.
   *
   * @var string
   */
  public $noSpeechTimeout;
  /**
   * Use timeout based endpointing, interpreting endpointer sensitivity as
   * seconds of timeout value.
   *
   * @var bool
   */
  public $useTimeoutBasedEndpointing;

  /**
   * Sensitivity of the speech model that detects the end of speech. Scale from
   * 0 to 100.
   *
   * @param int $endpointerSensitivity
   */
  public function setEndpointerSensitivity($endpointerSensitivity)
  {
    $this->endpointerSensitivity = $endpointerSensitivity;
  }
  /**
   * @return int
   */
  public function getEndpointerSensitivity()
  {
    return $this->endpointerSensitivity;
  }
  /**
   * Mapping from language to Speech-to-Text model. The mapped Speech-to-Text
   * model will be selected for requests from its corresponding language. For
   * more information, see [Speech
   * models](https://cloud.google.com/dialogflow/cx/docs/concept/speech-models).
   *
   * @param string[] $models
   */
  public function setModels($models)
  {
    $this->models = $models;
  }
  /**
   * @return string[]
   */
  public function getModels()
  {
    return $this->models;
  }
  /**
   * Timeout before detecting no speech.
   *
   * @param string $noSpeechTimeout
   */
  public function setNoSpeechTimeout($noSpeechTimeout)
  {
    $this->noSpeechTimeout = $noSpeechTimeout;
  }
  /**
   * @return string
   */
  public function getNoSpeechTimeout()
  {
    return $this->noSpeechTimeout;
  }
  /**
   * Use timeout based endpointing, interpreting endpointer sensitivity as
   * seconds of timeout value.
   *
   * @param bool $useTimeoutBasedEndpointing
   */
  public function setUseTimeoutBasedEndpointing($useTimeoutBasedEndpointing)
  {
    $this->useTimeoutBasedEndpointing = $useTimeoutBasedEndpointing;
  }
  /**
   * @return bool
   */
  public function getUseTimeoutBasedEndpointing()
  {
    return $this->useTimeoutBasedEndpointing;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3AdvancedSettingsSpeechSettings::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3AdvancedSettingsSpeechSettings');
