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

class GooglePrivacyDlpV2CryptoReplaceFfxFpeConfig extends \Google\Model
{
  /**
   * Unused.
   */
  public const COMMON_ALPHABET_FFX_COMMON_NATIVE_ALPHABET_UNSPECIFIED = 'FFX_COMMON_NATIVE_ALPHABET_UNSPECIFIED';
  /**
   * `[0-9]` (radix of 10)
   */
  public const COMMON_ALPHABET_NUMERIC = 'NUMERIC';
  /**
   * `[0-9A-F]` (radix of 16)
   */
  public const COMMON_ALPHABET_HEXADECIMAL = 'HEXADECIMAL';
  /**
   * `[0-9A-Z]` (radix of 36)
   */
  public const COMMON_ALPHABET_UPPER_CASE_ALPHA_NUMERIC = 'UPPER_CASE_ALPHA_NUMERIC';
  /**
   * `[0-9A-Za-z]` (radix of 62)
   */
  public const COMMON_ALPHABET_ALPHA_NUMERIC = 'ALPHA_NUMERIC';
  /**
   * Common alphabets.
   *
   * @var string
   */
  public $commonAlphabet;
  protected $contextType = GooglePrivacyDlpV2FieldId::class;
  protected $contextDataType = '';
  protected $cryptoKeyType = GooglePrivacyDlpV2CryptoKey::class;
  protected $cryptoKeyDataType = '';
  /**
   * This is supported by mapping these to the alphanumeric characters that the
   * FFX mode natively supports. This happens before/after
   * encryption/decryption. Each character listed must appear only once. Number
   * of characters must be in the range [2, 95]. This must be encoded as ASCII.
   * The order of characters does not matter. The full list of allowed
   * characters is: ``0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuv
   * wxyz~`!@#$%^&*()_-+={[}]|\:;"'<,>.?/``
   *
   * @var string
   */
  public $customAlphabet;
  /**
   * The native way to select the alphabet. Must be in the range [2, 95].
   *
   * @var int
   */
  public $radix;
  protected $surrogateInfoTypeType = GooglePrivacyDlpV2InfoType::class;
  protected $surrogateInfoTypeDataType = '';

  /**
   * Common alphabets.
   *
   * Accepted values: FFX_COMMON_NATIVE_ALPHABET_UNSPECIFIED, NUMERIC,
   * HEXADECIMAL, UPPER_CASE_ALPHA_NUMERIC, ALPHA_NUMERIC
   *
   * @param self::COMMON_ALPHABET_* $commonAlphabet
   */
  public function setCommonAlphabet($commonAlphabet)
  {
    $this->commonAlphabet = $commonAlphabet;
  }
  /**
   * @return self::COMMON_ALPHABET_*
   */
  public function getCommonAlphabet()
  {
    return $this->commonAlphabet;
  }
  /**
   * The 'tweak', a context may be used for higher security since the same
   * identifier in two different contexts won't be given the same surrogate. If
   * the context is not set, a default tweak will be used. If the context is set
   * but: 1. there is no record present when transforming a given value or 1.
   * the field is not present when transforming a given value, a default tweak
   * will be used. Note that case (1) is expected when an
   * `InfoTypeTransformation` is applied to both structured and unstructured
   * `ContentItem`s. Currently, the referenced field may be of value type
   * integer or string. The tweak is constructed as a sequence of bytes in big
   * endian byte order such that: - a 64 bit integer is encoded followed by a
   * single byte of value 1 - a string is encoded in UTF-8 format followed by a
   * single byte of value 2
   *
   * @param GooglePrivacyDlpV2FieldId $context
   */
  public function setContext(GooglePrivacyDlpV2FieldId $context)
  {
    $this->context = $context;
  }
  /**
   * @return GooglePrivacyDlpV2FieldId
   */
  public function getContext()
  {
    return $this->context;
  }
  /**
   * Required. The key used by the encryption algorithm.
   *
   * @param GooglePrivacyDlpV2CryptoKey $cryptoKey
   */
  public function setCryptoKey(GooglePrivacyDlpV2CryptoKey $cryptoKey)
  {
    $this->cryptoKey = $cryptoKey;
  }
  /**
   * @return GooglePrivacyDlpV2CryptoKey
   */
  public function getCryptoKey()
  {
    return $this->cryptoKey;
  }
  /**
   * This is supported by mapping these to the alphanumeric characters that the
   * FFX mode natively supports. This happens before/after
   * encryption/decryption. Each character listed must appear only once. Number
   * of characters must be in the range [2, 95]. This must be encoded as ASCII.
   * The order of characters does not matter. The full list of allowed
   * characters is: ``0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuv
   * wxyz~`!@#$%^&*()_-+={[}]|\:;"'<,>.?/``
   *
   * @param string $customAlphabet
   */
  public function setCustomAlphabet($customAlphabet)
  {
    $this->customAlphabet = $customAlphabet;
  }
  /**
   * @return string
   */
  public function getCustomAlphabet()
  {
    return $this->customAlphabet;
  }
  /**
   * The native way to select the alphabet. Must be in the range [2, 95].
   *
   * @param int $radix
   */
  public function setRadix($radix)
  {
    $this->radix = $radix;
  }
  /**
   * @return int
   */
  public function getRadix()
  {
    return $this->radix;
  }
  /**
   * The custom infoType to annotate the surrogate with. This annotation will be
   * applied to the surrogate by prefixing it with the name of the custom
   * infoType followed by the number of characters comprising the surrogate. The
   * following scheme defines the format:
   * info_type_name(surrogate_character_count):surrogate For example, if the
   * name of custom infoType is 'MY_TOKEN_INFO_TYPE' and the surrogate is 'abc',
   * the full replacement value will be: 'MY_TOKEN_INFO_TYPE(3):abc' This
   * annotation identifies the surrogate when inspecting content using the
   * custom infoType [`SurrogateType`](https://cloud.google.com/sensitive-data-
   * protection/docs/reference/rest/v2/InspectConfig#surrogatetype). This
   * facilitates reversal of the surrogate when it occurs in free text. In order
   * for inspection to work properly, the name of this infoType must not occur
   * naturally anywhere in your data; otherwise, inspection may find a surrogate
   * that does not correspond to an actual identifier. Therefore, choose your
   * custom infoType name carefully after considering what your data looks like.
   * One way to select a name that has a high chance of yielding reliable
   * detection is to include one or more unicode characters that are highly
   * improbable to exist in your data. For example, assuming your data is
   * entered from a regular ASCII keyboard, the symbol with the hex code point
   * 29DD might be used like so: ⧝MY_TOKEN_TYPE
   *
   * @param GooglePrivacyDlpV2InfoType $surrogateInfoType
   */
  public function setSurrogateInfoType(GooglePrivacyDlpV2InfoType $surrogateInfoType)
  {
    $this->surrogateInfoType = $surrogateInfoType;
  }
  /**
   * @return GooglePrivacyDlpV2InfoType
   */
  public function getSurrogateInfoType()
  {
    return $this->surrogateInfoType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2CryptoReplaceFfxFpeConfig::class, 'Google_Service_DLP_GooglePrivacyDlpV2CryptoReplaceFfxFpeConfig');
