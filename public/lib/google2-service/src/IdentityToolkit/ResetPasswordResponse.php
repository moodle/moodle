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

class ResetPasswordResponse extends \Google\Model
{
  /**
   * The user's email. If the out-of-band code is for email recovery, the user's
   * original email.
   *
   * @var string
   */
  public $email;
  /**
   * The fixed string "identitytoolkit#ResetPasswordResponse".
   *
   * @var string
   */
  public $kind;
  /**
   * If the out-of-band code is for email recovery, the user's new email.
   *
   * @var string
   */
  public $newEmail;
  /**
   * The request type.
   *
   * @var string
   */
  public $requestType;

  /**
   * The user's email. If the out-of-band code is for email recovery, the user's
   * original email.
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
   * The fixed string "identitytoolkit#ResetPasswordResponse".
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
   * If the out-of-band code is for email recovery, the user's new email.
   *
   * @param string $newEmail
   */
  public function setNewEmail($newEmail)
  {
    $this->newEmail = $newEmail;
  }
  /**
   * @return string
   */
  public function getNewEmail()
  {
    return $this->newEmail;
  }
  /**
   * The request type.
   *
   * @param string $requestType
   */
  public function setRequestType($requestType)
  {
    $this->requestType = $requestType;
  }
  /**
   * @return string
   */
  public function getRequestType()
  {
    return $this->requestType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ResetPasswordResponse::class, 'Google_Service_IdentityToolkit_ResetPasswordResponse');
