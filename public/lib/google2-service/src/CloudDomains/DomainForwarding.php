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

namespace Google\Service\CloudDomains;

class DomainForwarding extends \Google\Model
{
  /**
   * Redirect Type is unspecified.
   */
  public const REDIRECT_TYPE_REDIRECT_TYPE_UNSPECIFIED = 'REDIRECT_TYPE_UNSPECIFIED';
  /**
   * 301 redirect. Allows to propagate changes to the forwarding address
   * quickly.
   */
  public const REDIRECT_TYPE_TEMPORARY = 'TEMPORARY';
  /**
   * 302 redirect. Allows browsers to cache the forwarding address. This may
   * help the address resolve more quickly. Changes may take longer to propagate
   */
  public const REDIRECT_TYPE_PERMANENT = 'PERMANENT';
  /**
   * If true, forwards the path after the domain name to the same path at the
   * new address.
   *
   * @var bool
   */
  public $pathForwarding;
  /**
   * The PEM-encoded certificate chain.
   *
   * @var string
   */
  public $pemCertificate;
  /**
   * The redirect type.
   *
   * @var string
   */
  public $redirectType;
  /**
   * If true, the forwarding works also over HTTPS.
   *
   * @var bool
   */
  public $sslEnabled;
  /**
   * The subdomain of the registered domain that is being forwarded. E.g.
   * `www.example.com`, `example.com` (i.e. the registered domain itself) or
   * `*.example.com` (i.e. all subdomains).
   *
   * @var string
   */
  public $subdomain;
  /**
   * The target of the domain forwarding, i.e. the path to redirect the
   * `subdomain` to.
   *
   * @var string
   */
  public $targetUri;

  /**
   * If true, forwards the path after the domain name to the same path at the
   * new address.
   *
   * @param bool $pathForwarding
   */
  public function setPathForwarding($pathForwarding)
  {
    $this->pathForwarding = $pathForwarding;
  }
  /**
   * @return bool
   */
  public function getPathForwarding()
  {
    return $this->pathForwarding;
  }
  /**
   * The PEM-encoded certificate chain.
   *
   * @param string $pemCertificate
   */
  public function setPemCertificate($pemCertificate)
  {
    $this->pemCertificate = $pemCertificate;
  }
  /**
   * @return string
   */
  public function getPemCertificate()
  {
    return $this->pemCertificate;
  }
  /**
   * The redirect type.
   *
   * Accepted values: REDIRECT_TYPE_UNSPECIFIED, TEMPORARY, PERMANENT
   *
   * @param self::REDIRECT_TYPE_* $redirectType
   */
  public function setRedirectType($redirectType)
  {
    $this->redirectType = $redirectType;
  }
  /**
   * @return self::REDIRECT_TYPE_*
   */
  public function getRedirectType()
  {
    return $this->redirectType;
  }
  /**
   * If true, the forwarding works also over HTTPS.
   *
   * @param bool $sslEnabled
   */
  public function setSslEnabled($sslEnabled)
  {
    $this->sslEnabled = $sslEnabled;
  }
  /**
   * @return bool
   */
  public function getSslEnabled()
  {
    return $this->sslEnabled;
  }
  /**
   * The subdomain of the registered domain that is being forwarded. E.g.
   * `www.example.com`, `example.com` (i.e. the registered domain itself) or
   * `*.example.com` (i.e. all subdomains).
   *
   * @param string $subdomain
   */
  public function setSubdomain($subdomain)
  {
    $this->subdomain = $subdomain;
  }
  /**
   * @return string
   */
  public function getSubdomain()
  {
    return $this->subdomain;
  }
  /**
   * The target of the domain forwarding, i.e. the path to redirect the
   * `subdomain` to.
   *
   * @param string $targetUri
   */
  public function setTargetUri($targetUri)
  {
    $this->targetUri = $targetUri;
  }
  /**
   * @return string
   */
  public function getTargetUri()
  {
    return $this->targetUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DomainForwarding::class, 'Google_Service_CloudDomains_DomainForwarding');
