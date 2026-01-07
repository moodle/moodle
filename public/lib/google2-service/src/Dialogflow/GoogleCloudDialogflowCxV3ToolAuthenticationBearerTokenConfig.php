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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowCxV3ToolAuthenticationBearerTokenConfig extends \Google\Model
{
  /**
   * Optional. The name of the SecretManager secret version resource storing the
   * Bearer token. If this field is set, the `token` field will be ignored.
   * Format: `projects/{project}/secrets/{secret}/versions/{version}`
   *
   * @var string
   */
  public $secretVersionForToken;
  /**
   * Optional. The text token appended to the text `Bearer` to the request
   * Authorization header. [Session parameters reference](https://cloud.google.c
   * om/dialogflow/cx/docs/concept/parameter#session-ref) can be used to pass
   * the token dynamically, e.g. `$session.params.parameter-id`.
   *
   * @var string
   */
  public $token;

  /**
   * Optional. The name of the SecretManager secret version resource storing the
   * Bearer token. If this field is set, the `token` field will be ignored.
   * Format: `projects/{project}/secrets/{secret}/versions/{version}`
   *
   * @param string $secretVersionForToken
   */
  public function setSecretVersionForToken($secretVersionForToken)
  {
    $this->secretVersionForToken = $secretVersionForToken;
  }
  /**
   * @return string
   */
  public function getSecretVersionForToken()
  {
    return $this->secretVersionForToken;
  }
  /**
   * Optional. The text token appended to the text `Bearer` to the request
   * Authorization header. [Session parameters reference](https://cloud.google.c
   * om/dialogflow/cx/docs/concept/parameter#session-ref) can be used to pass
   * the token dynamically, e.g. `$session.params.parameter-id`.
   *
   * @param string $token
   */
  public function setToken($token)
  {
    $this->token = $token;
  }
  /**
   * @return string
   */
  public function getToken()
  {
    return $this->token;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3ToolAuthenticationBearerTokenConfig::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3ToolAuthenticationBearerTokenConfig');
