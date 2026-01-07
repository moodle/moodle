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

class GoogleCloudAiplatformV1AuthConfigGoogleServiceAccountConfig extends \Google\Model
{
  /**
   * Optional. The service account that the extension execution service runs as.
   * - If the service account is specified, the
   * `iam.serviceAccounts.getAccessToken` permission should be granted to Vertex
   * AI Extension Service Agent (https://cloud.google.com/vertex-
   * ai/docs/general/access-control#service-agents) on the specified service
   * account. - If not specified, the Vertex AI Extension Service Agent will be
   * used to execute the Extension.
   *
   * @var string
   */
  public $serviceAccount;

  /**
   * Optional. The service account that the extension execution service runs as.
   * - If the service account is specified, the
   * `iam.serviceAccounts.getAccessToken` permission should be granted to Vertex
   * AI Extension Service Agent (https://cloud.google.com/vertex-
   * ai/docs/general/access-control#service-agents) on the specified service
   * account. - If not specified, the Vertex AI Extension Service Agent will be
   * used to execute the Extension.
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
class_alias(GoogleCloudAiplatformV1AuthConfigGoogleServiceAccountConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1AuthConfigGoogleServiceAccountConfig');
