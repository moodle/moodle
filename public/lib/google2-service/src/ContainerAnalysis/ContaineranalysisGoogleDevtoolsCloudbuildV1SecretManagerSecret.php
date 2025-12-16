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

namespace Google\Service\ContainerAnalysis;

class ContaineranalysisGoogleDevtoolsCloudbuildV1SecretManagerSecret extends \Google\Model
{
  /**
   * Environment variable name to associate with the secret. Secret environment
   * variables must be unique across all of a build's secrets, and must be used
   * by at least one build step.
   *
   * @var string
   */
  public $env;
  /**
   * Resource name of the SecretVersion. In format: projects/secrets/versions
   *
   * @var string
   */
  public $versionName;

  /**
   * Environment variable name to associate with the secret. Secret environment
   * variables must be unique across all of a build's secrets, and must be used
   * by at least one build step.
   *
   * @param string $env
   */
  public function setEnv($env)
  {
    $this->env = $env;
  }
  /**
   * @return string
   */
  public function getEnv()
  {
    return $this->env;
  }
  /**
   * Resource name of the SecretVersion. In format: projects/secrets/versions
   *
   * @param string $versionName
   */
  public function setVersionName($versionName)
  {
    $this->versionName = $versionName;
  }
  /**
   * @return string
   */
  public function getVersionName()
  {
    return $this->versionName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ContaineranalysisGoogleDevtoolsCloudbuildV1SecretManagerSecret::class, 'Google_Service_ContainerAnalysis_ContaineranalysisGoogleDevtoolsCloudbuildV1SecretManagerSecret');
