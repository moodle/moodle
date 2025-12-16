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

class Certificate extends \Google\Collection
{
  /**
   * Not specified.
   */
  public const SUBJECT_MODE_SUBJECT_REQUEST_MODE_UNSPECIFIED = 'SUBJECT_REQUEST_MODE_UNSPECIFIED';
  /**
   * The default mode used in most cases. Indicates that the certificate's
   * Subject and/or SubjectAltNames are specified in the certificate request.
   * This mode requires the caller to have the `privateca.certificates.create`
   * permission.
   */
  public const SUBJECT_MODE_DEFAULT = 'DEFAULT';
  /**
   * A mode used to get an accurate representation of the Subject field's
   * distinguished name. Indicates that the certificate's Subject and/or
   * SubjectAltNames are specified in the certificate request. When parsing a
   * PEM CSR this mode will maintain the sequence of RDNs found in the CSR's
   * subject field in the issued Certificate. This mode requires the caller to
   * have the `privateca.certificates.create` permission.
   */
  public const SUBJECT_MODE_RDN_SEQUENCE = 'RDN_SEQUENCE';
  /**
   * A mode reserved for special cases. Indicates that the certificate should
   * have one SPIFFE SubjectAltNames set by the service based on the caller's
   * identity. This mode will ignore any explicitly specified Subject and/or
   * SubjectAltNames in the certificate request. This mode requires the caller
   * to have the `privateca.certificates.createForSelf` permission.
   */
  public const SUBJECT_MODE_REFLECTED_SPIFFE = 'REFLECTED_SPIFFE';
  protected $collection_key = 'pemCertificateChain';
  protected $certificateDescriptionType = CertificateDescription::class;
  protected $certificateDescriptionDataType = '';
  /**
   * Immutable. The resource name for a CertificateTemplate used to issue this
   * certificate, in the format `projects/locations/certificateTemplates`. If
   * this is specified, the caller must have the necessary permission to use
   * this template. If this is omitted, no template will be used. This template
   * must be in the same location as the Certificate.
   *
   * @var string
   */
  public $certificateTemplate;
  protected $configType = CertificateConfig::class;
  protected $configDataType = '';
  /**
   * Output only. The time at which this Certificate was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The resource name of the issuing CertificateAuthority in the
   * format `projects/locations/caPools/certificateAuthorities`.
   *
   * @var string
   */
  public $issuerCertificateAuthority;
  /**
   * Optional. Labels with user-defined metadata.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Required. Immutable. The desired lifetime of a certificate. Used to create
   * the "not_before_time" and "not_after_time" fields inside an X.509
   * certificate. Note that the lifetime may be truncated if it would extend
   * past the life of any certificate authority in the issuing chain.
   *
   * @var string
   */
  public $lifetime;
  /**
   * Identifier. The resource name for this Certificate in the format
   * `projects/locations/caPools/certificates`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The pem-encoded, signed X.509 certificate.
   *
   * @var string
   */
  public $pemCertificate;
  /**
   * Output only. The chain that may be used to verify the X.509 certificate.
   * Expected to be in issuer-to-root order according to RFC 5246.
   *
   * @var string[]
   */
  public $pemCertificateChain;
  /**
   * Immutable. A pem-encoded X.509 certificate signing request (CSR).
   *
   * @var string
   */
  public $pemCsr;
  protected $revocationDetailsType = RevocationDetails::class;
  protected $revocationDetailsDataType = '';
  /**
   * Immutable. Specifies how the Certificate's identity fields are to be
   * decided. If this is omitted, the `DEFAULT` subject mode will be used.
   *
   * @var string
   */
  public $subjectMode;
  /**
   * Output only. The time at which this Certificate was updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. A structured description of the issued X.509 certificate.
   *
   * @param CertificateDescription $certificateDescription
   */
  public function setCertificateDescription(CertificateDescription $certificateDescription)
  {
    $this->certificateDescription = $certificateDescription;
  }
  /**
   * @return CertificateDescription
   */
  public function getCertificateDescription()
  {
    return $this->certificateDescription;
  }
  /**
   * Immutable. The resource name for a CertificateTemplate used to issue this
   * certificate, in the format `projects/locations/certificateTemplates`. If
   * this is specified, the caller must have the necessary permission to use
   * this template. If this is omitted, no template will be used. This template
   * must be in the same location as the Certificate.
   *
   * @param string $certificateTemplate
   */
  public function setCertificateTemplate($certificateTemplate)
  {
    $this->certificateTemplate = $certificateTemplate;
  }
  /**
   * @return string
   */
  public function getCertificateTemplate()
  {
    return $this->certificateTemplate;
  }
  /**
   * Immutable. A description of the certificate and key that does not require
   * X.509 or ASN.1.
   *
   * @param CertificateConfig $config
   */
  public function setConfig(CertificateConfig $config)
  {
    $this->config = $config;
  }
  /**
   * @return CertificateConfig
   */
  public function getConfig()
  {
    return $this->config;
  }
  /**
   * Output only. The time at which this Certificate was created.
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
   * Output only. The resource name of the issuing CertificateAuthority in the
   * format `projects/locations/caPools/certificateAuthorities`.
   *
   * @param string $issuerCertificateAuthority
   */
  public function setIssuerCertificateAuthority($issuerCertificateAuthority)
  {
    $this->issuerCertificateAuthority = $issuerCertificateAuthority;
  }
  /**
   * @return string
   */
  public function getIssuerCertificateAuthority()
  {
    return $this->issuerCertificateAuthority;
  }
  /**
   * Optional. Labels with user-defined metadata.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Required. Immutable. The desired lifetime of a certificate. Used to create
   * the "not_before_time" and "not_after_time" fields inside an X.509
   * certificate. Note that the lifetime may be truncated if it would extend
   * past the life of any certificate authority in the issuing chain.
   *
   * @param string $lifetime
   */
  public function setLifetime($lifetime)
  {
    $this->lifetime = $lifetime;
  }
  /**
   * @return string
   */
  public function getLifetime()
  {
    return $this->lifetime;
  }
  /**
   * Identifier. The resource name for this Certificate in the format
   * `projects/locations/caPools/certificates`.
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
   * Output only. The pem-encoded, signed X.509 certificate.
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
   * Output only. The chain that may be used to verify the X.509 certificate.
   * Expected to be in issuer-to-root order according to RFC 5246.
   *
   * @param string[] $pemCertificateChain
   */
  public function setPemCertificateChain($pemCertificateChain)
  {
    $this->pemCertificateChain = $pemCertificateChain;
  }
  /**
   * @return string[]
   */
  public function getPemCertificateChain()
  {
    return $this->pemCertificateChain;
  }
  /**
   * Immutable. A pem-encoded X.509 certificate signing request (CSR).
   *
   * @param string $pemCsr
   */
  public function setPemCsr($pemCsr)
  {
    $this->pemCsr = $pemCsr;
  }
  /**
   * @return string
   */
  public function getPemCsr()
  {
    return $this->pemCsr;
  }
  /**
   * Output only. Details regarding the revocation of this Certificate. This
   * Certificate is considered revoked if and only if this field is present.
   *
   * @param RevocationDetails $revocationDetails
   */
  public function setRevocationDetails(RevocationDetails $revocationDetails)
  {
    $this->revocationDetails = $revocationDetails;
  }
  /**
   * @return RevocationDetails
   */
  public function getRevocationDetails()
  {
    return $this->revocationDetails;
  }
  /**
   * Immutable. Specifies how the Certificate's identity fields are to be
   * decided. If this is omitted, the `DEFAULT` subject mode will be used.
   *
   * Accepted values: SUBJECT_REQUEST_MODE_UNSPECIFIED, DEFAULT, RDN_SEQUENCE,
   * REFLECTED_SPIFFE
   *
   * @param self::SUBJECT_MODE_* $subjectMode
   */
  public function setSubjectMode($subjectMode)
  {
    $this->subjectMode = $subjectMode;
  }
  /**
   * @return self::SUBJECT_MODE_*
   */
  public function getSubjectMode()
  {
    return $this->subjectMode;
  }
  /**
   * Output only. The time at which this Certificate was updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Certificate::class, 'Google_Service_CertificateAuthorityService_Certificate');
