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

namespace Google\Service\CertificateAuthorityService;

class NameConstraints extends \Google\Collection
{
  protected $collection_key = 'permittedUris';
  /**
   * Indicates whether or not the name constraints are marked critical.
   *
   * @var bool
   */
  public $critical;
  /**
   * Contains excluded DNS names. Any DNS name that can be constructed by simply
   * adding zero or more labels to the left-hand side of the name satisfies the
   * name constraint. For example, `example.com`, `www.example.com`,
   * `www.sub.example.com` would satisfy `example.com` while `example1.com` does
   * not.
   *
   * @var string[]
   */
  public $excludedDnsNames;
  /**
   * Contains the excluded email addresses. The value can be a particular email
   * address, a hostname to indicate all email addresses on that host or a
   * domain with a leading period (e.g. `.example.com`) to indicate all email
   * addresses in that domain.
   *
   * @var string[]
   */
  public $excludedEmailAddresses;
  /**
   * Contains the excluded IP ranges. For IPv4 addresses, the ranges are
   * expressed using CIDR notation as specified in RFC 4632. For IPv6 addresses,
   * the ranges are expressed in similar encoding as IPv4 addresses.
   *
   * @var string[]
   */
  public $excludedIpRanges;
  /**
   * Contains the excluded URIs that apply to the host part of the name. The
   * value can be a hostname or a domain with a leading period (like
   * `.example.com`)
   *
   * @var string[]
   */
  public $excludedUris;
  /**
   * Contains permitted DNS names. Any DNS name that can be constructed by
   * simply adding zero or more labels to the left-hand side of the name
   * satisfies the name constraint. For example, `example.com`,
   * `www.example.com`, `www.sub.example.com` would satisfy `example.com` while
   * `example1.com` does not.
   *
   * @var string[]
   */
  public $permittedDnsNames;
  /**
   * Contains the permitted email addresses. The value can be a particular email
   * address, a hostname to indicate all email addresses on that host or a
   * domain with a leading period (e.g. `.example.com`) to indicate all email
   * addresses in that domain.
   *
   * @var string[]
   */
  public $permittedEmailAddresses;
  /**
   * Contains the permitted IP ranges. For IPv4 addresses, the ranges are
   * expressed using CIDR notation as specified in RFC 4632. For IPv6 addresses,
   * the ranges are expressed in similar encoding as IPv4 addresses.
   *
   * @var string[]
   */
  public $permittedIpRanges;
  /**
   * Contains the permitted URIs that apply to the host part of the name. The
   * value can be a hostname or a domain with a leading period (like
   * `.example.com`)
   *
   * @var string[]
   */
  public $permittedUris;

  /**
   * Indicates whether or not the name constraints are marked critical.
   *
   * @param bool $critical
   */
  public function setCritical($critical)
  {
    $this->critical = $critical;
  }
  /**
   * @return bool
   */
  public function getCritical()
  {
    return $this->critical;
  }
  /**
   * Contains excluded DNS names. Any DNS name that can be constructed by simply
   * adding zero or more labels to the left-hand side of the name satisfies the
   * name constraint. For example, `example.com`, `www.example.com`,
   * `www.sub.example.com` would satisfy `example.com` while `example1.com` does
   * not.
   *
   * @param string[] $excludedDnsNames
   */
  public function setExcludedDnsNames($excludedDnsNames)
  {
    $this->excludedDnsNames = $excludedDnsNames;
  }
  /**
   * @return string[]
   */
  public function getExcludedDnsNames()
  {
    return $this->excludedDnsNames;
  }
  /**
   * Contains the excluded email addresses. The value can be a particular email
   * address, a hostname to indicate all email addresses on that host or a
   * domain with a leading period (e.g. `.example.com`) to indicate all email
   * addresses in that domain.
   *
   * @param string[] $excludedEmailAddresses
   */
  public function setExcludedEmailAddresses($excludedEmailAddresses)
  {
    $this->excludedEmailAddresses = $excludedEmailAddresses;
  }
  /**
   * @return string[]
   */
  public function getExcludedEmailAddresses()
  {
    return $this->excludedEmailAddresses;
  }
  /**
   * Contains the excluded IP ranges. For IPv4 addresses, the ranges are
   * expressed using CIDR notation as specified in RFC 4632. For IPv6 addresses,
   * the ranges are expressed in similar encoding as IPv4 addresses.
   *
   * @param string[] $excludedIpRanges
   */
  public function setExcludedIpRanges($excludedIpRanges)
  {
    $this->excludedIpRanges = $excludedIpRanges;
  }
  /**
   * @return string[]
   */
  public function getExcludedIpRanges()
  {
    return $this->excludedIpRanges;
  }
  /**
   * Contains the excluded URIs that apply to the host part of the name. The
   * value can be a hostname or a domain with a leading period (like
   * `.example.com`)
   *
   * @param string[] $excludedUris
   */
  public function setExcludedUris($excludedUris)
  {
    $this->excludedUris = $excludedUris;
  }
  /**
   * @return string[]
   */
  public function getExcludedUris()
  {
    return $this->excludedUris;
  }
  /**
   * Contains permitted DNS names. Any DNS name that can be constructed by
   * simply adding zero or more labels to the left-hand side of the name
   * satisfies the name constraint. For example, `example.com`,
   * `www.example.com`, `www.sub.example.com` would satisfy `example.com` while
   * `example1.com` does not.
   *
   * @param string[] $permittedDnsNames
   */
  public function setPermittedDnsNames($permittedDnsNames)
  {
    $this->permittedDnsNames = $permittedDnsNames;
  }
  /**
   * @return string[]
   */
  public function getPermittedDnsNames()
  {
    return $this->permittedDnsNames;
  }
  /**
   * Contains the permitted email addresses. The value can be a particular email
   * address, a hostname to indicate all email addresses on that host or a
   * domain with a leading period (e.g. `.example.com`) to indicate all email
   * addresses in that domain.
   *
   * @param string[] $permittedEmailAddresses
   */
  public function setPermittedEmailAddresses($permittedEmailAddresses)
  {
    $this->permittedEmailAddresses = $permittedEmailAddresses;
  }
  /**
   * @return string[]
   */
  public function getPermittedEmailAddresses()
  {
    return $this->permittedEmailAddresses;
  }
  /**
   * Contains the permitted IP ranges. For IPv4 addresses, the ranges are
   * expressed using CIDR notation as specified in RFC 4632. For IPv6 addresses,
   * the ranges are expressed in similar encoding as IPv4 addresses.
   *
   * @param string[] $permittedIpRanges
   */
  public function setPermittedIpRanges($permittedIpRanges)
  {
    $this->permittedIpRanges = $permittedIpRanges;
  }
  /**
   * @return string[]
   */
  public function getPermittedIpRanges()
  {
    return $this->permittedIpRanges;
  }
  /**
   * Contains the permitted URIs that apply to the host part of the name. The
   * value can be a hostname or a domain with a leading period (like
   * `.example.com`)
   *
   * @param string[] $permittedUris
   */
  public function setPermittedUris($permittedUris)
  {
    $this->permittedUris = $permittedUris;
  }
  /**
   * @return string[]
   */
  public function getPermittedUris()
  {
    return $this->permittedUris;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NameConstraints::class, 'Google_Service_CertificateAuthorityService_NameConstraints');
