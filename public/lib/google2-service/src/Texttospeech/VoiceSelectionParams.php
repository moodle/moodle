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

class VoiceSelectionParams extends \Google\Model
{
  /**
   * An unspecified gender. In VoiceSelectionParams, this means that the client
   * doesn't care which gender the selected voice will have. In the Voice field
   * of ListVoicesResponse, this may mean that the voice doesn't fit any of the
   * other categories in this enum, or that the gender of the voice isn't known.
   */
  public const SSML_GENDER_SSML_VOICE_GENDER_UNSPECIFIED = 'SSML_VOICE_GENDER_UNSPECIFIED';
  /**
   * A male voice.
   */
  public const SSML_GENDER_MALE = 'MALE';
  /**
   * A female voice.
   */
  public const SSML_GENDER_FEMALE = 'FEMALE';
  /**
   * A gender-neutral voice. This voice is not yet supported.
   */
  public const SSML_GENDER_NEUTRAL = 'NEUTRAL';
  protected $customVoiceType = CustomVoiceParams::class;
  protected $customVoiceDataType = '';
  /**
   * Required. The language (and potentially also the region) of the voice
   * expressed as a [BCP-47](https://www.rfc-editor.org/rfc/bcp/bcp47.txt)
   * language tag, e.g. "en-US". This should not include a script tag (e.g. use
   * "cmn-cn" rather than "cmn-Hant-cn"), because the script will be inferred
   * from the input provided in the SynthesisInput. The TTS service will use
   * this parameter to help choose an appropriate voice. Note that the TTS
   * service may choose a voice with a slightly different language code than the
   * one selected; it may substitute a different region (e.g. using en-US rather
   * than en-CA if there isn't a Canadian voice available), or even a different
   * language, e.g. using "nb" (Norwegian Bokmal) instead of "no" (Norwegian)".
   *
   * @var string
   */
  public $languageCode;
  /**
   * Optional. The name of the model. If set, the service will choose the model
   * matching the specified configuration.
   *
   * @var string
   */
  public $modelName;
  protected $multiSpeakerVoiceConfigType = MultiSpeakerVoiceConfig::class;
  protected $multiSpeakerVoiceConfigDataType = '';
  /**
   * The name of the voice. If both the name and the gender are not set, the
   * service will choose a voice based on the other parameters such as
   * language_code.
   *
   * @var string
   */
  public $name;
  /**
   * The preferred gender of the voice. If not set, the service will choose a
   * voice based on the other parameters such as language_code and name. Note
   * that this is only a preference, not requirement; if a voice of the
   * appropriate gender is not available, the synthesizer should substitute a
   * voice with a different gender rather than failing the request.
   *
   * @var string
   */
  public $ssmlGender;
  protected $voiceCloneType = VoiceCloneParams::class;
  protected $voiceCloneDataType = '';

  /**
   * The configuration for a custom voice. If [CustomVoiceParams.model] is set,
   * the service will choose the custom voice matching the specified
   * configuration.
   *
   * @param CustomVoiceParams $customVoice
   */
  public function setCustomVoice(CustomVoiceParams $customVoice)
  {
    $this->customVoice = $customVoice;
  }
  /**
   * @return CustomVoiceParams
   */
  public function getCustomVoice()
  {
    return $this->customVoice;
  }
  /**
   * Required. The language (and potentially also the region) of the voice
   * expressed as a [BCP-47](https://www.rfc-editor.org/rfc/bcp/bcp47.txt)
   * language tag, e.g. "en-US". This should not include a script tag (e.g. use
   * "cmn-cn" rather than "cmn-Hant-cn"), because the script will be inferred
   * from the input provided in the SynthesisInput. The TTS service will use
   * this parameter to help choose an appropriate voice. Note that the TTS
   * service may choose a voice with a slightly different language code than the
   * one selected; it may substitute a different region (e.g. using en-US rather
   * than en-CA if there isn't a Canadian voice available), or even a different
   * language, e.g. using "nb" (Norwegian Bokmal) instead of "no" (Norwegian)".
   *
   * @param string $languageCode
   */
  public function setLanguageCode($languageCode)
  {
    $this->languageCode = $languageCode;
  }
  /**
   * @return string
   */
  public function getLanguageCode()
  {
    return $this->languageCode;
  }
  /**
   * Optional. The name of the model. If set, the service will choose the model
   * matching the specified configuration.
   *
   * @param string $modelName
   */
  public function setModelName($modelName)
  {
    $this->modelName = $modelName;
  }
  /**
   * @return string
   */
  public function getModelName()
  {
    return $this->modelName;
  }
  /**
   * Optional. The configuration for a Gemini multi-speaker text-to-speech
   * setup. Enables the use of two distinct voices in a single synthesis
   * request.
   *
   * @param MultiSpeakerVoiceConfig $multiSpeakerVoiceConfig
   */
  public function setMultiSpeakerVoiceConfig(MultiSpeakerVoiceConfig $multiSpeakerVoiceConfig)
  {
    $this->multiSpeakerVoiceConfig = $multiSpeakerVoiceConfig;
  }
  /**
   * @return MultiSpeakerVoiceConfig
   */
  public function getMultiSpeakerVoiceConfig()
  {
    return $this->multiSpeakerVoiceConfig;
  }
  /**
   * The name of the voice. If both the name and the gender are not set, the
   * service will choose a voice based on the other parameters such as
   * language_code.
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
   * The preferred gender of the voice. If not set, the service will choose a
   * voice based on the other parameters such as language_code and name. Note
   * that this is only a preference, not requirement; if a voice of the
   * appropriate gender is not available, the synthesizer should substitute a
   * voice with a different gender rather than failing the request.
   *
   * Accepted values: SSML_VOICE_GENDER_UNSPECIFIED, MALE, FEMALE, NEUTRAL
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
  /**
   * Optional. The configuration for a voice clone. If
   * [VoiceCloneParams.voice_clone_key] is set, the service chooses the voice
   * clone matching the specified configuration.
   *
   * @param VoiceCloneParams $voiceClone
   */
  public function setVoiceClone(VoiceCloneParams $voiceClone)
  {
    $this->voiceClone = $voiceClone;
  }
  /**
   * @return VoiceCloneParams
   */
  public function getVoiceClone()
  {
    return $this->voiceClone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VoiceSelectionParams::class, 'Google_Service_Texttospeech_VoiceSelectionParams');
