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

class SubjectAltNames extends \Google\Collection
{
  protected $collection_key = 'uris';
  protected $customSansType = X509Extension::class;
  protected $customSansDataType = 'array';
  /**
   * Contains only valid, fully-qualified host names.
   *
   * @var string[]
   */
  public $dnsNames;
  /**
   * Contains only valid RFC 2822 E-mail addresses.
   *
   * @var string[]
   */
  public $emailAddresses;
  /**
   * Contains only valid 32-bit IPv4 addresses or RFC 4291 IPv6 addresses.
   *
   * @var string[]
   */
  public $ipAddresses;
  /**
   * Contains only valid RFC 3986 URIs.
   *
   * @var string[]
   */
  public $uris;

  /**
   * Contains additional subject alternative name values. For each custom_san,
   * the `value` field must contain an ASN.1 encoded UTF8String.
   *
   * @param X509Extension[] $customSans
   */
  public function setCustomSans($customSans)
  {
    $this->customSans = $customSans;
  }
  /**
   * @return X509Extension[]
   */
  public function getCustomSans()
  {
    return $this->customSans;
  }
  /**
   * Contains only valid, fully-qualified host names.
   *
   * @param string[] $dnsNames
   */
  public function setDnsNames($dnsNames)
  {
    $this->dnsNames = $dnsNames;
  }
  /**
   * @return string[]
   */
  public function getDnsNames()
  {
    return $this->dnsNames;
  }
  /**
   * Contains only valid RFC 2822 E-mail addresses.
   *
   * @param string[] $emailAddresses
   */
  public function setEmailAddresses($emailAddresses)
  {
    $this->emailAddresses = $emailAddresses;
  }
  /**
   * @return string[]
   */
  public function getEmailAddresses()
  {
    return $this->emailAddresses;
  }
  /**
   * Contains only valid 32-bit IPv4 addresses or RFC 4291 IPv6 addresses.
   *
   * @param string[] $ipAddresses
   */
  public function setIpAddresses($ipAddresses)
  {
    $this->ipAddresses = $ipAddresses;
  }
  /**
   * @return string[]
   */
  public function getIpAddresses()
  {
    return $this->ipAddresses;
  }
  /**
   * Contains only valid RFC 3986 URIs.
   *
   * @param string[] $uris
   */
  public function setUris($uris)
  {
    $this->uris = $uris;
  }
  /**
   * @return string[]
   */
  public function getUris()
  {
    return $this->uris;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SubjectAltNames::class, 'Google_Service_CertificateAuthorityService_SubjectAltNames');
