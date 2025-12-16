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

namespace Google\Service\IdentityToolkit;

class SignupNewUserResponse extends \Google\Model
{
  /**
   * The name of the user.
   *
   * @var string
   */
  public $displayName;
  /**
   * The email of the user.
   *
   * @var string
   */
  public $email;
  /**
   * If idToken is STS id token, then this field will be expiration time of STS
   * id token in seconds.
   *
   * @var string
   */
  public $expiresIn;
  /**
   * The Gitkit id token to login the newly sign up user.
   *
   * @var string
   */
  public $idToken;
  /**
   * The fixed string "identitytoolkit#SignupNewUserResponse".
   *
   * @var string
   */
  public $kind;
  /**
   * The RP local ID of the user.
   *
   * @var string
   */
  public $localId;
  /**
   * If idToken is STS id token, then this field will be refresh token.
   *
   * @var string
   */
  public $refreshToken;

  /**
   * The name of the user.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * The email of the user.
   *
   * @param string $email
   */
  public function setEmail($email)
  {
    $this->email = $email;
  }
  /**
   * @return string
   */
  public function getEmail()
  {
    return $this->email;
  }
  /**
   * If idToken is STS id token, then this field will be expiration time of STS
   * id token in seconds.
   *
   * @param string $expiresIn
   */
  public function setExpiresIn($expiresIn)
  {
    $this->expiresIn = $expiresIn;
  }
  /**
   * @return string
   */
  public function getExpiresIn()
  {
    return $this->expiresIn;
  }
  /**
   * The Gitkit id token to login the newly sign up user.
   *
   * @param string $idToken
   */
  public function setIdToken($idToken)
  {
    $this->idToken = $idToken;
  }
  /**
   * @return string
   */
  public function getIdToken()
  {
    return $this->idToken;
  }
  /**
   * The fixed string "identitytoolkit#SignupNewUserResponse".
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
   * The RP local ID of the user.
   *
   * @param string $localId
   */
  public function setLocalId($localId)
  {
    $this->localId = $localId;
  }
  /**
   * @return string
   */
  public function getLocalId()
  {
    return $this->localId;
  }
  /**
   * If idToken is STS id token, then this field will be refresh token.
   *
   * @param string $refreshToken
   */
  public function setRefreshToken($refreshToken)
  {
    $this->refreshToken = $refreshToken;
  }
  /**
   * @return string
   */
  public function getRefreshToken()
  {
    return $this->refreshToken;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SignupNewUserResponse::class, 'Google_Service_IdentityToolkit_SignupNewUserResponse');
