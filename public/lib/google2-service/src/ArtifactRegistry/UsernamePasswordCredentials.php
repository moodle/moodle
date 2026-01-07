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

namespace Google\Service\ArtifactRegistry;

class UsernamePasswordCredentials extends \Google\Model
{
  /**
   * The Secret Manager key version that holds the password to access the remote
   * repository. Must be in the format of
   * `projects/{project}/secrets/{secret}/versions/{version}`.
   *
   * @var string
   */
  public $passwordSecretVersion;
  /**
   * The username to access the remote repository.
   *
   * @var string
   */
  public $username;

  /**
   * The Secret Manager key version that holds the password to access the remote
   * repository. Must be in the format of
   * `projects/{project}/secrets/{secret}/versions/{version}`.
   *
   * @param string $passwordSecretVersion
   */
  public function setPasswordSecretVersion($passwordSecretVersion)
  {
    $this->passwordSecretVersion = $passwordSecretVersion;
  }
  /**
   * @return string
   */
  public function getPasswordSecretVersion()
  {
    return $this->passwordSecretVersion;
  }
  /**
   * The username to access the remote repository.
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
class_alias(UsernamePasswordCredentials::class, 'Google_Service_ArtifactRegistry_UsernamePasswordCredentials');
