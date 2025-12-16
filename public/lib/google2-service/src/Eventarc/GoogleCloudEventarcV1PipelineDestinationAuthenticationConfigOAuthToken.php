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

namespace Google\Service\Eventarc;

class GoogleCloudEventarcV1PipelineDestinationAuthenticationConfigOAuthToken extends \Google\Model
{
  /**
   * Optional. OAuth scope to be used for generating OAuth access token. If not
   * specified, "https://www.googleapis.com/auth/cloud-platform" will be used.
   *
   * @var string
   */
  public $scope;
  /**
   * Required. Service account email used to generate the [OAuth
   * token](https://developers.google.com/identity/protocols/OAuth2). The
   * principal who calls this API must have iam.serviceAccounts.actAs permission
   * in the service account. See
   * https://cloud.google.com/iam/docs/understanding-service-accounts for more
   * information. Eventarc service agents must have
   * roles/roles/iam.serviceAccountTokenCreator role to allow Pipeline to create
   * OAuth2 tokens for authenticated requests.
   *
   * @var string
   */
  public $serviceAccount;

  /**
   * Optional. OAuth scope to be used for generating OAuth access token. If not
   * specified, "https://www.googleapis.com/auth/cloud-platform" will be used.
   *
   * @param string $scope
   */
  public function setScope($scope)
  {
    $this->scope = $scope;
  }
  /**
   * @return string
   */
  public function getScope()
  {
    return $this->scope;
  }
  /**
   * Required. Service account email used to generate the [OAuth
   * token](https://developers.google.com/identity/protocols/OAuth2). The
   * principal who calls this API must have iam.serviceAccounts.actAs permission
   * in the service account. See
   * https://cloud.google.com/iam/docs/understanding-service-accounts for more
   * information. Eventarc service agents must have
   * roles/roles/iam.serviceAccountTokenCreator role to allow Pipeline to create
   * OAuth2 tokens for authenticated requests.
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
class_alias(GoogleCloudEventarcV1PipelineDestinationAuthenticationConfigOAuthToken::class, 'Google_Service_Eventarc_GoogleCloudEventarcV1PipelineDestinationAuthenticationConfigOAuthToken');
