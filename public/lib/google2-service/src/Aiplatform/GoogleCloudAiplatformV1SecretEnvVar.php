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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1SecretEnvVar extends \Google\Model
{
  /**
   * Required. Name of the secret environment variable.
   *
   * @var string
   */
  public $name;
  protected $secretRefType = GoogleCloudAiplatformV1SecretRef::class;
  protected $secretRefDataType = '';

  /**
   * Required. Name of the secret environment variable.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Required. Reference to a secret stored in the Cloud Secret Manager that
   * will provide the value for this environment variable.
   *
   * @param GoogleCloudAiplatformV1SecretRef $secretRef
   */
  public function setSecretRef(GoogleCloudAiplatformV1SecretRef $secretRef)
  {
    $this->secretRef = $secretRef;
  }
  /**
   * @return GoogleCloudAiplatformV1SecretRef
   */
  public function getSecretRef()
  {
    return $this->secretRef;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SecretEnvVar::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SecretEnvVar');
