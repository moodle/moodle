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

namespace Google\Service\Integrations;

class GoogleCloudIntegrationsV1alphaAuthConfig extends \Google\Collection
{
  /**
   * Unspecified credential type
   */
  public const CREDENTIAL_TYPE_CREDENTIAL_TYPE_UNSPECIFIED = 'CREDENTIAL_TYPE_UNSPECIFIED';
  /**
   * Regular username/password pair.
   */
  public const CREDENTIAL_TYPE_USERNAME_AND_PASSWORD = 'USERNAME_AND_PASSWORD';
  /**
   * API key.
   */
  public const CREDENTIAL_TYPE_API_KEY = 'API_KEY';
  /**
   * OAuth 2.0 Authorization Code Grant Type.
   */
  public const CREDENTIAL_TYPE_OAUTH2_AUTHORIZATION_CODE = 'OAUTH2_AUTHORIZATION_CODE';
  /**
   * OAuth 2.0 Implicit Grant Type.
   */
  public const CREDENTIAL_TYPE_OAUTH2_IMPLICIT = 'OAUTH2_IMPLICIT';
  /**
   * OAuth 2.0 Client Credentials Grant Type.
   */
  public const CREDENTIAL_TYPE_OAUTH2_CLIENT_CREDENTIALS = 'OAUTH2_CLIENT_CREDENTIALS';
  /**
   * OAuth 2.0 Resource Owner Credentials Grant Type.
   */
  public const CREDENTIAL_TYPE_OAUTH2_RESOURCE_OWNER_CREDENTIALS = 'OAUTH2_RESOURCE_OWNER_CREDENTIALS';
  /**
   * JWT Token.
   */
  public const CREDENTIAL_TYPE_JWT = 'JWT';
  /**
   * Auth Token, e.g. bearer token.
   */
  public const CREDENTIAL_TYPE_AUTH_TOKEN = 'AUTH_TOKEN';
  /**
   * Service Account which can be used to generate token for authentication.
   */
  public const CREDENTIAL_TYPE_SERVICE_ACCOUNT = 'SERVICE_ACCOUNT';
  /**
   * Client Certificate only.
   */
  public const CREDENTIAL_TYPE_CLIENT_CERTIFICATE_ONLY = 'CLIENT_CERTIFICATE_ONLY';
  /**
   * Google OIDC ID Token
   */
  public const CREDENTIAL_TYPE_OIDC_TOKEN = 'OIDC_TOKEN';
  /**
   * Status not specified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Valid Auth config.
   */
  public const STATE_VALID = 'VALID';
  /**
   * General invalidity, if it doesn't fits in the detailed issue below.
   */
  public const STATE_INVALID = 'INVALID';
  /**
   * Auth config soft deleted.
   */
  public const STATE_SOFT_DELETED = 'SOFT_DELETED';
  /**
   * Auth config expired.
   */
  public const STATE_EXPIRED = 'EXPIRED';
  /**
   * Auth config unauthorized.
   */
  public const STATE_UNAUTHORIZED = 'UNAUTHORIZED';
  /**
   * Auth config not supported.
   */
  public const STATE_UNSUPPORTED = 'UNSUPPORTED';
  /**
   * Visibility not specified.
   */
  public const VISIBILITY_AUTH_CONFIG_VISIBILITY_UNSPECIFIED = 'AUTH_CONFIG_VISIBILITY_UNSPECIFIED';
  /**
   * Profile visible to the creator only.
   */
  public const VISIBILITY_PRIVATE = 'PRIVATE';
  /**
   * Profile visible within the client.
   */
  public const VISIBILITY_CLIENT_VISIBLE = 'CLIENT_VISIBLE';
  protected $collection_key = 'expiryNotificationDuration';
  /**
   * Certificate id for client certificate
   *
   * @var string
   */
  public $certificateId;
  /**
   * Output only. The timestamp when the auth config is created.
   *
   * @var string
   */
  public $createTime;
  /**
   * The creator's email address. Generated based on the End User
   * Credentials/LOAS role of the user making the call.
   *
   * @var string
   */
  public $creatorEmail;
  /**
   * Required. Credential type of the encrypted credential.
   *
   * @var string
   */
  public $credentialType;
  protected $decryptedCredentialType = GoogleCloudIntegrationsV1alphaCredential::class;
  protected $decryptedCredentialDataType = '';
  /**
   * Optional. A description of the auth config.
   *
   * @var string
   */
  public $description;
  /**
   * Required. The name of the auth config.
   *
   * @var string
   */
  public $displayName;
  /**
   * Auth credential encrypted by Cloud KMS. Can be decrypted as Credential with
   * proper KMS key.
   *
   * @var string
   */
  public $encryptedCredential;
  /**
   * Optional. User can define the time to receive notification after which the
   * auth config becomes invalid. Support up to 30 days. Support granularity in
   * hours.
   *
   * @var string[]
   */
  public $expiryNotificationDuration;
  /**
   * The last modifier's email address. Generated based on the End User
   * Credentials/LOAS role of the user making the call.
   *
   * @var string
   */
  public $lastModifierEmail;
  /**
   * Resource name of the auth config. For more information, see Manage
   * authentication profiles.
   * projects/{project}/locations/{location}/authConfigs/{authConfig}.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. User provided expiry time to override. For the example of
   * Salesforce, username/password credentials can be valid for 6 months
   * depending on the instance settings.
   *
   * @var string
   */
  public $overrideValidTime;
  /**
   * Output only. The reason / details of the current status.
   *
   * @var string
   */
  public $reason;
  /**
   * Output only. The status of the auth config.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The timestamp when the auth config is modified.
   *
   * @var string
   */
  public $updateTime;
  /**
   * Optional. The time until the auth config is valid. Empty or max value is
   * considered the auth config won't expire.
   *
   * @var string
   */
  public $validTime;
  /**
   * Optional. The visibility of the auth config.
   *
   * @var string
   */
  public $visibility;

