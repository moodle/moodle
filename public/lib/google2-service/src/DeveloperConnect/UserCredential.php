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

class UserCredential extends \Google\Model
{
  /**
   * Required. A SecretManager resource containing the user token that
   * authorizes the Developer Connect connection. Format:
   * `projects/secrets/versions` or `projects/locations/secrets/versions` (if
   * regional secrets are supported in that location).
   *
   * @var string
   */
  public $userTokenSecretVersion;
  /**
   * Output only. The username associated with this token.
   *
   * @var string
   */
  public $username;

  /**
   * Required. A SecretManager resource containing the user token that
   * authorizes the Developer Connect connection. Format:
   * `projects/secrets/versions` or `projects/locations/secrets/versions` (if
   * regional secrets are supported in that location).
   *
   * @param string $userTokenSecretVersion
   */
  public function setUserTokenSecretVersion($userTokenSecretVersion)
  {
    $this->userTokenSecretVersion = $userTokenSecretVersion;
  }
  /**
   * @return string
   */
  public function getUserTokenSecretVersion()
  {
    return $this->userTokenSecretVersion;
  }
  /**
   * Output only. The username associated with this token.
   *
   * @param string $username
   */
  public function setUsername($username)
  {
    $this->username = $username;
  }
  /**
   * @return string
   */
  public function getUsername()
  {
    return $this->username;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UserCredential::class, 'Google_Service_DeveloperConnect_UserCredential');
