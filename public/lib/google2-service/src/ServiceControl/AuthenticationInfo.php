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

namespace Google\Service\ServiceControl;

class AuthenticationInfo extends \Google\Collection
{
  protected $collection_key = 'serviceAccountDelegationInfo';
  /**
   * The authority selector specified by the requestor, if any. It is not
   * guaranteed that the principal was allowed to use this authority.
   *
   * @var string
   */
  public $authoritySelector;
  /**
   * Converted from "identity_cloudgaia.AuditLoggableShortLivedCredential"
   * proto. This message will be used by security, detection and response team.
   * For context please refer to go/cg:short-lived-credential-logging. When the
   * JSON object represented here has a proto equivalent, the proto name will be
   * indicated in the `@type` property.
   *
   * @var array[]
   */
  public $loggableShortLivedCredential;
  protected $oauthInfoType = OAuthInfo::class;
  protected $oauthInfoDataType = '';
  /**
   * The email address of the authenticated user (or service account on behalf
   * of third party principal) making the request. For third party identity
   * callers, the `principal_subject` field is populated instead of this field.
   * For privacy reasons, the principal email address is sometimes redacted. For
   * more information, see [Caller identities in audit
   * logs](https://cloud.google.com/logging/docs/audit#user-id).
   *
   * @var string
   */
  public $principalEmail;
  /**
   * String representation of identity of requesting party. Populated for both
   * first and third party identities.
   *
   * @var string
   */
  public $principalSubject;
  protected $serviceAccountDelegationInfoType = ServiceAccountDelegationInfo::class;
  protected $serviceAccountDelegationInfoDataType = 'array';
  /**
   * The name of the service account key used to create or exchange credentials
   * for authenticating the service account making the request. This is a
   * scheme-less URI full resource name. For example: "//iam.googleapis.com/proj
   * ects/{PROJECT_ID}/serviceAccounts/{ACCOUNT}/keys/{key}"
   *
   * @var string
   */
  public $serviceAccountKeyName;
  protected $serviceDelegationHistoryType = ServiceDelegationHistory::class;
  protected $serviceDelegationHistoryDataType = '';
  /**
   * The third party identification (if any) of the authenticated user making
   * the request. When the JSON object represented here has a proto equivalent,
   * the proto name will be indicated in the `@type` property.
   *
   * @var array[]
   */
  public $thirdPartyPrincipal;

  /**
   * The authority selector specified by the requestor, if any. It is not
   * guaranteed that the principal was allowed to use this authority.
   *
   * @param string $authoritySelector
   */
  public function setAuthoritySelector($authoritySelector)
  {
    $this->authoritySelector = $authoritySelector;
  }
  /**
   * @return string
   */
  public function getAuthoritySelector()
  {
    return $this->authoritySelector;
  }
  /**
   * Converted from "identity_cloudgaia.AuditLoggableShortLivedCredential"
   * proto. This message will be used by security, detection and response team.
   * For context please refer to go/cg:short-lived-credential-logging. When the
   * JSON object represented here has a proto equivalent, the proto name will be
   * indicated in the `@type` property.
   *
   * @param array[] $loggableShortLivedCredential
   */
  public function setLoggableShortLivedCredential($loggableShortLivedCredential)
  {
    $this->loggableShortLivedCredential = $loggableShortLivedCredential;
  }
  /**
   * @return array[]
   */
  public function getLoggableShortLivedCredential()
  {
    return $this->loggableShortLivedCredential;
  }
  /**
   * OAuth authentication information such as the OAuth client ID.
   *
   * @param OAuthInfo $oauthInfo
   */
  public function setOauthInfo(OAuthInfo $oauthInfo)
  {
    $this->oauthInfo = $oauthInfo;
  }
  /**
   * @return OAuthInfo
   */
  public function getOauthInfo()
  {
    return $this->oauthInfo;
  }
  /**
   * The email address of the authenticated user (or service account on behalf
   * of third party principal) making the request. For third party identity
   * callers, the `principal_subject` field is populated instead of this field.
   * For privacy reasons, the principal email address is sometimes redacted. For
   * more information, see [Caller identities in audit
   * logs](https://cloud.google.com/logging/docs/audit#user-id).
   *
   * @param string $principalEmail
   */
  public function setPrincipalEmail($principalEmail)
  {
    $this->principalEmail = $principalEmail;
  }
  /**
   * @return string
   */
  public function getPrincipalEmail()
  {
    return $this->principalEmail;
  }
  /**
   * String representation of identity of requesting party. Populated for both
   * first and third party identities.
   *
   * @param string $principalSubject
   */
  public function setPrincipalSubject($principalSubject)
  {
    $this->principalSubject = $principalSubject;
  }
  /**
   * @return string
   */
  public function getPrincipalSubject()
  {
    return $this->principalSubject;
  }
  /**
   * Identity delegation history of an authenticated service account that makes
   * the request. It contains information on the real authorities that try to
   * access GCP resources by delegating on a service account. When multiple
   * authorities present, they are guaranteed to be sorted based on the original
   * ordering of the identity delegation events.
   *
   * @param ServiceAccountDelegationInfo[] $serviceAccountDelegationInfo
   */
  public function setServiceAccountDelegationInfo($serviceAccountDelegationInfo)
  {
    $this->serviceAccountDelegationInfo = $serviceAccountDelegationInfo;
  }
  /**
   * @return ServiceAccountDelegationInfo[]
   */
  public function getServiceAccountDelegationInfo()
  {
    return $this->serviceAccountDelegationInfo;
  }
  /**
   * The name of the service account key used to create or exchange credentials
   * for authenticating the service account making the request. This is a
   * scheme-less URI full resource name. For example: "//iam.googleapis.com/proj
   * ects/{PROJECT_ID}/serviceAccounts/{ACCOUNT}/keys/{key}"
   *
   * @param string $serviceAccountKeyName
   */
  public function setServiceAccountKeyName($serviceAccountKeyName)
  {
    $this->serviceAccountKeyName = $serviceAccountKeyName;
  }
  /**
   * @return string
   */
  public function getServiceAccountKeyName()
  {
    return $this->serviceAccountKeyName;
  }
  /**
   * Records the history of delegated resource access across Google services.
   *
   * @param ServiceDelegationHistory $serviceDelegationHistory
   */
  public function setServiceDelegationHistory(ServiceDelegationHistory $serviceDelegationHistory)
  {
    $this->serviceDelegationHistory = $serviceDelegationHistory;
  }
  /**
   * @return ServiceDelegationHistory
   */
  public function getServiceDelegationHistory()
  {
    return $this->serviceDelegationHistory;
  }
  /**
   * The third party identification (if any) of the authenticated user making
   * the request. When the JSON object represented here has a proto equivalent,
   * the proto name will be indicated in the `@type` property.
   *
   * @param array[] $thirdPartyPrincipal
   */
  public function setThirdPartyPrincipal($thirdPartyPrincipal)
  {
    $this->thirdPartyPrincipal = $thirdPartyPrincipal;
  }
  /**
   * @return array[]
   */
  public function getThirdPartyPrincipal()
  {
    return $this->thirdPartyPrincipal;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AuthenticationInfo::class, 'Google_Service_ServiceControl_AuthenticationInfo');
