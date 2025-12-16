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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1CertInfo extends \Google\Collection
{
  protected $collection_key = 'subjectAlternativeNames';
  /**
   * X.509 basic constraints extension.
   *
   * @var string
   */
  public $basicConstraints;
  /**
   * X.509 `notAfter` validity period in milliseconds since epoch.
   *
   * @var string
   */
  public $expiryDate;
  /**
   * Flag that specifies whether the certificate is valid. Flag is set to `Yes`
   * if the certificate is valid, `No` if expired, or `Not yet` if not yet
   * valid.
   *
   * @var string
   */
  public $isValid;
  /**
   * X.509 issuer.
   *
   * @var string
   */
  public $issuer;
  /**
   * Public key component of the X.509 subject public key info.
   *
   * @var string
   */
  public $publicKey;
  /**
   * X.509 serial number.
   *
   * @var string
   */
  public $serialNumber;
  /**
   * X.509 signatureAlgorithm.
   *
   * @var string
   */
  public $sigAlgName;
  /**
   * X.509 subject.
   *
   * @var string
   */
  public $subject;
  /**
   * X.509 subject alternative names (SANs) extension.
   *
   * @var string[]
   */
  public $subjectAlternativeNames;
  /**
   * X.509 `notBefore` validity period in milliseconds since epoch.
   *
   * @var string
   */
  public $validFrom;
  /**
   * X.509 version.
   *
   * @var int
   */
  public $version;

  /**
   * X.509 basic constraints extension.
   *
   * @param string $basicConstraints
   */
  public function setBasicConstraints($basicConstraints)
  {
    $this->basicConstraints = $basicConstraints;
  }
  /**
   * @return string
   */
  public function getBasicConstraints()
  {
    return $this->basicConstraints;
  }
  /**
   * X.509 `notAfter` validity period in milliseconds since epoch.
   *
   * @param string $expiryDate
   */
  public function setExpiryDate($expiryDate)
  {
    $this->expiryDate = $expiryDate;
  }
  /**
   * @return string
   */
  public function getExpiryDate()
  {
    return $this->expiryDate;
  }
  /**
   * Flag that specifies whether the certificate is valid. Flag is set to `Yes`
   * if the certificate is valid, `No` if expired, or `Not yet` if not yet
   * valid.
   *
   * @param string $isValid
   */
  public function setIsValid($isValid)
  {
    $this->isValid = $isValid;
  }
  /**
   * @return string
   */
  public function getIsValid()
  {
    return $this->isValid;
  }
  /**
   * X.509 issuer.
   *
   * @param string $issuer
   */
  public function setIssuer($issuer)
  {
    $this->issuer = $issuer;
  }
  /**
   * @return string
   */
  public function getIssuer()
  {
    return $this->issuer;
  }
  /**
   * Public key component of the X.509 subject public key info.
   *
   * @param string $publicKey
   */
  public function setPublicKey($publicKey)
  {
    $this->publicKey = $publicKey;
  }
  /**
   * @return string
   */
  public function getPublicKey()
  {
    return $this->publicKey;
  }
  /**
   * X.509 serial number.
   *
   * @param string $serialNumber
   */
  public function setSerialNumber($serialNumber)
  {
    $this->serialNumber = $serialNumber;
  }
  /**
   * @return string
   */
  public function getSerialNumber()
  {
    return $this->serialNumber;
  }
  /**
   * X.509 signatureAlgorithm.
   *
   * @param string $sigAlgName
   */
  public function setSigAlgName($sigAlgName)
  {
    $this->sigAlgName = $sigAlgName;
  }
  /**
   * @return string
   */
  public function getSigAlgName()
  {
    return $this->sigAlgName;
  }
  /**
   * X.509 subject.
   *
   * @param string $subject
   */
  public function setSubject($subject)
  {
    $this->subject = $subject;
  }
  /**
   * @return string
   */
  public function getSubject()
  {
    return $this->subject;
  }
  /**
   * X.509 subject alternative names (SANs) extension.
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
   * X.509 `notBefore` validity period in milliseconds since epoch.
   *
   * @param string $validFrom
   */
  public function setValidFrom($validFrom)
  {
    $this->validFrom = $validFrom;
  }
  /**
   * @return string
   */
  public function getValidFrom()
  {
    return $this->validFrom;
  }
  /**
   * X.509 version.
   *
   * @param int $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return int
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1CertInfo::class, 'Google_Service_Apigee_GoogleCloudApigeeV1CertInfo');
