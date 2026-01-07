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

namespace Google\Service\CloudIdentity;

class GoogleAppsCloudidentityDevicesV1CertificateAttributes extends \Google\Model
{
  /**
   * Default value.
   */
  public const VALIDATION_STATE_CERTIFICATE_VALIDATION_STATE_UNSPECIFIED = 'CERTIFICATE_VALIDATION_STATE_UNSPECIFIED';
  /**
   * Certificate validation was successful.
   */
  public const VALIDATION_STATE_VALIDATION_SUCCESSFUL = 'VALIDATION_SUCCESSFUL';
  /**
   * Certificate validation failed.
   */
  public const VALIDATION_STATE_VALIDATION_FAILED = 'VALIDATION_FAILED';
  protected $certificateTemplateType = GoogleAppsCloudidentityDevicesV1CertificateTemplate::class;
  protected $certificateTemplateDataType = '';
  /**
   * The encoded certificate fingerprint.
   *
   * @var string
   */
  public $fingerprint;
  /**
   * The name of the issuer of this certificate.
   *
   * @var string
   */
  public $issuer;
  /**
   * Serial number of the certificate, Example: "123456789".
   *
   * @var string
   */
  public $serialNumber;
  /**
   * The subject name of this certificate.
   *
   * @var string
   */
  public $subject;
  /**
   * The certificate thumbprint.
   *
   * @var string
   */
  public $thumbprint;
  /**
   * Output only. Validation state of this certificate.
   *
   * @var string
   */
  public $validationState;
  /**
   * Certificate not valid at or after this timestamp.
   *
   * @var string
   */
  public $validityExpirationTime;
  /**
   * Certificate not valid before this timestamp.
   *
   * @var string
   */
  public $validityStartTime;

  /**
   * The X.509 extension for CertificateTemplate.
   *
   * @param GoogleAppsCloudidentityDevicesV1CertificateTemplate $certificateTemplate
   */
  public function setCertificateTemplate(GoogleAppsCloudidentityDevicesV1CertificateTemplate $certificateTemplate)
  {
    $this->certificateTemplate = $certificateTemplate;
  }
  /**
   * @return GoogleAppsCloudidentityDevicesV1CertificateTemplate
   */
  public function getCertificateTemplate()
  {
    return $this->certificateTemplate;
  }
  /**
   * The encoded certificate fingerprint.
   *
   * @param string $fingerprint
   */
  public function setFingerprint($fingerprint)
  {
    $this->fingerprint = $fingerprint;
  }
  /**
   * @return string
   */
  public function getFingerprint()
  {
    return $this->fingerprint;
  }
  /**
   * The name of the issuer of this certificate.
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
   * Serial number of the certificate, Example: "123456789".
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
   * The subject name of this certificate.
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
   * The certificate thumbprint.
   *
   * @param string $thumbprint
   */
  public function setThumbprint($thumbprint)
  {
    $this->thumbprint = $thumbprint;
  }
  /**
   * @return string
   */
  public function getThumbprint()
  {
    return $this->thumbprint;
  }
  /**
   * Output only. Validation state of this certificate.
   *
   * Accepted values: CERTIFICATE_VALIDATION_STATE_UNSPECIFIED,
   * VALIDATION_SUCCESSFUL, VALIDATION_FAILED
   *
   * @param self::VALIDATION_STATE_* $validationState
   */
  public function setValidationState($validationState)
  {
    $this->validationState = $validationState;
  }
  /**
   * @return self::VALIDATION_STATE_*
   */
  public function getValidationState()
  {
    return $this->validationState;
  }
  /**
   * Certificate not valid at or after this timestamp.
   *
   * @param string $validityExpirationTime
   */
  public function setValidityExpirationTime($validityExpirationTime)
  {
    $this->validityExpirationTime = $validityExpirationTime;
  }
  /**
   * @return string
   */
  public function getValidityExpirationTime()
  {
    return $this->validityExpirationTime;
  }
  /**
   * Certificate not valid before this timestamp.
   *
   * @param string $validityStartTime
   */
  public function setValidityStartTime($validityStartTime)
  {
    $this->validityStartTime = $validityStartTime;
  }
  /**
   * @return string
   */
  public function getValidityStartTime()
  {
    return $this->validityStartTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsCloudidentityDevicesV1CertificateAttributes::class, 'Google_Service_CloudIdentity_GoogleAppsCloudidentityDevicesV1CertificateAttributes');
