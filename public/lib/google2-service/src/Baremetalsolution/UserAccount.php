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

namespace Google\Service\Baremetalsolution;

class UserAccount extends \Google\Model
{
  /**
   * Encrypted initial password value.
   *
   * @var string
   */
  public $encryptedPassword;
  /**
   * KMS CryptoKey Version used to encrypt the password.
   *
   * @var string
   */
  public $kmsKeyVersion;

  /**
   * Encrypted initial password value.
   *
   * @param string $encryptedPassword
   */
  public function setEncryptedPassword($encryptedPassword)
  {
    $this->encryptedPassword = $encryptedPassword;
  }
  /**
   * @return string
   */
  public function getEncryptedPassword()
  {
    return $this->encryptedPassword;
  }
  /**
   * KMS CryptoKey Version used to encrypt the password.
   *
   * @param string $kmsKeyVersion
   */
  public function setKmsKeyVersion($kmsKeyVersion)
  {
    $this->kmsKeyVersion = $kmsKeyVersion;
  }
  /**
   * @return string
   */
  public function getKmsKeyVersion()
  {
    return $this->kmsKeyVersion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UserAccount::class, 'Google_Service_Baremetalsolution_UserAccount');
