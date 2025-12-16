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

namespace Google\Service\IdentityToolkit;

class IdentitytoolkitRelyingpartyGetProjectConfigResponse extends \Google\Collection
{
  protected $collection_key = 'idpConfig';
  /**
   * Whether to allow password user sign in or sign up.
   *
   * @var bool
   */
  public $allowPasswordUser;
  /**
   * Browser API key, needed when making http request to Apiary.
   *
   * @var string
   */
  public $apiKey;
  /**
   * Authorized domains.
   *
   * @var string[]
   */
  public $authorizedDomains;
  protected $changeEmailTemplateType = EmailTemplate::class;
  protected $changeEmailTemplateDataType = '';
  /**
   * @var string
   */
  public $dynamicLinksDomain;
  /**
   * Whether anonymous user is enabled.
   *
   * @var bool
   */
  public $enableAnonymousUser;
  protected $idpConfigType = IdpConfig::class;
  protected $idpConfigDataType = 'array';
  protected $legacyResetPasswordTemplateType = EmailTemplate::class;
  protected $legacyResetPasswordTemplateDataType = '';
  /**
   * Project ID of the relying party.
   *
   * @var string
   */
  public $projectId;
  protected $resetPasswordTemplateType = EmailTemplate::class;
  protected $resetPasswordTemplateDataType = '';
  /**
   * Whether to use email sending provided by Firebear.
   *
   * @var bool
   */
  public $useEmailSending;
  protected $verifyEmailTemplateType = EmailTemplate::class;
  protected $verifyEmailTemplateDataType = '';

  /**
   * Whether to allow password user sign in or sign up.
   *
   * @param bool $allowPasswordUser
   */
  public function setAllowPasswordUser($allowPasswordUser)
  {
    $this->allowPasswordUser = $allowPasswordUser;
  }
  /**
   * @return bool
   */
  public function getAllowPasswordUser()
  {
    return $this->allowPasswordUser;
  }
  /**
   * Browser API key, needed when making http request to Apiary.
   *
   * @param string $apiKey
   */
  public function setApiKey($apiKey)
  {
    $this->apiKey = $apiKey;
  }
  /**
   * @return string
   */
  public function getApiKey()
  {
    return $this->apiKey;
  }
  /**
   * Authorized domains.
   *
   * @param string[] $authorizedDomains
   */
  public function setAuthorizedDomains($authorizedDomains)
  {
    $this->authorizedDomains = $authorizedDomains;
  }
  /**
   * @return string[]
   */
  public function getAuthorizedDomains()
  {
    return $this->authorizedDomains;
  }
  /**
   * Change email template.
   *
   * @param EmailTemplate $changeEmailTemplate
   */
  public function setChangeEmailTemplate(EmailTemplate $changeEmailTemplate)
  {
    $this->changeEmailTemplate = $changeEmailTemplate;
  }
  /**
   * @return EmailTemplate
   */
  public function getChangeEmailTemplate()
  {
    return $this->changeEmailTemplate;
  }
  /**
   * @param string $dynamicLinksDomain
   */
  public function setDynamicLinksDomain($dynamicLinksDomain)
  {
    $this->dynamicLinksDomain = $dynamicLinksDomain;
  }
  /**
   * @return string
   */
  public function getDynamicLinksDomain()
  {
    return $this->dynamicLinksDomain;
  }
  /**
   * Whether anonymous user is enabled.
   *
   * @param bool $enableAnonymousUser
   */
  public function setEnableAnonymousUser($enableAnonymousUser)
  {
    $this->enableAnonymousUser = $enableAnonymousUser;
  }
  /**
   * @return bool
   */
  public function getEnableAnonymousUser()
  {
    return $this->enableAnonymousUser;
  }
  /**
   * OAuth2 provider configuration.
   *
   * @param IdpConfig[] $idpConfig
   */
  public function setIdpConfig($idpConfig)
  {
    $this->idpConfig = $idpConfig;
  }
  /**
   * @return IdpConfig[]
   */
  public function getIdpConfig()
  {
    return $this->idpConfig;
  }
  /**
   * Legacy reset password email template.
   *
   * @param EmailTemplate $legacyResetPasswordTemplate
   */
  public function setLegacyResetPasswordTemplate(EmailTemplate $legacyResetPasswordTemplate)
  {
    $this->legacyResetPasswordTemplate = $legacyResetPasswordTemplate;
  }
  /**
   * @return EmailTemplate
   */
  public function getLegacyResetPasswordTemplate()
  {
    return $this->legacyResetPasswordTemplate;
  }
  /**
   * Project ID of the relying party.
   *
   * @param string $projectId
   */
  public function setProjectId($projectId)
  {
    $this->projectId = $projectId;
  }
  /**
   * @return string
   */
  public function getProjectId()
  {
    return $this->projectId;
  }
  /**
   * Reset password email template.
   *
   * @param EmailTemplate $resetPasswordTemplate
   */
  public function setResetPasswordTemplate(EmailTemplate $resetPasswordTemplate)
  {
    $this->resetPasswordTemplate = $resetPasswordTemplate;
  }
  /**
   * @return EmailTemplate
   */
  public function getResetPasswordTemplate()
  {
    return $this->resetPasswordTemplate;
  }
  /**
   * Whether to use email sending provided by Firebear.
   *
   * @param bool $useEmailSending
   */
  public function setUseEmailSending($useEmailSending)
  {
    $this->useEmailSending = $useEmailSending;
  }
  /**
   * @return bool
   */
  public function getUseEmailSending()
  {
    return $this->useEmailSending;
  }
  /**
   * Verify email template.
   *
   * @param EmailTemplate $verifyEmailTemplate
   */
  public function setVerifyEmailTemplate(EmailTemplate $verifyEmailTemplate)
  {
    $this->verifyEmailTemplate = $verifyEmailTemplate;
  }
  /**
   * @return EmailTemplate
   */
  public function getVerifyEmailTemplate()
  {
    return $this->verifyEmailTemplate;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IdentitytoolkitRelyingpartyGetProjectConfigResponse::class, 'Google_Service_IdentityToolkit_IdentitytoolkitRelyingpartyGetProjectConfigResponse');
