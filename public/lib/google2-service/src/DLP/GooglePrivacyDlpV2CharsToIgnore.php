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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2CharsToIgnore extends \Google\Model
{
  /**
   * Unused.
   */
  public const COMMON_CHARACTERS_TO_IGNORE_COMMON_CHARS_TO_IGNORE_UNSPECIFIED = 'COMMON_CHARS_TO_IGNORE_UNSPECIFIED';
  /**
   * 0-9
   */
  public const COMMON_CHARACTERS_TO_IGNORE_NUMERIC = 'NUMERIC';
  /**
   * A-Z
   */
  public const COMMON_CHARACTERS_TO_IGNORE_ALPHA_UPPER_CASE = 'ALPHA_UPPER_CASE';
  /**
   * a-z
   */
  public const COMMON_CHARACTERS_TO_IGNORE_ALPHA_LOWER_CASE = 'ALPHA_LOWER_CASE';
  /**
   * US Punctuation, one of !"#$%&'()*+,-./:;<=>?@[\]^_`{|}~
   */
  public const COMMON_CHARACTERS_TO_IGNORE_PUNCTUATION = 'PUNCTUATION';
  /**
   * Whitespace character, one of [ \t\n\x0B\f\r]
   */
  public const COMMON_CHARACTERS_TO_IGNORE_WHITESPACE = 'WHITESPACE';
  /**
   * Characters to not transform when masking.
   *
   * @var string
   */
  public $charactersToSkip;
  /**
   * Common characters to not transform when masking. Useful to avoid removing
   * punctuation.
   *
   * @var string
   */
  public $commonCharactersToIgnore;

  /**
   * Characters to not transform when masking.
   *
   * @param string $charactersToSkip
   */
  public function setCharactersToSkip($charactersToSkip)
  {
    $this->charactersToSkip = $charactersToSkip;
  }
  /**
   * @return string
   */
  public function getCharactersToSkip()
  {
    return $this->charactersToSkip;
  }
  /**
   * Common characters to not transform when masking. Useful to avoid removing
   * punctuation.
   *
   * Accepted values: COMMON_CHARS_TO_IGNORE_UNSPECIFIED, NUMERIC,
   * ALPHA_UPPER_CASE, ALPHA_LOWER_CASE, PUNCTUATION, WHITESPACE
   *
   * @param self::COMMON_CHARACTERS_TO_IGNORE_* $commonCharactersToIgnore
   */
  public function setCommonCharactersToIgnore($commonCharactersToIgnore)
  {
    $this->commonCharactersToIgnore = $commonCharactersToIgnore;
  }
  /**
   * @return self::COMMON_CHARACTERS_TO_IGNORE_*
   */
  public function getCommonCharactersToIgnore()
  {
    return $this->commonCharactersToIgnore;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2CharsToIgnore::class, 'Google_Service_DLP_GooglePrivacyDlpV2CharsToIgnore');
