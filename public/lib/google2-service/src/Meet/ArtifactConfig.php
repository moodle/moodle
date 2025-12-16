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

namespace Google\Service\Meet;

class ArtifactConfig extends \Google\Model
{
  protected $recordingConfigType = RecordingConfig::class;
  protected $recordingConfigDataType = '';
  protected $smartNotesConfigType = SmartNotesConfig::class;
  protected $smartNotesConfigDataType = '';
  protected $transcriptionConfigType = TranscriptionConfig::class;
  protected $transcriptionConfigDataType = '';

  /**
   * Configuration for recording.
   *
   * @param RecordingConfig $recordingConfig
   */
  public function setRecordingConfig(RecordingConfig $recordingConfig)
  {
    $this->recordingConfig = $recordingConfig;
  }
  /**
   * @return RecordingConfig
   */
  public function getRecordingConfig()
  {
    return $this->recordingConfig;
  }
  /**
   * Configuration for auto-smart-notes.
   *
   * @param SmartNotesConfig $smartNotesConfig
   */
  public function setSmartNotesConfig(SmartNotesConfig $smartNotesConfig)
  {
    $this->smartNotesConfig = $smartNotesConfig;
  }
  /**
   * @return SmartNotesConfig
   */
  public function getSmartNotesConfig()
  {
    return $this->smartNotesConfig;
  }
  /**
   * Configuration for auto-transcript.
   *
   * @param TranscriptionConfig $transcriptionConfig
   */
  public function setTranscriptionConfig(TranscriptionConfig $transcriptionConfig)
  {
    $this->transcriptionConfig = $transcriptionConfig;
  }
  /**
   * @return TranscriptionConfig
   */
  public function getTranscriptionConfig()
  {
    return $this->transcriptionConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ArtifactConfig::class, 'Google_Service_Meet_ArtifactConfig');
