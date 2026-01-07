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

class GooglePrivacyDlpV2TransientCryptoKey extends \Google\Model
{
  /**
   * Required. Name of the key. This is an arbitrary string used to
   * differentiate different keys. A unique key is generated per name: two
   * separate `TransientCryptoKey` protos share the same generated key if their
   * names are the same. When the data crypto key is generated, this name is not
   * used in any way (repeating the api call will result in a different key
   * being generated).
   *
   * @var string
   */
  public $name;

  /**
   * Required. Name of the key. This is an arbitrary string used to
   * differentiate different keys. A unique key is generated per name: two
   * separate `TransientCryptoKey` protos share the same generated key if their
   * names are the same. When the data crypto key is generated, this name is not
   * used in any way (repeating the api call will result in a different key
   * being generated).
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2TransientCryptoKey::class, 'Google_Service_DLP_GooglePrivacyDlpV2TransientCryptoKey');
