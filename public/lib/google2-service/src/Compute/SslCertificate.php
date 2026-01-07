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

class SslCertificate extends \Google\Collection
{
  /**
   * Google-managed SSLCertificate.
   */
  public const TYPE_MANAGED = 'MANAGED';
  /**
   * Certificate uploaded by user.
   */
  public const TYPE_SELF_MANAGED = 'SELF_MANAGED';
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  protected $collection_key = 'subjectAlternativeNames';
  /**
   * A value read into memory from a certificate file. The certificate file must
   * be in PEM format. The certificate chain must be no greater than 5 certs
   * long. The chain must include at least one intermediate cert.
   *
   * @var string
   */
  public $certificate;
  /**
   * [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @var string
   */
  public $creationTimestamp;
  /**
   * An optional description of this resource. Provide this property when you
   * create the resource.
   *
   * @var string
   */
  public $description;
  /**
   * Output only. [Output Only] Expire time of the certificate. RFC3339
   *
   * @var string
   */
  public $expireTime;
  /**
   * [Output Only] The unique identifier for the resource. This identifier is
   * defined by the server.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. [Output Only] Type of the resource.
   * Alwayscompute#sslCertificate for SSL certificates.
   *
   * @var string
   */
  public $kind;
  protected $managedType = SslCertificateManagedSslCertificate::class;
  protected $managedDataType = '';
  /**
   * Name of the resource. Provided by the client when the resource is created.
   * The name must be 1-63 characters long, and comply withRFC1035.
   * Specifically, the name must be 1-63 characters long and match the regular
   * expression `[a-z]([-a-z0-9]*[a-z0-9])?` which means the first character
   * must be a lowercase letter, and all following characters must be a dash,
   * lowercase letter, or digit, except the last character, which cannot be a
   * dash.
   *
   * @var string
   */
  public $name;
  /**
   * A value read into memory from a write-only private key file. The private
   * key file must be in PEM format. For security, only insert requests include
   * this field.
   *
   * @var string
   */
  public $privateKey;
  /**
   * Output only. [Output Only] URL of the region where the regional SSL
   * Certificate resides. This field is not applicable to global SSL
   * Certificate.
   *
   * @var string
   */
  public $region;
  /**
   * [Output only] Server-defined URL for the resource.
   *
   * @var string
   */
  public $selfLink;
  protected $selfManagedType = SslCertificateSelfManagedSslCertificate::class;
  protected $selfManagedDataType = '';
  /**
   * Output only. [Output Only] Domains associated with the certificate via
   * Subject Alternative Name.
   *
   * @var string[]
   */
  public $subjectAlternativeNames;
  /**
   * (Optional) Specifies the type of SSL certificate, either "SELF_MANAGED" or
   * "MANAGED". If not specified, the certificate is self-managed and the
   * fieldscertificate and private_key are used.
   *
   * @var string
   */
  public $type;

  /**
   * A value read into memory from a certificate file. The certificate file must
   * be in PEM format. The certificate chain must be no greater than 5 certs
   * long. The chain must include at least one intermediate cert.
   *
   * @param string $certificate
   */
  public function setCertificate($certificate)
  {
    $this->certificate = $certificate;
  }
  /**
   * @return string
   */
  public function getCertificate()
  {
    return $this->certificate;
  }
  /**
   * [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @param string $creationTimestamp
   */
  public function setCreationTimestamp($creationTimestamp)
  {
    $this->creationTimestamp = $creationTimestamp;
  }
  /**
   * @return string
   */
  public function getCreationTimestamp()
  {
    return $this->creationTimestamp;
  }
  /**
   * An optional description of this resource. Provide this property when you
   * create the resource.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Output only. [Output Only] Expire time of the certificate. RFC3339
   *
   * @param string $expireTime
   */
  public function setExpireTime($expireTime)
  {
    $this->expireTime = $expireTime;
  }
  /**
   * @return string
   */
  public function getExpireTime()
  {
    return $this->expireTime;
  }
  /**
   * [Output Only] The unique identifier for the resource. This identifier is
   * defined by the server.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Output only. [Output Only] Type of the resource.
   * Alwayscompute#sslCertificate for SSL certificates.
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
   * Configuration and status of a managed SSL certificate.
   *
   * @param SslCertificateManagedSslCertificate $managed
   */
  public function setManaged(SslCertificateManagedSslCertificate $managed)
  {
    $this->managed = $managed;
  }
  /**
   * @return SslCertificateManagedSslCertificate
   */
  public function getManaged()
  {
    return $this->managed;
  }
  /**
   * Name of the resource. Provided by the client when the resource is created.
   * The name must be 1-63 characters long, and comply withRFC1035.
   * Specifically, the name must be 1-63 characters long and match the regular
   * expression `[a-z]([-a-z0-9]*[a-z0-9])?` which means the first character
   * must be a lowercase letter, and all following characters must be a dash,
   * lowercase letter, or digit, except the last character, which cannot be a
   * dash.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * A value read into memory from a write-only private key file. The private
   * key file must be in PEM format. For security, only insert requests include
   * this field.
   *
   * @param string $privateKey
   */
  public function setPrivateKey($privateKey)
  {
    $this->privateKey = $privateKey;
  }
  /**
   * @return string
   */
  public function getPrivateKey()
  {
    return $this->privateKey;
  }
  /**
   * Output only. [Output Only] URL of the region where the regional SSL
   * Certificate resides. This field is not applicable to global SSL
   * Certificate.
   *
   * @param string $region
   */
  public function setRegion($region)
  {
    $this->region = $region;
  }
  /**
   * @return string
   */
  public function getRegion()
  {
    return $this->region;
  }
  /**
   * [Output only] Server-defined URL for the resource.
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
   * Configuration and status of a self-managed SSL certificate.
   *
   * @param SslCertificateSelfManagedSslCertificate $selfManaged
   */
  public function setSelfManaged(SslCertificateSelfManagedSslCertificate $selfManaged)
  {
    $this->selfManaged = $selfManaged;
  }
  /**
   * @return SslCertificateSelfManagedSslCertificate
   */
  public function getSelfManaged()
  {
    return $this->selfManaged;
  }
  /**
   * Output only. [Output Only] Domains associated with the certificate via
   * Subject Alternative Name.
   *
   * @param string[] $subjectAlternativeNames
   */
  public function setSubjectAlternativeNames($subjectAlternativeNames)
  {
    $this->subjectAlternativeNames = $subjectAlternativeNames;
  }
  /**
   * @return string[]
   */
  public function getSubjectAlternativeNames()
  {
    return $this->subjectAlternativeNames;
  }
  /**
   * (Optional) Specifies the type of SSL certificate, either "SELF_MANAGED" or
   * "MANAGED". If not specified, the certificate is self-managed and the
   * fieldscertificate and private_key are used.
   *
   * Accepted values: MANAGED, SELF_MANAGED, TYPE_UNSPECIFIED
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SslCertificate::class, 'Google_Service_Compute_SslCertificate');