  /**
   * Certificate id for client certificate
   *
   * @param string $certificateId
   */
  public function setCertificateId($certificateId)
  {
    $this->certificateId = $certificateId;
  }
  /**
   * @return string
   */
  public function getCertificateId()
  {
    return $this->certificateId;
  }
  /**
   * Output only. The timestamp when the auth config is created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * The creator's email address. Generated based on the End User
   * Credentials/LOAS role of the user making the call.
   *
   * @param string $creatorEmail
   */
  public function setCreatorEmail($creatorEmail)
  {
    $this->creatorEmail = $creatorEmail;
  }
  /**
   * @return string
   */
  public function getCreatorEmail()
  {
    return $this->creatorEmail;
  }
  /**
   * Required. Credential type of the encrypted credential.
   *
   * Accepted values: CREDENTIAL_TYPE_UNSPECIFIED, USERNAME_AND_PASSWORD,
   * API_KEY, OAUTH2_AUTHORIZATION_CODE, OAUTH2_IMPLICIT,
   * OAUTH2_CLIENT_CREDENTIALS, OAUTH2_RESOURCE_OWNER_CREDENTIALS, JWT,
   * AUTH_TOKEN, SERVICE_ACCOUNT, CLIENT_CERTIFICATE_ONLY, OIDC_TOKEN
   *
   * @param self::CREDENTIAL_TYPE_* $credentialType
   */
  public function setCredentialType($credentialType)
  {
    $this->credentialType = $credentialType;
  }
  /**
   * @return self::CREDENTIAL_TYPE_*
   */
  public function getCredentialType()
  {
    return $this->credentialType;
  }
  /**
   * Raw auth credentials.
   *
   * @param GoogleCloudIntegrationsV1alphaCredential $decryptedCredential
   */
  public function setDecryptedCredential(GoogleCloudIntegrationsV1alphaCredential $decryptedCredential)
  {
    $this->decryptedCredential = $decryptedCredential;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaCredential
   */
  public function getDecryptedCredential()
  {
    return $this->decryptedCredential;
  }
  /**
   * Optional. A description of the auth config.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Required. The name of the auth config.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Auth credential encrypted by Cloud KMS. Can be decrypted as Credential with
   * proper KMS key.
   *
   * @param string $encryptedCredential
   */
  public function setEncryptedCredential($encryptedCredential)
  {
    $this->encryptedCredential = $encryptedCredential;
  }
  /**
   * @return string
   */
  public function getEncryptedCredential()
  {
    return $this->encryptedCredential;
  }
  /**
   * Optional. User can define the time to receive notification after which the
   * auth config becomes invalid. Support up to 30 days. Support granularity in
   * hours.
   *
   * @param string[] $expiryNotificationDuration
   */
  public function setExpiryNotificationDuration($expiryNotificationDuration)
  {
    $this->expiryNotificationDuration = $expiryNotificationDuration;
  }
  /**
   * @return string[]
   */
  public function getExpiryNotificationDuration()
  {
    return $this->expiryNotificationDuration;
  }
  /**
   * The last modifier's email address. Generated based on the End User
   * Credentials/LOAS role of the user making the call.
   *
   * @param string $lastModifierEmail
   */
  public function setLastModifierEmail($lastModifierEmail)
  {
    $this->lastModifierEmail = $lastModifierEmail;
  }
  /**
   * @return string
   */
  public function getLastModifierEmail()
  {
    return $this->lastModifierEmail;
  }
  /**
   * Resource name of the auth config. For more information, see Manage
   * authentication profiles.
   * projects/{project}/locations/{location}/authConfigs/{authConfig}.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Optional. User provided expiry time to override. For the example of
   * Salesforce, username/password credentials can be valid for 6 months
   * depending on the instance settings.
   *
   * @param string $overrideValidTime
   */
  public function setOverrideValidTime($overrideValidTime)
  {
    $this->overrideValidTime = $overrideValidTime;
  }
  /**
   * @return string
   */
  public function getOverrideValidTime()
  {
    return $this->overrideValidTime;
  }
  /**
   * Output only. The reason / details of the current status.
   *
   * @param string $reason
   */
  public function setReason($reason)
  {
    $this->reason = $reason;
  }
  /**
   * @return string
   */
  public function getReason()
  {
    return $this->reason;
  }
  /**
   * Output only. The status of the auth config.
   *
   * Accepted values: STATE_UNSPECIFIED, VALID, INVALID, SOFT_DELETED, EXPIRED,
   * UNAUTHORIZED, UNSUPPORTED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Output only. The timestamp when the auth config is modified.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
  /**
   * Optional. The time until the auth config is valid. Empty or max value is
   * considered the auth config won't expire.
   *
   * @param string $validTime
   */
  public function setValidTime($validTime)
  {
    $this->validTime = $validTime;
  }
  /**
   * @return string
   */
  public function getValidTime()
  {
    return $this->validTime;
  }
  /**
   * Optional. The visibility of the auth config.
   *
   * Accepted values: AUTH_CONFIG_VISIBILITY_UNSPECIFIED, PRIVATE,
   * CLIENT_VISIBLE
   *
   * @param self::VISIBILITY_* $visibility
   */
  public function setVisibility($visibility)
  {
    $this->visibility = $visibility;
  }
  /**
   * @return self::VISIBILITY_*
   */
  public function getVisibility()
  {
    return $this->visibility;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudIntegrationsV1alphaAuthConfig::class, 'Google_Service_Integrations_GoogleCloudIntegrationsV1alphaAuthConfig');
