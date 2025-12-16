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

class CustomPronunciationParams extends \Google\Model
{
  /**
   * Not specified.
   */
  public const PHONETIC_ENCODING_PHONETIC_ENCODING_UNSPECIFIED = 'PHONETIC_ENCODING_UNSPECIFIED';
  /**
   * IPA, such as apple -> ˈæpəl.
   * https://en.wikipedia.org/wiki/International_Phonetic_Alphabet
   */
  public const PHONETIC_ENCODING_PHONETIC_ENCODING_IPA = 'PHONETIC_ENCODING_IPA';
  /**
   * X-SAMPA, such as apple -> "{p@l". https://en.wikipedia.org/wiki/X-SAMPA
   */
  public const PHONETIC_ENCODING_PHONETIC_ENCODING_X_SAMPA = 'PHONETIC_ENCODING_X_SAMPA';
  /**
   * For reading-to-pron conversion to work well, the `pronunciation` field
   * should only contain Kanji, Hiragana, and Katakana. The pronunciation can
   * also contain pitch accents. The start of a pitch phrase is specified with
   * `^` and the down-pitch position is specified with `!`, for example:
   * phrase:端 pronunciation:^はし phrase:箸 pronunciation:^は!し phrase:橋
   * pronunciation:^はし! We currently only support the Tokyo dialect, which
   * allows at most one down-pitch per phrase (i.e. at most one `!` between
   * `^`).
   */
  public const PHONETIC_ENCODING_PHONETIC_ENCODING_JAPANESE_YOMIGANA = 'PHONETIC_ENCODING_JAPANESE_YOMIGANA';
  /**
   * Used to specify pronunciations for Mandarin words. See
   * https://en.wikipedia.org/wiki/Pinyin. For example: 朝阳, the pronunciation is
   * "chao2 yang2". The number represents the tone, and there is a space between
   * syllables. Neutral tones are represented by 5, for example 孩子 "hai2 zi5".
   */
  public const PHONETIC_ENCODING_PHONETIC_ENCODING_PINYIN = 'PHONETIC_ENCODING_PINYIN';
  /**
   * The phonetic encoding of the phrase.
   *
   * @var string
   */
  public $phoneticEncoding;
  /**
   * The phrase to which the customization is applied. The phrase can be
   * multiple words, such as proper nouns, but shouldn't span the length of the
   * sentence.
   *
   * @var string
   */
  public $phrase;
  /**
   * The pronunciation of the phrase. This must be in the phonetic encoding
   * specified above.
   *
   * @var string
   */
  public $pronunciation;

  /**
   * The phonetic encoding of the phrase.
   *
   * Accepted values: PHONETIC_ENCODING_UNSPECIFIED, PHONETIC_ENCODING_IPA,
   * PHONETIC_ENCODING_X_SAMPA, PHONETIC_ENCODING_JAPANESE_YOMIGANA,
   * PHONETIC_ENCODING_PINYIN
   *
   * @param self::PHONETIC_ENCODING_* $phoneticEncoding
   */
  public function setPhoneticEncoding($phoneticEncoding)
  {
    $this->phoneticEncoding = $phoneticEncoding;
  }
  /**
   * @return self::PHONETIC_ENCODING_*
   */
  public function getPhoneticEncoding()
  {
    return $this->phoneticEncoding;
  }
  /**
   * The phrase to which the customization is applied. The phrase can be
   * multiple words, such as proper nouns, but shouldn't span the length of the
   * sentence.
   *
   * @param string $phrase
   */
  public function setPhrase($phrase)
  {
    $this->phrase = $phrase;
  }
  /**
   * @return string
   */
  public function getPhrase()
  {
    return $this->phrase;
  }
  /**
   * The pronunciation of the phrase. This must be in the phonetic encoding
   * specified above.
   *
   * @param string $pronunciation
   */
  public function setPronunciation($pronunciation)
  {
    $this->pronunciation = $pronunciation;
  }
  /**
   * @return string
   */
  public function getPronunciation()
  {
    return $this->pronunciation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CustomPronunciationParams::class, 'Google_Service_Texttospeech_CustomPronunciationParams');
