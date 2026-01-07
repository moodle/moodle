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

class GoogleCloudDialogflowCxV3VoiceSelectionParams extends \Google\Model
{
  /**
   * An unspecified gender, which means that the client doesn't care which
   * gender the selected voice will have.
   */
  public const SSML_GENDER_SSML_VOICE_GENDER_UNSPECIFIED = 'SSML_VOICE_GENDER_UNSPECIFIED';
  /**
   * A male voice.
   */
  public const SSML_GENDER_SSML_VOICE_GENDER_MALE = 'SSML_VOICE_GENDER_MALE';
  /**
   * A female voice.
   */
  public const SSML_GENDER_SSML_VOICE_GENDER_FEMALE = 'SSML_VOICE_GENDER_FEMALE';
  /**
   * A gender-neutral voice.
   */
  public const SSML_GENDER_SSML_VOICE_GENDER_NEUTRAL = 'SSML_VOICE_GENDER_NEUTRAL';
  /**
   * Optional. The name of the voice. If not set, the service will choose a
   * voice based on the other parameters such as language_code and ssml_gender.
   * For the list of available voices, please refer to [Supported voices and
   * languages](https://cloud.google.com/text-to-speech/docs/voices).
   *
   * @var string
   */
  public $name;
  /**
   * Optional. The preferred gender of the voice. If not set, the service will
   * choose a voice based on the other parameters such as language_code and
   * name. Note that this is only a preference, not requirement. If a voice of
   * the appropriate gender is not available, the synthesizer substitutes a
   * voice with a different gender rather than failing the request.
   *
   * @var string
   */
  public $ssmlGender;

  /**
   * Optional. The name of the voice. If not set, the service will choose a
   * voice based on the other parameters such as language_code and ssml_gender.
   * For the list of available voices, please refer to [Supported voices and
   * languages](https://cloud.google.com/text-to-speech/docs/voices).
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Optional. The preferred gender of the voice. If not set, the service will
   * choose a voice based on the other parameters such as language_code and
   * name. Note that this is only a preference, not requirement. If a voice of
   * the appropriate gender is not available, the synthesizer substitutes a
   * voice with a different gender rather than failing the request.
   *
   * Accepted values: SSML_VOICE_GENDER_UNSPECIFIED, SSML_VOICE_GENDER_MALE,
   * SSML_VOICE_GENDER_FEMALE, SSML_VOICE_GENDER_NEUTRAL
   *
   * @param self::SSML_GENDER_* $ssmlGender
   */
  public function setSsmlGender($ssmlGender)
  {
    $this->ssmlGender = $ssmlGender;
  }
  /**
   * @return self::SSML_GENDER_*
   */
  public function getSsmlGender()
  {
    return $this->ssmlGender;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3VoiceSelectionParams::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3VoiceSelectionParams');
