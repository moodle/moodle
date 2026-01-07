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

namespace Google\Service\SQLAdmin;

class SslCert extends \Google\Model
{
  /**
   * PEM representation.
   *
   * @var string
   */
  public $cert;
  /**
   * Serial number, as extracted from the certificate.
   *
   * @var string
   */
  public $certSerialNumber;
  /**
   * User supplied name. Constrained to [a-zA-Z.-_ ]+.
   *
   * @var string
   */
  public $commonName;
  /**
   * The time when the certificate was created in [RFC
   * 3339](https://tools.ietf.org/html/rfc3339) format, for example
   * `2012-11-15T16:19:00.094Z`
   *
   * @var string
   */
  public $createTime;
  /**
   * The time when the certificate expires in [RFC
   * 3339](https://tools.ietf.org/html/rfc3339) format, for example
   * `2012-11-15T16:19:00.094Z`.
   *
   * @var string
   */
  public $expirationTime;
  /**
   * Name of the database instance.
   *
   * @var string
   */
  public $instance;
  /**
   * This is always `sql#sslCert`.
   *
   * @var string
   */
  public $kind;
  /**
   * The URI of this resource.
   *
   * @var string
   */
  public $selfLink;
  /**
   * Sha1 Fingerprint.
   *
   * @var string
   */
  public $sha1Fingerprint;

  /**
   * PEM representation.
   *
   * @param string $cert
   */
  public function setCert($cert)
  {
    $this->cert = $cert;
  }
  /**
   * @return string
   */
  public function getCert()
  {
    return $this->cert;
  }
  /**
   * Serial number, as extracted from the certificate.
   *
   * @param string $certSerialNumber
   */
  public function setCertSerialNumber($certSerialNumber)
  {
    $this->certSerialNumber = $certSerialNumber;
  }
  /**
   * @return string
   */
  public function getCertSerialNumber()
  {
    return $this->certSerialNumber;
  }
  /**
   * User supplied name. Constrained to [a-zA-Z.-_ ]+.
   *
   * @param string $commonName
   */
  public function setCommonName($commonName)
  {
    $this->commonName = $commonName;
  }
  /**
   * @return string
   */
  public function getCommonName()
  {
    return $this->commonName;
  }
  /**
   * The time when the certificate was created in [RFC
   * 3339](https://tools.ietf.org/html/rfc3339) format, for example
   * `2012-11-15T16:19:00.094Z`
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
   * The time when the certificate expires in [RFC
   * 3339](https://tools.ietf.org/html/rfc3339) format, for example
   * `2012-11-15T16:19:00.094Z`.
   *
   * @param string $expirationTime
   */
  public function setExpirationTime($expirationTime)
  {
    $this->expirationTime = $expirationTime;
  }
  /**
   * @return string
   */
  public function getExpirationTime()
  {
    return $this->expirationTime;
  }
  /**
   * Name of the database instance.
   *
   * @param string $instance
   */
  public function setInstance($instance)
  {
    $this->instance = $instance;
  }
  /**
   * @return string
   */
  public function getInstance()
  {
    return $this->instance;
  }
  /**
   * This is always `sql#sslCert`.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * The URI of this resource.
   *
   * @param string $selfLink
   */
  public function setSelfLink($selfLink)
  {
    $this->selfLink = $selfLink;
  }
  /**
   * @return string
   */
  public function getSelfLink()
  {
    return $this->selfLink;
  }
  /**
   * Sha1 Fingerprint.
   *
   * @param string $sha1Fingerprint
   */
  public function setSha1Fingerprint($sha1Fingerprint)
  {
    $this->sha1Fingerprint = $sha1Fingerprint;
  }
  /**
   * @return string
   */
  public function getSha1Fingerprint()
  {
    return $this->sha1Fingerprint;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SslCert::class, 'Google_Service_SQLAdmin_SslCert');
