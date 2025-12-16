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

namespace Google\Service\CertificateManager;

class ManagedCertificate extends \Google\Collection
{
  /**
   * State is unspecified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Certificate Manager attempts to provision or renew the certificate. If the
   * process takes longer than expected, consult the `provisioning_issue` field.
   */
  public const STATE_PROVISIONING = 'PROVISIONING';
  /**
   * Multiple certificate provisioning attempts failed and Certificate Manager
   * gave up. To try again, delete and create a new managed Certificate
   * resource. For details see the `provisioning_issue` field.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The certificate management is working, and a certificate has been
   * provisioned.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  protected $collection_key = 'domains';
  protected $authorizationAttemptInfoType = AuthorizationAttemptInfo::class;
  protected $authorizationAttemptInfoDataType = 'array';
  /**
   * Optional. Immutable. Authorizations that will be used for performing domain
   * authorization.
   *
   * @var string[]
   */
  public $dnsAuthorizations;
  /**
   * Optional. Immutable. The domains for which a managed SSL certificate will
   * be generated. Wildcard domains are only supported with DNS challenge
   * resolution.
   *
   * @var string[]
   */
  public $domains;
  /**
   * Optional. Immutable. The resource name for a CertificateIssuanceConfig used
   * to configure private PKI certificates in the format
   * `projects/locations/certificateIssuanceConfigs`. If this field is not set,
   * the certificates will instead be publicly signed as documented at
   * https://cloud.google.com/load-balancing/docs/ssl-certificates/google-
   * managed-certs#caa.
   *
   * @var string
   */
  public $issuanceConfig;
  protected $provisioningIssueType = ProvisioningIssue::class;
  protected $provisioningIssueDataType = '';
  /**
   * Output only. State of the managed certificate resource.
   *
   * @var string
   */
  public $state;

  /**
   * Output only. Detailed state of the latest authorization attempt for each
   * domain specified for managed certificate resource.
   *
   * @param AuthorizationAttemptInfo[] $authorizationAttemptInfo
   */
  public function setAuthorizationAttemptInfo($authorizationAttemptInfo)
  {
    $this->authorizationAttemptInfo = $authorizationAttemptInfo;
  }
  /**
   * @return AuthorizationAttemptInfo[]
   */
  public function getAuthorizationAttemptInfo()
  {
    return $this->authorizationAttemptInfo;
  }
  /**
   * Optional. Immutable. Authorizations that will be used for performing domain
   * authorization.
   *
   * @param string[] $dnsAuthorizations
   */
  public function setDnsAuthorizations($dnsAuthorizations)
  {
    $this->dnsAuthorizations = $dnsAuthorizations;
  }
  /**
   * @return string[]
   */
  public function getDnsAuthorizations()
  {
    return $this->dnsAuthorizations;
  }
  /**
   * Optional. Immutable. The domains for which a managed SSL certificate will
   * be generated. Wildcard domains are only supported with DNS challenge
   * resolution.
   *
   * @param string[] $domains
   */
  public function setDomains($domains)
  {
    $this->domains = $domains;
  }
  /**
   * @return string[]
   */
  public function getDomains()
  {
    return $this->domains;
  }
  /**
   * Optional. Immutable. The resource name for a CertificateIssuanceConfig used
   * to configure private PKI certificates in the format
   * `projects/locations/certificateIssuanceConfigs`. If this field is not set,
   * the certificates will instead be publicly signed as documented at
   * https://cloud.google.com/load-balancing/docs/ssl-certificates/google-
   * managed-certs#caa.
   *
   * @param string $issuanceConfig
   */
  public function setIssuanceConfig($issuanceConfig)
  {
    $this->issuanceConfig = $issuanceConfig;
  }
  /**
   * @return string
   */
  public function getIssuanceConfig()
  {
    return $this->issuanceConfig;
  }
  /**
   * Output only. Information about issues with provisioning a Managed
   * Certificate.
   *
   * @param ProvisioningIssue $provisioningIssue
   */
  public function setProvisioningIssue(ProvisioningIssue $provisioningIssue)
  {
    $this->provisioningIssue = $provisioningIssue;
  }
  /**
   * @return ProvisioningIssue
   */
  public function getProvisioningIssue()
  {
    return $this->provisioningIssue;
  }
  /**
   * Output only. State of the managed certificate resource.
   *
   * Accepted values: STATE_UNSPECIFIED, PROVISIONING, FAILED, ACTIVE
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ManagedCertificate::class, 'Google_Service_CertificateManager_ManagedCertificate');
