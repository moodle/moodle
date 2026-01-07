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

namespace Google\Service\FirebaseAppHosting;

class CustomDomainStatus extends \Google\Collection
{
  /**
   * The certificate's state is unspecified. The message is invalid if this is
   * unspecified.
   */
  public const CERT_STATE_CERT_STATE_UNSPECIFIED = 'CERT_STATE_UNSPECIFIED';
  /**
   * The initial state of every certificate, represents App Hosting's intent to
   * create a certificate before requests to a Certificate Authority are made.
   */
  public const CERT_STATE_CERT_PREPARING = 'CERT_PREPARING';
  /**
   * App Hosting is validating whether a domain name's DNS records are in a
   * state that allow certificate creation on its behalf.
   */
  public const CERT_STATE_CERT_VALIDATING = 'CERT_VALIDATING';
  /**
   * The certificate was recently created, and needs time to propagate in App
   * Hosting's load balancers.
   */
  public const CERT_STATE_CERT_PROPAGATING = 'CERT_PROPAGATING';
  /**
   * The certificate is active, providing secure connections for the domain
   * names it represents.
   */
  public const CERT_STATE_CERT_ACTIVE = 'CERT_ACTIVE';
  /**
   * The certificate is expiring, all domain names on it will be given new
   * certificates.
   */
  public const CERT_STATE_CERT_EXPIRING_SOON = 'CERT_EXPIRING_SOON';
  /**
   * The certificate has expired. App Hosting can no longer serve secure content
   * on your domain name.
   */
  public const CERT_STATE_CERT_EXPIRED = 'CERT_EXPIRED';
  /**
   * Your custom domain's host state is unspecified. The message is invalid if
   * this is unspecified.
   */
  public const HOST_STATE_HOST_STATE_UNSPECIFIED = 'HOST_STATE_UNSPECIFIED';
  /**
   * Your custom domain isn't associated with any IP addresses.
   */
  public const HOST_STATE_HOST_UNHOSTED = 'HOST_UNHOSTED';
  /**
   * Your custom domain can't be reached. App Hosting services' DNS queries to
   * find your domain's IP addresses resulted in errors. See your
   * `CustomDomainStatus`'s `issues` field for more details.
   */
  public const HOST_STATE_HOST_UNREACHABLE = 'HOST_UNREACHABLE';
  /**
   * Your domain has only IP addresses that don't ultimately resolve to App
   * Hosting.
   */
  public const HOST_STATE_HOST_NON_FAH = 'HOST_NON_FAH';
  /**
   * Your domain has IP addresses that resolve to both App Hosting and to other
   * services. To ensure consistent results, remove `A` and `AAAA` records
   * related to non-App-Hosting services.
   */
  public const HOST_STATE_HOST_CONFLICT = 'HOST_CONFLICT';
  /**
   * Your domain has IP addresses that resolve to an incorrect instance of the
   * App Hosting Data Plane. App Hosting has multiple data plane instances to
   * ensure high availability. The SSL certificate that App Hosting creates for
   * your domain is only available on its assigned instance. If your domain's IP
   * addresses resolve to an incorrect instance, App Hosting won't be able to
   * serve secure content on it.
   */
  public const HOST_STATE_HOST_WRONG_SHARD = 'HOST_WRONG_SHARD';
  /**
   * All requests against your domain are served by App Hosting, via your
   * domain's assigned shard. If the custom domain's `OwnershipState` is also
   * `OWNERSHIP_ACTIVE`, App Hosting serves its backend's content on requests
   * for the domain.
   */
  public const HOST_STATE_HOST_ACTIVE = 'HOST_ACTIVE';
  /**
   * Your custom domain's ownership state is unspecified. This should never
   * happen.
   */
  public const OWNERSHIP_STATE_OWNERSHIP_STATE_UNSPECIFIED = 'OWNERSHIP_STATE_UNSPECIFIED';
  /**
   * Your custom domain's domain has no App-Hosting-related ownership records;
   * no backend is authorized to serve on the domain in this Origin shard.
   */
  public const OWNERSHIP_STATE_OWNERSHIP_MISSING = 'OWNERSHIP_MISSING';
  /**
   * Your custom domain can't be reached. App Hosting services' DNS queries to
   * find your domain's ownership records resulted in errors. See your
   * `CustomDomainStatus`'s `issues` field for more details.
   */
  public const OWNERSHIP_STATE_OWNERSHIP_UNREACHABLE = 'OWNERSHIP_UNREACHABLE';
  /**
   * Your custom domain is owned by another App Hosting custom domain. Remove
   * the conflicting records and replace them with records for your current
   * custom domain.
   */
  public const OWNERSHIP_STATE_OWNERSHIP_MISMATCH = 'OWNERSHIP_MISMATCH';
  /**
   * Your custom domain has conflicting `TXT` records that indicate ownership by
   * both your current custom domain one or more others. Remove the extraneous
   * ownership records to grant the current custom domain ownership.
   */
  public const OWNERSHIP_STATE_OWNERSHIP_CONFLICT = 'OWNERSHIP_CONFLICT';
  /**
   * Your custom domain's DNS records are configured correctly. App Hosting will
   * transfer ownership of your domain to this custom domain within 24 hours.
   */
  public const OWNERSHIP_STATE_OWNERSHIP_PENDING = 'OWNERSHIP_PENDING';
  /**
   * Your custom domain owns its domain.
   */
  public const OWNERSHIP_STATE_OWNERSHIP_ACTIVE = 'OWNERSHIP_ACTIVE';
  protected $collection_key = 'requiredDnsUpdates';
  /**
   * Output only. Tracks SSL certificate status for the domain.
   *
   * @var string
   */
  public $certState;
  /**
   * Output only. Tracks whether a custom domain is detected as appropriately
   * directing traffic to App Hosting.
   *
   * @var string
   */
  public $hostState;
  protected $issuesType = Status::class;
  protected $issuesDataType = 'array';
  /**
   * Output only. Tracks whether the backend is permitted to serve content on
   * the domain, based off the domain's DNS records.
   *
   * @var string
   */
  public $ownershipState;
  protected $requiredDnsUpdatesType = DnsUpdates::class;
  protected $requiredDnsUpdatesDataType = 'array';

