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

class GoogleCloudDialogflowCxV3ToolAuthenticationServiceAgentAuthConfig extends \Google\Model
{
  /**
   * Service agent auth type unspecified. Default to ID_TOKEN.
   */
  public const SERVICE_AGENT_AUTH_SERVICE_AGENT_AUTH_UNSPECIFIED = 'SERVICE_AGENT_AUTH_UNSPECIFIED';
  /**
   * Use [ID token](https://cloud.google.com/docs/authentication/token-types#id)
   * generated from service agent. This can be used to access Cloud Function and
   * Cloud Run after you grant Invoker role to `service-@gcp-sa-
   * dialogflow.iam.gserviceaccount.com`.
   */
  public const SERVICE_AGENT_AUTH_ID_TOKEN = 'ID_TOKEN';
  /**
   * Use [access token](https://cloud.google.com/docs/authentication/token-
   * types#access) generated from service agent. This can be used to access
   * other Google Cloud APIs after you grant required roles to `service-@gcp-sa-
   * dialogflow.iam.gserviceaccount.com`.
   */
  public const SERVICE_AGENT_AUTH_ACCESS_TOKEN = 'ACCESS_TOKEN';
  /**
   * Optional. Indicate the auth token type generated from the [Diglogflow
   * service agent](https://cloud.google.com/iam/docs/service-agents#dialogflow-
   * service-agent). The generated token is sent in the Authorization header.
   *
   * @var string
   */
  public $serviceAgentAuth;

  /**
   * Optional. Indicate the auth token type generated from the [Diglogflow
   * service agent](https://cloud.google.com/iam/docs/service-agents#dialogflow-
   * service-agent). The generated token is sent in the Authorization header.
   *
   * Accepted values: SERVICE_AGENT_AUTH_UNSPECIFIED, ID_TOKEN, ACCESS_TOKEN
   *
   * @param self::SERVICE_AGENT_AUTH_* $serviceAgentAuth
   */
  public function setServiceAgentAuth($serviceAgentAuth)
  {
    $this->serviceAgentAuth = $serviceAgentAuth;
  }
  /**
   * @return self::SERVICE_AGENT_AUTH_*
   */
  public function getServiceAgentAuth()
  {
    return $this->serviceAgentAuth;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3ToolAuthenticationServiceAgentAuthConfig::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3ToolAuthenticationServiceAgentAuthConfig');
