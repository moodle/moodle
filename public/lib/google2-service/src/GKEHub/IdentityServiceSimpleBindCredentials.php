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

namespace Google\Service\GKEHub;

class IdentityServiceSimpleBindCredentials extends \Google\Model
{
  /**
   * Required. The distinguished name(DN) of the service account object/user.
   *
   * @var string
   */
  public $dn;
  /**
   * Output only. The encrypted password of the service account object/user.
   *
   * @var string
   */
  public $encryptedPassword;
  /**
   * Required. Input only. The password of the service account object/user.
   *
   * @var string
   */
  public $password;

  /**
   * Required. The distinguished name(DN) of the service account object/user.
   *
   * @param string $dn
   */
  public function setDn($dn)
  {
    $this->dn = $dn;
  }
  /**
   * @return string
   */
  public function getDn()
  {
    return $this->dn;
  }
  /**
   * Output only. The encrypted password of the service account object/user.
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
   * Required. Input only. The password of the service account object/user.
   *
   * @param string $password
   */
  public function setPassword($password)
  {
    $this->password = $password;
  }
  /**
   * @return string
   */
  public function getPassword()
  {
    return $this->password;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IdentityServiceSimpleBindCredentials::class, 'Google_Service_GKEHub_IdentityServiceSimpleBindCredentials');
