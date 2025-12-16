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

class IdentityServiceOidcConfig extends \Google\Model
{
  /**
   * PEM-encoded CA for OIDC provider.
   *
   * @var string
   */
  public $certificateAuthorityData;
  /**
   * ID for OIDC client application.
   *
   * @var string
   */
  public $clientId;
  /**
   * Input only. Unencrypted OIDC client secret will be passed to the GKE Hub
   * CLH.
   *
   * @var string
   */
  public $clientSecret;
  /**
   * Flag to denote if reverse proxy is used to connect to auth provider. This
   * flag should be set to true when provider is not reachable by Google Cloud
   * Console.
   *
   * @var bool
   */
  public $deployCloudConsoleProxy;
  /**
   * Enable access token.
   *
   * @var bool
   */
  public $enableAccessToken;
  /**
   * Output only. Encrypted OIDC Client secret
   *
   * @var string
   */
  public $encryptedClientSecret;
  /**
   * Comma-separated list of key-value pairs.
   *
   * @var string
   */
  public $extraParams;
  /**
   * Prefix to prepend to group name.
   *
   * @var string
   */
  public $groupPrefix;
  /**
   * Claim in OIDC ID token that holds group information.
   *
   * @var string
   */
  public $groupsClaim;
  /**
   * URI for the OIDC provider. This should point to the level below .well-
   * known/openid-configuration.
   *
   * @var string
   */
  public $issuerUri;
  /**
   * Registered redirect uri to redirect users going through OAuth flow using
   * kubectl plugin.
   *
   * @var string
   */
  public $kubectlRedirectUri;
  /**
   * Comma-separated list of identifiers.
   *
   * @var string
   */
  public $scopes;
  /**
   * Claim in OIDC ID token that holds username.
   *
   * @var string
   */
  public $userClaim;
  /**
   * Prefix to prepend to user name.
   *
   * @var string
   */
  public $userPrefix;

  /**
   * PEM-encoded CA for OIDC provider.
   *
   * @param string $certificateAuthorityData
   */
  public function setCertificateAuthorityData($certificateAuthorityData)
  {
    $this->certificateAuthorityData = $certificateAuthorityData;
  }
  /**
   * @return string
   */
  public function getCertificateAuthorityData()
  {
    return $this->certificateAuthorityData;
  }
  /**
   * ID for OIDC client application.
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
   * Input only. Unencrypted OIDC client secret will be passed to the GKE Hub
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
   * Flag to denote if reverse proxy is used to connect to auth provider. This
   * flag should be set to true when provider is not reachable by Google Cloud
   * Console.
   *
   * @param bool $deployCloudConsoleProxy
   */
  public function setDeployCloudConsoleProxy($deployCloudConsoleProxy)
  {
    $this->deployCloudConsoleProxy = $deployCloudConsoleProxy;
  }
  /**
   * @return bool
   */
  public function getDeployCloudConsoleProxy()
  {
    return $this->deployCloudConsoleProxy;
  }
  /**
   * Enable access token.
   *
   * @param bool $enableAccessToken
   */
  public function setEnableAccessToken($enableAccessToken)
  {
    $this->enableAccessToken = $enableAccessToken;
  }
  /**
   * @return bool
   */
  public function getEnableAccessToken()
  {
    return $this->enableAccessToken;
  }
  /**
   * Output only. Encrypted OIDC Client secret
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
   * Comma-separated list of key-value pairs.
   *
   * @param string $extraParams
   */
  public function setExtraParams($extraParams)
  {
    $this->extraParams = $extraParams;
  }
  /**
   * @return string
   */
  public function getExtraParams()
  {
    return $this->extraParams;
  }
  /**
   * Prefix to prepend to group name.
   *
   * @param string $groupPrefix
   */
  public function setGroupPrefix($groupPrefix)
  {
    $this->groupPrefix = $groupPrefix;
  }
  /**
   * @return string
   */
  public function getGroupPrefix()
  {
    return $this->groupPrefix;
  }
  /**
   * Claim in OIDC ID token that holds group information.
   *
   * @param string $groupsClaim
   */
  public function setGroupsClaim($groupsClaim)
  {
    $this->groupsClaim = $groupsClaim;
  }
  /**
   * @return string
   */
  public function getGroupsClaim()
  {
    return $this->groupsClaim;
  }
  /**
   * URI for the OIDC provider. This should point to the level below .well-
   * known/openid-configuration.
   *
   * @param string $issuerUri
   */
  public function setIssuerUri($issuerUri)
  {
    $this->issuerUri = $issuerUri;
  }
  /**
   * @return string
   */
  public function getIssuerUri()
  {
    return $this->issuerUri;
  }
  /**
   * Registered redirect uri to redirect users going through OAuth flow using
   * kubectl plugin.
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
   * Comma-separated list of identifiers.
   *
   * @param string $scopes
   */
  public function setScopes($scopes)
  {
    $this->scopes = $scopes;
  }
  /**
   * @return string
   */
  public function getScopes()
  {
    return $this->scopes;
  }
  /**
   * Claim in OIDC ID token that holds username.
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
  /**
   * Prefix to prepend to user name.
   *
   * @param string $userPrefix
   */
  public function setUserPrefix($userPrefix)
  {
    $this->userPrefix = $userPrefix;
  }
  /**
   * @return string
   */
  public function getUserPrefix()
  {
    return $this->userPrefix;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IdentityServiceOidcConfig::class, 'Google_Service_GKEHub_IdentityServiceOidcConfig');
