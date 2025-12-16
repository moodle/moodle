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

class TranscriptionConfig extends \Google\Model
{
  /**
   * Default value specified by user policy. This should never be returned.
   */
  public const AUTO_TRANSCRIPTION_GENERATION_AUTO_GENERATION_TYPE_UNSPECIFIED = 'AUTO_GENERATION_TYPE_UNSPECIFIED';
  /**
   * The artifact is generated automatically.
   */
  public const AUTO_TRANSCRIPTION_GENERATION_ON = 'ON';
  /**
   * The artifact is not generated automatically.
   */
  public const AUTO_TRANSCRIPTION_GENERATION_OFF = 'OFF';
  /**
   * Defines whether the content of a meeting is automatically transcribed when
   * someone with the privilege to transcribe joins the meeting.
   *
   * @var string
   */
  public $autoTranscriptionGeneration;

  /**
   * Defines whether the content of a meeting is automatically transcribed when
   * someone with the privilege to transcribe joins the meeting.
   *
   * Accepted values: AUTO_GENERATION_TYPE_UNSPECIFIED, ON, OFF
   *
   * @param self::AUTO_TRANSCRIPTION_GENERATION_* $autoTranscriptionGeneration
   */
  public function setAutoTranscriptionGeneration($autoTranscriptionGeneration)
  {
    $this->autoTranscriptionGeneration = $autoTranscriptionGeneration;
  }
  /**
   * @return self::AUTO_TRANSCRIPTION_GENERATION_*
   */
  public function getAutoTranscriptionGeneration()
  {
    return $this->autoTranscriptionGeneration;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TranscriptionConfig::class, 'Google_Service_Meet_TranscriptionConfig');
