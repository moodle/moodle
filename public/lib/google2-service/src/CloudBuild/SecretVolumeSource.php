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

namespace Google\Service\CloudBuild;

class SecretVolumeSource extends \Google\Model
{
  /**
   * Name of the secret referenced by the WorkspaceBinding.
   *
   * @var string
   */
  public $secretName;
  /**
   * Optional. Resource name of the SecretVersion. In format:
   * projects/secrets/versions
   *
   * @var string
   */
  public $secretVersion;

  /**
   * Name of the secret referenced by the WorkspaceBinding.
   *
   * @param string $secretName
   */
  public function setSecretName($secretName)
  {
    $this->secretName = $secretName;
  }
  /**
   * @return string
   */
  public function getSecretName()
  {
    return $this->secretName;
  }
  /**
   * Optional. Resource name of the SecretVersion. In format:
   * projects/secrets/versions
   *
   * @param string $secretVersion
   */
  public function setSecretVersion($secretVersion)
  {
    $this->secretVersion = $secretVersion;
  }
  /**
   * @return string
   */
  public function getSecretVersion()
  {
    return $this->secretVersion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SecretVolumeSource::class, 'Google_Service_CloudBuild_SecretVolumeSource');
