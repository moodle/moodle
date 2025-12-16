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

class RevocationDetails extends \Google\Model
{
  /**
   * Default unspecified value. This value does indicate that a Certificate has
   * been revoked, but that a reason has not been recorded.
   */
  public const REVOCATION_STATE_REVOCATION_REASON_UNSPECIFIED = 'REVOCATION_REASON_UNSPECIFIED';
  /**
   * Key material for this Certificate may have leaked.
   */
  public const REVOCATION_STATE_KEY_COMPROMISE = 'KEY_COMPROMISE';
  /**
   * The key material for a certificate authority in the issuing path may have
   * leaked.
   */
  public const REVOCATION_STATE_CERTIFICATE_AUTHORITY_COMPROMISE = 'CERTIFICATE_AUTHORITY_COMPROMISE';
  /**
   * The subject or other attributes in this Certificate have changed.
   */
  public const REVOCATION_STATE_AFFILIATION_CHANGED = 'AFFILIATION_CHANGED';
  /**
   * This Certificate has been superseded.
   */
  public const REVOCATION_STATE_SUPERSEDED = 'SUPERSEDED';
  /**
   * This Certificate or entities in the issuing path have ceased to operate.
   */
  public const REVOCATION_STATE_CESSATION_OF_OPERATION = 'CESSATION_OF_OPERATION';
  /**
   * This Certificate should not be considered valid, it is expected that it may
   * become valid in the future.
   */
  public const REVOCATION_STATE_CERTIFICATE_HOLD = 'CERTIFICATE_HOLD';
  /**
   * This Certificate no longer has permission to assert the listed attributes.
   */
  public const REVOCATION_STATE_PRIVILEGE_WITHDRAWN = 'PRIVILEGE_WITHDRAWN';
  /**
   * The authority which determines appropriate attributes for a Certificate may
   * have been compromised.
   */
  public const REVOCATION_STATE_ATTRIBUTE_AUTHORITY_COMPROMISE = 'ATTRIBUTE_AUTHORITY_COMPROMISE';
  /**
   * Indicates why a Certificate was revoked.
   *
   * @var string
   */
  public $revocationState;
  /**
   * The time at which this Certificate was revoked.
   *
   * @var string
   */
  public $revocationTime;

  /**
   * Indicates why a Certificate was revoked.
   *
   * Accepted values: REVOCATION_REASON_UNSPECIFIED, KEY_COMPROMISE,
   * CERTIFICATE_AUTHORITY_COMPROMISE, AFFILIATION_CHANGED, SUPERSEDED,
   * CESSATION_OF_OPERATION, CERTIFICATE_HOLD, PRIVILEGE_WITHDRAWN,
   * ATTRIBUTE_AUTHORITY_COMPROMISE
   *
   * @param self::REVOCATION_STATE_* $revocationState
   */
  public function setRevocationState($revocationState)
  {
    $this->revocationState = $revocationState;
  }
  /**
   * @return self::REVOCATION_STATE_*
   */
  public function getRevocationState()
  {
    return $this->revocationState;
  }
  /**
   * The time at which this Certificate was revoked.
   *
   * @param string $revocationTime
   */
  public function setRevocationTime($revocationTime)
  {
    $this->revocationTime = $revocationTime;
  }
  /**
   * @return string
   */
  public function getRevocationTime()
  {
    return $this->revocationTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RevocationDetails::class, 'Google_Service_CertificateAuthorityService_RevocationDetails');
