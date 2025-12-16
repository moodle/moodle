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

class CertificateDescription extends \Google\Collection
{
  protected $collection_key = 'crlDistributionPoints';
  /**
   * Describes lists of issuer CA certificate URLs that appear in the "Authority
   * Information Access" extension in the certificate.
   *
   * @var string[]
   */
  public $aiaIssuingCertificateUrls;
  protected $authorityKeyIdType = KeyId::class;
  protected $authorityKeyIdDataType = '';
  protected $certFingerprintType = CertificateFingerprint::class;
  protected $certFingerprintDataType = '';
  /**
   * Describes a list of locations to obtain CRL information, i.e. the
   * DistributionPoint.fullName described by
   * https://tools.ietf.org/html/rfc5280#section-4.2.1.13
   *
   * @var string[]
   */
  public $crlDistributionPoints;
  protected $publicKeyType = PublicKey::class;
  protected $publicKeyDataType = '';
  protected $subjectDescriptionType = SubjectDescription::class;
  protected $subjectDescriptionDataType = '';
  protected $subjectKeyIdType = KeyId::class;
  protected $subjectKeyIdDataType = '';
  /**
   * The hash of the pre-signed certificate, which will be signed by the CA.
   * Corresponds to the TBS Certificate in
   * https://tools.ietf.org/html/rfc5280#section-4.1.2. The field will always be
   * populated.
   *
   * @var string
   */
  public $tbsCertificateDigest;
  protected $x509DescriptionType = X509Parameters::class;
  protected $x509DescriptionDataType = '';

  /**
   * Describes lists of issuer CA certificate URLs that appear in the "Authority
   * Information Access" extension in the certificate.
   *
   * @param string[] $aiaIssuingCertificateUrls
   */
  public function setAiaIssuingCertificateUrls($aiaIssuingCertificateUrls)
  {
    $this->aiaIssuingCertificateUrls = $aiaIssuingCertificateUrls;
  }
  /**
   * @return string[]
   */
  public function getAiaIssuingCertificateUrls()
  {
    return $this->aiaIssuingCertificateUrls;
  }
  /**
   * Identifies the subject_key_id of the parent certificate, per
   * https://tools.ietf.org/html/rfc5280#section-4.2.1.1
   *
   * @param KeyId $authorityKeyId
   */
  public function setAuthorityKeyId(KeyId $authorityKeyId)
  {
    $this->authorityKeyId = $authorityKeyId;
  }
  /**
   * @return KeyId
   */
  public function getAuthorityKeyId()
  {
    return $this->authorityKeyId;
  }
  /**
   * The hash of the x.509 certificate.
   *
   * @param CertificateFingerprint $certFingerprint
   */
  public function setCertFingerprint(CertificateFingerprint $certFingerprint)
  {
    $this->certFingerprint = $certFingerprint;
  }
  /**
   * @return CertificateFingerprint
   */
  public function getCertFingerprint()
  {
    return $this->certFingerprint;
  }
  /**
   * Describes a list of locations to obtain CRL information, i.e. the
   * DistributionPoint.fullName described by
   * https://tools.ietf.org/html/rfc5280#section-4.2.1.13
   *
   * @param string[] $crlDistributionPoints
   */
  public function setCrlDistributionPoints($crlDistributionPoints)
  {
    $this->crlDistributionPoints = $crlDistributionPoints;
  }
  /**
   * @return string[]
   */
  public function getCrlDistributionPoints()
  {
    return $this->crlDistributionPoints;
  }
  /**
   * The public key that corresponds to an issued certificate.
   *
   * @param PublicKey $publicKey
   */
  public function setPublicKey(PublicKey $publicKey)
  {
    $this->publicKey = $publicKey;
  }
  /**
   * @return PublicKey
   */
  public function getPublicKey()
  {
    return $this->publicKey;
  }
  /**
   * Describes some of the values in a certificate that are related to the
   * subject and lifetime.
   *
   * @param SubjectDescription $subjectDescription
   */
  public function setSubjectDescription(SubjectDescription $subjectDescription)
  {
    $this->subjectDescription = $subjectDescription;
  }
  /**
   * @return SubjectDescription
   */
  public function getSubjectDescription()
  {
    return $this->subjectDescription;
  }
  /**
   * Provides a means of identifiying certificates that contain a particular
   * public key, per https://tools.ietf.org/html/rfc5280#section-4.2.1.2.
   *
   * @param KeyId $subjectKeyId
   */
  public function setSubjectKeyId(KeyId $subjectKeyId)
  {
    $this->subjectKeyId = $subjectKeyId;
  }
  /**
   * @return KeyId
   */
  public function getSubjectKeyId()
  {
    return $this->subjectKeyId;
  }
  /**
   * The hash of the pre-signed certificate, which will be signed by the CA.
   * Corresponds to the TBS Certificate in
   * https://tools.ietf.org/html/rfc5280#section-4.1.2. The field will always be
   * populated.
   *
   * @param string $tbsCertificateDigest
   */
  public function setTbsCertificateDigest($tbsCertificateDigest)
  {
    $this->tbsCertificateDigest = $tbsCertificateDigest;
  }
  /**
   * @return string
   */
  public function getTbsCertificateDigest()
  {
    return $this->tbsCertificateDigest;
  }
  /**
   * Describes some of the technical X.509 fields in a certificate.
   *
   * @param X509Parameters $x509Description
   */
  public function setX509Description(X509Parameters $x509Description)
  {
    $this->x509Description = $x509Description;
  }
  /**
   * @return X509Parameters
   */
  public function getX509Description()
  {
    return $this->x509Description;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CertificateDescription::class, 'Google_Service_CertificateAuthorityService_CertificateDescription');
