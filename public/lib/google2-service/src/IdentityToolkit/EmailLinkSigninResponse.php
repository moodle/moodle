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

class EmailLinkSigninResponse extends \Google\Model
{
  /**
   * The user's email.
   *
   * @var string
   */
  public $email;
  /**
   * Expiration time of STS id token in seconds.
   *
   * @var string
   */
  public $expiresIn;
  /**
   * The STS id token to login the newly signed in user.
   *
   * @var string
   */
  public $idToken;
  /**
   * Whether the user is new.
   *
   * @var bool
   */
  public $isNewUser;
  /**
   * The fixed string "identitytoolkit#EmailLinkSigninResponse".
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
   * The refresh token for the signed in user.
   *
   * @var string
   */
  public $refreshToken;

  /**
   * The user's email.
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
   * Expiration time of STS id token in seconds.
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
   * The STS id token to login the newly signed in user.
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
   * Whether the user is new.
   *
   * @param bool $isNewUser
   */
  public function setIsNewUser($isNewUser)
  {
    $this->isNewUser = $isNewUser;
  }
  /**
   * @return bool
   */
  public function getIsNewUser()
  {
    return $this->isNewUser;
  }
  /**
   * The fixed string "identitytoolkit#EmailLinkSigninResponse".
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
   * The refresh token for the signed in user.
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
class_alias(EmailLinkSigninResponse::class, 'Google_Service_IdentityToolkit_EmailLinkSigninResponse');
