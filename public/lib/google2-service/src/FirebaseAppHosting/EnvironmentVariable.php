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

namespace Google\Service\FirebaseAppHosting;

class EnvironmentVariable extends \Google\Collection
{
  protected $collection_key = 'availability';
  /**
   * Optional. Where this variable should be made available. If left
   * unspecified, will be available in both BUILD and BACKEND.
   *
   * @var string[]
   */
  public $availability;
  /**
   * A fully qualified secret version. The value of the secret will be accessed
   * once while building the application and once per cold start of the
   * container at runtime. The service account used by Cloud Build and by Cloud
   * Run must each have the `secretmanager.versions.access` permission on the
   * secret.
   *
   * @var string
   */
  public $secret;
  /**
   * A plaintext value. This value is encrypted at rest, but all project readers
   * can view the value when reading your backend configuration.
   *
   * @var string
   */
  public $value;
  /**
   * Required. The name of the environment variable. - Must be a valid
   * environment variable name (e.g. A-Z or underscores). - May not start with
   * "FIREBASE" or "GOOGLE". - May not be a reserved environment variable for
   * KNative/Cloud Run
   *
   * @var string
   */
  public $variable;

  /**
   * Optional. Where this variable should be made available. If left
   * unspecified, will be available in both BUILD and BACKEND.
   *
   * @param string[] $availability
   */
  public function setAvailability($availability)
  {
    $this->availability = $availability;
  }
  /**
   * @return string[]
   */
  public function getAvailability()
  {
    return $this->availability;
  }
  /**
   * A fully qualified secret version. The value of the secret will be accessed
   * once while building the application and once per cold start of the
   * container at runtime. The service account used by Cloud Build and by Cloud
   * Run must each have the `secretmanager.versions.access` permission on the
   * secret.
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
   * A plaintext value. This value is encrypted at rest, but all project readers
   * can view the value when reading your backend configuration.
   *
   * @param string $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }
  /**
   * Required. The name of the environment variable. - Must be a valid
   * environment variable name (e.g. A-Z or underscores). - May not start with
   * "FIREBASE" or "GOOGLE". - May not be a reserved environment variable for
   * KNative/Cloud Run
   *
   * @param string $variable
   */
  public function setVariable($variable)
  {
    $this->variable = $variable;
  }
  /**
   * @return string
   */
  public function getVariable()
  {
    return $this->variable;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnvironmentVariable::class, 'Google_Service_FirebaseAppHosting_EnvironmentVariable');
