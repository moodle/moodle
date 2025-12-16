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

namespace Google\Service\Transcoder;

class TrackDefinition extends \Google\Collection
{
  protected $collection_key = 'languages';
  /**
   * Optional. Whether to automatically detect the languages present in the
   * track. If true, the system will attempt to identify all the languages
   * present in the track and populate the languages field.
   *
   * @var bool
   */
  public $detectLanguages;
  /**
   * Output only. A list of languages detected in the input asset, represented
   * by a BCP 47 language code, such as "en-US" or "sr-Latn". For more
   * information, see
   * https://www.unicode.org/reports/tr35/#Unicode_locale_identifier. This field
   * is only populated if the detect_languages field is set to true.
   *
   * @var string[]
   */
  public $detectedLanguages;
  /**
   * The input track.
   *
   * @var int
   */
  public $inputTrack;
  /**
   * Optional. A list of languages spoken in the input asset, represented by a
   * BCP 47 language code, such as "en-US" or "sr-Latn". For more information,
   * see https://www.unicode.org/reports/tr35/#Unicode_locale_identifier.
   *
   * @var string[]
   */
  public $languages;

  /**
   * Optional. Whether to automatically detect the languages present in the
   * track. If true, the system will attempt to identify all the languages
   * present in the track and populate the languages field.
   *
   * @param bool $detectLanguages
   */
  public function setDetectLanguages($detectLanguages)
  {
    $this->detectLanguages = $detectLanguages;
  }
  /**
   * @return bool
   */
  public function getDetectLanguages()
  {
    return $this->detectLanguages;
  }
  /**
   * Output only. A list of languages detected in the input asset, represented
   * by a BCP 47 language code, such as "en-US" or "sr-Latn". For more
   * information, see
   * https://www.unicode.org/reports/tr35/#Unicode_locale_identifier. This field
   * is only populated if the detect_languages field is set to true.
   *
   * @param string[] $detectedLanguages
   */
  public function setDetectedLanguages($detectedLanguages)
  {
    $this->detectedLanguages = $detectedLanguages;
  }
  /**
   * @return string[]
   */
  public function getDetectedLanguages()
  {
    return $this->detectedLanguages;
  }
  /**
   * The input track.
   *
   * @param int $inputTrack
   */
  public function setInputTrack($inputTrack)
  {
    $this->inputTrack = $inputTrack;
  }
  /**
   * @return int
   */
  public function getInputTrack()
  {
    return $this->inputTrack;
  }
  /**
   * Optional. A list of languages spoken in the input asset, represented by a
   * BCP 47 language code, such as "en-US" or "sr-Latn". For more information,
   * see https://www.unicode.org/reports/tr35/#Unicode_locale_identifier.
   *
   * @param string[] $languages
   */
  public function setLanguages($languages)
  {
    $this->languages = $languages;
  }
  /**
   * @return string[]
   */
  public function getLanguages()
  {
    return $this->languages;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TrackDefinition::class, 'Google_Service_Transcoder_TrackDefinition');
