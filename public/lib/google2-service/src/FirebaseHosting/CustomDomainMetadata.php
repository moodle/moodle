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

namespace Google\Service\FirebaseHosting;

class CustomDomainMetadata extends \Google\Collection
{
  /**
   * The certificate's state is unspecified. The message is invalid if this is
   * unspecified.
   */
  public const CERT_STATE_CERT_STATE_UNSPECIFIED = 'CERT_STATE_UNSPECIFIED';
  /**
   * The initial state of every certificate, represents Hosting's intent to
   * create a certificate, before requests to a Certificate Authority are made.
   */
  public const CERT_STATE_CERT_PREPARING = 'CERT_PREPARING';
  /**
   * Hosting is validating whether a domain name's DNS records are in a state
   * that allow certificate creation on its behalf.
   */
  public const CERT_STATE_CERT_VALIDATING = 'CERT_VALIDATING';
  /**
   * The certificate was recently created, and needs time to propagate in
   * Hosting's CDN.
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
   * The certificate has expired. Hosting can no longer serve secure content on
   * your domain name.
   */
  public const CERT_STATE_CERT_EXPIRED = 'CERT_EXPIRED';
  /**
   * Your custom domain's host state is unspecified. The message is invalid if
   * this is unspecified.
   */
  public const HOST_STATE_HOST_STATE_UNSPECIFIED = 'HOST_STATE_UNSPECIFIED';
  /**
   * Your custom domain's domain name isn't associated with any IP addresses.
   */
  public const HOST_STATE_HOST_UNHOSTED = 'HOST_UNHOSTED';
  /**
   * Your custom domain's domain name can't be reached. Hosting services' DNS
   * queries to find your domain name's IP addresses resulted in errors. See
   * your `CustomDomain` object's `issues` field for more details.
   */
  public const HOST_STATE_HOST_UNREACHABLE = 'HOST_UNREACHABLE';
  /**
   * Your custom domain's domain name has IP addresses that don't ultimately
   * resolve to Hosting.
   */
  public const HOST_STATE_HOST_MISMATCH = 'HOST_MISMATCH';
  /**
   * Your custom domain's domain name has IP addresses that resolve to both
   * Hosting and other services. To ensure consistent results, remove `A` and
   * `AAAA` records related to non-Hosting services.
   */
  public const HOST_STATE_HOST_CONFLICT = 'HOST_CONFLICT';
  /**
   * All requests against your custom domain's domain name are served by
   * Hosting. If the custom domain's `OwnershipState` is also `ACTIVE`, Hosting
   * serves your Hosting site's content on the domain name.
   */
  public const HOST_STATE_HOST_ACTIVE = 'HOST_ACTIVE';
  /**
   * Your custom domain's ownership state is unspecified. This should never
   * happen.
   */
  public const OWNERSHIP_STATE_OWNERSHIP_STATE_UNSPECIFIED = 'OWNERSHIP_STATE_UNSPECIFIED';
  /**
   * Your custom domain's domain name has no Hosting-related ownership records;
   * no Firebase project has permission to act on the domain name's behalf.
   */
  public const OWNERSHIP_STATE_OWNERSHIP_MISSING = 'OWNERSHIP_MISSING';
  /**
   * Your custom domain's domain name can't be reached. Hosting services' DNS
   * queries to find your domain name's ownership records resulted in errors.
   * See your `CustomDomain` object's `issues` field for more details.
   */
  public const OWNERSHIP_STATE_OWNERSHIP_UNREACHABLE = 'OWNERSHIP_UNREACHABLE';
  /**
   * Your custom domain's domain name is owned by another Firebase project.
   * Remove the conflicting `TXT` records and replace them with project-specific
   * records for your current Firebase project.
   */
  public const OWNERSHIP_STATE_OWNERSHIP_MISMATCH = 'OWNERSHIP_MISMATCH';
  /**
   * Your custom domain's domain name has conflicting `TXT` records that
   * indicate ownership by both your current Firebase project and another
   * project. Remove the other project's ownership records to grant the current
   * project ownership.
   */
  public const OWNERSHIP_STATE_OWNERSHIP_CONFLICT = 'OWNERSHIP_CONFLICT';
  /**
   * Your custom domain's DNS records are configured correctly. Hosting will
   * transfer ownership of your domain to this `CustomDomain` within 24 hours.
   */
  public const OWNERSHIP_STATE_OWNERSHIP_PENDING = 'OWNERSHIP_PENDING';
  /**
   * Your custom domain's domain name has `TXT` records that grant its project
   * permission to act on its behalf.
   */
  public const OWNERSHIP_STATE_OWNERSHIP_ACTIVE = 'OWNERSHIP_ACTIVE';
  protected $collection_key = 'liveMigrationSteps';
  /**
   * The `CertState` of the domain name's SSL certificate.
   *
   * @var string
   */
  public $certState;
  /**
   * The `HostState` of the domain name this `CustomDomain` refers to.
   *
   * @var string
   */
  public $hostState;
  protected $issuesType = Status::class;
  protected $issuesDataType = 'array';
  protected $liveMigrationStepsType = LiveMigrationStep::class;
  protected $liveMigrationStepsDataType = 'array';
  /**
   * The `OwnershipState` of the domain name this `CustomDomain` refers to.
   *
   * @var string
   */
  public $ownershipState;
  protected $quickSetupUpdatesType = DnsUpdates::class;
  protected $quickSetupUpdatesDataType = '';

