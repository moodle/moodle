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

namespace Google\Service\DeveloperConnect;

class FetchReadTokenResponse extends \Google\Model
{
  /**
   * Expiration timestamp. Can be empty if unknown or non-expiring.
   *
   * @var string
   */
  public $expirationTime;
  /**
   * The git_username to specify when making a git clone with the token. For
   * example, for GitHub GitRepositoryLinks, this would be "x-access-token"
   *
   * @var string
   */
  public $gitUsername;
  /**
   * The token content.
   *
   * @var string
   */
  public $token;

  /**
   * Expiration timestamp. Can be empty if unknown or non-expiring.
   *
   * @param string $expirationTime
   */
  public function setExpirationTime($expirationTime)
  {
    $this->expirationTime = $expirationTime;
  }
  /**
   * @return string
   */
  public function getExpirationTime()
  {
    return $this->expirationTime;
  }
  /**
   * The git_username to specify when making a git clone with the token. For
   * example, for GitHub GitRepositoryLinks, this would be "x-access-token"
   *
   * @param string $gitUsername
   */
  public function setGitUsername($gitUsername)
  {
    $this->gitUsername = $gitUsername;
  }
  /**
   * @return string
   */
  public function getGitUsername()
  {
    return $this->gitUsername;
  }
  /**
   * The token content.
   *
   * @param string $token
   */
  public function setToken($token)
  {
    $this->token = $token;
  }
  /**
   * @return string
   */
  public function getToken()
  {
    return $this->token;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FetchReadTokenResponse::class, 'Google_Service_DeveloperConnect_FetchReadTokenResponse');
