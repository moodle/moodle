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

class GoogleCloudDialogflowCxV3ToolAuthenticationServiceAccountAuthConfig extends \Google\Model
{
  /**
   * Required. The email address of the service account used to authenticate the
   * tool call. Dialogflow uses this service account to exchange an access token
   * and the access token is then sent in the `Authorization` header of the tool
   * request. The service account must have the
   * `roles/iam.serviceAccountTokenCreator` role granted to the [Dialogflow
   * service agent](https://cloud.google.com/iam/docs/service-agents#dialogflow-
   * service-agent).
   *
   * @var string
   */
  public $serviceAccount;

  /**
   * Required. The email address of the service account used to authenticate the
   * tool call. Dialogflow uses this service account to exchange an access token
   * and the access token is then sent in the `Authorization` header of the tool
   * request. The service account must have the
   * `roles/iam.serviceAccountTokenCreator` role granted to the [Dialogflow
   * service agent](https://cloud.google.com/iam/docs/service-agents#dialogflow-
   * service-agent).
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
class_alias(GoogleCloudDialogflowCxV3ToolAuthenticationServiceAccountAuthConfig::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3ToolAuthenticationServiceAccountAuthConfig');
