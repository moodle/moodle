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

namespace Google\Service\Storage;

class HmacKey extends \Google\Model
{
  /**
   * The kind of item this is. For HMAC keys, this is always storage#hmacKey.
   *
   * @var string
   */
  public $kind;
  protected $metadataType = HmacKeyMetadata::class;
  protected $metadataDataType = '';
  /**
   * HMAC secret key material.
   *
   * @var string
   */
  public $secret;

  /**
   * The kind of item this is. For HMAC keys, this is always storage#hmacKey.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Key metadata.
   *
   * @param HmacKeyMetadata $metadata
   */
  public function setMetadata(HmacKeyMetadata $metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return HmacKeyMetadata
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * HMAC secret key material.
   *
   * @param string $secret
   */
  public function setSecret($secret)
  {
    $this->secret = $secret;
  }
  /**
   * @return string
   */
  public function getSecret()
  {
    return $this->secret;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HmacKey::class, 'Google_Service_Storage_HmacKey');
