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

namespace Google\Service\Oauth2;

class Tokeninfo extends \Google\Model
{
  protected $internal_gapi_mappings = [
        "expiresIn" => "expires_in",
        "issuedTo" => "issued_to",
        "userId" => "user_id",
        "verifiedEmail" => "verified_email",
  ];
  /**
   * Who is the intended audience for this token. In general the same as
   * issued_to.
   *
   * @var string
   */
  public $audience;
  /**
   * The email address of the user. Present only if the email scope is present
   * in the request.
   *
   * @var string
   */
  public $email;
  /**
   * The expiry time of the token, as number of seconds left until expiry.
   *
   * @var int
   */
  public $expiresIn;
  /**
   * To whom was the token issued to. In general the same as audience.
   *
   * @var string
   */
  public $issuedTo;
  /**
   * The space separated list of scopes granted to this token.
   *
   * @var string
   */
  public $scope;
  /**
   * The obfuscated user id.
   *
   * @var string
   */
  public $userId;
  /**
   * Boolean flag which is true if the email address is verified. Present only
   * if the email scope is present in the request.
   *
   * @var bool
   */
  public $verifiedEmail;

  /**
   * Who is the intended audience for this token. In general the same as
   * issued_to.
   *
   * @param string $audience
   */
  public function setAudience($audience)
  {
    $this->audience = $audience;
  }
  /**
   * @return string
   */
  public function getAudience()
  {
    return $this->audience;
  }
  /**
   * The email address of the user. Present only if the email scope is present
   * in the request.
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
   * The expiry time of the token, as number of seconds left until expiry.
   *
   * @param int $expiresIn
   */
  public function setExpiresIn($expiresIn)
  {
    $this->expiresIn = $expiresIn;
  }
  /**
   * @return int
   */
  public function getExpiresIn()
  {
    return $this->expiresIn;
  }
  /**
   * To whom was the token issued to. In general the same as audience.
   *
   * @param string $issuedTo
   */
  public function setIssuedTo($issuedTo)
  {
    $this->issuedTo = $issuedTo;
  }
  /**
   * @return string
   */
  public function getIssuedTo()
  {
    return $this->issuedTo;
  }
  /**
   * The space separated list of scopes granted to this token.
   *
   * @param string $scope
   */
  public function setScope($scope)
  {
    $this->scope = $scope;
  }
  /**
   * @return string
   */
  public function getScope()
  {
    return $this->scope;
  }
  /**
   * The obfuscated user id.
   *
   * @param string $userId
   */
  public function setUserId($userId)
  {
    $this->userId = $userId;
  }
  /**
   * @return string
   */
  public function getUserId()
  {
    return $this->userId;
  }
  /**
   * Boolean flag which is true if the email address is verified. Present only
   * if the email scope is present in the request.
   *
   * @param bool $verifiedEmail
   */
  public function setVerifiedEmail($verifiedEmail)
  {
    $this->verifiedEmail = $verifiedEmail;
  }
  /**
   * @return bool
   */
  public function getVerifiedEmail()
  {
    return $this->verifiedEmail;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Tokeninfo::class, 'Google_Service_Oauth2_Tokeninfo');
