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

class GoogleCloudAiplatformV1AuthConfigOidcConfig extends \Google\Model
{
  /**
   * OpenID Connect formatted ID token for extension endpoint. Only used to
   * propagate token from [[ExecuteExtensionRequest.runtime_auth_config]] at
   * request time.
   *
   * @var string
   */
  public $idToken;
  /**
   * The service account used to generate an OpenID Connect (OIDC)-compatible
   * JWT token signed by the Google OIDC Provider (accounts.google.com) for
   * extension endpoint (https://cloud.google.com/iam/docs/create-short-lived-
   * credentials-direct#sa-credentials-oidc). - The audience for the token will
   * be set to the URL in the server url defined in the OpenApi spec. - If the
   * service account is provided, the service account should grant
   * `iam.serviceAccounts.getOpenIdToken` permission to Vertex AI Extension
   * Service Agent (https://cloud.google.com/vertex-ai/docs/general/access-
   * control#service-agents).
   *
   * @var string
   */
  public $serviceAccount;

  /**
   * OpenID Connect formatted ID token for extension endpoint. Only used to
   * propagate token from [[ExecuteExtensionRequest.runtime_auth_config]] at
   * request time.
   *
   * @param string $idToken
   */
  public function setIdToken($idToken)
  {
    $this->idToken = $idToken;
  }
  /**
   * @return string
   */
  public function getIdToken()
  {
    return $this->idToken;
  }
  /**
   * The service account used to generate an OpenID Connect (OIDC)-compatible
   * JWT token signed by the Google OIDC Provider (accounts.google.com) for
   * extension endpoint (https://cloud.google.com/iam/docs/create-short-lived-
   * credentials-direct#sa-credentials-oidc). - The audience for the token will
   * be set to the URL in the server url defined in the OpenApi spec. - If the
   * service account is provided, the service account should grant
   * `iam.serviceAccounts.getOpenIdToken` permission to Vertex AI Extension
   * Service Agent (https://cloud.google.com/vertex-ai/docs/general/access-
   * control#service-agents).
   *
   * @param string $serviceAccount
   */
  public function setServiceAccount($serviceAccount)
  {
    $this->serviceAccount = $serviceAccount;
  }
  /**
   * @return string
   */
  public function getServiceAccount()
  {
    return $this->serviceAccount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1AuthConfigOidcConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1AuthConfigOidcConfig');
