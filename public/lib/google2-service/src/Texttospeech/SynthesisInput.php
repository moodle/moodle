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

class SynthesisInput extends \Google\Model
{
  protected $customPronunciationsType = CustomPronunciations::class;
  protected $customPronunciationsDataType = '';
  /**
   * Markup for Chirp 3: HD voices specifically. This field may not be used with
   * any other voices.
   *
   * @var string
   */
  public $markup;
  protected $multiSpeakerMarkupType = MultiSpeakerMarkup::class;
  protected $multiSpeakerMarkupDataType = '';
  /**
   * This system instruction is supported only for controllable/promptable voice
   * models. If this system instruction is used, we pass the unedited text to
   * Gemini-TTS. Otherwise, a default system instruction is used. AI Studio
   * calls this system instruction, Style Instructions.
   *
   * @var string
   */
  public $prompt;
  /**
   * The SSML document to be synthesized. The SSML document must be valid and
   * well-formed. Otherwise the RPC will fail and return
   * google.rpc.Code.INVALID_ARGUMENT. For more information, see
   * [SSML](https://cloud.google.com/text-to-speech/docs/ssml).
   *
   * @var string
   */
  public $ssml;
  /**
   * The raw text to be synthesized.
   *
   * @var string
   */
  public $text;

  /**
   * Optional. The pronunciation customizations are applied to the input. If
   * this is set, the input is synthesized using the given pronunciation
   * customizations. The initial support is for en-us, with plans to expand to
   * other locales in the future. Instant Clone voices aren't supported. In
   * order to customize the pronunciation of a phrase, there must be an exact
   * match of the phrase in the input types. If using SSML, the phrase must not
   * be inside a phoneme tag.
   *
   * @param CustomPronunciations $customPronunciations
   */
  public function setCustomPronunciations(CustomPronunciations $customPronunciations)
  {
    $this->customPronunciations = $customPronunciations;
  }
  /**
   * @return CustomPronunciations
   */
  public function getCustomPronunciations()
  {
    return $this->customPronunciations;
  }
  /**
   * Markup for Chirp 3: HD voices specifically. This field may not be used with
   * any other voices.
   *
   * @param string $markup
   */
  public function setMarkup($markup)
  {
    $this->markup = $markup;
  }
  /**
   * @return string
   */
  public function getMarkup()
  {
    return $this->markup;
  }
  /**
   * The multi-speaker input to be synthesized. Only applicable for multi-
   * speaker synthesis.
   *
   * @param MultiSpeakerMarkup $multiSpeakerMarkup
   */
  public function setMultiSpeakerMarkup(MultiSpeakerMarkup $multiSpeakerMarkup)
  {
    $this->multiSpeakerMarkup = $multiSpeakerMarkup;
  }
  /**
   * @return MultiSpeakerMarkup
   */
  public function getMultiSpeakerMarkup()
  {
    return $this->multiSpeakerMarkup;
  }
  /**
   * This system instruction is supported only for controllable/promptable voice
   * models. If this system instruction is used, we pass the unedited text to
   * Gemini-TTS. Otherwise, a default system instruction is used. AI Studio
   * calls this system instruction, Style Instructions.
   *
   * @param string $prompt
   */
  public function setPrompt($prompt)
  {
    $this->prompt = $prompt;
  }
  /**
   * @return string
   */
  public function getPrompt()
  {
    return $this->prompt;
  }
  /**
   * The SSML document to be synthesized. The SSML document must be valid and
   * well-formed. Otherwise the RPC will fail and return
   * google.rpc.Code.INVALID_ARGUMENT. For more information, see
   * [SSML](https://cloud.google.com/text-to-speech/docs/ssml).
   *
   * @param string $ssml
   */
  public function setSsml($ssml)
  {
    $this->ssml = $ssml;
  }
  /**
   * @return string
   */
  public function getSsml()
  {
    return $this->ssml;
  }
  /**
   * The raw text to be synthesized.
   *
   * @param string $text
   */
  public function setText($text)
  {
    $this->text = $text;
  }
  /**
   * @return string
   */
  public function getText()
  {
    return $this->text;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SynthesisInput::class, 'Google_Service_Texttospeech_SynthesisInput');