  /**
   * Output only. Tracks SSL certificate status for the domain.
   *
   * Accepted values: CERT_STATE_UNSPECIFIED, CERT_PREPARING, CERT_VALIDATING,
   * CERT_PROPAGATING, CERT_ACTIVE, CERT_EXPIRING_SOON, CERT_EXPIRED
   *
   * @param self::CERT_STATE_* $certState
   */
  public function setCertState($certState)
  {
    $this->certState = $certState;
  }
  /**
   * @return self::CERT_STATE_*
   */
  public function getCertState()
  {
    return $this->certState;
  }
  /**
   * Output only. Tracks whether a custom domain is detected as appropriately
   * directing traffic to App Hosting.
   *
   * Accepted values: HOST_STATE_UNSPECIFIED, HOST_UNHOSTED, HOST_UNREACHABLE,
   * HOST_NON_FAH, HOST_CONFLICT, HOST_WRONG_SHARD, HOST_ACTIVE
   *
   * @param self::HOST_STATE_* $hostState
   */
  public function setHostState($hostState)
  {
    $this->hostState = $hostState;
  }
  /**
   * @return self::HOST_STATE_*
   */
  public function getHostState()
  {
    return $this->hostState;
  }
  /**
   * Output only. A list of issues with domain configuration. Allows users to
   * self-correct problems with DNS records.
   *
   * @param Status[] $issues
   */
  public function setIssues($issues)
  {
    $this->issues = $issues;
  }
  /**
   * @return Status[]
   */
  public function getIssues()
  {
    return $this->issues;
  }
  /**
   * Output only. Tracks whether the backend is permitted to serve content on
   * the domain, based off the domain's DNS records.
   *
   * Accepted values: OWNERSHIP_STATE_UNSPECIFIED, OWNERSHIP_MISSING,
   * OWNERSHIP_UNREACHABLE, OWNERSHIP_MISMATCH, OWNERSHIP_CONFLICT,
   * OWNERSHIP_PENDING, OWNERSHIP_ACTIVE
   *
   * @param self::OWNERSHIP_STATE_* $ownershipState
   */
  public function setOwnershipState($ownershipState)
  {
    $this->ownershipState = $ownershipState;
  }
  /**
   * @return self::OWNERSHIP_STATE_*
   */
  public function getOwnershipState()
  {
    return $this->ownershipState;
  }
  /**
   * Output only. Lists the records that must added or removed to a custom
   * domain's DNS in order to finish setup and start serving content. Field is
   * present during onboarding. Also present after onboarding if one or more of
   * the above states is not *_ACTIVE, indicating the domain's DNS records are
   * in a bad state.
   *
   * @param DnsUpdates[] $requiredDnsUpdates
   */
  public function setRequiredDnsUpdates($requiredDnsUpdates)
  {
    $this->requiredDnsUpdates = $requiredDnsUpdates;
  }
  /**
   * @return DnsUpdates[]
   */
  public function getRequiredDnsUpdates()
  {
    return $this->requiredDnsUpdates;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CustomDomainStatus::class, 'Google_Service_FirebaseAppHosting_CustomDomainStatus');
