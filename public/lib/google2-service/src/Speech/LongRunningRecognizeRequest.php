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

class LongRunningRecognizeRequest extends \Google\Model
{
  protected $audioType = RecognitionAudio::class;
  protected $audioDataType = '';
  protected $configType = RecognitionConfig::class;
  protected $configDataType = '';
  protected $outputConfigType = TranscriptOutputConfig::class;
  protected $outputConfigDataType = '';

  /**
   * Required. The audio data to be recognized.
   *
   * @param RecognitionAudio $audio
   */
  public function setAudio(RecognitionAudio $audio)
  {
    $this->audio = $audio;
  }
  /**
   * @return RecognitionAudio
   */
  public function getAudio()
  {
    return $this->audio;
  }
  /**
   * Required. Provides information to the recognizer that specifies how to
   * process the request.
   *
   * @param RecognitionConfig $config
   */
  public function setConfig(RecognitionConfig $config)
  {
    $this->config = $config;
  }
  /**
   * @return RecognitionConfig
   */
  public function getConfig()
  {
    return $this->config;
  }
  /**
   * Optional. Specifies an optional destination for the recognition results.
   *
   * @param TranscriptOutputConfig $outputConfig
   */
  public function setOutputConfig(TranscriptOutputConfig $outputConfig)
  {
    $this->outputConfig = $outputConfig;
  }
  /**
   * @return TranscriptOutputConfig
   */
  public function getOutputConfig()
  {
    return $this->outputConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LongRunningRecognizeRequest::class, 'Google_Service_Speech_LongRunningRecognizeRequest');
