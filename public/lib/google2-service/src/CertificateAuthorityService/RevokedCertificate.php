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

class RevokedCertificate extends \Google\Model
{
  /**
   * Default unspecified value. This value does indicate that a Certificate has
   * been revoked, but that a reason has not been recorded.
   */
  public const REVOCATION_REASON_REVOCATION_REASON_UNSPECIFIED = 'REVOCATION_REASON_UNSPECIFIED';
  /**
   * Key material for this Certificate may have leaked.
   */
  public const REVOCATION_REASON_KEY_COMPROMISE = 'KEY_COMPROMISE';
  /**
   * The key material for a certificate authority in the issuing path may have
   * leaked.
   */
  public const REVOCATION_REASON_CERTIFICATE_AUTHORITY_COMPROMISE = 'CERTIFICATE_AUTHORITY_COMPROMISE';
  /**
   * The subject or other attributes in this Certificate have changed.
   */
  public const REVOCATION_REASON_AFFILIATION_CHANGED = 'AFFILIATION_CHANGED';
  /**
   * This Certificate has been superseded.
   */
  public const REVOCATION_REASON_SUPERSEDED = 'SUPERSEDED';
  /**
   * This Certificate or entities in the issuing path have ceased to operate.
   */
  public const REVOCATION_REASON_CESSATION_OF_OPERATION = 'CESSATION_OF_OPERATION';
  /**
   * This Certificate should not be considered valid, it is expected that it may
   * become valid in the future.
   */
  public const REVOCATION_REASON_CERTIFICATE_HOLD = 'CERTIFICATE_HOLD';
  /**
   * This Certificate no longer has permission to assert the listed attributes.
   */
  public const REVOCATION_REASON_PRIVILEGE_WITHDRAWN = 'PRIVILEGE_WITHDRAWN';
  /**
   * The authority which determines appropriate attributes for a Certificate may
   * have been compromised.
   */
  public const REVOCATION_REASON_ATTRIBUTE_AUTHORITY_COMPROMISE = 'ATTRIBUTE_AUTHORITY_COMPROMISE';
  /**
   * The resource name for the Certificate in the format
   * `projects/locations/caPools/certificates`.
   *
   * @var string
   */
  public $certificate;
  /**
   * The serial number of the Certificate.
   *
   * @var string
   */
  public $hexSerialNumber;
  /**
   * The reason the Certificate was revoked.
   *
   * @var string
   */
  public $revocationReason;

  /**
   * The resource name for the Certificate in the format
   * `projects/locations/caPools/certificates`.
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
   * The serial number of the Certificate.
   *
   * @param string $hexSerialNumber
   */
  public function setHexSerialNumber($hexSerialNumber)
  {
    $this->hexSerialNumber = $hexSerialNumber;
  }
  /**
   * @return string
   */
  public function getHexSerialNumber()
  {
    return $this->hexSerialNumber;
  }
  /**
   * The reason the Certificate was revoked.
   *
   * Accepted values: REVOCATION_REASON_UNSPECIFIED, KEY_COMPROMISE,
   * CERTIFICATE_AUTHORITY_COMPROMISE, AFFILIATION_CHANGED, SUPERSEDED,
   * CESSATION_OF_OPERATION, CERTIFICATE_HOLD, PRIVILEGE_WITHDRAWN,
   * ATTRIBUTE_AUTHORITY_COMPROMISE
   *
   * @param self::REVOCATION_REASON_* $revocationReason
   */
  public function setRevocationReason($revocationReason)
  {
    $this->revocationReason = $revocationReason;
  }
  /**
   * @return self::REVOCATION_REASON_*
   */
  public function getRevocationReason()
  {
    return $this->revocationReason;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RevokedCertificate::class, 'Google_Service_CertificateAuthorityService_RevokedCertificate');
