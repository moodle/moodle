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

namespace Google\Service\GKEHub;

class IdentityServiceAzureADConfig extends \Google\Model
{
  /**
   * ID for the registered client application that makes authentication requests
   * to the Azure AD identity provider.
   *
   * @var string
   */
  public $clientId;
  /**
   * Input only. Unencrypted AzureAD client secret will be passed to the GKE Hub
   * CLH.
   *
   * @var string
   */
  public $clientSecret;
  /**
   * Output only. Encrypted AzureAD client secret.
   *
   * @var string
   */
  public $encryptedClientSecret;
  /**
   * Optional. Format of the AzureAD groups that the client wants for auth.
   *
   * @var string
   */
  public $groupFormat;
  /**
   * The redirect URL that kubectl uses for authorization.
   *
   * @var string
   */
  public $kubectlRedirectUri;
  /**
   * Kind of Azure AD account to be authenticated. Supported values are or for
   * accounts belonging to a specific tenant.
   *
   * @var string
   */
  public $tenant;
  /**
   * Optional. Claim in the AzureAD ID Token that holds the user details.
   *
   * @var string
   */
  public $userClaim;

  /**
   * ID for the registered client application that makes authentication requests
   * to the Azure AD identity provider.
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
   * Input only. Unencrypted AzureAD client secret will be passed to the GKE Hub
   * CLH.
   *
   * @param string $clientSecret
   */
  public function setClientSecret($clientSecret)
  {
    $this->clientSecret = $clientSecret;
  }
  /**
   * @return string
   */
  public function getClientSecret()
  {
    return $this->clientSecret;
  }
  /**
   * Output only. Encrypted AzureAD client secret.
   *
   * @param string $encryptedClientSecret
   */
  public function setEncryptedClientSecret($encryptedClientSecret)
  {
    $this->encryptedClientSecret = $encryptedClientSecret;
  }
  /**
   * @return string
   */
  public function getEncryptedClientSecret()
  {
    return $this->encryptedClientSecret;
  }
  /**
   * Optional. Format of the AzureAD groups that the client wants for auth.
   *
   * @param string $groupFormat
   */
  public function setGroupFormat($groupFormat)
  {
    $this->groupFormat = $groupFormat;
  }
  /**
   * @return string
   */
  public function getGroupFormat()
  {
    return $this->groupFormat;
  }
  /**
   * The redirect URL that kubectl uses for authorization.
   *
   * @param string $kubectlRedirectUri
   */
  public function setKubectlRedirectUri($kubectlRedirectUri)
  {
    $this->kubectlRedirectUri = $kubectlRedirectUri;
  }
  /**
   * @return string
   */
  public function getKubectlRedirectUri()
  {
    return $this->kubectlRedirectUri;
  }
  /**
   * Kind of Azure AD account to be authenticated. Supported values are or for
   * accounts belonging to a specific tenant.
   *
   * @param string $tenant
   */
  public function setTenant($tenant)
  {
    $this->tenant = $tenant;
  }
  /**
   * @return string
   */
  public function getTenant()
  {
    return $this->tenant;
  }
  /**
   * Optional. Claim in the AzureAD ID Token that holds the user details.
   *
   * @param string $userClaim
   */
  public function setUserClaim($userClaim)
  {
    $this->userClaim = $userClaim;
  }
  /**
   * @return string
   */
  public function getUserClaim()
  {
    return $this->userClaim;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IdentityServiceAzureADConfig::class, 'Google_Service_GKEHub_IdentityServiceAzureADConfig');
