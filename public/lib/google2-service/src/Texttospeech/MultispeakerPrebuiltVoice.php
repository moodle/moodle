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

class MultispeakerPrebuiltVoice extends \Google\Model
{
  /**
   * Required. The speaker alias of the voice. This is the user-chosen speaker
   * name that is used in the multispeaker text input, such as "Speaker1".
   *
   * @var string
   */
  public $speakerAlias;
  /**
   * Required. The speaker ID of the voice. See https://cloud.google.com/text-
   * to-speech/docs/gemini-tts#voice_options for available values.
   *
   * @var string
   */
  public $speakerId;

  /**
   * Required. The speaker alias of the voice. This is the user-chosen speaker
   * name that is used in the multispeaker text input, such as "Speaker1".
   *
   * @param string $speakerAlias
   */
  public function setSpeakerAlias($speakerAlias)
  {
    $this->speakerAlias = $speakerAlias;
  }
  /**
   * @return string
   */
  public function getSpeakerAlias()
  {
    return $this->speakerAlias;
  }
  /**
   * Required. The speaker ID of the voice. See https://cloud.google.com/text-
   * to-speech/docs/gemini-tts#voice_options for available values.
   *
   * @param string $speakerId
   */
  public function setSpeakerId($speakerId)
  {
    $this->speakerId = $speakerId;
  }
  /**
   * @return string
   */
  public function getSpeakerId()
  {
    return $this->speakerId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MultispeakerPrebuiltVoice::class, 'Google_Service_Texttospeech_MultispeakerPrebuiltVoice');
