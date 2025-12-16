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

namespace Google\Service\Batch;

class Environment extends \Google\Model
{
  protected $encryptedVariablesType = KMSEnvMap::class;
  protected $encryptedVariablesDataType = '';
  /**
   * A map of environment variable names to Secret Manager secret names. The VM
   * will access the named secrets to set the value of each environment
   * variable.
   *
   * @var string[]
   */
  public $secretVariables;
  /**
   * A map of environment variable names to values.
   *
   * @var string[]
   */
  public $variables;

  /**
   * An encrypted JSON dictionary where the key/value pairs correspond to
   * environment variable names and their values.
   *
   * @param KMSEnvMap $encryptedVariables
   */
  public function setEncryptedVariables(KMSEnvMap $encryptedVariables)
  {
    $this->encryptedVariables = $encryptedVariables;
  }
  /**
   * @return KMSEnvMap
   */
  public function getEncryptedVariables()
  {
    return $this->encryptedVariables;
  }
  /**
   * A map of environment variable names to Secret Manager secret names. The VM
   * will access the named secrets to set the value of each environment
   * variable.
   *
   * @param string[] $secretVariables
   */
  public function setSecretVariables($secretVariables)
  {
    $this->secretVariables = $secretVariables;
  }
  /**
   * @return string[]
   */
  public function getSecretVariables()
  {
    return $this->secretVariables;
  }
  /**
   * A map of environment variable names to values.
   *
   * @param string[] $variables
   */
  public function setVariables($variables)
  {
    $this->variables = $variables;
  }
  /**
   * @return string[]
   */
  public function getVariables()
  {
    return $this->variables;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Environment::class, 'Google_Service_Batch_Environment');
