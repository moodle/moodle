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

namespace Google\Service\APIhub;

class GoogleCloudApihubV1Oauth2ClientCredentialsConfig extends \Google\Model
{
  /**
   * Required. The client identifier.
   *
   * @var string
   */
  public $clientId;
  protected $clientSecretType = GoogleCloudApihubV1Secret::class;
  protected $clientSecretDataType = '';

  /**
   * Required. The client identifier.
   *
   * @param string $clientId
   */
  public function setClientId($clientId)
  {
    $this->clientId = $clientId;
  }
  /**
   * @return string
   */
  public function getClientId()
  {
    return $this->clientId;
  }
  /**
   * Required. Secret version reference containing the client secret. The
   * `secretmanager.versions.access` permission should be granted to the service
   * account accessing the secret.
   *
   * @param GoogleCloudApihubV1Secret $clientSecret
   */
  public function setClientSecret(GoogleCloudApihubV1Secret $clientSecret)
  {
    $this->clientSecret = $clientSecret;
  }
  /**
   * @return GoogleCloudApihubV1Secret
   */
  public function getClientSecret()
  {
    return $this->clientSecret;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApihubV1Oauth2ClientCredentialsConfig::class, 'Google_Service_APIhub_GoogleCloudApihubV1Oauth2ClientCredentialsConfig');
