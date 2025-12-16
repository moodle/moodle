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

namespace Google\Service\CertificateAuthorityService;

class KeyId extends \Google\Model
{
  /**
   * Optional. The value of this KeyId encoded in lowercase hexadecimal. This is
   * most likely the 160 bit SHA-1 hash of the public key.
   *
   * @var string
   */
  public $keyId;

  /**
   * Optional. The value of this KeyId encoded in lowercase hexadecimal. This is
   * most likely the 160 bit SHA-1 hash of the public key.
   *
   * @param string $keyId
   */
  public function setKeyId($keyId)
  {
    $this->keyId = $keyId;
  }
  /**
   * @return string
   */
  public function getKeyId()
  {
    return $this->keyId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(KeyId::class, 'Google_Service_CertificateAuthorityService_KeyId');
