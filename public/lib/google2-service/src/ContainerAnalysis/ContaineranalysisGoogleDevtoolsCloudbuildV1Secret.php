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

class ContaineranalysisGoogleDevtoolsCloudbuildV1Secret extends \Google\Model
{
  /**
   * Cloud KMS key name to use to decrypt these envs.
   *
   * @var string
   */
  public $kmsKeyName;
  /**
   * Map of environment variable name to its encrypted value. Secret environment
   * variables must be unique across all of a build's secrets, and must be used
   * by at least one build step. Values can be at most 64 KB in size. There can
   * be at most 100 secret values across all of a build's secrets.
   *
   * @var string[]
   */
  public $secretEnv;

  /**
   * Cloud KMS key name to use to decrypt these envs.
   *
   * @param string $kmsKeyName
   */
  public function setKmsKeyName($kmsKeyName)
  {
    $this->kmsKeyName = $kmsKeyName;
  }
  /**
   * @return string
   */
  public function getKmsKeyName()
  {
    return $this->kmsKeyName;
  }
  /**
   * Map of environment variable name to its encrypted value. Secret environment
   * variables must be unique across all of a build's secrets, and must be used
   * by at least one build step. Values can be at most 64 KB in size. There can
   * be at most 100 secret values across all of a build's secrets.
   *
   * @param string[] $secretEnv
   */
  public function setSecretEnv($secretEnv)
  {
    $this->secretEnv = $secretEnv;
  }
  /**
   * @return string[]
   */
  public function getSecretEnv()
  {
    return $this->secretEnv;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ContaineranalysisGoogleDevtoolsCloudbuildV1Secret::class, 'Google_Service_ContainerAnalysis_ContaineranalysisGoogleDevtoolsCloudbuildV1Secret');
