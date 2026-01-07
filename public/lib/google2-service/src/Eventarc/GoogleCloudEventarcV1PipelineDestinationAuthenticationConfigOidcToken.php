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

class GoogleCloudEventarcV1PipelineDestinationAuthenticationConfigOidcToken extends \Google\Model
{
  /**
   * Optional. Audience to be used to generate the OIDC Token. The audience
   * claim identifies the recipient that the JWT is intended for. If
   * unspecified, the destination URI will be used.
   *
   * @var string
   */
  public $audience;
  /**
   * Required. Service account email used to generate the OIDC Token. The
   * principal who calls this API must have iam.serviceAccounts.actAs permission
   * in the service account. See
   * https://cloud.google.com/iam/docs/understanding-service-accounts for more
   * information. Eventarc service agents must have
   * roles/roles/iam.serviceAccountTokenCreator role to allow the Pipeline to
   * create OpenID tokens for authenticated requests.
   *
   * @var string
   */
  public $serviceAccount;

  /**
   * Optional. Audience to be used to generate the OIDC Token. The audience
   * claim identifies the recipient that the JWT is intended for. If
   * unspecified, the destination URI will be used.
   *
   * @param string $audience
   */
  public function setAudience($audience)
  {
    $this->audience = $audience;
  }
  /**
   * @return string
   */
  public function getAudience()
  {
    return $this->audience;
  }
  /**
   * Required. Service account email used to generate the OIDC Token. The
   * principal who calls this API must have iam.serviceAccounts.actAs permission
   * in the service account. See
   * https://cloud.google.com/iam/docs/understanding-service-accounts for more
   * information. Eventarc service agents must have
   * roles/roles/iam.serviceAccountTokenCreator role to allow the Pipeline to
   * create OpenID tokens for authenticated requests.
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
class_alias(GoogleCloudEventarcV1PipelineDestinationAuthenticationConfigOidcToken::class, 'Google_Service_Eventarc_GoogleCloudEventarcV1PipelineDestinationAuthenticationConfigOidcToken');
