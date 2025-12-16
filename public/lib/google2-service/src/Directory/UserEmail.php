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

namespace Google\Service\Directory;

class UserEmail extends \Google\Model
{
  protected $internal_gapi_mappings = [
        "publicKeyEncryptionCertificates" => "public_key_encryption_certificates",
  ];
  /**
   * Email id of the user.
   *
   * @var string
   */
  public $address;
  /**
   * Custom Type.
   *
   * @var string
   */
  public $customType;
  /**
   * If this is user's primary email. Only one entry could be marked as primary.
   *
   * @var bool
   */
  public $primary;
  protected $publicKeyEncryptionCertificatesType = UserEmailPublicKeyEncryptionCertificates::class;
  protected $publicKeyEncryptionCertificatesDataType = '';
  /**
   * Each entry can have a type which indicates standard types of that entry.
   * For example email could be of home, work etc. In addition to the standard
   * type, an entry can have a custom type and can take any value Such types
   * should have the CUSTOM value as type and also have a customType value.
   *
   * @var string
   */
  public $type;

  /**
   * Email id of the user.
   *
   * @param string $address
   */
  public function setAddress($address)
  {
    $this->address = $address;
  }
  /**
   * @return string
   */
  public function getAddress()
  {
    return $this->address;
  }
  /**
   * Custom Type.
   *
   * @param string $customType
   */
  public function setCustomType($customType)
  {
    $this->customType = $customType;
  }
  /**
   * @return string
   */
  public function getCustomType()
  {
    return $this->customType;
  }
  /**
   * If this is user's primary email. Only one entry could be marked as primary.
   *
   * @param bool $primary
   */
  public function setPrimary($primary)
  {
    $this->primary = $primary;
  }
  /**
   * @return bool
   */
  public function getPrimary()
  {
    return $this->primary;
  }
  /**
   * Public Key Encryption Certificates. Current limit: 1 per email address, and
   * 5 per user.
   *
   * @param UserEmailPublicKeyEncryptionCertificates $publicKeyEncryptionCertificates
   */
  public function setPublicKeyEncryptionCertificates(UserEmailPublicKeyEncryptionCertificates $publicKeyEncryptionCertificates)
  {
    $this->publicKeyEncryptionCertificates = $publicKeyEncryptionCertificates;
  }
  /**
   * @return UserEmailPublicKeyEncryptionCertificates
   */
  public function getPublicKeyEncryptionCertificates()
  {
    return $this->publicKeyEncryptionCertificates;
  }
  /**
   * Each entry can have a type which indicates standard types of that entry.
   * For example email could be of home, work etc. In addition to the standard
   * type, an entry can have a custom type and can take any value Such types
   * should have the CUSTOM value as type and also have a customType value.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UserEmail::class, 'Google_Service_Directory_UserEmail');