  /**
   * The `CertState` of the domain name's SSL certificate.
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
   * The `HostState` of the domain name this `CustomDomain` refers to.
   *
   * Accepted values: HOST_STATE_UNSPECIFIED, HOST_UNHOSTED, HOST_UNREACHABLE,
   * HOST_MISMATCH, HOST_CONFLICT, HOST_ACTIVE
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
   * A list of issues that are currently preventing Hosting from completing the
   * operation. These are generally DNS-related issues that Hosting encounters
   * when querying a domain name's records or attempting to mint an SSL
   * certificate.
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
   * A set of DNS record updates and ACME challenges that allow you to
   * transition domain names to Firebase Hosting with zero downtime. These
   * updates allow Hosting to create an SSL certificate and establish ownership
   * for your custom domain before Hosting begins serving traffic on it. If your
   * domain name is already in active use with another provider, add one of the
   * challenges and make the recommended DNS updates. After adding challenges
   * and adjusting DNS records as necessary, wait for the `ownershipState` to be
   * `OWNERSHIP_ACTIVE` and the `certState` to be `CERT_ACTIVE` before sending
   * traffic to Hosting.
   *
   * @param LiveMigrationStep[] $liveMigrationSteps
   */
  public function setLiveMigrationSteps($liveMigrationSteps)
  {
    $this->liveMigrationSteps = $liveMigrationSteps;
  }
  /**
   * @return LiveMigrationStep[]
   */
  public function getLiveMigrationSteps()
  {
    return $this->liveMigrationSteps;
  }
  /**
   * The `OwnershipState` of the domain name this `CustomDomain` refers to.
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
   * A set of DNS record updates that allow Hosting to serve secure content on
   * your domain name. The record type determines the update's purpose: - `A`
   * and `AAAA`: Updates your domain name's IP addresses so that they direct
   * traffic to Hosting servers. - `TXT`: Updates ownership permissions on your
   * domain name, letting Hosting know that your custom domain's project has
   * permission to perform actions for that domain name. - `CAA`: Updates your
   * domain name's list of authorized Certificate Authorities (CAs). Only
   * present if you have existing `CAA` records that prohibit Hosting's CA from
   * minting certs for your domain name. These updates include all DNS changes
   * you'll need to get started with Hosting, but, if made all at once, can
   * result in a brief period of downtime for your domain name--while Hosting
   * creates and uploads an SSL cert, for example. If you'd like to add your
   * domain name to Hosting without downtime, complete the `liveMigrationSteps`
   * first, before making the remaining updates in this field.
   *
   * @param DnsUpdates $quickSetupUpdates
   */
  public function setQuickSetupUpdates(DnsUpdates $quickSetupUpdates)
  {
    $this->quickSetupUpdates = $quickSetupUpdates;
  }
  /**
   * @return DnsUpdates
   */
  public function getQuickSetupUpdates()
  {
    return $this->quickSetupUpdates;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CustomDomainMetadata::class, 'Google_Service_FirebaseHosting_CustomDomainMetadata');
