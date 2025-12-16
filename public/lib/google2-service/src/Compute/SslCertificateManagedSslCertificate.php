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

namespace Google\Service\Compute;

class SslCertificateManagedSslCertificate extends \Google\Collection
{
  /**
   * The certificate management is working, and a certificate has been
   * provisioned.
   */
  public const STATUS_ACTIVE = 'ACTIVE';
  public const STATUS_MANAGED_CERTIFICATE_STATUS_UNSPECIFIED = 'MANAGED_CERTIFICATE_STATUS_UNSPECIFIED';
  /**
   * The certificate management is working. GCP will attempt to provision the
   * first certificate.
   */
  public const STATUS_PROVISIONING = 'PROVISIONING';
  /**
   * Certificate provisioning failed due to an issue with the DNS or load
   * balancing configuration. For details of which domain failed, consult
   * domain_status field.
   */
  public const STATUS_PROVISIONING_FAILED = 'PROVISIONING_FAILED';
  /**
   * Certificate provisioning failed due to an issue with the DNS or load
   * balancing configuration. It won't be retried. To try again delete and
   * create a new managed SslCertificate resource. For details of which domain
   * failed, consult domain_status field.
   */
  public const STATUS_PROVISIONING_FAILED_PERMANENTLY = 'PROVISIONING_FAILED_PERMANENTLY';
  /**
   * Renewal of the certificate has failed due to an issue with the DNS or load
   * balancing configuration. The existing cert is still serving; however, it
   * will expire shortly. To provision a renewed certificate, delete and create
   * a new managed SslCertificate resource. For details on which domain failed,
   * consult domain_status field.
   */
  public const STATUS_RENEWAL_FAILED = 'RENEWAL_FAILED';
  protected $collection_key = 'domains';
  /**
   * Output only. [Output only] Detailed statuses of the domains specified for
   * managed certificate resource.
   *
   * @var string[]
   */
  public $domainStatus;
  /**
   * The domains for which a managed SSL certificate will be generated. Each
   * Google-managed SSL certificate supports up to the [maximum number of
   * domains per Google-managed SSL certificate](/load-
   * balancing/docs/quotas#ssl_certificates).
   *
   * @var string[]
   */
  public $domains;
  /**
   * Output only. [Output only] Status of the managed certificate resource.
   *
   * @var string
   */
  public $status;

  /**
   * Output only. [Output only] Detailed statuses of the domains specified for
   * managed certificate resource.
   *
   * @param string[] $domainStatus
   */
  public function setDomainStatus($domainStatus)
  {
    $this->domainStatus = $domainStatus;
  }
  /**
   * @return string[]
   */
  public function getDomainStatus()
  {
    return $this->domainStatus;
  }
  /**
   * The domains for which a managed SSL certificate will be generated. Each
   * Google-managed SSL certificate supports up to the [maximum number of
   * domains per Google-managed SSL certificate](/load-
   * balancing/docs/quotas#ssl_certificates).
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
   * Output only. [Output only] Status of the managed certificate resource.
   *
   * Accepted values: ACTIVE, MANAGED_CERTIFICATE_STATUS_UNSPECIFIED,
   * PROVISIONING, PROVISIONING_FAILED, PROVISIONING_FAILED_PERMANENTLY,
   * RENEWAL_FAILED
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SslCertificateManagedSslCertificate::class, 'Google_Service_Compute_SslCertificateManagedSslCertificate');
