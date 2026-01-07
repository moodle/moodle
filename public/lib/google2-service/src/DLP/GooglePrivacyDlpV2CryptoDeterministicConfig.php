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

class GooglePrivacyDlpV2CryptoDeterministicConfig extends \Google\Model
{
  protected $contextType = GooglePrivacyDlpV2FieldId::class;
  protected $contextDataType = '';
  protected $cryptoKeyType = GooglePrivacyDlpV2CryptoKey::class;
  protected $cryptoKeyDataType = '';
  protected $surrogateInfoTypeType = GooglePrivacyDlpV2InfoType::class;
  protected $surrogateInfoTypeDataType = '';

  /**
   * A context may be used for higher security and maintaining referential
   * integrity such that the same identifier in two different contexts will be
   * given a distinct surrogate. The context is appended to plaintext value
   * being encrypted. On decryption the provided context is validated against
   * the value used during encryption. If a context was provided during
   * encryption, same context must be provided during decryption as well. If the
   * context is not set, plaintext would be used as is for encryption. If the
   * context is set but: 1. there is no record present when transforming a given
   * value or 2. the field is not present when transforming a given value,
   * plaintext would be used as is for encryption. Note that case (1) is
   * expected when an `InfoTypeTransformation` is applied to both structured and
   * unstructured `ContentItem`s.
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
   * The key used by the encryption function. For deterministic encryption using
   * AES-SIV, the provided key is internally expanded to 64 bytes prior to use.
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
   * The custom info type to annotate the surrogate with. This annotation will
   * be applied to the surrogate by prefixing it with the name of the custom
   * info type followed by the number of characters comprising the surrogate.
   * The following scheme defines the format: {info type name}({surrogate
   * character count}):{surrogate} For example, if the name of custom info type
   * is 'MY_TOKEN_INFO_TYPE' and the surrogate is 'abc', the full replacement
   * value will be: 'MY_TOKEN_INFO_TYPE(3):abc' This annotation identifies the
   * surrogate when inspecting content using the custom info type 'Surrogate'.
   * This facilitates reversal of the surrogate when it occurs in free text.
   * Note: For record transformations where the entire cell in a table is being
   * transformed, surrogates are not mandatory. Surrogates are used to denote
   * the location of the token and are necessary for re-identification in free
   * form text. In order for inspection to work properly, the name of this info
   * type must not occur naturally anywhere in your data; otherwise, inspection
   * may either - reverse a surrogate that does not correspond to an actual
   * identifier - be unable to parse the surrogate and result in an error
   * Therefore, choose your custom info type name carefully after considering
   * what your data looks like. One way to select a name that has a high chance
   * of yielding reliable detection is to include one or more unicode characters
   * that are highly improbable to exist in your data. For example, assuming
   * your data is entered from a regular ASCII keyboard, the symbol with the hex
   * code point 29DD might be used like so: ⧝MY_TOKEN_TYPE.
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
class_alias(GooglePrivacyDlpV2CryptoDeterministicConfig::class, 'Google_Service_DLP_GooglePrivacyDlpV2CryptoDeterministicConfig');
