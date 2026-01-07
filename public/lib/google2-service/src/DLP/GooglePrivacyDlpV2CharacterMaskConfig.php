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

class GooglePrivacyDlpV2CharacterMaskConfig extends \Google\Collection
{
  protected $collection_key = 'charactersToIgnore';
  protected $charactersToIgnoreType = GooglePrivacyDlpV2CharsToIgnore::class;
  protected $charactersToIgnoreDataType = 'array';
  /**
   * Character to use to mask the sensitive values—for example, `*` for an
   * alphabetic string such as a name, or `0` for a numeric string such as ZIP
   * code or credit card number. This string must have a length of 1. If not
   * supplied, this value defaults to `*` for strings, and `0` for digits.
   *
   * @var string
   */
  public $maskingCharacter;
  /**
   * Number of characters to mask. If not set, all matching chars will be
   * masked. Skipped characters do not count towards this tally. If
   * `number_to_mask` is negative, this denotes inverse masking. Cloud DLP masks
   * all but a number of characters. For example, suppose you have the following
   * values: - `masking_character` is `*` - `number_to_mask` is `-4` -
   * `reverse_order` is `false` - `CharsToIgnore` includes `-` - Input string is
   * `1234-5678-9012-3456` The resulting de-identified string is
   * `****-****-****-3456`. Cloud DLP masks all but the last four characters. If
   * `reverse_order` is `true`, all but the first four characters are masked as
   * `1234-****-****-****`.
   *
   * @var int
   */
  public $numberToMask;
  /**
   * Mask characters in reverse order. For example, if `masking_character` is
   * `0`, `number_to_mask` is `14`, and `reverse_order` is `false`, then the
   * input string `1234-5678-9012-3456` is masked as `00000000000000-3456`. If
   * `masking_character` is `*`, `number_to_mask` is `3`, and `reverse_order` is
   * `true`, then the string `12345` is masked as `12***`.
   *
   * @var bool
   */
  public $reverseOrder;

  /**
   * When masking a string, items in this list will be skipped when replacing
   * characters. For example, if the input string is `555-555-5555` and you
   * instruct Cloud DLP to skip `-` and mask 5 characters with `*`, Cloud DLP
   * returns `***-**5-5555`.
   *
   * @param GooglePrivacyDlpV2CharsToIgnore[] $charactersToIgnore
   */
  public function setCharactersToIgnore($charactersToIgnore)
  {
    $this->charactersToIgnore = $charactersToIgnore;
  }
  /**
   * @return GooglePrivacyDlpV2CharsToIgnore[]
   */
  public function getCharactersToIgnore()
  {
    return $this->charactersToIgnore;
  }
  /**
   * Character to use to mask the sensitive values—for example, `*` for an
   * alphabetic string such as a name, or `0` for a numeric string such as ZIP
   * code or credit card number. This string must have a length of 1. If not
   * supplied, this value defaults to `*` for strings, and `0` for digits.
   *
   * @param string $maskingCharacter
   */
  public function setMaskingCharacter($maskingCharacter)
  {
    $this->maskingCharacter = $maskingCharacter;
  }
  /**
   * @return string
   */
  public function getMaskingCharacter()
  {
    return $this->maskingCharacter;
  }
  /**
   * Number of characters to mask. If not set, all matching chars will be
   * masked. Skipped characters do not count towards this tally. If
   * `number_to_mask` is negative, this denotes inverse masking. Cloud DLP masks
   * all but a number of characters. For example, suppose you have the following
   * values: - `masking_character` is `*` - `number_to_mask` is `-4` -
   * `reverse_order` is `false` - `CharsToIgnore` includes `-` - Input string is
   * `1234-5678-9012-3456` The resulting de-identified string is
   * `****-****-****-3456`. Cloud DLP masks all but the last four characters. If
   * `reverse_order` is `true`, all but the first four characters are masked as
   * `1234-****-****-****`.
   *
   * @param int $numberToMask
   */
  public function setNumberToMask($numberToMask)
  {
    $this->numberToMask = $numberToMask;
  }
  /**
   * @return int
   */
  public function getNumberToMask()
  {
    return $this->numberToMask;
  }
  /**
   * Mask characters in reverse order. For example, if `masking_character` is
   * `0`, `number_to_mask` is `14`, and `reverse_order` is `false`, then the
   * input string `1234-5678-9012-3456` is masked as `00000000000000-3456`. If
   * `masking_character` is `*`, `number_to_mask` is `3`, and `reverse_order` is
   * `true`, then the string `12345` is masked as `12***`.
   *
   * @param bool $reverseOrder
   */
  public function setReverseOrder($reverseOrder)
  {
    $this->reverseOrder = $reverseOrder;
  }
  /**
   * @return bool
   */
  public function getReverseOrder()
  {
    return $this->reverseOrder;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2CharacterMaskConfig::class, 'Google_Service_DLP_GooglePrivacyDlpV2CharacterMaskConfig');
