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

class VerificationCode extends \Google\Model
{
  /**
   * ETag of the resource.
   *
   * @var string
   */
  public $etag;
  /**
   * The type of the resource. This is always
   * `admin#directory#verificationCode`.
   *
   * @var string
   */
  public $kind;
  /**
   * The obfuscated unique ID of the user.
   *
   * @var string
   */
  public $userId;
  /**
   * A current verification code for the user. Invalidated or used verification
   * codes are not returned as part of the result.
   *
   * @var string
   */
  public $verificationCode;

  /**
   * ETag of the resource.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * The type of the resource. This is always
   * `admin#directory#verificationCode`.
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
   * The obfuscated unique ID of the user.
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
   * A current verification code for the user. Invalidated or used verification
   * codes are not returned as part of the result.
   *
   * @param string $verificationCode
   */
  public function setVerificationCode($verificationCode)
  {
    $this->verificationCode = $verificationCode;
  }
  /**
   * @return string
   */
  public function getVerificationCode()
  {
    return $this->verificationCode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VerificationCode::class, 'Google_Service_Directory_VerificationCode');
