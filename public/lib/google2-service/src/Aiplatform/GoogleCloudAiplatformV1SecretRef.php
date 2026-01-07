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

class GoogleCloudAiplatformV1SecretRef extends \Google\Model
{
  /**
   * Required. The name of the secret in Cloud Secret Manager. Format:
   * {secret_name}.
   *
   * @var string
   */
  public $secret;
  /**
   * The Cloud Secret Manager secret version. Can be 'latest' for the latest
   * version, an integer for a specific version, or a version alias.
   *
   * @var string
   */
  public $version;

  /**
   * Required. The name of the secret in Cloud Secret Manager. Format:
   * {secret_name}.
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
   * The Cloud Secret Manager secret version. Can be 'latest' for the latest
   * version, an integer for a specific version, or a version alias.
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
class_alias(GoogleCloudAiplatformV1SecretRef::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SecretRef');
