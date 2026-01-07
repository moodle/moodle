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

namespace Google\Service\Dataform;

class SshAuthenticationConfig extends \Google\Model
{
  /**
   * Required. Content of a public SSH key to verify an identity of a remote Git
   * host.
   *
   * @var string
   */
  public $hostPublicKey;
  /**
   * Required. The name of the Secret Manager secret version to use as a ssh
   * private key for Git operations. Must be in the format
   * `projects/secrets/versions`.
   *
   * @var string
   */
  public $userPrivateKeySecretVersion;

  /**
   * Required. Content of a public SSH key to verify an identity of a remote Git
   * host.
   *
   * @param string $hostPublicKey
   */
  public function setHostPublicKey($hostPublicKey)
  {
    $this->hostPublicKey = $hostPublicKey;
  }
  /**
   * @return string
   */
  public function getHostPublicKey()
  {
    return $this->hostPublicKey;
  }
  /**
   * Required. The name of the Secret Manager secret version to use as a ssh
   * private key for Git operations. Must be in the format
   * `projects/secrets/versions`.
   *
   * @param string $userPrivateKeySecretVersion
   */
  public function setUserPrivateKeySecretVersion($userPrivateKeySecretVersion)
  {
    $this->userPrivateKeySecretVersion = $userPrivateKeySecretVersion;
  }
  /**
   * @return string
   */
  public function getUserPrivateKeySecretVersion()
  {
    return $this->userPrivateKeySecretVersion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SshAuthenticationConfig::class, 'Google_Service_Dataform_SshAuthenticationConfig');
