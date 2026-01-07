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

namespace Google\Service\CloudFunctions;

class SecretEnvVar extends \Google\Model
{
  /**
   * Name of the environment variable.
   *
   * @var string
   */
  public $key;
  /**
   * Project identifier (preferably project number but can also be the project
   * ID) of the project that contains the secret. If not set, it is assumed that
   * the secret is in the same project as the function.
   *
   * @var string
   */
  public $projectId;
  /**
   * Name of the secret in secret manager (not the full resource name).
   *
   * @var string
   */
  public $secret;
  /**
   * Version of the secret (version number or the string 'latest'). It is
   * recommended to use a numeric version for secret environment variables as
   * any updates to the secret value is not reflected until new instances start.
   *
   * @var string
   */
  public $version;

  /**
   * Name of the environment variable.
   *
   * @param string $key
   */
  public function setKey($key)
  {
    $this->key = $key;
  }
  /**
   * @return string
   */
  public function getKey()
  {
    return $this->key;
  }
  /**
   * Project identifier (preferably project number but can also be the project
   * ID) of the project that contains the secret. If not set, it is assumed that
   * the secret is in the same project as the function.
   *
   * @param string $projectId
   */
  public function setProjectId($projectId)
  {
    $this->projectId = $projectId;
  }
  /**
   * @return string
   */
  public function getProjectId()
  {
    return $this->projectId;
  }
  /**
   * Name of the secret in secret manager (not the full resource name).
   *
   * @param string $secret
   */
  public function setSecret($secret)
  {
    $this->secret = $secret;
  }
  /**
   * @return string
   */
  public function getSecret()
  {
    return $this->secret;
  }
  /**
   * Version of the secret (version number or the string 'latest'). It is
   * recommended to use a numeric version for secret environment variables as
   * any updates to the secret value is not reflected until new instances start.
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SecretEnvVar::class, 'Google_Service_CloudFunctions_SecretEnvVar');
