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

class Voice extends \Google\Collection
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
  protected $collection_key = 'languageCodes';
  /**
   * The languages that this voice supports, expressed as
   * [BCP-47](https://www.rfc-editor.org/rfc/bcp/bcp47.txt) language tags (e.g.
   * "en-US", "es-419", "cmn-tw").
   *
   * @var string[]
   */
  public $languageCodes;
  /**
   * The name of this voice. Each distinct voice has a unique name.
   *
   * @var string
   */
  public $name;
  /**
   * The natural sample rate (in hertz) for this voice.
   *
   * @var int
   */
  public $naturalSampleRateHertz;
  /**
   * The gender of this voice.
   *
   * @var string
   */
  public $ssmlGender;

  /**
   * The languages that this voice supports, expressed as
   * [BCP-47](https://www.rfc-editor.org/rfc/bcp/bcp47.txt) language tags (e.g.
   * "en-US", "es-419", "cmn-tw").
   *
   * @param string[] $languageCodes
   */
  public function setLanguageCodes($languageCodes)
  {
    $this->languageCodes = $languageCodes;
  }
  /**
   * @return string[]
   */
  public function getLanguageCodes()
  {
    return $this->languageCodes;
  }
  /**
   * The name of this voice. Each distinct voice has a unique name.
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
   * The natural sample rate (in hertz) for this voice.
   *
   * @param int $naturalSampleRateHertz
   */
  public function setNaturalSampleRateHertz($naturalSampleRateHertz)
  {
    $this->naturalSampleRateHertz = $naturalSampleRateHertz;
  }
  /**
   * @return int
   */
  public function getNaturalSampleRateHertz()
  {
    return $this->naturalSampleRateHertz;
  }
  /**
   * The gender of this voice.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Voice::class, 'Google_Service_Texttospeech_Voice');
