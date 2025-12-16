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

class GenerateVoiceCloningKeyRequest extends \Google\Model
{
  /**
   * @var string
   */
  public $consentScript;
  /**
   * @var string
   */
  public $languageCode;
  protected $referenceAudioType = InputAudio::class;
  protected $referenceAudioDataType = '';
  protected $voiceTalentConsentType = InputAudio::class;
  protected $voiceTalentConsentDataType = '';

  /**
   * @param string
   */
  public function setConsentScript($consentScript)
  {
    $this->consentScript = $consentScript;
  }
  /**
   * @return string
   */
  public function getConsentScript()
  {
    return $this->consentScript;
  }
  /**
   * @param string
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
   * @param InputAudio
   */
  public function setReferenceAudio(InputAudio $referenceAudio)
  {
    $this->referenceAudio = $referenceAudio;
  }
  /**
   * @return InputAudio
   */
  public function getReferenceAudio()
  {
    return $this->referenceAudio;
  }
  /**
   * @param InputAudio
   */
  public function setVoiceTalentConsent(InputAudio $voiceTalentConsent)
  {
    $this->voiceTalentConsent = $voiceTalentConsent;
  }
  /**
   * @return InputAudio
   */
  public function getVoiceTalentConsent()
  {
    return $this->voiceTalentConsent;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GenerateVoiceCloningKeyRequest::class, 'Google_Service_Texttospeech_GenerateVoiceCloningKeyRequest');
